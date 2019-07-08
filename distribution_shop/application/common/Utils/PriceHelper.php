<?php
namespace app\common\Utils;


class PriceHelper
{

    /**
     * 格式化商品价格
     *
     * @access  public
     * @param   float $price 商品价格
     * @return  string
     */
    public static function format($price, $change_price = true)
    {
        if ($price === '') {
            $price = 0;
        }

        if ($change_price) {
            switch (3) {
                case 0:
                    $price = number_format($price, 2, '.', '');
                    break;
                case 1: // 保留不为 0 的尾数
                    $price = preg_replace('/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format($price, 2, '.', ''));

                    if (substr($price, -1) == '.') {
                        $price = substr($price, 0, -1);
                    }
                    break;
                case 2: // 不四舍五入，保留1位
                    $price = substr(number_format($price, 2, '.', ''), 0, -1);
                    break;
                case 3: // 直接取整
                    $price = floatval($price);
                    break;
                case 4: // 四舍五入，保留 1 位
                    $price = number_format($price, 2, '.', '');
                    break;
                case 5: // 先四舍五入，不保留小数
                    $price = round($price);
                    break;
            }
        } else {
            if (!$price) {
                $price = 0;
            }
            $price = intval($price);
        }

        return sprintf("%s 积分", $price);
    }

}