<?php

namespace app\api\exception;

use app\common\Enums\Code;
use think\exception\Handle;
use think\exception\HttpException;

class Http extends Handle
{
    public function render(\Exception $e)
    {

        switch ($e) {

            case ($e instanceof ValidateException):
                $message = $e->getMessage();
                $statusCode = $e->getCode();
                break;
            case ($e instanceof HttpException):
                $message = $e->getMessage();
                $statusCode = $e->getStatusCode();
                break;
            default:
                $message = '网络错误';
                $statusCode = Code::SYSTEM_ERROR;
                $message = $e->getMessage();
        }

        $result = [
            'code' => $statusCode,
            'msg' => $message,
            'data' => null
//            'time' => $_SERVER['REQUEST_TIME'],
//            'line'=>$e->getLine(),
//            'file'=>$e->getFile()
        ];

        //http码
        return json($result, Code::SUCCESS);
    }

}