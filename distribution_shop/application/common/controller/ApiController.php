<?php

namespace app\common\controller;

use app\api\exception\ValidateException;
use app\common\Enums\Code;
use app\common\Enums\RedisKeyEnums;
use app\common\Utils\Token;
use app\common\model\Users;
use app\service\Vip\VipUserInfoService;
use GuzzleHttp\Client;
use think\Controller;
use think\exception\HttpException;
use think\Request;

class ApiController extends Controller
{

    protected $statusCode = 200;
    protected $request;
    protected $data;
    protected $header;
    protected $source;

    protected $is_vip = 0;

    const SYSTEM_ERROR = -1; //系统错误
    const EMPTY_ERROR = -2; //返回值为空
    const SUCCESS = 200; //请求成功
    const ERROR = 400; //失败
    const ERROR_NO_AUTH = 1000; //签名失败
    const EMPTY_PARAMETER = 450;
    const UNLOGIN = 2001; //未登录
    const INVALID_PARAMETER = 2002; //不合法的参数,缺少参数
    const INVALID_REQUEST = 2003; //不合法的请求格式
    const INVALID_TOKEN = 2004; //不合法TOKEN
    const REFRESH_TOKEN = 2005;//刷新token值标识符
    const CHECK_CODE_ERROR = 2006; //验证码错误
    const UN_REGISTERED = 2007; //未注册
    const REGISTERED = 2009; //已注册，直接登录
    const REDIRECT = 2010; //前端路由重定向标识符
    const ACCOUNT_SYSTEM = 2018;//系统账号
    const ACCOUNT_BAD = 2019;//账号异常
    const BEYOND_LIMIT = 2020; //超过上限
    const PAYMENT_ERROR = 2030;//支付失败
    const AUTH_FALSE = 0; //未认证
    const AUTH_TURE = 1; //已认证
    const PAY_DONE = 2040;
    const qinniu_url = '';
    const ttl = 2592000; /*-一个月的秒数*/
    const short_ttl = 3600;
    const PRO_SECRET = '';

    public function __construct(Request $request)
    {

        parent::__construct();
        $this->request = $request;
        $this->data = $request->param();


        $this->source = $this->request->header('source') ? $this->request->header('source') : '';
        /*--未登录时默认折扣和等级*/
        $GLOBALS['user_id'] = 0;
        $GLOBALS['discount'] = 1;
        $this->is_vip = 0;
    }

    /*-
    初级验证 仅返回等级和折扣
    -*/
    protected function checkBasicAuth($rank = 0, $sign = false)
    {

        $GLOBALS['referer'] = $this->referer();
        /*-获取body或者header中的值-*/

        if (!empty($this->data['key'])) {
            $key = $this->data['key'];
        } else if (!empty($this->request->header('key'))) {
            $key = $this->request->header('key');
        } else {
            $key = '';
        }
        $GLOBALS['key'] = $key;
        $key = sprintf(RedisKeyEnums::ACCESS_TOKEN, $key);
        if ($rank == 0) {
            if ($key) {
                if (redis()->exists($key)) {
                    $hmset = redis()->hGetAll($key);

                    $GLOBALS['user_id'] = $hmset['user_id'];
                    $GLOBALS['mobile'] = $hmset['mobile'];
                    $GLOBALS['discount'] = $hmset['discount'];

                    /*-方便验证-*/
                    $this->is_vip = $hmset['is_vip'];
                    $this->data['user_id'] = $hmset['user_id'];
                }
            }
        }
        if ($rank == 1) {
            if (!empty($key)) {
                if (redis()->exists($key)) {
                    $hmset = redis()->hGetAll($key);

                    $GLOBALS['user_id'] = $hmset['user_id'];
                    $GLOBALS['mobile'] = $hmset['mobile'];
                    $GLOBALS['discount'] = $hmset['discount'];
                    /*-方便验证-*/
                    $this->is_vip = $hmset['is_vip'];
                    $this->data['user_id'] = $hmset['user_id'];
                } else {
                    throw new ValidateException('账号异常,请重新登录', Code::INVALID_TOKEN);
                }
            } else {
                throw new ValidateException('账号异常,请重新登录', Code::UNLOGIN);
            }

        }

        //开启sign验证
        if ($sign == true) {

        }

    }

    protected function referer()
    {


        $source = $this->request->header('Source');
        $agent = $this->request->server('HTTP_USER_AGENT');
        if (isset($source) && $source == 'xcx') {
            return 'xcx';
        }
        if (isset($source) && $source == 'xcx') {
            return 'md';
        }
        if (isset($source) && $source == 'h5') {
            return 'h5';
        }

        if (strpos($agent, 'iPhone') || strpos($agent, 'iPad') || strpos($agent, 'iOS')) {
            return 'APP-IOS';
        }
        if (strpos($agent, 'Android')) {
            return 'APP-Android';
        }

        return 'other';


    }

    /*-
    产生token加密
    -*/
    protected function generateToken($u_phone)
    {
        $time = time();
        $salt = config('others.')['salt'];
        $token = md5(md5($u_phone . $time . $salt . strval(rand(0, 999999))));
        $token_key = sprintf(RedisKeyEnums::ACCESS_TOKEN, $token);
        /*-查找redis库中是否存在重复的key  存在在递归  概率很小 不排除发生-*/
        if (redis()->exists($token_key)) {
            return $this->generateToken($u_phone);
        }
        return $token;
    }

