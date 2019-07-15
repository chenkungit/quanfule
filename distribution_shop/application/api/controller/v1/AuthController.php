<?php

namespace app\api\controller\v1;

use app\api\validate\AuthValidate;
use app\common\controller\ApiController;
use app\common\Entity\DsRelation;
use app\common\Enums\RedisKeyEnums;
use app\common\Utils\Token;
use app\service\CaptchaService;
use think\Db;
use think\Request;
use app\common\model\Users;
use GuzzleHttp\Client;
use code;

class AuthController extends ApiController
{
    protected $users;
    protected $client;

    public function __construct(Request $request, Users $users, Client $client)
    {
        parent::__construct($request);
        $this->users = $users;
        $this->client = $client;
    }


    public function signup_p()
    {
        $authValidate = new AuthValidate();
        $authValidate->scene('sign_up_p')->check($this->data);

//        CaptchaService::service()->validate($this->data['device_id'], $this->data['captcha']);

        $res = Db::name('users')->where('user_name', $this->data['account'])->field('user_id')->find();
        if (!empty($res)) {
            return json_error('', '该用户已注册', self::SYSTEM_ERROR);
        }
        Db::startTrans();
        try {

            $user_id = $this->users->register($this->data['account'], '', trim($this->data['password']));

            /*存储用户全局信息到redis*/
            $token = $this->redis_store($this->generateToken($this->data['account']), $user_id, $this->data['account']);
            /*返回个人信息*/
            $info = $this->users->user_info($user_id);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort(400, $e->getMessage());
        }
        return $this->respondWithArray(['key' => $token, 'info' => $info], '注册成功');

    }

    /* 注册
    * 手机号码的准确性已经在发送手机验证码时确定过了
    */
    public function signup()
    {

        $validate = $this->validate($this->data, 'app\api\validate\AuthValidate.signup');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }

        $flag = $this->checkCode($this->data['sms_code'], $this->data['mobile'], 2);

