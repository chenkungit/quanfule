<?php

namespace app\api\controller\v1;


use app\common\controller\ApiController;
use app\common\controller\Upload;
use app\common\Entity\SystemSetting;
use app\common\Enums\UploadEnums;
use app\common\Factory\UploadFactory;
use app\service\System\SystemSettingService;
use app\service\Vip\VipUserInfoService;
use think\Db;
use think\Request;
use app\common\model\Users;

class UserController extends ApiController
{
    protected $users;

    public function __construct(Request $request, Users $users)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
        $this->users = $users;

    }

    /*-给个标识符 暂时给网页端只获取info-*/
    public function info()
    {

        $flag = empty($this->data['flag']) ? 0 : $this->data['flag'];

        if ($flag) {
            $arr['info'] = $this->users->user_info();
        } else {
            $arr['info'] = $this->users->user_info();
            $arr['vip_info'] = [];

            if ($arr['info']['is_vip'] == 1) {
                $arr['vip_info'] = VipUserInfoService::service()->getInfo($this->data['user_id']);
            }
            $arr['system_info'] = SystemSetting::field('key,value')->column('value', 'key');

            $arr['order_count'] = $this->users->order_count();
            $arr['cart_sum'] = Db::name('cart')->where('user_id', $GLOBALS['user_id'])->sum('goods_number');
        }

        return $this->respondWithArray($arr);
    }

    public function info_edit()
    {

        $validate = $this->validate($this->data, 'app\api\validate\UserValidate.info_edit');

        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }
        unset($this->data['avatar']);
        /*--如果修改图片则直接先返回图片url-*/
        $cb = [];
        if (isset($_FILES['avatar'])) {
            $upload = UploadFactory::getService(UploadEnums::LOCAL);
            $this->data['avatar'] = $upload->singleUpload($_FILES['avatar'], 'avatar');
            $cb['avatar'] = $this->data['avatar'];
        }

        try {
            $this->users->allowField(['nick', 'avatar', 'sex', 'birthday'])->save($this->data, ['user_id' => $GLOBALS['user_id']]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this->respondWithArray($cb, '编辑成功');

    }

    /*-
    * 登陆后换绑或者绑定手机号
    * 手机号的唯一性 在发送 绑定验证码时已确定
     * 将key替换
-   */
    public function modify_password()
    {

        $validate = $this->validate($this->data, 'app\api\validate\UserValidate.modify_password');

        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        $res = $this->users->where('user_id', $GLOBALS['user_id'])->find();

        if ($res['password'] != md5(md5(trim($this->data['oldpassword'])) . $res['salt'])) {
            return json_error(NULL, '请输入正确的旧密码', self::INVALID_PARAMETER);
        }

        $update['password'] = md5(md5(trim($this->data['newpassword'])) . $res['salt']);

        try {
            Db::name('users')->where('user_id', $GLOBALS['user_id'])->update($update);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        $this->submit_lock(get_class(), 5);
        return $this->respondWithArray(NULL, '修改密码成功');
    }


    public function upload(Upload $upload)
    {


        if (!isset($this->data['prefix']) || empty($this->data['prefix'])) {
            abort(2002, '请选择文件前缀');
        }

        if (!isset($_FILES['img'])) {
            return json_error(NULL, '请上传图片', self::INVALID_PARAMETER);
        }
        $img_arr = $upload->multi_arrange($_FILES);
        foreach ($img_arr as $item) {
            $arr['img_arr'][] = self::qinniu_url . '/' . $upload->single_upload($item, $this->data['prefix']);
        }

        return $this->respondWithArray($arr);
    }


}