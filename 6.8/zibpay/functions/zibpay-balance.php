<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-04-17 17:49:02
 * @LastEditTime: 2022-09-04 17:15:48
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|用户余额系统 balance
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

/**
 * @description: 获取用户余额
 * @param {*} $user_id
 * @return {*}
 */
function zibpay_get_user_balance($user_id)
{
    $balance = get_user_meta($user_id, 'balance', true);
    return floatval(round((float) $balance, 2));
}

/**
 * @description: 获取用户提现中的余额
 * @param {*} $user_id
 * @return {*}
 */
function zibpay_get_user_withdraw_ing_balance($user_id)
{
    $balance = get_user_meta($user_id, 'balance_withdraw_ing', true);
    return floatval($balance);
}

/**
 * @description: 获取用户待转入的
 * @param {*} $user_id
 * @return {*}
 */
function zibpay_get_user_add_ing_balance($user_id)
{
    $balance = get_user_meta($user_id, 'balance_add_ing', true);
    return abs(floatval($balance));
}

/**
 * @description: 获取充值模态框的按钮
 * @param {*} $class
 * @param {*} $con
 * @return {*}
 */
function zibpay_get_balance_charge_link($class = '', $con = '充值')
{
    $user_id = get_current_user_id();
    if (!$user_id || !_pz('pay_balance_s')) {
        return;
    }

    $args = array(
        'tag'           => 'a',
        'data_class'    => 'modal-mini full-sm',
        'class'         => 'balance-charge-link ' . $class,
        'mobile_bottom' => true,
        'height'        => 330,
        'text'          => $con,
        'query_arg'     => array(
            'action' => 'balance_charge_modal',
        ),
    );

    //每次都刷新的modal
    return zib_get_refresh_modal_link($args);
}

/**
 * @description: 获取购买余额金额限制
 * @param {*}
 * @return {*}
 */
function zibpay_get_pay_balance_product_custom_limit()
{
    $option = _pz('pay_balance_product_custom_limit', array('min' => 10, 'max' => 500));

    return array(
        'min' => floatval($option['min']),
        'max' => floatval($option['max']),
    );
}

/**
 * @description: 用户余额变动统一接口
 * @param {*} $user_id
 * @param {*} $data
 * @return {*}
 */
function zibpay_update_user_balance($user_id, $data)
{
    $defaults = array(
        'order_num' => '', //订单号
        'value'     => 0, //值 整数为加，负数为减去
        'type'      => '', //中文说明
        'desc'      => '', //说明
        'time'      => current_time('Y-m-d H:i'),
    );
    $data = wp_parse_args($data, $defaults);
    if (!$user_id || $data['value'] === 0) {
        return;
    }

    $user_balance    = zibpay_get_user_balance($user_id);
    $data['balance'] = $user_balance + $data['value']; //记录当前余额
    $data['balance'] = round($data['balance'], 2); //最大两位小数

    if ($data['balance'] < 0) {
        $data['balance'] = 0;
    }

    $record = get_user_meta($user_id, 'balance_record', true);
    if (!$record || !is_array($record)) {
        $record = array();
    }

    $max        = 50; //最多保存多少条记录
    $record     = array_slice($record, 0, $max - 1, true); //数据切割，删除多余的记录
    $new_record = array_merge(array($data), $record);

    update_user_meta($user_id, 'balance', $data['balance']);
    return update_user_meta($user_id, 'balance_record', $new_record);
}

/**
 * @description: 用户充值的模态框内容
 * @param {*} $user_id
 * @return {*}
 */