        if ($flag !== true) {
            return $flag;
        }
        $res = Db::name('users')->where('user_name', $this->data['mobile'])->field('user_id')->find();
        if (!empty($res)) {
            return json_error('', '该用户已注册', self::SYSTEM_ERROR);
        }
        Db::startTrans();
        try {

            $user_id = $this->users->register($this->data['mobile'], '', trim($this->data['password']));

            /*存储用户全局信息到redis*/
            $token = $this->redis_store($this->generateToken($this->data['mobile']), $user_id, $this->data['mobile']);
            /*返回个人信息*/
            $info = $this->users->user_info($user_id);
            /*注册默认添加一条关系记录，parent_id为0 */
            Db::execute('insert into ecs_ds_relation (user_id, parent_id,level,current_achieve) values (?,?,?,?)',[$user_id,0,0,0]);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort(400, $e->getMessage());
        }
        return $this->respondWithArray(['key' => $token, 'info' => $info], '注册成功');
    }

    /* 登录入口*/
    public function signin()
    {

        $validate = $this->validate($this->data, 'app\api\validate\AuthValidate.signin_first');

        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        switch ($this->data['login_type']) {
            case 1: /*-密码登陆-*/
                return $this->password_signin();
            case 2: /*-验证码登陆-*/
                return $this->sms_code_signin();
        }
    }


    /*-
    * 用户密码登陆
    * @return json
    -*/
    private function password_signin()
    {
//        $this->submit_lock(get_class(),5);
        $validate = $this->validate($this->data, 'app\api\validate\AuthValidate.password_signin');

        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }
        $res = $this->users->findUser($this->data['mobile']);

        $flag = $this->password_checked(trim($this->data['password']), $res['salt'], $res['password']);
        if ($flag !== true) {
            return $flag;
        }

        return $this->common_signin_operate($res['user_id'], [], $this->data['mobile']);
    }


    /*-
    * 手机 手机验证码登陆
    * @return json
    -*/
    private function sms_code_signin()
    {

        $validate = $this->validate($this->data, 'app\api\validate\AuthValidate.sms_code_signin');

        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }
        $res = $this->users->findUser($this->data['mobile']);

        $flag = $this->checkCode($this->data['sms_code'], $this->data['mobile'], 1);
        if ($flag !== true) {
            return $flag;
        }

        return $this->common_signin_operate($res['user_id'], [], $this->data['mobile']);
    }

    /*-第三方登陆-*/
    public function oauth2()
    {
        $validate = $this->validate($this->data, 'app\api\validate\AuthValidate.oauth2');

        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        $data = $this->switch_oauth($this->data['type'], $this->data['openid']);

        $res = Db::name('users')->where($data)->find();

        if ($res) {
            return $this->common_signin_operate($res['user_id'], [], $res['mobile']);
        } else {
            return $this->setStatusCode(self::UN_REGISTERED)->respondWithError('请绑定手机号');
        }
    }

    public function oauth2_binding()
    {

        $validate = $this->validate($this->data, 'app\api\validate\AuthValidate.oauth2_binding');

        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        $info = $this->users->findUser($this->data['mobile']);


        $flag = $this->checkCode($this->data['sms_code'], $this->data['mobile'], 5);

        if ($flag !== true) {
            return $flag;
        }

        $data = $this->switch_oauth($this->data['type'], $this->data['openid']);

        /*-用户已存在则更新字段 将第三方登陆的openid更新到用户信息中-*/
        if ($info) {

            return $this->common_signin_operate($info['user_id'], $data, $info['mobile']);

        } else {

            $oauth2_data = [
                'nick' => isset($this->data['nick']) ? $this->data['nick'] : '',
                'avatar' => isset($this->data['avatar']) ? $this->data['avatar'] : '',
                'sex' => isset($this->data['sex']) ? $this->data['sex'] : 0
            ];
            /*-将第三方的标识符和可能传的头像和性格合并 -*/
            $oauth2_data = array_merge($oauth2_data, $data);
            try {
                $user_id = $this->users->register($this->data['mobile'], '', '', $oauth2_data);

                $token = $this->generateToken($this->data['mobile']);
                /*存储用户全局信息到redis*/
                $this->redis_store($token, $user_id, $this->data['mobile']);

                /*返回个人信息*/
                $permission_info = $this->users->user_info($user_id);

            } catch (\Exception $e) {
                abort(500, $e->getMessage());
            }
        }

        return $this->respondWithArray(['key' => $token, 'info' => $permission_info], '绑定成功');
    }


    /*-
     * 用户登陆成功后的公共操作
     * @param int        $user_id  用户id
     * @param array      $data     第一次第三方登陆 绑定下 唯一标识符
     * @param number     $mobile   手机号码
     * @return json
    -*/
    private function common_signin_operate($user_id, $data = [], $mobile)
    {
        $update['last_ip'] = $this->request->ip();
        $update['visit_count'] = Db::raw('visit_count+1');

        if (count($data) > 0) {
            $update = array_merge($update, $data);
        }

        try {
            Db::name('users')->where('user_id', $user_id)->update($update);

            /*返回个人信息*/
            $info = $this->users->user_info($user_id);
            /*存储用户全局信息到redis*/
            $token = $this->redis_store($this->generateToken($mobile), $user_id, $mobile);
            /*-更新下购物车-*/
            $this->users->recalculate_price($user_id);

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this->respondWithArray(['key' => $token, 'info' => $info], '登陆成功');
    }


    public function forget()
    {

        $validate = $this->validate($this->data, 'app\api\validate\AuthValidate.forget');

        if ($validate !== true) {
            return json_error('', $validate, self::INVALID_PARAMETER);
        }
        $flag = $this->checkCode($this->data['sms_code'], $this->data['mobile'], 3);
        if ($flag !== true) {
            return $flag;
        }

        $res = $this->users->findUser($this->data['mobile']);

        $update = [
            'password' => md5(md5(trim($this->data['password'])) . $res['salt']),
            'last_time' => date('Y-m-d H:i:s'),
            'last_ip' => $this->request->ip(),
            'visit_count' => Db::raw('visit_count+1')
        ];

        try {

            Db::name('users')->where('user_id', $res['user_id'])->update($update);

            $token = $this->generateToken($this->data['mobile']);
            /*返回个人信息*/
            $info = $this->users->user_info($res['user_id']);
            /*存储用户全局信息到redis*/
            $this->redis_store($token, $res['user_id'], $info['mobile']);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this->respondWithArray(['key' => $token, 'info' => $info], '修改密码成功');
    }

    public function logout()
    {

        try {
            $access_token_key = Token::getAccessToken($this->data['key']);

            $user_id = redis()->hGet($access_token_key, 'user_id');
            redis()->delete($access_token_key);
            redis()->delete(Token::getTokenIndex($user_id));
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        return $this->respondWithArray('', '退出登录成功');
    }


    private function password_checked($password, $salt, $md5_password)
    {
        $md5Password = md5(md5($password) . $salt);
        if ($md5Password == $md5_password) {
            return true;
        } else {
            return json_error('', '密码错误', self::INVALID_PARAMETER);
        }
    }


    private function switch_oauth($type, $openid)
    {
        switch ($type) {
            case code::oauth2_wechat:
                $data['wxunionid'] = $openid;
                break;
            case code::oauth2_qq:
                $data['qqopenid'] = $openid;
                break;
            case code::oauth2_alipay:
                $data['alipayopenid'] = $openid;
                break;
            case code::oauth2_sina:
                $data['sinaopenid'] = $openid;
                break;
            default:
                abort(500, '未知类型');
        }
        return $data;
    }


}