<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-04-17 17:49:02
 * @LastEditTime: 2022-07-15 12:59:46
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|用户余额系统
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//余额充值
function zibpay_ajax_balance_charge_modal()
{

    $user_id = get_current_user_id();

    echo zibpay_get_balance_charge_modal($user_id);
    exit;
}
add_action('wp_ajax_balance_charge_modal', 'zibpay_ajax_balance_charge_modal');

//购买积分
function zibpay_ajax_points_pay_modal()
{

    $user_id = get_current_user_id();

    echo zibpay_get_points_pay_modal($user_id);
    exit;
}
add_action('wp_ajax_points_pay_modal', 'zibpay_ajax_points_pay_modal');

//管理员后台添加或扣除余额或者积分
function zibpay_ajax_admin_update_user_balance_or_points()
{

    if (!is_super_admin()) {
        zib_send_json_error('权限不足，仅管理员可操作');
    }

    $action  = $_REQUEST['action'];
    $user_id = !empty($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;
    $val     = !empty($_REQUEST['val']) ? ($action === 'admin_update_user_balance' ? round((float) $_REQUEST['val'], 2) : (int) $_REQUEST['val']) : 0;
    $decs    = !empty($_REQUEST['decs']) ? esc_attr($_REQUEST['decs']) : '';
    $type    = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '';

    if (!$type) {
        zib_send_json_error('请选择添加或扣除');
    }

    if (!$val) {
        zib_send_json_error('请输入数额');
    }

    if (!$user_id) {
        zib_send_json_error('数据或或环境异常');
    }

    $val = $type === 'add' ? $val : -$val;

    $data = array(
        'value' => $val, //值 整数为加，负数为减去
        'type'  => '管理员手动' . ($type === 'add' ? '添加' : '扣除'),
        'desc'  => $decs, //说明
    );

    if ($action === 'admin_update_user_balance') {
        //余额管理
        if (!_pz('pay_balance_s')) {
            zib_send_json_error('余额功能已关闭');
        }
        zibpay_update_user_balance($user_id, $data);
    } else {
        //积分管理
        if (!_pz('points_s')) {
            zib_send_json_error('积分功能已关闭');
        }
        zibpay_update_user_points($user_id, $data);
    }

    zib_send_json_success('操作成功，请刷新页面后查看最新数据');

}
add_action('wp_ajax_admin_update_user_balance', 'zibpay_ajax_admin_update_user_balance_or_points');
add_action('wp_ajax_admin_update_user_points', 'zibpay_ajax_admin_update_user_balance_or_points');