function zibpay_get_balance_charge_modal($user_id)
{

    $current_user      = get_userdata($user_id);
    $desc              = _pz('pay_balance_desc');
    $desc              = $desc ? '<div class="muted-box muted-2-color padding-10 mb10 em09">' . $desc . '</div>' : '';
    $product           = _pz('pay_balance_product');
    $custom_s          = _pz('pay_balance_product_custom_s', true);
    $custom_limit      = zibpay_get_pay_balance_product_custom_limit();
    $mark              = zibpay_get_pay_mark();
    $custom_limit_html = !empty($custom_limit['min']) ? '最低充值' . $mark . $custom_limit['min'] : '';
    $custom_limit_html .= $custom_limit_html ? '，' : '';
    $custom_limit_html .= !empty($custom_limit['max']) ? '最高充值' . $mark . $custom_limit['max'] : '';
    $default_pay_price = 0; //默认支付金额
    $custom_product    = '<div class="" data-for="balance_product" data-value="custom">
    <div class="relative flex ab">
        <span class="ml6 mr10 muted-color shrink0">' . $mark . '</span>
        <input class="line-form-input em16 key-color" style="padding: 1px;" name="custom_price" type="number" ' . (!empty($custom_limit['min']) ? ' limit-min="' . $custom_limit['min'] . '"' : '') . (!empty($custom_limit['max']) ? ' limit-max="' . $custom_limit['max'] . '"' : '') . ' warning-max="最高可充值1$元" warning-min="最低需充值1$元">
        <i class="line-form-line"></i>
    </div>
    <div class="muted-2-color em09 mt6">' . $custom_limit_html . '</div></div>';

    $header = '<div class="mb10 touch"><button class="close" data-dismiss="modal">' . zib_get_svg('close', null, 'ic-close') . '</button><b class="modal-title flex ac"><span class="mr6 em14">' . zib_get_svg('money-color-2') . '</span>余额充值</b></div>';

    $product_html = '';
    foreach ($product as $product_i => $vip_product) {
        $price      = $vip_product['price'];
        $show_price = $vip_product['pay_price'] ?: $price;
        if ($product_i === 0) {
            $default_pay_price = $show_price; //默认支付金额
        }

        $vip_tag = $vip_product['tag'];
        $vip_tag = $vip_tag ? '<div class="abs-right vip-tag ' . $vip_product['tag_class'] . '">' . $vip_tag . '</div>' : '';

        $product_html .= '<div class="zib-widget vip-product relative product-box' . ($product_i === 0 ? ' active' : '') . '"  data-for="balance_product" data-value="' . $product_i . '">' . $vip_tag . '
        <div class="em14"><span class="px12">' . $mark . '</span>' . $price . '</div>
        <div class="c-red"><span class="px12">￥</span><span class="">' . $show_price . '</span>' . ($show_price < $price ? '<span class="ml6 c-yellow smail">省' . ($price - $show_price) . '</span>' : '') . '</div>
        </div>';
    }

    if ($product_html) {
        $product_html = '<div class="muted-color mb6">请选择充值金额</div>' . $product_html;
        if ($custom_s) {
            $product_html .= '<div class="muted-color mt20 mb6">自定义充值金额</div>' . $custom_product;
        }
    }

    if (!$product_html) {
        $product_html = '<div class="muted-color mb6">请输入充值金额</div>' . $custom_product;
    }

    //卡密支付
    if (_pz('pay_balance_pass_charge_s')) {
        add_filter('zibpay_is_allow_card_pass_pay', '__return_true'); //添加卡密充值
        add_filter('zibpay_card_pass_payment_desc', function () {
            $password_desc = _pz('pay_balance_pass_charge_desc');
            return $password_desc ? '<div class="muted-box muted-2-color padding-10 mb10 em09">' . $password_desc . '</div>' : '';
        });
        $payment_methods = zibpay_get_payment_methods(8);
        if (count($payment_methods) <= 1) {
            $product_html = '';
        }
    }

    $charge_html = $product_html ? '<div class="charge-box mb20">' . $product_html . '</div>' : '';
    $charge_html .= $desc;

    $hidden = '<input type="hidden" name="balance_product" value="0">';
    $hidden .= '<input type="hidden" name="action" value="initiate_pay">';
    $hidden .= '<input type="hidden" name="order_type" value="8">';
    $hidden .= '<input type="hidden" name="order_name" value="' . get_bloginfo('name') . '-余额充值">';
    $pay_button = zibpay_get_initiate_pay_input(8);

    $form = '<form class="balance-charge-form mini-scrollbar scroll-y max-vh7">' . $charge_html . $hidden . $pay_button . '</form>';

    $html = '';
    $html .= $header . $form;

    return $html;
}

