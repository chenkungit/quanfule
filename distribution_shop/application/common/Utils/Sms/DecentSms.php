<?php

namespace app\common\Utils\Sms;

use app\common\Mapping\AbstractSms;
use GuzzleHttp\Client;

class DecentSms extends AbstractSms
{

    //{"return_code":"00000","order_id":"ALY1561605963286660695"}
    public function send($phoneNumber, $templateId, $data)
    {
        // TODO: Implement send() method.
        $client = new Client(['base_uri' => 'http://dingxin.market.alicloudapi.com']);

        $response = $client->request('post', '/dx/sendSms', [
            'headers' => [
                'Authorization' => 'APPCODE ' . config('sms.')['DECENT']['AppCode']
            ],
            'query' => [
                'mobile' => $phoneNumber,
                'param' => $data,
                'tpl_id' => $templateId
            ]
        ]);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, 'true');

        if ($res['return_code'] == '00000') {
            return true;
        } else {
            return false;
        }
    }
}