<?php

namespace app\api\exception;


use app\common\Enums\Code;

class IllegalException extends \RuntimeException
{
    public function __construct($message = "非法请求", int $code = Code::INVALID_PARAMETER, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}