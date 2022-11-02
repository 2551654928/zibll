<?php

/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-05 17:40:41
 * @LastEditTime: 2022-10-26 03:07:32
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|工具函数
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

/**判断是否在微信APP内 */
function zib_is_wechat_app()
{
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    return strripos($useragent, 'micromessenger');
}

//删除内容或者数组的两端空格
function zib_trim($Input)
{
    if (!is_array($Input)) {
        return trim($Input);
    }
    return array_map('zib_trim', $Input);
}

//判断是否是蜘蛛爬虫
function zib_is_crawler()
{

    static $zib_is_crawler = 'is-null';
    if ($zib_is_crawler !== 'is-null') {
        return $zib_is_crawler;
    }

    $bots = array(
        'Baidu'         => 'baiduspider',
        'Google Bot'    => 'google',
        '360spider'     => '360spider',
        'Sogou'         => 'spider',
        'soso.com'      => 'sosospider',
        'MSN'           => 'msnbot',
        'Alex'          => 'ia_archiver',
        'Lycos'         => 'lycos',
        'Ask Jeeves'    => 'jeeves',
        'Altavista'     => 'scooter',
        'AllTheWeb'     => 'fast-webcrawler',
        'Inktomi'       => 'slurp@inktomi',
        'Turnitin.com'  => 'turnitinbot',
        'Technorati'    => 'technorati',
        'Yahoo'         => 'yahoo',
        'Findexa'       => 'findexa',
        'NextLinks'     => 'findlinks',
        'Gais'          => 'gaisbo',
        'WiseNut'       => 'zyborg',
        'WhoisSource'   => 'surveybot',
        'Bloglines'     => 'bloglines',
        'BlogSearch'    => 'blogsearch',
        'PubSub'        => 'pubsub',
        'Syndic8'       => 'syndic8',
        'RadioUserland' => 'userland',
        'Gigabot'       => 'gigabot',
        'Become.com'    => 'become.com',
        'Yandex'        => 'yandex',
    );
    $useragent      = isset($_SERVER['HTTP_USER_AGENT']) ? addslashes(strtolower($_SERVER['HTTP_USER_AGENT'])) : '';
    $zib_is_crawler = false;
    if ($useragent) {
        foreach ($bots as $name => $lookfor) {
            if (!empty($useragent) && (false !== stripos($useragent, $lookfor))) {
                $zib_is_crawler = $name;
            }
        }
    }

    return $zib_is_crawler;
}

/**后台生成二维码图片 */
function zib_get_qrcode_base64($url)
{
    //引入phpqrcode类库
    require_once get_theme_file_path('/inc/class/qrcode.class.php');
    $errorCorrectionLevel = 'L'; //容错级别
    $matrixPointSize      = 6; //生成图片大小
    ob_start();
    QRcode::png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    $data = ob_get_contents();
    ob_end_clean();

    $imageString = base64_encode($data);
    header("content-type:application/json; charset=utf-8");
    return 'data:image/jpeg;base64,' . $imageString;
}

/**
 * @description:
 * @param {*} $url
 * @return {*}
 */
function zib_get_img_auto_base64($url)
{
    $base64 = zib_get_img_base64($url);
    return $base64 ?: $url;
}

/**
 * @description: 将图片链接转为base64格式
 * @param {*} $url
 * @return {*}
 */
function zib_get_img_base64($url)
{
    $cache_key = md5($url);
    $cache     = wp_cache_get($cache_key, 'image_base64', true);
    if ($cache !== false) {
        return $cache;
    }

    $base64_encode = base64_encode(file_get_contents($url));
    $base64        = $base64_encode ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($url)) : false;

    wp_cache_set($cache_key, $base64, 'image_base64');

    return $base64;
}

//判断是否启用了图片懒加载
function zib_is_lazy($key, $default = false)
{
    if (zib_is_crawler()) {
        return false;
    }

    return _pz($key, $default);
}

function zib_get_lazy_attr($key, $src, $class = '', $lazy_src = ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail.svg')
{
    return zib_is_lazy($key) ? ' class="lazyload ' . $class . '" src="' . $lazy_src . '" data-src="' . $src . '"' : ' class="' . $class . '" src="' . $src . '"';
}

//为已经添加了图片懒加载的元素移出懒加载的内容
function zib_str_remove_lazy($str = '')
{
    $pattern     = "/<img(.*?)src=('|\")(.*?)('|\") data-src=('|\")(.*?)('|\")(.*?)>/i";
    $replacement = '<img$1src=$5$6$7$8>';

    return preg_replace($pattern, $replacement, str_replace('lazyload', '', $str));
}

function zib_imgtobase64($img = '')
{
    $imageInfo = getimagesize($img);
    return 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode(file_get_contents($img)));
}

//搜索数组多维数组
function zib_array_search($array, $search, $key = 'id', $value = 'count')
{
    //if (!is_array($array) || !is_object($array)) return array();
    $array = (array) $array;
    foreach ($array as $v) {
        $v = (array) $v;
        if ($search == $v[$key]) {
            return $v[$value];
        }
    }
    return false;
}

