<?php
namespace app\dashboard\model;


use think\Model;

class Privilege extends Model
{
    protected $table = 'ecs_admin_user';

    public function get_admin_users(){

        $fields = "user_id, user_name,node_list, email,created_time as add_time, FROM_UNIXTIME(last_login,'%Y/%m/%d') as last_login , role_id ";

        return $this->name('admin_user')->field($fields)->order('last_login desc')->select()->toArray();
    }
}