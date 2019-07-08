<?php
/**
 * User: cpj
 * Date: 2019/6/13
 */

namespace app\service;


use app\common\Enums\Code;
use app\common\Enums\RedisKeyEnums;
use Gregwar\Captcha\CaptchaBuilder;
use think\exception\HttpException;

class CaptchaService extends BaseService
{

    public function create(string $device_id)
    {
        $captchaBuilder = new CaptchaBuilder();

        $captchaBuilder->setIgnoreAllEffects(true);
        $captchaBuilder->setBackgroundColor(255, 255, 255);
        $captchaBuilder->setMaxBehindLines(0);
        $captchaBuilder->setMaxFrontLines(0);
        $captchaBuilder->build(100, 50);

        $captcha_key = sprintf(RedisKeyEnums::CAPTCHA, $device_id);

        redis()->set($captcha_key, $captchaBuilder->getPhrase(), ['EX' => 60]);

        return $captchaBuilder->inline(100);
    }

    public function validate($device_id, $captcha)
    {
        $captcha_key = sprintf(RedisKeyEnums::CAPTCHA, $device_id);

        $value = redis()->get($captcha_key);
        if (!$value) {
            throw new HttpException(200, '验证码已失效,请重新获取', null, [], Code::INVALID_PARAMETER);
        }
        if ($value != $captcha) {
            redis()->delete($captcha_key);
            throw new HttpException(200, '验证码错误,请重新获取', null, [], Code::INVALID_PARAMETER);
        }
    }
}