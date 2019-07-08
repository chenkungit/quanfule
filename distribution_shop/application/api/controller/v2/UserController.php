<?php
namespace app\api\controller\v2;


use app\common\controller\ApiController;
use think\Db;
use think\Request;
use app\common\model\Users;

class UserController extends ApiController
{
    protected $users;

    public function __construct(Request $request,Users $users)
    {
        parent::__construct($request);
        $this->checkBasicAuth(1);
        $this->users = $users;

    }

    /*-
    * 登陆后换绑或者绑定手机号
    * 手机号的唯一性 在发送 绑定验证码时已确定
     * 将key替换
-   */
    public function modify_password(){

        $validate = $this->validate($this->data, 'app\api\validate\UserValidate.modify_password');

        if($validate !== true){
            return json_error(NULL,$validate,self::INVALID_PARAMETER);
        }

        $res = $this->users->where('user_id',$GLOBALS['user_id'])->find();

        if($res['password'] != md5(md5($this->data['oldpassword']).$res['ec_salt'])){
            return json_error(NULL,'请输入正确的旧密码',self::INVALID_PARAMETER);
        }

        $update['password'] = md5(md5(trim($this->data['newpassword'])).$res['ec_salt']);

        try{
            Db::name('users')->where('user_id',$GLOBALS['user_id'])->update($update);

            $key = $this->generateToken($GLOBALS['mobile']);

            redis()->RENAME($this->data['key'],$key);

        }catch (\Exception $e){
            abort(500,$e->getMessage());
        }
        $this->submit_lock(get_class(),5);
        return $this->respondWithArray(['key'=>$key],'修改密码成功');
    }





}