/**
 * @description: 为支付方式添加卡密支付
 * @param {*} $payment_methods
 * @return {*}
 */
function zibpay_payment_methods_add_password($payment_methods)
{
    $payment_methods[] = 'password';
    return $payment_methods;
}

/**
 * @description: 支付成功后，对余额功能的相关处理
 * @param {*} $pay_order
 * @return {*}
 */
function zibpay_payment_order_balance($pay_order)
{
    $order_type = $pay_order->order_type;
    if ($order_type == 8) {
        //如果是余额充值
        $product_id = $pay_order->product_id;
        if (!$product_id) {
            $charge_price = $pay_order->order_price;
        } else {
            $product      = _pz('pay_balance_product');
            $product_id   = str_replace('balance_', '', $product_id);
            $charge_price = $product[$product_id]['price'];
        }

        $data = array(
            'order_num' => $pay_order->order_num, //订单号
            'value'     => $charge_price, //值 整数为加，负数为减去
            'type'      => '充值',
            'desc'      => '', //说明
        );
        zibpay_update_user_balance($pay_order->user_id, $data);
    }
}

if (_pz('pay_balance_s')) {
    add_action('payment_order_success', 'zibpay_payment_order_balance', 8); //支付成功后更新数据
}

/**
 * @description: 自动创建充值的卡密
 * @param {*} $num
 * @param {*} $price
 * @param {*} $rand_number
 * @param {*} $rand_password
 * @return {*}
 */
function zibpay_generate_recharge_card($num, $price, $rand_number = 20, $rand_password = 35, $other = '')
{
    $meta = array('price' => $price);
    $time = current_time('mysql');

    for ($i = 1; $i <= $num; $i++) {
        ZibCardPass::add(array(
            'card'          => ZibCardPass::rand_number($rand_number),
            'password'      => ZibCardPass::rand_password($rand_password),
            'type'          => 'balance_charge',
            'create_time'   => $time,
            'modified_time' => $time,
            'status'        => '0', //正常
            'meta'          => $meta,
            'other'         => $other,
        ));
    }

    return true;
}

/**
 * @description: 获取卡密的充值金额
 * @param {*} $db
 * @return {*}
 */
function zibpay_get_recharge_card_price($db)
{
    $db   = (array) $db;
    $meta = maybe_unserialize($db['meta']);

    return isset($meta['price']) ? $meta['price'] : 0;
}

/**
 * @description: 通过卡号和卡密查找是否有对应的卡密
 * @param {*} $card
 * @param {*} $pass
 * @return {*}
 */
function zibpay_get_recharge_card($card, $password)
{
    $msg_db = ZibCardPass::get_row(array('card' => $card, 'password' => $password, 'type' => 'balance_charge'));
    return $msg_db;
}

/**
 * @description: 使用卡密
 * @param {*} $args
 * @return {*}
 */
function zibpay_use_recharge_card($zibpay_card_pass, $order_data)
{

    if (!is_array($zibpay_card_pass->meta)) {
        $zibpay_card_pass->meta = array();
    }

    $new_meta = array(
        'user_id' => $order_data['user_id'],
        'price'   => zibpay_get_recharge_card_price($zibpay_card_pass),
    );

    $data = array(
        'id'        => $zibpay_card_pass->id,
        'status'    => 'used',
        'order_num' => $order_data['order_num'],
        'meta'      => array_merge($zibpay_card_pass->meta, $new_meta),
    );

    return ZibCardPass::update($data);
}

/**
 * @description: 获取用户余额使用记录
 * @param {*} $user_id
 * @return {*}
 */