    /*
    *签名验证
    */

    protected function check_sign($params)
    {
        if (!isset($params['sign'])) {
            throw new HttpException(200, '签名验证失败', null, [], self::ERROR_NO_AUTH);
        }
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $query = '';
        foreach ($params as $k => $v) {
            $query .= $k . '=' . $v . '&';
        }
        $pro_validate_sign = md5('cwj' . substr(md5($query . self::PRO_SECRET), 0, -3));
        if ($pro_validate_sign != $sign) {
            throw new HttpException(200, '签名验证失败', null, [], self::ERROR_NO_AUTH);
        }
    }

    protected function redis_store($token, $user_id, $mobile, $ttl = true)
    {
        $User = new Users();
        $hmset = $User->update_user_info($user_id);

        $hmset['user_id'] = $user_id;
        $hmset['mobile'] = $mobile;
        $hmset['is_vip'] = 0;
        $hmset['discount'] = 1;
        if ($vipUserInfo = VipUserInfoService::service()->getInfo($user_id)) {
            $hmset['is_vip'] = 1;
            $hmset['discount'] = $vipUserInfo['discount'];
        }

        if ($old_token_string = redis()->get(Token::getTokenIndex($user_id))) {
            $token = $old_token_string;
        }

        redis()->hMset(Token::getAccessToken($token), $hmset);
        /*-短期令牌-*/
        if ($ttl === false) {
            redis()->expire(Token::getAccessToken($token), static::short_ttl);
        } else {
            redis()->expire(Token::getAccessToken($token), static::ttl);
        }

        $this->setUniqueIndex($user_id, $token, false);
        return $token;
    }

    // 设置唯一索引 暂时不需要单点登录 只给最后的token一个标识符
    protected function setUniqueIndex($user_id, $token, $flag = false)
    {
        $UniqueIndex = Token::getTokenIndex($user_id);

        // 删除旧token数据
        if ($flag === true) {
            $beforeToken = redis()->get($user_id);
            if (!empty($beforeToken)) {
                redis()->del($beforeToken);
            }
        }
        // 更新唯一索引
        redis()->set($UniqueIndex, $token, ['EX' => RedisKeyEnums::MONTH_TTL]);
    }


    /**
     * 防止重复提交表单redis锁
     * @param $class_name {方法名}
     * @param $ttl {锁时间}
     */
    public function submit_lock($class_name, $ttl)
    {
        $lock_name = sprintf(RedisKeyEnums::SUBMIT_LOCK, $class_name, $GLOBALS['user_id']);

        if (redis()->exists($lock_name)) {
            abort(500, '操作频繁,请稍后再试');
        }

        redis()->set($lock_name, 1, ['EX' => $ttl]);
    }

    /**
     * 验证验证码
     * @param $sms_code {手机验证码}
     * @param $mobile {手机号码}
     * @param $type {短信类型}
     *
     * @return bool
     */
    protected function checkCode($sms_code, $mobile, $type)
    {

        $hash_name = sprintf(RedisKeyEnums::SMS_CONTROL_HASH, $type, $mobile);

        $smsCheckCode = redis()->hGetAll($hash_name);
//        $smsCheckCode['sms_code'] = 111111;
//        $smsCheckCode['ttl'] = 111111;

        if (!$smsCheckCode) {
            return json_error('', '验证码错误，请重新获取！', self::SYSTEM_ERROR);
        }

        if ($smsCheckCode['ttl'] == 1) {
            return json_error('', '验证码错误已达上限，请重新获取验证码!', self::INVALID_PARAMETER);
        }

        if ($smsCheckCode['sms_code'] != $sms_code) {
            if ($smsCheckCode['ttl'] > 1) {
                $smsCheckCode['ttl']--;
                redis()->hSet($hash_name, 'ttl', $smsCheckCode['ttl']);
                return json_error('', '验证码错误，请重新获取', self::INVALID_PARAMETER);
            }

//            RedisII::delete($hash_name);
            return json_error('', '验证码错误已达上限，请重新获取验证码!', self::INVALID_PARAMETER);
        }
        //正式环境开启
        redis()->delete($hash_name);

        return true;

    }

    protected function _validate()
    {
        foreach (func_get_args() as $key) {
            if (!isset($this->data[$key])) {
                throw new \Exception("The $key parameter is required");
            }
        }
    }

    /**
     * Get the status code.
     *
     * @return int $statusCode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set the status code.
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Repond a no content response.
     *
     * @return response
     */
    public function noContent()
    {
        return json(null, 204);
    }

    public function respondWithArray($array = [], $msg = '请求成功', array $headers = [])
    {
        return json_success($array, $msg, $this->statusCode, $headers);
    }

    /**
     * Respond the error message.
     *
     * @param  string $message
     * @return json
     */
    protected function respondWithError($message)
    {
//        if ($this->statusCode === 200) {
//            trigger_error(
//                "You better have a really good reason for erroring on a 200...",
//                E_USER_WARNING
//            );
//        }

        return json_error(NULL, $message, $this->statusCode);

    }

}