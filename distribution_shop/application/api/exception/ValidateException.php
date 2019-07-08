<?php

namespace app\api\exception;

use app\common\Enums\Code;
use RuntimeException;
use Throwable;

class ValidateException extends RuntimeException
{
    public function __construct($message = "", int $code = Code::INVALID_PARAMETER, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}