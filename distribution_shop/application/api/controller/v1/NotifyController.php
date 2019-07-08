<?php
namespace app\api\controller\v1;

use app\common\controller\ApiController;
use app\common\Enums\RedisKeyEnums;
use app\common\model\Payment;
use think\exception\HttpException;
use think\Request;
use Omnipay\Omnipay;

class NotifyController extends ApiController
{
    protected $payment;

    public function __construct(Request $request,Payment $payment)
    {
        parent::__construct($request);
        $this->payment = $payment;
    }

    public function alipayJs(){
        return $this->alipay_uniform('Js支付宝');
    }

    public function alipayJsdj(){
        return $this->alipay_uniform('Js支付宝',1);
    }

    public function alipayApp(){
        return $this->alipay_uniform('App支付宝');
    }

    public function alipayAppdj(){
        return $this->alipay_uniform('App支付宝',1);
    }

    public function wechatJs_shop(){
        return $this->wechat_uniform('Js-wechat-shop',0);
    }

    public function wechatJs_shop_dj(){
        return $this->wechat_uniform('Js-wechat-shop',0,1);
    }

    public function wechatJs_md(){
        return $this->wechat_uniform('Js-wechat-md',1,1);
    }

    public function wechatApp(){
        return $this->wechat_uniform('App微信',2);
    }

    public function wechatAppdj(){
        return $this->wechat_uniform('App微信',2,1);
    }

    public function union(){
        return $this->union_uniform('银联支付',0);
    }

    public function uniondj(){
        return $this->union_uniform('银联支付',1);
    }

    private function alipay_uniform($pay_type,$is_dingjin=0){

        $alipay = config('alipay.');
        $gateway = Omnipay::create('Alipay_AopApp');
        $gateway->setSignType('RSA'); //RSA/RSA2
        $gateway->setAlipayPublicKey($alipay['alipayrsaPublicKey']);

        $request = $gateway->completePurchase();
        $request->setParams($_POST);

        try {
            $response = $request->send();

            if($response->isPaid()){
                $order_sn   = $_POST['out_trade_no'];//商家内部流水号--为微信的外部流水号
                $order_no   = $_POST['trade_no']; //支付宝的内部流水号--为商家的外部流水号
                $total_fee  = $_POST['total_amount'];       //支付金额
//                $attach = json_encode($_POST['body'],true);
                $trade_status = $_POST['trade_status'];
                if($trade_status == 'TRADE_FINISHED'){
                    $this->payment->set_pay_finished($order_sn);
                    return 'success';
                }

                $flag = $this->payment->new_order_paid($order_sn, $total_fee,$order_no, $pay_type,$is_dingjin);
                if($flag){
                    return 'success';
                }else{
                    $this->payment->set_pay_error($order_sn, $order_no, $total_fee, $pay_type, '付款成功！订单状态更新失败！');
                    return 'fail';
                }

            }else{
                return 'fail';
            }
        } catch (\Exception $e) {
            return 'fail';
        }
    }

    private function wechat_uniform($pay_type,$mendian,$is_dingjin=0){

        if($mendian == 1){
            $wxpay = config('wechat.wxpay_md');
        }else{
            $wxpay = config('wechat.wxpay');
        }

        $gateway    = Omnipay::create('WechatPay');
//        $gateway->setAppId($wxpay['appId']);
        $gateway->setMchId($wxpay['mch_id']);
        $gateway->setApiKey($wxpay['mch_secret']);

        $response = $gateway->completePurchase([
            'request_params' => file_get_contents('php://input')
        ])->send();

        if ($response->isPaid()) {

            $res = $response->getRequestData(); //返回的参数

            $order_sn = $res['out_trade_no'];   //商家内部流水号--为微信的外部流水号
            $order_no = $res['transaction_id']; //微信的内部流水号--为商家的外部流水号
            $total_fee = $res['total_fee']/100; //微信支付的单位为分
            $attach = json_decode($res['attach'],true);

            $flag = $this->payment->new_order_paid($order_sn, $total_fee,$order_no, $pay_type,$is_dingjin,$attach);

            if($flag === true){
                return 'SUCCESS';
            }else{
                $this->payment->set_pay_error($order_sn, $order_no, $total_fee, $pay_type, '付款成功！订单状态更新失败！');
                return 'FAIL';
            }
        }else{
            //pay fail
            return 'FAIL';
        }
    }

    public function union_uniform($pay_type,$is_dingjin=0){

        $union = config('union');

        $gateway    = Omnipay::create('UnionPay_Express');
        $gateway->setMerId($union['MerId']);
        $gateway->setPublicKey($union['PublicKey']); // path or content

        $response = $gateway->completePurchase(['request_params'=>$_POST])->send();

        if ($_POST['respCode'] == '00') { //$response->isPaid() 不知道为什么验签失败 公钥有问题？

            $order_sn   = $_POST['orderId'];//商家内部流水号--为微信的外部流水号
            $order_no   = $_POST['queryId']; //银联的内部流水号--为商家的外部流水号
            $total_fee  = $_POST['settleAmt']/100;       //支付金额


            $flag = $this->payment->new_order_paid($order_sn, $total_fee,$order_no, $pay_type,$is_dingjin);

            //pay success
            if($flag === true){
                return 'SUCCESS';
            }else{
                $this->payment->set_pay_error($order_sn, $order_no, $total_fee, $pay_type, '付款成功！订单状态更新失败！');
                throw new HttpException(400,'FAIL');
            }
        }else{
            throw new HttpException(400,'FAIL');
        }

    }
}