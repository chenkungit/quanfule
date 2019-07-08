<?php

namespace app\dashboard\controller;

use app\api\exception\IllegalException;
use app\common\controller\WebController;
use app\common\Enums\AdminUserEnums;
use app\dashboard\model\Log;
use com\Tree;
use think\Db;
use think\Request;
use app\dashboard\model\Privilege;

class PrivilegeController extends WebController
{
    protected $Privilege;

    public function __construct(Request $request, Privilege $privilege)
    {
        parent::__construct($request);
        $this->Privilege = $privilege;
    }

    public function lists()
    {

        $arr['item'] = $this->Privilege->get_admin_users();
        $arr['pagecount'] = 1;


        foreach ($arr['item'] as &$item) {
            if ($GLOBALS['user_id'] == AdminUserEnums::ADMIN_ID) {
                $item['can_modify'] = 1;
            }
            if ($GLOBALS['user_id'] == $item['user_id']) {
                $item['can_modify'] = 1;
            }
        }

        return $this->respondWithArray($arr);

    }

    public function allot_get(Tree $tree)
    {

        $node_list = Db::name('admin_user')->where('user_id', $this->data['user_id'])->value('node_list');

        $arr = Db::name('admin_menu')->field('ar.*')->alias('am')->leftJoin('ecs_admin_rule ar', 'am.rule_id = ar.rule_id and am.status = 1')->select();

        foreach ($arr as $item) {
            $res[$item['rule_id']] = $item;
        }

        /*- -1拥有所有权限 -*/
        if ($node_list == '-1') {
            foreach ($res as &$item) {
                $item['connect'] = 1;
            }
        } else {
            $tmp = explode(',', $node_list);
            $jiaoji = array_intersect(array_keys($res), $tmp);

            foreach ($jiaoji as $item) {
                $res[$item]['connect'] = 1;
            }
        }


        $arr = $tree->list_to_tree($res, 'rule_id');
        $arr = memuLevelClear($arr);
        return $this->respondWithArray($arr);
    }


    public function allot_put()
    {
        $this->_validate('node_list', 'user_id');
        try {
            $this->Privilege->allowField('node_list')->save($this->data, ['user_id' => $this->data['user_id']]);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        return $this->respondWithArray(null, '分配权限成功');
    }

    public function add()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\PrivilegeValidate.add');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }
        try {
            $insert['ec_salt'] = rand(1000, 9999);
            $insert['user_name'] = $this->data['user_name'];
            $insert['password'] = md5(md5($this->data['password']) . $insert['ec_salt']);
            $insert['add_time'] = time();

            $this->Privilege->allowField('user_name,password,ec_salt')->save($insert);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
        return $this->respondWithArray(null, '注册成功');
    }

    public function rePassword()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\PrivilegeValidate.repassword');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }


        if ($GLOBALS['user_id'] != AdminUserEnums::ADMIN_ID && $GLOBALS['user_id'] != $this->data['user_id']) {
            throw new IllegalException('非法请求');
        }

        $update['ec_salt'] = rand(1000, 9999);
        $update['password'] = md5(md5($this->data['password']) . $update['ec_salt']);
        try {
            Db::name('admin_user')->where('user_id', $this->data['user_id'])->update($update);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this->respondWithArray(NULL, '更新成功');
    }

    public function delete()
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\PrivilegeValidate.delete');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        if ($this->data['user_id'] == $GLOBALS['user_id']) {
            return json_error(NULL, '不能删除自己', self::INVALID_PARAMETER);
        }

        try {
            $this->Privilege->where('user_id', $this->data['id'])->delete();
            Log::record($this->data['id'], $this->ip);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }

        return $this->respondWithArray(NULL, \lang::delete);
    }
}