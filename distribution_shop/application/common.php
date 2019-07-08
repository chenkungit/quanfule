<?php
/**
 * @var RedisManager
 * @return RedisManager
 * */
function redis($database = 1)
{
//    return $client = new \Predis\Client(config('Redis'));

    return \RedisManager::getInstance($database);

    /*-phpiredis php7才能用 5装扩展会报错并不知道如何解决-*/
//    return $client = new \Predis\Client($config,[
//        'connections' => [
//            'unix' => 'Predis\Connection\PhpiredisSocketConnection',  // ext-socket resources
//        ],
//    ]);
}

function CacheGet($name)
{
    $cache = redis()->get($name);
    if ($cache) {
        return json_decode($cache, true);
    } else {
        return false;
    }
}

function remember($name, $value, $expire = 0)
{

    if ($value) {
        $cache = json_encode($value);
        if (is_int($expire) && $expire) {
            redis()->set($name, $cache, ['EX' => $expire]);
        } else {
            redis()->set($name, $cache);
        }
    }
    return $value;
}

function is_hei_wu_date()
{
    $md = date("m-d");
    if ($md == '11-24' || $md == '11-25' || $md == '11-26') {
        return true;
    }
    return false;
}

function is_66()
{
    $md = date("m-d");
    if ($md == '06-06') {
        return true;
    }
    return false;
}

function json_success($data, $msg = 'success', $code = 200, $header = [], $options = [])
{
    if (empty($data)) {
        $data = [];
    }
    $params = [
        'code' => $code,
        'data' => $data,
        'msg' => $msg
    ];
    return json($params, 200, $header, $options);
}

function json_error($arr = [], $msg = 'failed', $code = 400)
{
    if (empty($data)) {
        $data = [];
    }
    $params = [
        'code' => $code,
        'data' => $data,
        'msg' => $msg
    ];
    return json($params);
}

if (!function_exists('dd')) {
    function dd()
    {
        echo "<pre>";
        array_map(function ($x) {
            var_dump($x);
        }, func_get_args());
        die(1);
    }
}

function build_order_no()
{
    return date('YmdHis') . mt_rand(1000, 9999);
}

function get_refund_sn()
{
    return date('YmdHi') . mt_rand(10000, 99999);
}


/**
 * 数组转xml
 * @param  string $root 根命名
 * @param  array $arr [description]
 * @param  string $flag 是否加上xml头部
 * @param  string $encoding 头部声明编码
 */
function xml_encode($root, $arr, $flag = false, $encoding = 'utf-8')
{

    if ($flag) {
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= "<$root>";
    } else {
        $xml = "<$root>";
    }

    $xml .= arrayToXml($arr);
    $xml .= "</$root>";
    return $xml;
}

function arrayToXml($arr)
{

    $xml = '';
    foreach ($arr as $key => $val) {

        $xml .= "<{$key}>";
        $xml .= (is_array($val) || is_object($val)) ? arrayToXml($val) : $val;
        $xml .= "</{$key}>";

    }
    return $xml;
}

