<?php

namespace app\dashboard\controller;

use app\common\controller\WebController;
use app\common\Enums\AccountLogEnums;
use app\common\model\ErpModel;
use app\common\model\Goods;
use app\common\model\Order;
use app\common\model\Users;
use app\service\Member\AccountService;
use Erp\Core\Response\TradePushResponse;
use think\Db;
use think\exception\HttpException;
use think\Request;
use app\dashboard\model\Refund;
use code;

class RefundController extends WebController
{
    protected $Refund;

    public function __construct(Request $request, Refund $refund)
    {
        parent::__construct($request);
        $this->Refund = $refund;
    }

    public function lists()
    {
        $res = $this->Refund->refund_list();

        return $this->respondWithArray($res);
    }

    public function info()
    {

        $this->_validate('refund_id');

        $res = $this->Refund->refund_info($this->data['refund_id']);

        return $this->respondWithArray($res);
    }

    public function back()
    {
        $validate = $this->validate($this->data, 'app\dashboard\validate\RefundValidate.back');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

//        $where = ['ro.id'=>$this->data['refund_id']];
        $refund_info = $this->Refund->refund_info($this->data['refund_id'], true);

        if (!$refund_info || in_array($refund_info['status'], [code::NRS_CANCLE, code::NDS_FINISH_D])) {
            return json_error(NULL, '退货单不存在', self::INVALID_PARAMETER);
        }

        Db::startTrans();
        try {
            switch ($refund_info['type']) {
                case 1:
                    $this->all_back($refund_info);
                    break; //整单退货
                case 2:
                    $this->section_back($refund_info);
                    break; //部分退货
            }
            $this->common_remain($refund_info);

            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            abort(500, $exception->getMessage());
        }



        return $this->respondWithArray(NULL, '退款成功');
    }

    public function cancel_back()
    {

        $validate = $this->validate($this->data, 'app\dashboard\validate\RefundValidate.back');
        if ($validate !== true) {
            return json_error(NULL, $validate, self::INVALID_PARAMETER);
        }

        $order = new Order();

        $refund_info = Db::name('refund_order')->alias('ro')->join('ecs_order_info oi', 'ro.order_id = oi.order_id', 'LEFT')->where('ro.id', $this->data['refund_id'])->find();
        if (!$refund_info || $refund_info['status'] == \code::NRS_CANCLE) {
            return json_error('', '退货单不存在', '');
        }

        $update_order_arr = [
            'o_status' => $refund_info['r_status'],
            'r_status' => 0,
            'refund_time' => 0,
            'refund_reason' => '',
        ];

        Db::startTrans();
        try {
            Db::name('order_info')->where('order_id', $refund_info['order_id'])->update($update_order_arr);
            Db::name('order_goods')->alias('og')->leftJoin('ecs_refund_goods rg', 'og.rec_id=rg.rec_id')->where('og.order_id', $refund_info['order_id'])->update(['o_status' => $refund_info['r_status'], 'r_status' => 1]);
            Db::name('refund_order')->where('id', $this->data['refund_id'])->update(['status' => \code::NRS_CANCLE]);
            $order->order_action($refund_info['order_id'], $refund_info['r_status'], $refund_info['d_status'], $refund_info['p_status'], '取消退货单(' . $this->data['action_note'] . ')');
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort(500, $e->getMessage());
        }

        return $this->respondWithArray(NULL, '取消退货成功');
    }


    /*-
    已发货则不退运费
    未发货则退全部费用
    不退换已使用的优惠券及运费卷
    -*/
    private function all_back($refund_info)
    {


        $map[] = ['order_id', '=', $refund_info['order_id']];
        $map[] = ['status', '=', code::NDS_FINISH_D];
        /**-加回库存-*/
        (new Goods())->change_order_goods_storage($refund_info['order_id'], false, 1);
        /**-回写订单-*/
        $this->Refund->set_order_refund($refund_info);

    }

    /*-部分退款-*/
    private function section_back($refund_info)
    {

        /**-加回库存-*/
        (new Goods())->change_order_goods_storage($refund_info['order_id'], false, 2);
        /**-回写订单-*/
        $this->Refund->set_section_order_refund($refund_info);
    }

    /*-回写-*/
    private function common_remain($refund_info)
    {
        $this->common_refund($refund_info['pay_id'], $refund_info['user_id'], $refund_info['refund_money_paid'], $refund_info['total_fee'], $refund_info['order_sn'], $refund_info['refund_sn'], $refund_info['refund_surplus'], 0);

        Db::name('refund_order')->where('id', $this->data['refund_id'])->update(['status' => code::NRS_FINISH_R, 'operate_time' => time()]);
    }


    /*-
    公共退款处理
     * @access  public
     * @param   int     $pay_id          支付id标识符
     * @param   int     $user_id         用户id
     * @param   float   $refund_money    退款第三方金额+会员卡金额
     * @param   float   $total_fee       支付的总金额 微信需要
     * @param   string  $order_sn        内部订单号
     * @param   string  $refund_sn       退款号
     * @param   float   $surplus           退款余额
     * @param   int   $vip_card          会员卡号
     * @return bool
    -*/
    private function common_refund($pay_id, $user_id, $refund_money, $total_fee, $order_sn, $refund_sn, $surplus, $vip_card)
    {

        $refund_money = round($refund_money, 2);
        $total_fee = round($total_fee, 2);

        $refund = new \app\common\controller\Refund();
        $user = new Users();


        if ($pay_id == code::balance) {
            AccountService::service()
                ->setUserId($user_id)
                ->setChangeDesc(sprintf('订单%s退款_余额部分', $order_sn))
                ->setChangeType(AccountLogEnums::CHANGE_TYPE_REFUND)
                ->setUserMoney(floatval($surplus))
                ->change();
        } else {
            /*-其他支付方式部分使用余额支付-*/
            if ($surplus) {
                AccountService::service()
                    ->setUserId($user_id)
                    ->setChangeDesc(sprintf('订单%s退款_余额部分', $order_sn))
                    ->setChangeType(AccountLogEnums::CHANGE_TYPE_REFUND)
                    ->setUserMoney(floatval($surplus))
                    ->change();
            }
        }

        if ($refund_money == 0) return;
        /*-会员卡支付原路返回-*/

//        if ($pay_id == code::alipay_id) {
//            $refund->Alipay_refund($order_sn, $refund_money, false, $refund_sn);
//            $desc = sprintf('订单%s退款_支付宝部分', $order_sn);
//
//            $user->log_account_change($refund_money, 0, 0, 0, $desc, \code::ACT_OTHER, false, $user_id);
//        }
//        if ($pay_id == code::wxpay_id) {
//
//            $refund->Wechat_refund($order_sn, null, $refund_money, false, $refund_sn);
//
//            $desc = sprintf('订单%s退款_微信部分', $order_sn);
//            $user->log_account_change($refund_money, 0, 0, 0, $desc, \code::ACT_OTHER, false, $user_id);
//        }
    }


}