function zibpay_get_user_balance_record_lists($user_id = 0)
{

    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return;
    }

    $record = (array) get_user_meta($user_id, 'balance_record', true);
    $lists  = '';

    foreach ($record as $k => $v) {
        if (isset($v['value']) && isset($v['balance'])) {
            $_class = $v['value'] < 0 ? 'c-red' : 'c-blue';
            $badge  = '<div class="badg badg-sm mr6 ' . $_class . '">' . $v['type'] . '</div>';
            $lists .= '<div class="border-bottom padding-h10 flex jsb">';
            $lists .= '<div class="muted-2-color">';
            $lists .= '<div class="mb6">' . $badge . $v['desc'] . '</div>';
            $lists .= $v['order_num'] ? '<div class="em09">订单号：' . $v['order_num'] . '</div>' : '';
            $lists .= $v['time'] ? '<div class="em09">时间：' . $v['time'] . '</div>' : '';
            $lists .= '</div>';
            $lists .= '<div class="flex jsb xx text-right flex0 ml10"><b class="em12 ' . $_class . '">' . ($v['value'] < 0 ? $v['value'] : '+' . $v['value']) . '</b><div class="em09 muted-2-color">余额：' . $v['balance'] . '</div></div>';
            $lists .= '</div>';
        }
    }

    if (!$lists) {
        $lists = zib_get_null('暂无余额记录', 42, 'null-order.svg');
    } else {
        if (count($record) > 49) {
            $lists .= '<div class="text-center mt20 muted-3-color">最多显示近50条记录</div>';
        }
    }
    return $lists;
}

/**
 * @description: 用户钱包卡片 
 * @param {*} $user_id
 * @return {*}
 */
function zib_get_user_wallet_mini_box($user_id = 0, $class = '')
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return;
    }
    $points_s      = _pz('points_s');
    $pay_balance_s = _pz('pay_balance_s');
    if (!$points_s && !$pay_balance_s) {
        return;
    }

    $box = '';
    if ($pay_balance_s) {
        $user_balance = _cut_count(zibpay_get_user_balance($user_id));

        $box .= '<a style="flex: 1;" class="muted-box padding-6 flex1" href="' . zib_get_user_center_url('balance') . '">
                    <div class="muted-2-color px12">余额<i class="ml6 fa fa-angle-right em12"></i></div>
                    <div class="flex jsb"><span class="font-bold c-blue-2">' . $user_balance . '</span>' . zib_get_svg('money-color-2', null, 'em14') . '</div>
                </a>';

    }
    if ($points_s) {
        $user_points = _cut_count(zibpay_get_user_points($user_id));

        $box .= '<a style="flex: 1;" class="muted-box padding-6 flex1 ml6" href="' . zib_get_user_center_url('balance') . '">
                    <div class="muted-2-color px12">积分<i class="ml6 fa fa-angle-right em12"></i></div>
                    <div class="flex jsb"><span class="font-bold c-yellow">' . $user_points . '</span>' . zib_get_svg('points-color', null, 'em14') . '</div>
                </a>';
    }

    return '<div class="flex ab jsb">' . $box . '</div>';
}

