<?php
namespace app\dashboard\controller;

use app\common\controller\WebController;
use app\dashboard\model\Delivery;
use think\Db;
use think\Request;
use express;

class DeliveryController extends WebController
{

    protected $Delivery;

    public function __construct(Request $request,Delivery $Delivery)
    {
        parent::__construct($request);
        $this->Delivery = $Delivery;

    }

    public function lists(){
        $this->_validate('order_id');

        $res = $this->Delivery->delivery_invoice($this->data['order_id']);

        return $this->respondWithArray($res);
    }

    public function tracking(){

        $this->_validate('delivery_id');

        $info = Db::name('delivery_invoice')->where('delivery_id',$this->data['delivery_id'])->find();
        if($info){
            $com = express::switch_shipping($info['logistics_name']);
            $msg = express::tracking($info['invoice_no'], $com);
            return $this->respondWithArray($msg);
        }
        return $this->respondWithArray(null);
    }




}