<?php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as gzRequest;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class express
{
    protected $configure;

    public function __construct($cfg = [])
    {
        $this->configure = $cfg;
    }

    /**
     * 计算订单的配送费用的函数
     *
     * @param   float   $goods_weight   商品重量
     * @param   float   $goods_amount   商品金额
     * @param   float   $goods_number   商品件数
     * @return  number
     */
    function calculate($goods_weight, $goods_amount, $goods_number)
    {
        if ($this->configure['free_money'] > 0 && $goods_amount >= $this->configure['free_money'])
        {
            return 0;
        }
        else
        {
            $fee = $this->configure['base_fee'];
            $this->configure['fee_compute_mode'] = !empty($this->configure['fee_compute_mode']) ? $this->configure['fee_compute_mode'] : 'by_weight';

            if ($this->configure['fee_compute_mode'] == 'by_number')
            {
                $fee = $goods_number * $this->configure['item_fee'];
            }
            else
            {
                if ($goods_weight > 1)
                {
                    $fee += (ceil(($goods_weight - 1))) * $this->configure['step_fee'];
                }
            }

            return $fee;
        }
    }

    public static function tracking($nu,$com,$muti=0,$order='desc'){


        $client = new Client();

        $response = $client->request('GET', 'http://aliapi.kuaidi.com/kuaidiinfo', [
            'headers'=>[
                'Authorization'=>''
            ],
            'query' => [
                'nu'=>$nu,
                'com'=>$com,
                'muti'=>$muti,
                'order'=>$order
            ],
            'verify'=>false,
        ]);

        return \GuzzleHttp\json_decode($response->getBody(),true);

    }


    public static function switch_shipping($name){
        switch ($name){
            case "EMS"://ecshop后台中显示的快递公司名称
                $postcom = 'ems';//快递公司代码
                break;
            case "中国邮政":
                $postcom = 'ems';
                break;
            case "申通快递":
                $postcom = 'shentong';
                break;
            case "申通E物流":
                $postcom = 'shentong';
                break;
            case "快递":
                $postcom = 'yuantong';
                break;
            case "圆通速递":
                $postcom = 'yuantong';
                break;
            case "圆通快递":
                $postcom = 'yuantong';
                break;
            case "圆通":
                $postcom = 'yuantong';
                break;
            case "顺丰速运":
                $postcom = 'shunfeng';
                break;
            case "顺丰特惠":
                $postcom = 'shunfeng';
                break;
            case "天天快递":
                $postcom = 'tiantian';
                break;
            case "韵达快递":
                $postcom = 'yunda';
                break;
            case "中通速递":
                $postcom = 'zhongtong';
                break;
            case "龙邦物流":
                $postcom = 'longbanwuliu';
                break;
            case "宅急送":
                $postcom = 'zhaijisong';
                break;
            case "全一快递":
                $postcom = 'quanyikuaidi';
                break;
            case "汇通速递":
                $postcom = 'huitong';
                break;
            case "民航快递":
                $postcom = 'minghangkuaidi';
                break;
            case "亚风速递":
                $postcom = 'yafengsudi';
                break;
            case "快捷速递":
                $postcom = 'kuaijiesudi';
                break;
            case "华宇物流":
                $postcom = 'tiandihuayu';
                break;
            case "中铁快运":
                $postcom = 'zhongtiewuliu';
                break;
            case "FedEx":
                $postcom = 'fedex';
                break;
            case "UPS":
                $postcom = 'ups';
                break;
            case "DHL":
                $postcom = 'dhl';
                break;
            case "安能物流":
                $postcom = 'annengwuliu';
                break;
            default:
                $postcom = '';
        }
        return $postcom;
    }


}