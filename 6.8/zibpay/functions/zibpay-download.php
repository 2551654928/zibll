<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:50
 * @LastEditTime: 2022-04-26 13:20:07
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//获取用户已下载的次数
function zibpay_get_user_down_number($user_id = '')
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return false;
    }

    $time = current_time('Y-m-d');

    $user_mate        = get_user_meta($user_id, 'pay_down_number', true);
    $user_down_number = !empty($user_mate[$time]) ? count($user_mate[$time]) : 0;

    return $user_down_number;
}

//储存用户下载次数
function zibpay_set_user_down_number($post_id, $user_id)
{

    if (!$user_id) {
        return false;
    }

    $time = current_time('Y-m-d');

    $user_mate              = get_user_meta($user_id, 'pay_down_number', true);
    $user_mate              = $user_mate ? $user_mate : array();
    $today                  = !empty($user_mate[$time]) ? $user_mate : array();
    $today[$time][$post_id] = 1;

    update_user_meta($user_id, 'pay_down_number', $today);
}

//储存用户下载次数
function zibpay_get_down_limit($post_id, $user_id = 0)
{

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

}

//获取下载按钮
function zibpay_get_post_down_buts($pay_mate, $paid_type = 'pay', $post_id = '')
{
    if (empty($pay_mate['pay_download'])) {
        return '<div class="muted-2-color text-center">暂无可下载资源</div>';
    }

    $down     = zibpay_get_post_down_array($pay_mate);
    $con      = '';
    $down_but = '';
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }
    $down_url = add_query_arg(array('post_id' => $post_id, 'key' => wp_create_nonce('pay_down')), get_template_directory_uri() . '/zibpay/download.php');
    foreach ($down as $key => $down_v) {
        $down_link = add_query_arg(array('down_id' => $key), $down_url);
        $down_name = $down_v['name'] ? $down_v['name'] : '本地下载';
        $down_more = $down_v['more'] ? '<span class="badg">' . $down_v['more'] . '</span>' : '';
        $icon      = '<i class="fa fa-download" aria-hidden="true"></i>';
        $class     = 'b-theme';
        if (stripos($down_v['link'], 'weiyun') || stripos($down_v['link'], 'qq')) {
            $class .= ' weiyun';
            $down_name = $down_v['name'] ? $down_v['name'] : '腾讯微云';
            $icon      = zib_get_svg('weiyun', '0 0 1400 1024');
        }
        if (stripos($down_v['link'], 'baidu')) {
            $class .= ' baidu';
            $down_name = $down_v['name'] ? $down_v['name'] : '百度网盘';
            $icon      = zib_get_svg('pan_baidu');
        }
        if (stripos($down_v['link'], 'lanzou')) {
            $down_name = $down_v['name'] ? $down_v['name'] : '蓝奏云';
            $class .= ' lanzou';
            $icon = zib_get_svg('lanzou');
        }
        if (stripos($down_v['link'], 'onedrive') || stripos($down_v['link'], 'sharepoint')) {
            $down_name = $down_v['name'] ? $down_v['name'] : 'OneDrive';
            $class .= ' onedrive';
            $icon = zib_get_svg('onedrive');
        }
        if (stripos($down_v['link'], '.189.')) {
            $down_name = $down_v['name'] ? $down_v['name'] : '天翼云';
            $class .= ' tianyi';
            $icon = zib_get_svg('tianyi');
        }
        if (stripos($down_v['link'], 'ctfile')) {
            $down_name = $down_v['name'] ? $down_v['name'] : '城通网盘';
            $class .= ' ctfile';
            $icon = zib_get_svg('ctfile', '0 0 1260 1024');
        }

        @$class = !empty($down_v['class']) ? $down_v['class'] : $class;
        @$icon  = !empty($down_v['icon']) ? zib_get_cfs_icon($down_v['icon']) : $icon;

        $down_but .= '<div class="but-download"><a target="_blank" href="' . $down_link . '" class="mr10 but ' . $class . '">' . $icon . $down_name . '</a>' . $down_more . '</div>';
    }
    if (!$down_but) {
        return '<div class="muted-2-color text-center">暂无可下载资源</div>';
    }

    //限制下载次数
    $download_limit_html = '';
    $user_id             = get_current_user_id();
    if ($user_id && stristr($paid_type, 'free')) {
        //免费资源限制下载次数
        $download_limit   = 0;
        $user_vip_level   = zib_get_user_vip_level($user_id);
        $user_down_number = zibpay_get_user_down_number($user_id);

        if ($user_vip_level && _pz('pay_user_vip_' . $user_vip_level . '_s', true)) {
            $download_limit = _pz('vip_benefit', 0, 'pay_download_limit_vip_' . $user_vip_level);
        } else {
            $download_limit = _pz('vip_benefit', 0, 'pay_download_limit');
        }

        if ($download_limit) {
            $surplus = $download_limit - $user_down_number; //计算剩余下载次数
            if ($surplus < 1) {
                $down_but = '<div class=""><span class="badg c-red btn-block">您今日下载免费资源个数已超限，请明日再下载</span></div>';
            } else {
                $_text    = $user_vip_level ? '您是尊贵的' . _pz('pay_user_vip_' . $user_vip_level . '_name') . '，' : '您';
                $down_but = '<div class=""><span class="badg c-red btn-block">' . $_text . '今日还可下载' . $surplus . '个免费资源</span></div>' . $down_but;
            }
        }

        $download_limit       = _pz('vip_benefit', 0, 'pay_download_limit');
        $download_limit_vip_1 = _pz('vip_benefit', 0, 'pay_download_limit_vip_1');
        $download_limit_vip_2 = _pz('vip_benefit', 0, 'pay_download_limit_vip_2');

        if ($download_limit || $download_limit_vip_1 || $download_limit_vip_2) {
            $download_limit_html = '<div class="mb10" style=" padding: 10px 20px; background:var(--muted-border-color); border-radius: 4px; ">';
            $download_limit_html .= '<div class="mb6">免费资源每日可下载：</div>';
            $download_limit_html .= $download_limit ? '<div class="mb6">普通用户：' . ($download_limit ? $download_limit . '个' : '不限制') . '</div>' : '';
            $download_limit_html .= $download_limit_vip_1 ? '<div class="mb6">' . _pz('pay_user_vip_1_name') . '：' . ($download_limit_vip_1 ? $download_limit_vip_1 . '个' : '不限制') . '</div>' : '';
            $download_limit_html .= $download_limit_vip_2 ? '<div class="">' . _pz('pay_user_vip_2_name') . '：' . ($download_limit_vip_2 ? $download_limit_vip_2 . '个' : '不限制') . '</div>' : '';
            $download_limit_html .= '</div>';
        }
    }

    $con .= '<div>';
    $con .= $download_limit_html;
    $con .= $down_but;
    $con .= '</div>';

    return $con;
}

