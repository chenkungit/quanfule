<?php

namespace app\common\model;

use Firebase\JWT\JWT;
use think\Db;
use think\Exception;
use think\Model;
use code;
use Sms;
use app\common\model\Bonus;

class Users extends Load
{

    public function findUser($mobile)
    {


        $sql = "SELECT user_id,user_name,password,salt,mobile,visit_count,nick,avatar FROM";
        $sql .= "(SELECT user_id,user_name,mobile,password, ec_salt AS salt,visit_count,nick,avatar FROM `ecs_users` ";
        $sql .= " WHERE `mobile` = '$mobile' AND `is_delete` = 0 ";
        $sql .= " union ";
        $sql .= " SELECT `user_id`, `user_name`, `mobile`,`password`, ec_salt AS salt, `visit_count`,`nick`,`avatar` FROM `ecs_users` ";
        $sql .= " WHERE `user_name` = '$mobile' AND `is_delete` = 0 ) as t1 ";
        $sql .= " order by mobile desc limit 1";

        $res = $this->query($sql);
        if ($res) {
            return $res[0];
        } else {
            return false;
        }
    }

    /**
     * 注册用户 并在k3中注册会员卡  {发送新人红包}
     * @param  number $mobile [手机号码]
     * @param  string $wxunionid [微信unionid]
     * @param  string $password 密码注册时取密码
     * @param  array $oauth_data 第三方登陆的data 方便
     * @return number    用户插入表中的自增id
     */
    public function register($mobile, $wxunionid = '', $password = '', $oauth_data = [])
    {
        $salt = rand(1000, 9999);

        $this->startTrans();
        try {
            if ($password) {
                $password = md5(md5($password) . $salt);
            } else {
                $password = md5(md5(substr($mobile, -6)) . $salt);
            }

            $insert = [
                'avatar' => "",
                'user_name' => $mobile,
                'password' => $password,
                'ec_salt' => $salt,
                'reg_time' => time(),
                'last_ip' => request()->ip(),
                'visit_count' => 1,
                'mobile' => '',
                'qqopenid' => '',
                'alipayopenid' => '',
                'sinaopenid' => '',
            ];

            if ($wxunionid) {
                $insert['wxunionid'] = $wxunionid;
            }

            if (count($oauth_data) > 0) {
                $insert = array_merge($insert, $oauth_data);
            }

            $user_id = $this->name('users')->insertGetId($insert);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            abort(500, $e->getMessage());
        }

        return $user_id;
    }

    /**
     * openid操作
     * @access  public
     * @param int $user_id
     * @param string $openid
     * @param string $platform ('app','mini_program','official_accounts')
     * @param bool $operate 1:新增|2:删除
     * @return bool
     */
    public function openid_operate(int $user_id, string $openid, string $platform, bool $operate)
    {
        if ($operate) {
            return $this->name('openid')->insert(['user_id' => $user_id, 'platform' => $platform, 'openid' => $openid]);
        } else {
            /*-解绑操作-*/
            return $this->name('openid')->where('openid', $openid)->delete();
        }

    }


    public function user_info($user_id = '', $fields = 'user_id,user_name,nick,avatar,sex,mobile,user_money,frozen_money,is_vip', $value = '')
    {

        if (empty($user_id)) {
            $user_id = $GLOBALS['user_id'];
//            $user_id = 276724;
        }

        $where = $this->name('users')->where('user_id', $user_id);
        if ($value) {
            return $where->value($value);
        } else {
            $res = $where->field($fields)->find();
            return $res;
        }

    }

    public function order_count()
    {

        $where = [
            ['user_id', '=', $GLOBALS['user_id']],
            ['is_delete', '=', 0],
//            'o_status'=>['neq','7'],
        ];

        $arr = $this->name('order_info')->where($where)->select();
        $res['dfk'] = 0;
        $res['dfh'] = 0;
        $res['bffh'] = 0;
        $res['dsh'] = 0;
        $res['tk'] = $this->name('refund_order')->where([['user_id', '=', $GLOBALS['user_id']], ['status', '=', 0]])->count();
        $res['dj'] = 0;
        $res['group'] = 0;
        $res['dpj'] = 0;
        foreach ($arr as $item) {
            if ($item['o_status'] == 0 && $item['is_delete'] == 0) {
                $res['dfk']++;
                continue;
            }
            if ($item['o_status'] == 1 || $item['o_status'] == 2) {
                $res['dfh']++;
                continue;
            }
            if ($item['o_status'] == 3) {
                $res['bffh']++;
                continue;
            }
            if ($item['o_status'] == 4) {
                $res['dsh']++;
                continue;
            }
        }
        return $res;
    }


