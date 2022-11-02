<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:50
 * @LastEditTime: 2022-09-14 17:07:59
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//引入函数文件
require get_theme_file_path('/zibpay/class/order-class.php');
require get_theme_file_path('/zibpay/class/order-rebate.php');
require get_theme_file_path('/zibpay/class/order-income.php');
require get_theme_file_path('/zibpay/class/ajax-class.php');
require get_theme_file_path('/zibpay/class/card-pass.php');

foreach (array(
    'zibpay-func',
    'zibpay-post',
    'zibpay-ajax',
    'zibpay-download',
    'zibpay-user',
    'zibpay-vip',
    'zibpay-withdraw',
    'zibpay-rebate',
    'zibpay-income',
    'zibpay-points',
    'zibpay-balance',
    'income-ajax',
    'rebate-ajax',
    'balance-ajax',
    'zibpay-msg',
    'ajax',
    'widget',
) as $php) {
    require get_theme_file_path('/zibpay/functions/' . $php . '.php');
}

if (is_admin()) {
    require get_theme_file_path('/zibpay/functions/admin/admin.php');
    require get_theme_file_path('/zibpay/functions/admin/admin-options.php');
}

/**挂钩到主题启动 */
function zibpay_creat_table_order()
{
    ZibPay::create_db();
    ZibCardPass::create_db();
}
add_action('admin_head', 'zibpay_creat_table_order');
add_action('init', array('ZibCardPassAut', 'locate'));

/**
 * 排队插入JS文件
 */
add_action('admin_enqueue_scripts', 'zibpay_setting_scripts');
function zibpay_setting_scripts()
{
    if (isset($_GET['page']) && stristr($_GET['page'], "zibpay")) {
        wp_enqueue_style('zibpay_page', get_template_directory_uri() . '/zibpay/assets/css/pay-page.css', array(), THEME_VERSION);
        wp_enqueue_script('highcharts', get_template_directory_uri() . '/zibpay/assets/js/highcharts.js', array('jquery'), THEME_VERSION);
        wp_enqueue_script('westeros', get_template_directory_uri() . '/zibpay/assets/js/westeros.min.js', array('jquery', 'highcharts'), THEME_VERSION);
        wp_enqueue_script('zibpay_page', get_template_directory_uri() . '/zibpay/assets/js/pay-page.js', array('jquery', 'jquery_form'), THEME_VERSION);
    }
}

/**创建编辑器短代码 */
//添加隐藏内容，付费可见
function zibpay_to_show($atts, $content = null)
{

    $a     = '#posts-pay';
    $_hide = '<div class="hidden-box"><a class="hidden-text" href="javascript:(scrollTo(\'' . $a . '\',-120));"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;此处内容已隐藏，请付费后查看</a></div>';
    global $post;

    $pay_mate = get_post_meta($post->ID, 'posts_zibpay', true);

    $paid = zibpay_is_paid($post->ID);
    /**如果未设置付费阅读功能，则直接显示 */
    if (empty($pay_mate['pay_type']) || '1' != $pay_mate['pay_type']) {
        return $content;
    }

    /**
     * 判断逻辑
     * 1. 管理登录
     * 2. 已经付费
     * 3. 必须设置了付费阅读
     */
    if (is_super_admin()) {
        return '<div class="hidden-box show"><div class="hidden-text">本文隐藏内容 - 管理员可见</div>' . do_shortcode($content) . '</div>';
    } elseif ($paid) {
        $paid_name = zibpay_get_paid_type_name($paid['paid_type']);
        return '<div class="hidden-box show"><div class="hidden-text">本文隐藏内容 - ' . $paid_name . '</div>' . do_shortcode($content) . '</div>';
    } else {
        return $_hide;
    }
}
add_shortcode('payshow', 'zibpay_to_show');