//用户中心挂钩
function zibpay_user_content_balance($user_id = 0)
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return;
    }
    $card          = '';
    $tab_but       = '';
    $tab_content   = '';
    $points_s      = _pz('points_s');
    $pay_balance_s = _pz('pay_balance_s');

    if ($pay_balance_s) {
        $pay_balance_withdraw_s = _pz('pay_balance_withdraw_s');
        $mark                   = zibpay_get_pay_mark();
        $user_balance           = zibpay_get_user_balance($user_id);
        $charge_link            = zibpay_get_balance_charge_link('but b-blue-2', '充值'); //待处理“图标
        $withdraw_ing           = zibpay_get_user_withdraw_ing_balance($user_id);
        $add_ing                = zibpay_get_user_add_ing_balance($user_id); //带转入余额
        $withdraw_link          = '';
        $withdraw_record_link   = '';
        if ($pay_balance_withdraw_s) {
            $withdraw_record_link = zibpay_get_withdraw_record_link('icon-spot');

            $withdraw_link = zibpay_get_withdraw_link('but c-blue-2 mr6 hollow', '提现' . ($withdraw_ing ? '处理中<i style="margin:0 0 0 6px;" class="fa fa-angle-right em12"></i>' : ''));
        }
        $card .= '<div class="col-sm-6">
                <div class="zib-widget relative" style="padding-left: 24px;">
                    <div class="mb6">
                    ' . zib_get_svg('money-color-2', null, 'em12 mr6') . '余额
                    </div>
                    <div class="flex jsb ac">
                        <div class="mb6 c-blue-2">' . $mark . '<span class="em3x font-bold ml6">' . $user_balance . '</span></div>
                        <div class="flex0">' . $withdraw_link . $charge_link . '</div>
                    </div>
                    <div class="muted-2-color em09">' . ($add_ing ? '<span data-toggle="tooltip" title="提现待转入">待转入' . $mark . $add_ing . '</span>' : '<span>待转入' . $mark . $add_ing . '</span>') . $withdraw_record_link . '</div>
                </div>
            </div>';

        // tab-列表内容：余额变动
        $tab_but .= '<li class="active"><a data-toggle="tab" href="#record_tab_balance">余额记录</a></li>';
        $tab_content .= '<div class="tab-pane fade active in" id="record_tab_balance">';
        $tab_content .= zibpay_get_user_balance_record_lists($user_id);
        $tab_content .= '</div>';
    }

    if ($points_s) {
        $user_points     = zibpay_get_user_points($user_id);
        $points_pay_link = zibpay_get_points_pay_link('but c-green', '购买积分');
        $checkin_btn     = zib_get_user_checkin_btn('muted-color px12', '签到领积分<i class="fa fa-angle-right em12 ml3"></i>', '今日已签到<i class="fa fa-angle-right em12 ml3"></i>');

        $card .= '<div class="col-sm-6">
                    <div class="zib-widget relative" style="padding-left: 24px;">
                        <div class="flex jsb">
                            <div class="mb6">
                            ' . zib_get_svg('points-color', null, 'em12 mr6') . '积分
                            </div>
                            ' . $checkin_btn . '
                        </div>
                        <div class="flex jsb ac">
                            <div class="mb6 c-green">' . zib_get_svg('points') . '<span class="em3x font-bold ml6">' . $user_points . '</span></div>
                            <div class="flex0">' . $points_pay_link . '</div>
                        </div>
                        <div class="muted-2-color em09 pointer" data-onclick="[href=\'#record_tab_points_free\']">做任务赚积分<i style="margin:0 0 0 6px;" class="fa fa-angle-right em12"></i></div>
                    </div>
                </div>';
        // tab-列表内容：积分变动
        $tab_but .= '<li class="' . (!$pay_balance_s ? 'active' : '') . '"><a data-toggle="tab" href="#record_tab_points">积分记录</a></li>';
        $tab_but .= '<li class=""><a data-toggle="tab" href="#record_tab_points_free">积分任务</a></li>';

        $tab_content .= '<div class="tab-pane fade' . (!$pay_balance_s ? '  active in' : '') . '" id="record_tab_points">' . zibpay_get_user_points_record_lists($user_id) . '</div>';
        $tab_content .= '<div class="tab-pane fade" id="record_tab_points_free">' . zib_get_points_free_lists($user_id) . '</div>';
        $tab_content .= '<div class="tab-pane fade" id="tab_points_date">' . zib_get_user_free_points_date_detail_lists($user_id) . '</div>';
    }

    $html = '<div class="row gutters-10">' . $card . '</div><div class="zib-widget"><div class="padding-w10 nop-sm"><ul class="list-inline scroll-x mini-scrollbar tab-nav-theme font-bold">' . $tab_but . '</ul><div class="tab-content">' . $tab_content . '</div></div></div>';

    return $html;
}

function zibpay_user_page_tab_content_balance()
{
    return zib_get_ajax_ajaxpager_one_centent(zibpay_user_content_balance());
}
add_filter('main_user_tab_content_balance', 'zibpay_user_page_tab_content_balance');