    /**
     * 显示优惠券
     * @param  number $type [0:未使用|1:已过期|2:已使用]
     *
     * @return number    用户插入表中的自增id
     */
    public function get_user_bouns_list($type, $page, $limit)
    {


        if ($type == 1) //已过期 //已使用
        {
            $where = "ub.order_id>0 or (ub.order_id = 0 and ub.use_end_date<" . time() . ") or ub.present = 2";
        }
        if ($type == 0) //未使用(未过期的 未赠送的)
        {
            $where = 'ub.order_id = 0 and ub.use_end_date >=' . time() . " and ub.present in(0,1)";

        }
//        $GLOBALS['user_id'] = 276724;
        $fields = 'ub.bonus_sn,ub.bonus_id, ub.order_id, b.type_name, b.type_money,b.is_heiwu,b.is_shipping, b.min_goods_amount,b.use_date_type,b.act_range,b.act_range_ext,b.act_range_ext_name, ';
        $fields .= "FROM_UNIXTIME( b.use_start_date,'%Y/%m/%d %H:%i:%s') as use_start_date,FROM_UNIXTIME( ub.use_end_date,'%Y/%m/%d %H:%i:%s') as use_end_date,b.is_present,ub.present";
//        $fields .= "b.use_start_date,ub.use_end_date";
        $result = $this->name('user_bonus')->field($fields)->alias('ub')
            ->join('ecs_bonus_type b', 'ub.bonus_type_id = b.type_id and ub.user_id =' . $GLOBALS['user_id'], 'inner')
            ->where($where)->page("$page,$limit")->order('min_goods_amount asc,use_end_date desc')->select();
        if (count($result)) {
            foreach ($result as &$item) {
                $item['gshp_id'] = 0;
                if ($item['act_range'] == 3) {
                    $arr = explode(',', $item['act_range_ext']);
                    if (count($arr) == 1) {
                        $item['gshp_id'] = $this->name('goods_shp')->where('goods_id', $item['act_range_ext'])->value('id');
                    }
                }

                $item['bonus_id_encode'] = "";
                $item['act_range_ext_name'] = "";
            }
        }

        return $result;
    }

    public function update_user_info($user_id)
    {

        $row = $this->name('users')->where('user_id', $user_id)->find();
        $hmset['last_ip'] = $row['last_ip'];
        $hmset['login_fail'] = 0;

        $update = [];



        if (count($update) > 0) {
            $this->name('users')->where('user_id', $user_id)->update($update);
        }

        /*删除购物车中已下架售完删除的商品*/
        $join4 = [
            ['ecs_goods_shp shp', 'c.goods_id = shp.id', 'LEFT'],
            ['ecs_goods_sup sup', 'shp.goods_id = sup.id', 'LEFT'],
        ];
        $where4 = "(shp.is_delete = 1 or shp.is_sale = 1 or shp.status = 1 or sup.is_delete = 1 or sup.is_sale = 1) AND c.user_id =$user_id";
        $columns_array = $this->name('cart')->alias('c')->join($join4)->where($where4)->column('c.goods_id');

        if (!empty($columns_array)) {
            $columns = implode(',', $columns_array);
            $this->name('cart')->where([['goods_id', 'in', $columns], ['user_id', '=', $user_id]])->delete();
        }
        return $hmset;

    }

    /**
     * 记录帐户变动
     * @param   int $user_money 可用余额变动
     * @param   int $frozen_money 冻结余额变动
     * @param   int $rank_points 等级积分变动
     * @param   int $pay_points 消费积分变动
     * @param   string $change_desc 变动说明
     * @param   int $change_type 变动类型：参见常量文件
     * @param   bool $is_pay 是否扣取会员卡金额
     * @param   int $user_id 用户id 通知的时候没用用户态需要传递一下
     * @return  void
     */
    public function log_account_change($user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = code::ACT_OTHER, $is_pay = true, $user_id = '')
    {
        $user_id = empty($user_id) ? $GLOBALS['user_id'] : $user_id;
        /* 插入帐户变动记录 */
        $account_log = array(
            'user_id' => $user_id,
            'user_money' => $user_money,
            'frozen_money' => $frozen_money,
            'rank_points' => $rank_points,
            'pay_points' => $pay_points,
            'change_desc' => $change_desc,
            'change_type' => $change_type
        );

        $this->name('account_log')->insert($account_log);

        if ($is_pay) {
            /* 更新用户信息 */
            $update = [
                'user_money' => Db::raw("user_money+($user_money)"),
                'frozen_money' => Db::raw("user_money+($frozen_money)"),
                'rank_points' => Db::raw("rank_points+($rank_points)"),
                'pay_points' => Db::raw("pay_points+($pay_points)"),
            ];
            $this->name('users')->where(['user_id' => $user_id])->update($update);
        }
    }

}