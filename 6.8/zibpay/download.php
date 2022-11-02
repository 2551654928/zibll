<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:50
 * @LastEditTime: 2022-04-28 21:30:45
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Email         : 770349780@qq.com
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

require dirname(__FILE__) . '/../../../../wp-load.php';

if (!isset($_GET['down_id']) || empty($_GET['post_id'])) {
    wp_safe_redirect(home_url());
}

$down_id = $_GET['down_id'];
$post_id = $_GET['post_id'];

//安全验证
if (!isset($_GET['key']) || !wp_verify_nonce($_GET['key'], 'pay_down')) {
    wp_die('环境异常！请重新获取下载链接');
    exit();
}

//判断用户是否已经购买
//查询是否已经购买
$paid = zibpay_is_paid($post_id);
if (!$paid) {
    wp_die('支付信息获取失败，请刷新后重试！');
    exit;
}
if ($paid['paid_type'] == 'free' && _pz('pay_free_logged_show') && !is_user_logged_in()) {
    wp_die('登录信息异常，请重新登录！');
    exit;
}

$down = zibpay_get_post_down_array($post_id);

if (empty($down[$down_id]['link'])) {
    wp_die('未获取到资源文件或下载链接已失效，请与管理员联系！');
    exit;
}

//限制下载次数检测
$is_download_limit = false;
$user_id           = get_current_user_id();
if ($user_id && stristr($paid['paid_type'], 'free')) {
    //免费资源限制下载次数
    $download_limit = 0;
    $user_vip_level = zib_get_user_vip_level($user_id);
    if ($user_vip_level && _pz('pay_user_vip_' . $user_vip_level . '_s', true)) {
        $download_limit = _pz('vip_benefit', 0, 'pay_download_limit_vip_' . $user_vip_level);
    } else {
        $download_limit = _pz('vip_benefit', 0, 'pay_download_limit');
    }

    if ($download_limit) {
        $is_download_limit = true;
        $user_down_number  = zibpay_get_user_down_number($user_id);

        $surplus = $download_limit - $user_down_number;
        if ($surplus < 1) {
            wp_die('您今日下载免费资源个数已超限，请明日再下载');
            exit;
        }
    }
}

$file_dir = str_replace('&amp;', '&', trim($down[$down_id]['link']));

$home = home_url('/');

if (stripos($file_dir, $home) === 0) {
    $file_dir_local = chop(str_replace($home, "", $file_dir)); //本地
    $file_dir_local = ABSPATH . $file_dir;

    if (file_exists($file_dir)) {
        $file_dir = $file_dir_local;
    }
}

preg_match('/^(http|https|thunder|qqdl|ed2k|Flashget|qbrowser|magnet|ftp):\/\//i', $file_dir, $matches);

if ($matches) {
    $file_path = chop($file_dir);
    //储存下载次数
    if ($is_download_limit) {
        zibpay_set_user_down_number($post_id, $user_id);
    }

    header('location:' . $file_path);
    echo '<html><head><meta name="robots" content="noindex,nofollow"><script>location.href = "' . $file_path . '";</script></head><body></body></html>';
    exit;
}

//储存下载次数
if ($is_download_limit) {
    zibpay_set_user_down_number($post_id, $user_id);
}
$temp = explode("/", $file_dir);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . end($temp) . "\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($file_dir));
ob_end_flush();
@readfile($file_dir);
exit;
