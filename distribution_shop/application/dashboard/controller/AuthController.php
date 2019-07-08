<?php
namespace app\dashboard\controller;

use app\common\controller\WebController;
use Firebase\JWT\JWT;
use com\Tree;
use think\Db;

class AuthController extends WebController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function login(){

        $validate = $this->validate($this->data, 'app\dashboard\validate\AuthValidate.login');

        if(!is_array($validate)){
            return json_error('',$validate,self::INVALID_PARAMETER);
        }


        $MenuAndRule =$this->getMenuAndRule($validate['node_list']);

        $node_list = explode(',',$validate['node_list']);

        $arr['accessToken'] = $this->build_jwt($validate['user_id'],$validate['user_name'],$node_list);
        $arr['menu'] = $MenuAndRule['menusList'];

        $category = Db::name('category')->field('cat_id,cat_name,parent_id')->where('is_show',1)->order("sort_order ASC, cat_id ASC")->select();
        $tree = new Tree();
        $arr['argument']['category']    = $tree->list_to_tree($category,'cat_id','parent_id');
        $arr['argument']['sendaddress'] = Db::name('sendaddress')->field('id as value,address as label')->select();
        $arr['argument']['shipping']    = Db::name('shipping')->field('shipping_id,shipping_name')->select();
        $arr['argument']['brand']    = Db::name('brand')->field('brand_id as value,brand_name as label')->select();
        $arr['argument']['suppliers']    = Db::name('suppliers')->field('id as value,name as label')->select();
        $arr['argument']['vip_setting']    = Db::name('vip_setting')->field('id as value,name as label')->select();
        $o_status = [];
        foreach (\code::ss as $key => $item){
            $o_status[] = [
                'label'=>$item,
                'value'=>$key
            ];
        }
        array_unshift($o_status,['label'=>'请选择','value'=>99]);
        $arr['argument']['o_status'] = $o_status;
//        $arr['argument']['author_name'] = Db::name('users')->alias('u')->field('u.user_name')->join('admin_user a','a.author_id = u.user_id','LEFT')->where('a.user_id','=',$GLOBALS['user_id'])->find();
        $author_id = Db::name('admin_user')->field('anthor_id')->where('user_name',$this->data['account'])->find();

        $user = Db::name('users')->field('user_name')->where('user_id','=',$author_id['anthor_id'])->find();
       $arr['anthor_name'] = $user['user_name'];

        $update['last_login'] = time();
        $update['last_ip'] = $this->ip;
        /*更新登陆时间和ip*/
        Db::name('admin_user')->where('user_id',$validate['user_id'])->update($update);


        return $this->respondWithArray($arr,'登录成功');
    }



    private function build_jwt($user_id,$user_name,$role=[]){

        $salt = config('others.salt');
        $token = [
            'iat'=>time(),
            'exp' => $this->get_tomorrow_date(),
            'user_id'=>$user_id,
            'user_name'=>$user_name,
            'role'=>$role
        ];
        return JWT::encode($token, $salt);
    }

    /*-返回第二天凌晨3点为统一失效时间-*/
    private function get_tomorrow_date() {
        return mktime(3,0,0,date("m"),date("d")+1,date("Y"));
    }

    /**
     * 返回菜单及权限
     *
     * @access  protected
     * @param   int   $node_list  用户权限列表
     * @return  array
     */
    protected function getMenuAndRule($node_list)
    {
        $where = [];
        array_push($where,['status','=',1]);
//        $map[] = ['status','=','think'];
        /*-admin用户返回所有权限-*/

        /*--node_list为-1 返回所有权限--*/
        if(!empty($node_list) && $node_list != '-1'){
            array_push($where,['rule_id','in',explode(',',$node_list)]);
        }

        $menusList = Db::name('admin_menu')->where($where)->order('sort desc')->select();
        if (!$menusList) {
            abort('400','该用户无权限');
        }
        $roleList = Db::name('admin_rule')->where($where)->column('name');

        $roleList = array_values(array_filter($roleList));

        //处理菜单成树状
        $tree = new Tree();

        $ret['menusList'] = $tree->list_to_tree($menusList, 'id', 'pid', 'child', 0, true, array('pid'));
        $ret['menusList'] = memuLevelClear($ret['menusList']);
        // 处理规则成树状
        $ret['roleList'] = $roleList;
        return $ret;
    }

}