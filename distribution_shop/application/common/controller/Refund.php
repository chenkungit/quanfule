<?php
namespace app\common\controller;

use Omnipay\Omnipay;
use think\Exception;

class Refund
{
    protected $parameters;

    protected $Aligateway;
    protected $Wechatgateway;

    const ALI_ID    = 4;
    const WECHAT_ID = 6;

    public function __construct()
    {
        $this->Aligateway    = $this->Alipay_config();
        $this->Wechatgateway = $this->Wechat_config();
    }

    public function common_query($pay_id){
        switch ($pay_id){
            case static::ALI_ID:return  $this->Alipay_query();break;
            case static::WECHAT_ID:return $this->Wechat_query();break;
        }
    }

    public function common_refund_query($pay_id){
        switch ($pay_id){
            case static::ALI_ID:return  $this->Alipay_refund_query();break;
            case static::WECHAT_ID:return $this->Wechat_refund_query();break;
        }
    }

    public function common_refund($pay_id,$out_trade_no,$total_fee,$refund_fee,$is_auto=true,$refund_trade_no=''){
        switch ($pay_id){
            case static::ALI_ID:$res = $this->Alipay_refund($out_trade_no,$refund_fee,$is_auto,$refund_trade_no);break;
            case static::WECHAT_ID:$res = $this->Wechat_refund($out_trade_no,$total_fee,$refund_fee,$is_auto,$refund_trade_no);break;
        }
//        (new Users())->log_account_change(-$refund_fee,'','','','','');

    }


    public function Alipay_refund($out_trade_no,$refund_fee,$is_auto=true,$refund_trade_no=''){

        $gateway = $this->Alipay_config();

        $req = $gateway->refund();
//        $biz['trade_no'] = '2017110421001004350546411991';
        $biz = [
            'out_trade_no' => $out_trade_no,
            'refund_amount'=> $refund_fee
        ];
        /*-部分退款需要标识符-*/
        $biz['out_request_no'] = $is_auto ? build_order_no() : $refund_trade_no;

        ksort($biz);
        $req->setBizContent($biz);
        $res = $req -> send()->getData();

        if($res['alipay_trade_refund_response']['code'] != '10000'){
            abort(500,$res['alipay_trade_refund_response']['sub_msg']);
        }
        return $res;

    }

    public function Alipay_query(){
        $response = $this->Aligateway->query();
        $biz = $this->getParameters();
        ksort($biz);
        $response->setBizContent($biz);
        $res = $response -> send();
        return $res->getData();
    }

    public function Alipay_refund_query(){
        $response = $this->Aligateway->refundquery();
        $biz = $this->getParameters();
        ksort($biz);
        $response->setBizContent($biz);
        $res = $response -> send();
        return $res->getData();
    }

    public function Wechat_refund($out_trade_no,$total_fee,$refund_fee,$is_auto=true,$refund_trade_no=''){
        $gateway = $this->Wechat_config();

        $this->setArray(['out_trade_no'=>$out_trade_no]);

        $response = $gateway->refund([
            'out_refund_no' => $is_auto ? build_order_no():$refund_trade_no,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee ? $total_fee*100 : floatval($this->Wechat_query()['total_fee']), //=0.01
            'refund_fee' => $refund_fee*100, //=0.01
        ])->send();

        $res =  $response->getData();
        if($res['return_code'] != 'SUCCESS'){
            abort(500,'退款错误');
        }
        return $res;
    }

    public function Wechat_query(){
        $response = $this->Wechatgateway->query($this->getParameters())->send();
        return $response->getData();
    }

    public function Wechat_refund_query(){
        $response = $this->Wechatgateway->queryrefund($this->getParameters())->send();
        return $response->getData();
    }


    protected function Alipay_config(){

        $alipay = config('alipay.');

        $gateway = Omnipay::create('Alipay_AopApp');
        $gateway->production();
        $gateway->setSignType('RSA'); //RSA/RSA2
        $gateway->setAppId($alipay['appId']);

        $gateway->setPrivateKey($alipay['rsaPrivateKey']);
        $gateway->setAlipayPublicKey($alipay['alipayrsaPublicKey']);
        return $gateway;
    }


    protected function Wechat_config(){
        $wx= config('wechat.wxpay');

        $gateway    = Omnipay::create('WechatPay_App');
        $gateway->setAppId($wx['appId']);
        $gateway->setMchId($wx['mch_id']);
        $gateway->setApiKey($wx['mch_secret']);

        $gateway->setCertPath($wx['cert_path']);
        $gateway->setKeyPath($wx['key_path']);

        return $gateway;
    }

    protected function getParameters()
    {
        return $this->parameters;
    }

    public function setArray($array)
    {
        $this->parameters = $array;
    }
}