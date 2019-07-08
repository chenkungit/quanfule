<?php

namespace app\dashboard\controller;


use app\common\controller\WebController;
use think\Db;
use think\Request;
use app\dashboard\model\Order;

class OrderController extends WebController
{
    protected $order;
    protected $TradePushResponse;
    protected $LogisticsSyncQuery;
    protected $LogisticsSyncAck;

    public function __construct(Request $request, Order $order)
    {
        parent::__construct($request);
        $this->order = $order;
    }

    public function lists()
    {

        $res = $this->order->order_lists();

        return $this->respondWithArray($res);
    }

    public function info()
    {
        $this->_validate('order_id');
        $res = $this->order->order_info($this->data['order_id']);
        return $this->respondWithArray($res);
    }

    public function fahuo()
    {
        $this->_validate('order_id');
        if ($res = Db::name('order_info')->where('order_id', $this->data['order_id'])->where('o_status', 1)->find()) {
            $order_id = $res['order_id'];
            $action_note = '发货填写快递单号';
            $logistics_name = Db::name('logistics_type')->where('id', $this->data['logistics_type'])->value('name');
            if (!$logistics_name) {
                return json_error(null, '快递公司不存在', self::INVALID_PARAMETER);
            }
            Db::startTrans();
            try {
                Db::name('order_info')->where('order_id', $order_id)->update(['o_status' => \code::NS_FINISH_D, 'd_status' => \code::SS_SHIPPED]);
                Db::name('delivery_invoice')->insert(['order_id' => $order_id, 'invoice_no' => $this->data['invoice_no'], 'logistics_type' => $this->data['logistics_type'], 'logistics_name' => $logistics_name, 'consign_time' => $this->data['consign_time']]);
                Db::name('order_goods')->where('order_id', $order_id)->update(['o_status' => \code::NS_FINISH_D]);
                $this->order_action($this->data['order_id'], \code::NS_WAIT_D, \code::NS_FINISH_D, 2, $action_note, $GLOBALS['user_name']);
                Db::commit();
            } catch (\Exception $exception) {
                Db::rollback();
                abort(500, $exception->getMessage());
            }
            return $this->respondWithArray(null, '发货完成！');
        } else {
            return json_error(null, '订单状态错误', self::INVALID_PARAMETER);
        }

    }

    public function logistics_type()
    {
        $res = Db::name('logistics_type')->select();
        return $this->respondWithArray($res);
    }

    function order_action($order_id, $order_status, $shipping_status, $pay_status, $note = '', $username = null, $place = 0)
    {
        $insert_arr = array(
            'order_id' => $order_id,
            'action_user' => $username,
            'order_status' => $order_status,
            'shipping_status' => $shipping_status,
            'pay_status' => $pay_status,
            'action_place' => $place,
            'action_note' => $note,
            'log_time' => time(),
        );
        try {
            Db::name('order_action')->insert($insert_arr);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}