//中文文字计数
function zib_new_strlen($str, $charset = 'utf-8')
{
    //中文算一个，英文算半个
    return (int) ((strlen($str) + mb_strlen($str, $charset)) / 4);
}

//时间倒序格式化
function zib_get_time_ago($time)
{
    if (is_int($time)) {
        $time = intval($time);
    } else {
        $time = strtotime($time);
    }

    if (!_pz('time_ago_s', true) && _pz('time_format')) {
        return date(_pz('time_format'), $time);
    }
    $ctime = intval(strtotime(current_time('mysql')));
    $t     = $ctime - $time; //时间差 （秒）

    if ($t < 0) {
        return date('Y-m-d H:i', $time);
    }
    $y = intval(date('Y', $ctime) - date('Y', $time)); //是否跨年
    if (0 == $t) {
        $text = '刚刚';
    } elseif ($t < 60) {
        //一分钟内
        $text = $t . '秒前';
    } elseif ($t < 3600) {
        //一小时内
        $text = floor($t / 60) . '分钟前';
    } elseif ($t < 86400) { //一天内
        $text = floor($t / 3600) . '小时前'; // 一天内
    } elseif ($t < 2592000) {
        //30天内
        if ($time > strtotime(date('Ymd', strtotime("-1 day")))) {
            $text = '昨天';
        } elseif ($time > strtotime(date('Ymd', strtotime("-2 days")))) {
            $text = '前天';
        } else {
            $text = floor($t / 86400) . '天前';
        }
    } elseif ($t < 31536000 && 0 == $y) {
        //一年内 不跨年
        $m = date('m', $ctime) - date('m', $time) - 1;

        if (0 == $m) {
            $text = floor($t / 86400) . '天前';
        } else {
            $text = $m . '个月前';
        }
    } elseif ($t < 31536000 && $y > 0) {
        //一年内 跨年
        $text = (12 - date('m', $time) + date('m', $ctime)) . '个月前';
    } else {
        $text = (date('Y', $ctime) - date('Y', $time)) . '年前';
    }

    return $text;
}

//剩下的时间格式化
function zib_get_time_remaining($time, $over_text = '已过期')
{

    if (is_int($time)) {
        $time = intval($time);
    } else {
        $time = strtotime($time);
    }

    $ctime = intval(strtotime(current_time('mysql')));
    $t     = $time - $ctime; //时间差 （秒）

    if ($t <= 0) {
        return $over_text;
    }

    $y = intval(date('Y', $ctime) - date('Y', $time)); //是否跨年
    if ($t < 60) {
        //一分钟内
        $text = $t . '秒后';
    } elseif ($t < 3600) {
        //一小时内
        $text = floor($t / 60) . '分钟后';
    } elseif ($t < 86400) { //一天内
        $text = floor($t / 3600) . '小时后'; // 一天内
    } elseif ($t < 2592000) {
        //30天内
        $text = floor($t / 86400) . '天后';
    } elseif ($t < 31536000 && 0 == $y) {
        //一年内 不跨年
        $m = date('m', $ctime) - date('m', $time) - 1;
        if ($m > 0) {
            $text = $m . '月后';
        } else {
            $text = floor($t / 86400) . '天后';
        }
    } elseif ($t < 31536000 && $y > 0) {
        //一年内 跨年
        $text = (12 - date('m', $time) + date('m', $ctime)) . '月后';
    } else {
        $text = (date('Y', $ctime) - date('Y', $time)) . '年后';
    }

    return $text;
}

function zib_get_time_spend($time, $unit = 'day')
{
    if (is_int($time)) {
        $time = intval($time);
    } else {
        $time = strtotime($time);
    }

    $current_time = intval(strtotime(current_time('mysql')));
    $t            = $current_time - $time; //时间差 （秒）

    switch ($unit) {
        case 'day':
        case 'days':
            return floor($t / 86400);
            break;
    }

}

/**
 * 导出数据为excel表格
 * @param
 * array $data  一个二维数组,结构如同从数据库查出来的数组
 * array $title  excel的第一行标题,一个数组,如果为空则没有标题
 * String $filename 下载的文件名
 */
function zib_export_excel($data = array(), $title = array(), $filename = 'export_excel')
{
    header("Content-type:application/octet-stream");
    header("Accept-Ranges:bytes");
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:attachment;filename=" . $filename . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    //导出xls 开始
    if (!empty($title)) {
        foreach ($title as $k => $v) {
            $title[$k] = iconv("UTF-8", "GB2312", $v);
        }
        $title = implode("\t", $title);
        echo "$title\n";
    }

    if (!empty($data)) {
        $_data = array();
        foreach ($data as $val) {
            $val = (array) $val;
            foreach ($val as $ck => $cv) {
                $val[$ck] = mb_convert_encoding($cv, "GB2312", "UTF-8");
            }
            $_data[] = implode("\t", $val);
        }
        echo implode("\n", $_data);
    }

    exit;
}