/*-时：分：秒.毫秒-*/
function udate($format = 'u', $utimestamp = null)
{
    if (is_null($utimestamp))
        $utimestamp = microtime(true);

    $timestamp = floor($utimestamp);
    $milliseconds = round(($utimestamp - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

/*-返回当天剩余时间戳-*/
function today_rest($day = null, $month = null, $year = null)
{

    $day = $day ? $day : date('d');
    $month = $month ? $month : date('m');
    $year = $year ? $year : date('Y');
    /*-今天凌晨时间戳-*/
    $endtime = mktime(0, 0, 0, $month, $day + 1, $year) - 1;

    $surplus = $endtime - time();

    return $surplus;
}

/*-图片水印公共处理 -*/
function build_img_uri($icon, $stock = true, $watermark = '', $is_sale = 0)
{
    return $icon;
    if ($icon == '') {
        return '';
    }

    $img_url = '';

    if ($watermark) {

        return $img_url . $icon . '?watermark/3' . $watermark;
    } else {
        if ($is_sale == 1) {
            return $img_url . $icon . '?watermark/3' . config('qiniu.')['watermark']['xiajia'];
        }
        if ($is_sale == 2) {
            return $img_url . $icon . '?watermark/3' . config('qiniu.')['watermark']['jjsj'];
        }
        if ($stock == 0 && $is_sale != 3) {
            return $img_url . $icon . '?watermark/3' . config('qiniu.')['watermark']['sellout'];
        }
    }
    return $img_url . $icon;
}

function mark($is_sale, $is_presale, $is_dingjin, $stock)
{
//        func_get_arg();

    $arr = [
        'name' => null,
        'color' => null
    ];

    if ($is_sale == 1) {
        $arr['name'] = '已下架';
        $arr['color'] = 'rgba(208,208,208,1)';
        goto end;
    }
    if ($is_sale == 2) {
        $arr['name'] = '即将上架';
        $arr['color'] = 'rgba(51,199,128,1)';
        goto end;
    }
    if ($is_dingjin) {
        $arr['name'] = '定金';
        $arr['color'] = 'rgba(254,121,69,1)';
        goto end;
    }
    if ($is_presale) {
        $arr['name'] = '预售';
        $arr['color'] = 'rgba(255,126,0,1)';
        goto end;
    }
    if ($stock <= 5 && $stock > 0) {
        $arr['name'] = '即将售罄';
        $arr['color'] = 'rgba(219,59,65,1)';
        goto end;
    }

    end:
    return $arr;

}

function concat_img($argument1, $argument2 = '', $img_url = '')
{
    if (empty($argument2)) {
        $argument2 = $argument1;
    }
    return sprintf('%s as %s', $argument1, $argument2);
//    return sprintf('CONCAT(\'%s/\',%s) as %s', $img_url, $argument1, $argument2);
}


function mdate($time = NULL)
{
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t = time() - $time; //时间差 （秒）
    $y = date('Y', $time) - date('Y', time());//是否跨年
    switch ($t) {
        case $t == 0:
            $text = '刚刚';
            break;
        case $t < 60:
            $text = $t . '秒后'; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分钟后'; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '小时后'; // 一天内
            break;
        case $t < 60 * 60 * 24 * 3:
            $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); //昨天和前天
            break;
        case $t < 60 * 60 * 24 * 30:
            $text = floor($t / (3600 * 24)) . "天后";
//            date('m月d日 H:i', $time); //一个月内
            break;
        case $t < 60 * 60 * 24 * 365 && $y == 0:
            $text = date('m月d日', $time); //一年内
            break;
        default:
            $text = date('Y年m月d日', $time); //一年以前
            break;
    }

    return $text;
}

/**
 * 求两个已知经纬度之间的距离,单位为米
 *
 * @param $lng1 ,lng2 经度
 * @param $lat1 ,lat2 纬度
 * @return float 距离，单位米
 * @author www.Alixixi.com
 */
function getdistance($lng1, $lat1, $lng2, $lat2)
{
    // 将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
}


/**
 * 给树状菜单添加level并去掉没有子菜单的菜单项
 * @param  array $data [description]
 * @param  integer $root [description]
 * @param  string $child [description]
 * @param  string $level [description]
 * @return array
 */
function memuLevelClear($data, $root = 1, $child = 'child', $level = 'level')
{
    if (is_array($data)) {

        foreach ($data as $key => $val) {
            $data[$key]['level'] = $root;

//            if (!empty($val[$child]) && is_array($val[$child])) {
//
//                $data[$key][$child] = memuLevelClear($val[$child],$root+1);
//
//            }else if ($root<3&&$data[$key]['menu_type']==1) {
//                return $data[$key];
//                unset($data[$key]);
//            }

            if (empty($data[$key][$child]) && ($data[$key]['level'] == 1)) {
                unset($data[$key]);
            }

        }
        return array_values($data);
    }
    return array();
}

/**
 * [rulesDeal 给树状规则表处理成 module-controller-action ]
 * @AuthorHTL
 * @DateTime  2017-01-16T16:01:46+0800
 * @param     array $data [树状规则数组]
 * @return    array                         [返回数组]
 */
function rulesDeal($data)
{
    if (is_array($data)) {

        $ret = [];
        foreach ($data as $k1 => $v1) {
            $str1 = $v1['name'];
            if (isset($v1['child']) && is_array($v1['child'])) {
                foreach ($v1['child'] as $k2 => $v2) {
                    $str2 = $str1 . '-' . $v2['name'];
                    if (isset($v2['child']) && is_array($v2['child'])) {
                        foreach ($v2['child'] as $k3 => $v3) {
                            $str3 = $str2 . '-' . $v3['name'];
                            $ret[] = $str3;
                        }
                    } else {
                        $ret[] = $str2;
                    }
                }
            } else {
                $ret[] = $str1;
            }
        }
        return $ret;
    }
    return [];
}

//判断两数组键值是否相等
function check_arr_equal($arra, $arrb)
{
    //两个都为空，返回true
    if (empty($arra) && empty($arrb)) {
        return true;
    }
    //数组元素数量不同，直接返回false
    if (count($arra) != count($arrb)) {
        return false;
    }

    $a = array_diff($arra, $arrb);
    $b = array_diff($arrb, $arra);

    if (empty($a) && empty($b)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 处理序列化的支付、配送的配置参数
 * 返回一个以name为索引的数组
 *
 * @access  public
 * @param   string $cfg
 * @return  void
 */
function unserialize_config($cfg)
{
    if (is_string($cfg) && ($arr = unserialize($cfg)) !== false) {
        $config = array();

        foreach ($arr AS $key => $val) {
            $config[$val['name']] = $val['value'];
        }

        return $config;
    } else {
        return false;
    }
}

/*-excel表字母递增 到Z之后取余-*/
function excel_letter_increase($i = 1)
{
    $arr = range('A', 'Z');
    $count = count($arr);


    if ($i >= 27) {
        $remain_lottery = intval($i / $count);
        $lottery = $arr[$remain_lottery - 1] . $arr[$i % $count - 1];
    } else {
        $lottery = $arr[$i - 1];
    }
    return $lottery;

}

/*-返回数组所有的顺序集合-*/
function permute($source = [])
{

    sort($source); //保证初始数组是有序的
    $last = count($source) - 1; //$source尾部元素下标
    $x = $last;
    $count = 1; //组合个数统计
    $data = [];
    array_push($data, $source);
    while (true) {
        $y = $x--; //相邻的两个元素
        if ($source[$x] < $source[$y]) { //如果前一个元素的值小于后一个元素的值
            $z = $last;
            while ($source[$x] > $source[$z]) { //从尾部开始，找到第一个大于 $x 元素的值
                $z--;
            }
            /* 交换 $x 和 $z 元素的值 */
            list($source[$x], $source[$z]) = array($source[$z], $source[$x]);
            /* 将 $y 之后的元素全部逆向排列 */
            for ($i = $last; $i > $y; $i--, $y++) {
                list($source[$i], $source[$y]) = array($source[$y], $source[$i]);
            }
            array_push($data, $source);

            $x = $last;
            $count++;
        }
        if ($x == 0) { //全部组合完毕
            break;
        }
    }
    return $data;
}

function check(array $data)
{
    if ($data['sign'] !== sign($data)) {
        abort(500, '签名错误');
    }
    return true;
}

function sign($data)
{
    unset($data['sign']);

    ksort($data);//键名正序排序

    $query = urldecode(http_build_query($data));

    return strtoupper(md5($query));
}

// 过滤掉emoji表情
function filterEmoji($str)
{
    $str = preg_replace_callback(
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);

    return $str;
}


function getTheMonthLastDay($date = '')
{
    if (empty($date)) {
        $date = date('Y-m-d');
    }

    $firstDay = date('Y-m-01', strtotime($date));
    $lastDay = date('Y-m-d', strtotime("$firstDay +1 month -1 day"));
    return $lastDay;
}


function officialAccount()
{
    $app = \EasyWeChat\Factory::officialAccount(config('wechat.easywechat_config'));
    $cache = new \Symfony\Component\Cache\Simple\RedisCache(redis());
    $app->rebind('cache', $cache);
    return $app;
}