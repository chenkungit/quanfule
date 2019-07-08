<?php

namespace App\Utils;


class CommonString
{
    public static function hide($string, $type)
    {
        $strlen = strlen($string);
        if ($strlen <= 1) {
            return $string;
        }
        switch ($type) {
            //1 姓名 or 昵称
            case 1:
                //如果昵称中包含emoji，不隐藏字符串
                $string = self::hasEmoji($string) ? $string : self::substrCut($string);
                break;
            //2 手机号
            case 2:
                $string = substr_replace($string, '****', 3, 4);
                break;
        }
        return $string;
    }


    /**
     * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
     * @param string $user_name 姓名
     * @return string 格式化后的姓名
     */
    public static function substrCut($user_name)
    {
        $strlen = (int)mb_strlen($user_name, 'utf-8');
        $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }

    /**
     * 检测是否有
     * @param $str
     * @return int
     */
    public static function hasEmoji($str)
    {
        $text = json_encode($str); //暴露出unicode
        return preg_match("/(\\\u[ed][0-9a-f]{3})/i", $text);
    }


}