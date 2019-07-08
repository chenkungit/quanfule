<?php

namespace app\api\controller\v2;


use app\api\validate\SmsValidate;
use app\common\controller\ApiController;
use app\common\Enums\RedisKeyEnums;
use app\common\Enums\SmsEnums;
use app\common\Factory\SmsFactory;
use think\Db;
use think\Request;

class SmsController extends ApiController
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function send()
    {
        $smsValidate = new SmsValidate();
        $smsValidate->scene('send')->check($this->data);

        $string = sprintf(RedisKeyEnums::SMS_CONTROL_HASH, $this->data['type'], $this->data['mobile']);

        if (redis()->exists($string)) {
            $sms_ttl = redis()->ttl($string);
            if (600 - intval($sms_ttl) < 60) {
                return json_error('', '请求频繁！', self::ERROR);
            }
        }

        /*--风控 每天每个手机号和ip的记录--*/
//        $this->risk($this->request->ip());
//        $this->risk($this->data['mobile']);
        $templateCode = config('sms.')['DECENT']['template']['register'];

        $sms_code = rand(10000, 99999);
        $sms_data = 'code:'.$sms_code;
        $sms = SmsFactory::getService(SmsEnums::DECENT);

        $flag = $sms->send($this->data['mobile'], $templateCode, $sms_data);

        if ($flag) {
            $array['ttl'] = 3;
            $array['sms_code'] = $sms_code;
            try {
                redis()->hMset($string, $array);
                //正式环境开启
                redis()->expire($string, 600);
                Db::name('sms_record')->insert(['mobile' => $this->data['mobile'], 'template_code' => $templateCode, 'ip' => \Request()->ip(), 'date' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])]);
            } catch (\Exception $e) {
                return json_error('', $e->getMessage(), self::SYSTEM_ERROR);
            }
            return json_success('', '短信发送成功', self::SUCCESS);
        }
        return json_error('', '请稍后重试');
    }

}