/**
 * @description: 对链接进行数组处理
 * @param int|array $post_id 文章ID或者posts_zibpay的post_meta数组
 * @return {*}
 */
function zibpay_get_post_down_array($post_id = '')
{
    //允许传入数组
    if (is_array($post_id) && !empty($post_id['pay_download'])) {
        $pay_mate = $post_id;
    } else {
        if (!$post_id) {
            global $post;
            $post_id = !empty($post->ID) ? $post->ID : '';
        }
        $pay_mate = get_post_meta($post_id, 'posts_zibpay', true);
    }

    if (!$pay_mate) {
        return array();
    }

    //新版兼容
    if (is_array($pay_mate['pay_download']) && isset($pay_mate['pay_download'][0]['link'])) {
        return $pay_mate['pay_download'];
    }
    $down     = explode("\r\n", $pay_mate['pay_download']);
    $down_obj = array();
    $ii       = 0;
    foreach ($down as $down_v) {
        //如果没有链接则跳出
        $down_v = explode("|", $down_v);
        if (empty($down_v[0])) {
            continue;
        }

        $down_obj[$ii] = array(
            'link'  => trim($down_v[0]),
            'name'  => !empty($down_v[1]) ? trim($down_v[1]) : '',
            'more'  => !empty($down_v[2]) ? trim($down_v[2]) : '',
            'class' => !empty($down_v[3]) ? trim($down_v[3]) : '',
        );
        $ii++;
    }
    return $down_obj;
}

/**v5.0已经弃用 */
function zibpay_edit_posts_file_upload()
{
    //echo json_encode($_FILES);
    if (is_uploaded_file($_FILES['zibpayFile']['tmp_name']) && is_user_logged_in() && current_user_can('publish_posts')) {
        $vname = $_FILES['zibpayFile']['name'];
        if ($vname != "") {
            $filename = substr(md5(current_time("YmdHis")), 0, 10) . mt_rand(11, 99) . strrchr($vname, '.');
            //上传路径
            $upfile = WP_CONTENT_DIR . '/uploads/zibpaydown/';
            if (!file_exists($upfile)) {
                mkdir($upfile, 0777, true);
            }
            $file_path = WP_CONTENT_DIR . '/uploads/zibpaydown/' . $filename;
            if (move_uploaded_file($_FILES['zibpayFile']['tmp_name'], $file_path)) {
                echo home_url() . '/wp-content/uploads/zibpaydown/' . $filename;
                exit;
            }
        }
    }
}
//add_action('wp_ajax_zibpay_file_upload', 'zibpay_edit_posts_file_upload');
