<?php

class code
{
    const qinniu_url = '';

    const PAY_USE_DINGJIN = 'pud';//订单定金付款
    const PAY_USE_ORDER = 'puo';//订单付款
    const PAY_USE_RECHARGE = 'pur';//充值余额

    const PS_UNPAYED = 0;   // 未付款
    const PS_PAYING = 1;   // 付款中
    const PS_PAYED = 2;    // 已付款
    const PS_PAYED_DJ = 3; // 已付定金
    const PS_FINISHED = 4; //交易3个月后支付宝回调,不能退款

    //新版订单状态 o_status
    const NS_WAIT_P = 0; //等待买家付款
    const NS_WAIT_D = 1; //等待卖家发货
    const NS_D_ING = 2; //卖家发货中
    const NS_PART_D = 3; // 部分发货
    const NS_FINISH_D = 4; // 发货完成
    const NS_APPLY_R = 5; // 申请退货
    const NS_FINISH_R = 6; // 退货完成
    const NS_FINISH = 7; // 确定收货
    const NS_CLOSE = 100; // 订单关闭
    const NS_DINGJIN_P = 9; // 已付定金 待付尾款
    const GROUP = 10; //已成团 等待团满
    const CROWDFUNDING = 11; //众筹

    //客订单结算状态 p_status
    const AS_CREAT = 0; // 订单创建
    const AS_PAY = 1; // 订单付款
    const AS_SUCCESS = 2; // 订单成功
    const AS_SETTLE = 3; // 订单结算
    const AS_FAILED = 4; // 订单失效

    /* 配送状态 d_status  delivery*/
    const SS_UNSHIPPED = 0; // 未发货
    const SS_SHIPPED = 1; // 已发货
    const SS_RECEIVED = 2; // 已收货
    const SS_PREPARING = 3; // 备货中
    const SS_SHIPPED_PART = 4; // 已发货(部分商品)
    const SS_SHIPPED_ING = 5; // 发货中(处理分单)
    const OS_SHIPPED_PART = 6; // 已发货(部分商品)

    const PAY_ORDER = 0;// 订单支付
    const PAY_SURPLUS = 1;// 会员预付款


    /* 减库存时机 */
    const SDT_SHIP = 0;// 发货时
    const SDT_PLACE = 1;// 下订单时


    //新版发货单状态
    const NDS_WAIT_D = 0; // 等待发货
    const NDS_FINISH_D = 1; // 已发货
    const NDS_FINISH_R = 2; // 已退货


    //新版退货单状态
    const NRS_WAIT_R = 0; // 等待处理
    const NRS_FINISH_R = 1; // 处理完成
    const NRS_CANCLE = 9; // 取消

    /* 帐号变动类型 */
    const ACT_SAVING = 0;     // 帐户冲值
    const ACT_DRAWING = 1;     // 帐户提款
    const ACT_ADJUSTING = 2;     // 调节帐户
    const ACT_OTHER = 99;     // 其他类型


    const oauth2_wechat = 1;
    const oauth2_qq = 2;
    const oauth2_alipay = 3;
    const oauth2_sina = 4;


    const none = 0;//默认支付方式
    const alipay_id = 4; //支付宝支付
    const wxpay_id = 6;  //微信支付
    const balance = 1;   //余额支付
    const card_pay = 8;  //会员卡支付

    const MARKETING_NONE = 0; //无
    const MARKETING_DINGJIN = 1; //营销定金
    const MARKETING_GROUP = 2; //营销拼团
    const MARKETING_CROWDFUNDING = 3; //营销众筹
    const MARKETING_FLASH = 4; //营销限时购

    const refund_reason = [
        1 => '卖家缺货',
        2 => '质量问题/破损',
        3 => '尺寸拍错',
        4 => '颜色/规格/大小尺寸与描述不符',
        5 => '卖家发错货',
        6 => '卖家少发/漏发',
        7 => '其他问题',
    ];


    //获取订单中文状态
    public static function get_ostatus_ch($o_status, $del = 0)
    {
        if (!empty($del)) {
            return "交易关闭";
        }

        switch ($o_status) {
            case code::NS_WAIT_P   :
                return '等待买家付款';
                break;
            case code::NS_WAIT_D   :
                return '等待卖家发货';
                break;
            case code::NS_D_ING    :
                return '卖家配货中';
                break;
            case code::NS_PART_D   :
                return '部分发货';
                break;
            case code::NS_FINISH_D :
                return '发货完成';
                break;
            case code::NS_APPLY_R  :
                return '申请退货';
                break;
            case code::NS_FINISH_R :
                return '退货完成';
                break;
            case code::NS_FINISH   :
                return '交易成功';
                break;
            case code::NS_CLOSE    :
                return '交易关闭';
                break;
            case code::NS_DINGJIN_P    :
                return '待付尾款';
                break;
            case code::GROUP       :
                return '拼团中';
                break;
            case code::CROWDFUNDING :
                return '众筹中';
                break;
            default:
                return '未知状态';
                break;
        }
    }

    public static function parseGroup($is_group)
    {
        switch ($is_group) {
            case 1:
                return code::GROUP;
            case 2:
                return code::CROWDFUNDING;
            default:
                return code::NS_WAIT_D;
        }
    }

    const ss = [
//        0 => '等待买家付款',
        1 => '等待卖家发货',
//        2 => '卖家配货中',
//        3 => '部分发货',
        4 => '发货完成',
        5 => '申请退货',
        6 => '退货完成',
//        9 => '代付尾款',
//        10 => '拼团中',
//        11 => '众筹中'
    ];
    const ps = [
        self::PS_UNPAYED => '未付款',
        self::PS_PAYING => '付款中',
        self::PS_PAYED => '已付款',
        self::PS_PAYED_DJ => '已付定金',
        self::PS_FINISHED => '交易3个月后支付宝回调,不能退款'
    ];

    const source = [
        'xcx',
        'h5'
    ];
}