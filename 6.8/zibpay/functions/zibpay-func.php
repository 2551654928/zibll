<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2022-03-30 12:52:47
 * @LastEditTime: 2022-06-25 21:54:03
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

/**
 * @description: 获取订单的有效金额，参与分成的
 * @param {*} $order
 * @return {*}
 */
function zibpay_get_order_effective_amount($order)
{
    $order      = (array) $order;
    $pay_detail = maybe_unserialize($order['pay_detail']);
    if ($pay_detail && is_array($pay_detail)) {
        $price  = 0;
        $method = array('wechat', 'alipay', 'balance', 'card_pass'); //哪些支付方式是有效的
        foreach ($method as $t) {
            if (!empty($pay_detail[$t])) {
                $price += $pay_detail[$t];
            }
        }
        return $price;
    }

    return $order['pay_price'];
}

/**
 * @description: 获取订单的有效金额，参与分成的
 * @param {*} $order
 * @return {*}
 */
function zibpay_get_order_effective_points($order)
{
    $order      = (array) $order;
    $pay_detail = maybe_unserialize($order['pay_detail']);
    $points     = 0;
    if ($pay_detail && is_array($pay_detail)) {
        if (!empty($pay_detail['points'])) {
            $points = (int) $pay_detail['points'];
        }
    }

    return $points;
}
/**
 * @description: 获取订单的支付金额显示
 * @param {*} $order
 * @return {*}
 */
function zibpay_get_order_pay_price($order)
{
    $order      = (array) $order;
    $pay_detail = maybe_unserialize($order['pay_detail']);
    if ($order['pay_type'] === 'points') {
        $mark  = zibpay_get_points_mark();
        $price = isset($pay_detail['points']) ? $pay_detail['points'] : 0;
    } else {
        $mark  = zibpay_get_pay_mark();
        $price = zibpay_get_order_effective_amount($order);
    }
    return '<span class="pay-mark">' . $mark . '</span>' . $price;
}

/**
 * @description: 获取用户显示的付款明细
 * @param {*} $order
 * @param {*} $class
 * @return {*}
 */
function zibpay_get_order_pay_detail_lists($order, $separator = '<span class="icon-spot"></span>', $class = '')
{
    $methods    = zibpay_get_payment_method_args();
    $order      = (array) $order;
    $pay_detail = maybe_unserialize($order['pay_detail']);
    $lists      = '';
    $i          = 1;
    foreach ($methods as $k => $v) {
        if (!empty($pay_detail[$k])) {
            $lists .= $i !== 1 ? $separator : '';
            $lists .= '<lists class="' . $class . '">' . $v['name'] . '：' . $pay_detail[$k] . '</lists>';
            $i++;
        }
    }
    if (!$lists) {
        $lists = '<lists class="' . $class . '">' . zibpay_get_pay_mark() . floatval($order['pay_price']) . '</lists>';
    }
    return $lists;
}

/**
 * @description: 获取订单支付方式明细的文字数组
 * @param {*} $order
 * @param {*} $suffix
 * @return {*}
 */
function zibpay_get_order_pay_detail_text_args($order, $suffix = '元')
{
    $methods    = zibpay_get_payment_method_args();
    $order      = (array) $order;
    $pay_detail = maybe_unserialize($order['pay_detail']);
    $lists      = array();
    foreach ($methods as $k => $v) {
        if (!empty($pay_detail[$k])) {
            $lists[] = $v['name'] . '：' . $pay_detail[$k] . $suffix;
        }
    }
    if (!$lists) {
        $lists[] = floatval($order['pay_price']) . $suffix;
    }
    return $lists;
}

//支付方式
function zibpay_get_payment_methods($pay_type = 0)
{
    $payment_method_args = zibpay_get_payment_method_args();
    $methods             = array();
    $pay_wechat_sdk      = _pz('pay_wechat_sdk_options');
    $pay_alipay_sdk      = _pz('pay_alipay_sdk_options');
    if ($pay_wechat_sdk && 'null' != $pay_wechat_sdk) {
        $methods['wechat'] = $payment_method_args['wechat'];
    }

    if ($pay_alipay_sdk && 'null' != $pay_alipay_sdk) {
        $methods['alipay'] = $payment_method_args['alipay'];
    }

    if (zibpay_is_allow_balance_pay($pay_type)) {
        $methods['balance'] = $payment_method_args['balance'];
    }

    if (zibpay_is_allow_card_pass_pay($pay_type)) {
        $methods['card_pass'] = $payment_method_args['card_pass'];
    }

    return apply_filters('zibpay_payment_methods', $methods, $pay_type);
}

//支付方式参数数组
function zibpay_get_payment_method_args()
{

    $payment_method_names = array(
        'wechat'    => array(
            'name' => '微信',
            'img'  => '<img src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/zibpay/assets/img/pay-wechat-logo.svg" alt="wechat-logo">',
        ),
        'alipay'    => array(
            'name' => '支付宝',
            'img'  => '<img src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/zibpay/assets/img/pay-alipay-logo.svg" alt="alipay-logo">',
        ),
        'balance'   => array(
            'name' => '余额',
            'img'  => '<img src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/zibpay/assets/img/pay-balance-logo.svg" alt="balance-logo">',
        ),
        'points'    => array(
            'name' => '积分',
            'img'  => '',
        ),
        'card_pass' => array(
            'name' => '卡密',
            'img'  => '<img src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/zibpay/assets/img/pay-card-pass-logo.svg" alt="card-pass-logo">',
        ),
    );

    return $payment_method_names;
}

//获取付费类型的名称
function zibpay_get_pay_type_name($pay_type, $show_icon = false)
{
    $name = array(
        '1' => '付费阅读',
        '2' => '付费资源',
        '3' => '产品购买',
        '4' => '购买会员',
        '5' => '付费图片',
        '6' => '付费视频',
        '7' => '自动售卡',
        '8' => '余额充值',
        '9' => '购买积分',
    );
    $n = isset($name[$pay_type]) ? $name[$pay_type] : '付费内容';
    if ($show_icon) {
        return zibpay_get_pay_type_icon($pay_type, 'mr3') . $n;
    }
    return $n;
}

//获取付费类型的图标
function zibpay_get_pay_type_icon($pay_type, $class = '', $tip = false)
{
    $class = $class ? ' ' . $class : '';
    $icons = array(
        '1' => '<i class="fa fa-book' . $class . '"></i>',
        '2' => '<i class="fa fa-download' . $class . '"></i>',
        '3' => '<i class="fa fa-shopping-cart' . $class . '"></i>',
        '4' => '<i class="fa fa-diamond' . $class . '"></i>',
        '5' => '<i class="fa fa-file-image-o' . $class . '"></i>',
        '6' => '<i class="fa fa-play-circle' . $class . '"></i>',
        '7' => '<i class="fa fa-credit-card' . $class . '"></i>',
        '8' => '<i class="fa fa-jpy' . $class . '"></i>',
        '9' => '<i class="fa fa-rub' . $class . '"></i>',
    );
    if ($tip) {
        return '<span title="' . zibpay_get_pay_type_name($pay_type) . '" data-toggle="tooltip">' . $icons[$pay_type] . '<span>';
    } else {
        return $icons[$pay_type];
    }
}

/**获取默认支付方式 */
function zibpay_get_default_payment()
{
    $payment        = _pz('default_payment', 'wechat');
    $pay_wechat_sdk = _pz('pay_wechat_sdk_options');
    $pay_alipay_sdk = _pz('pay_alipay_sdk_options');
    if ('wechat' == $payment && (!$pay_wechat_sdk || 'null' == $pay_wechat_sdk)) {
        $payment = 'alipay';
    }

    if ('alipay' == $payment && (!$pay_alipay_sdk || 'null' == $pay_alipay_sdk)) {
        $payment = 'wechat';
    }

    return $payment;
}

/**
 * @description: 获取文章的推广优惠金额
 * @param {*} $post_id
 * @return {*}
 */
function zibpay_get_post_rebate_discount($post_id = 0, $user_id = 0)
{
    if (!_pz('pay_rebate_s')) {
        return 0;
    }
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    //文章参数判断
    $pay_mate = get_post_meta($post_id, 'posts_zibpay', true);
    if (empty($pay_mate['pay_type']) || 'no' == $pay_mate['pay_type'] || empty($pay_mate['pay_rebate_discount'])) {
        return 0;
    }

    //当前推荐人返利判断
    $rebate_ratio = zibpay_get_the_referrer_rebate_ratio($pay_mate['pay_type'], $user_id);

    if ($rebate_ratio) {
        return round((float) $pay_mate['pay_rebate_discount'], 2);
    }

    return 0;
}

/**
 * @description: 获取form中使用的支付按钮
 * @param {*} $pay_type
 * @param {*} $pay_price
 * @param {*} $class
 * @param {*} $text
 * @return {*}
 */
function zibpay_get_initiate_pay_input($pay_type, $pay_price = 0, $text = '立即支付')
{
    //准备订单数据
    $user_id             = get_current_user_id();
    $payment_methods     = zibpay_get_payment_methods($pay_type);
    $methods_lists       = '';
    $methods_active_html = '';

    if (!$payment_methods) {
        if (is_super_admin()) {
            return '<a href="' . zib_get_admin_csf_url('商城付费/收款接口') . '" class="but c-red btn-block">请先配置收款方式及收款接口</a>';
        } else {
            return '<span class="badg px12 c-yellow-2">暂时无法支付，请与客服联系</span>';
        }
    }

    $ii = 1;
    foreach ($payment_methods as $method_key => $method_val) {
        if ($ii === 1) {
            $method_default = $method_key;
        }
        $methods_lists .= '<div class="flex jc hh payment-method-radio hollow-radio flex-auto pointer' . ($ii === 1 ? ' active' : '') . '" data-for="payment_method"  data-value="' . $method_key . '" >' . $method_val['img'] . '<div>' . $method_val['name'] . '</div></div>';
        $ii++;
    }

    if ($methods_lists && $ii > 2) {
        $methods_active_html = '<div class="muted-2-color em09 mb6">请选择支付方式</div><div class="flex ac mb10">' . $methods_lists . '</div>';
    }

    //如果存在余额支付，则需显示我的余额
    $user_balance_box = '';
    if (isset($payment_methods['balance'])) {
        $user_balance        = zibpay_get_user_balance($user_id);
        $balance_charge_link = '';
        if ($pay_price && $pay_price > $user_balance) {
            $balance_charge_link = zibpay_get_balance_charge_link('but c-red block mt6', '抱歉！余额不足，请先充值 <i class="ml6 fa fa-angle-right em12"></i>');
        }

        $user_balance_box = '<div class="mb10 muted-box padding-h10" data-controller="payment_method" data-condition="==" data-value="balance"' . ($method_default !== 'balance' ? ' style="display: none;"' : '') . '>
        <div class="flex jsb ac">
            <span class="muted-2-color">' . zib_get_svg('money-color-2', null, 'em12 mr6') . '我的余额</span>
            <div><span class="c-blue-2"><span class="mr3 px12">' . zibpay_get_pay_mark() . '<span class="em14">' . $user_balance . '</span></span></div>
            </div>' . $balance_charge_link . '
        </div>';
    }

    //如果存在卡密支付，则显示卡密内容
    $password_box = '';
    if (isset($payment_methods['card_pass'])) {
        $password_box = '<div class="mb10  padding-h10 padding-w6" data-controller="payment_method" data-condition="==" data-value="card_pass"' . ($method_default !== 'card_pass' ? ' style="display: none;"' : '') . '>
        ' . apply_filters('zibpay_card_pass_payment_desc', '') . '
        <div class="muted-2-color em09 mb6">请输入卡号和密码</div>
            <div class="mb6">
                <input type="input" class="form-control" name="card_pass[card]" placeholder="卡号" value="">
            </div>
            <div class="">
                <input type="input" class="form-control" name="card_pass[password]" placeholder="密码" value="">
            </div>
        </div>';
    }

    //积分抵扣
    $points_deduction = '';
    if (zibpay_is_allow_points_deduction($pay_type)) {
        $points_deduction_rate  = _pz('points_deduction_rate', 30);
        $user_points            = zibpay_get_user_points($user_id);
        $points_deduction_price = round(($user_points / $points_deduction_rate), 2);
        $points_deduction .= $points_deduction_price > 0 ? '<label class="flex jsb ac mb10 muted-box padding-h10"><input class="hide" name="points_deduction" type="checkbox"><div class="flex1 mr20"><div class="muted-color mb6">积分抵扣</div><div style="font-weight: normal;" class="muted-2-color px12 points-deduction-text">使用' . $user_points . '积分抵扣' . $points_deduction_price . '元</div></div><div class="form-switch flex0"></div></label>' : '';
    }

    $html = '<div class="dependency-box">';
    $html .= $points_deduction;
    $html .= $methods_active_html;
    $html .= $user_balance_box . $password_box;
    $html .= '<input type="hidden" name="payment_method" value="' . $method_default . '">';
    $html .= '<button class="mt6 but jb-red initiate-pay btn-block radius">' . $text . '<span class="pay-price-text">' . ($pay_price ? '<span class="px12 ml10">￥</span>' . $pay_price : '') . '</span></button>';
    $html .= '</div>';

    return $html;
}

/**
 * @description: 判断是否允许积分抵扣
 * @param {*}
 * @return {*}
 */
function zibpay_is_allow_points_deduction($pay_type)
{

    //暂未启用
    return false;
    if (!_pz('points_s') || !_pz('points_deduction_s')) {
        return false;
    }
    $pay_type = (int) $pay_type;
    $user_id  = get_current_user_id();
    //禁止
    $prohibit_types = array(8, 9);

    return ($user_id && !in_array($pay_type, $prohibit_types));
}

/**
 * @description: 判断哪些哪些支付方式允许使用余额支付
 * @param {*} $pay_type
 * @return {*}
 */
function zibpay_is_allow_balance_pay($pay_type)
{

    if (!_pz('pay_balance_s')) {
        return false;
    }
    $pay_type = (int) $pay_type;
    $user_id  = get_current_user_id();
    //禁止
    $prohibit_types = array(8);

    return apply_filters('zibpay_is_allow_balance_pay', ($user_id && !in_array($pay_type, $prohibit_types)), $pay_type);
}

function zibpay_is_allow_card_pass_pay($pay_type)
{
    return apply_filters('zibpay_is_allow_card_pass_pay', false, $pay_type);
}

/**
 * @description: 获取货币符号
 * @param {*}
 * @return {*}
 */
function zibpay_get_pay_mark()
{
    //声明静态变量，加速获取
    static $pay_mark = null;
    if (!$pay_mark) {
        $pay_mark = _pz('pay_mark') ?: '￥';
    }

    return $pay_mark;
}

/**获取支付参数函数 */
function zibpay_get_payconfig($type)
{
    $defaults             = array();
    $defaults['xunhupay'] = array(
        'wechat_appid'     => '',
        'wechat_appsecret' => '',
        'alipay_appid'     => '',
        'alipay_appsecret' => '',
    );
    $defaults['official_wechat'] = array(
        'merchantid' => '',
        'appid'      => '',
        'key'        => '',
        'jsapi'      => '',
        'h5'         => '',
        'appsecret'  => '',
    );
    $defaults['official_alipay'] = array(
        'appid'         => '',
        'privatekey'    => '',
        'publickey'     => '',
        'pid'           => '',
        'md5key'        => '',
        'webappid'      => '',
        'webprivatekey' => '',
        'h5'            => '',
    );
    $defaults['codepay'] = array(
        'apiurl' => '',
        'id'     => '',
        'key'    => '',
        'token'  => '',
    );
    $defaults['payjs'] = array(
        'mchid' => '',
        'key'   => '',
    );
    $defaults['xhpay'] = array(
        'mchid'   => '',
        'key'     => '',
        'api_url' => '',
    );
    $defaults['epay'] = array(
        'apiurl'  => '',
        'partner' => '',
        'key'     => '',
        'qrcode'  => true,
    );
    $defaults['vmqphp'] = array(
        'apiurl' => '',
        'key'    => '',
    );
    $defaults_parse = isset($defaults[$type]) ? $defaults[$type] : array();
    $config         = wp_parse_args((array) _pz($type), $defaults_parse);
    return zib_trim($config);
}

/**根据订单号获取链接 */
function zibpay_get_order_num_link($order_num, $class = '')
{
    $href    = '';
    $user_id = get_current_user_id();
    if ($user_id) {
        $href = zib_get_user_center_url('order');
    }
    $a = '<a target="_blank" href="' . $href . '" class="' . $class . '">' . $order_num . '</a>';
    if ($href) {
        return $a;
    } else {
        return '<span class="' . $class . '">' . $order_num . '</span>';
    }
}

/**查看权限转文字 */
function zibpay_get_paid_type_name($paid_type)
{
    $paid_name = array(
        'free'      => '免费内容',
        'paid'      => '已购买',
        'vip1_free' => _pz('pay_user_vip_1_name') . '免费',
        'vip2_free' => _pz('pay_user_vip_2_name') . '免费',
    );

    return $paid_name[$paid_type];
}

/**
 * @description: 判断是否允许查看（已付费）
 * @param {*} $post_id
 * @param {*} $user_id
 * @return {*}
 */
function zibpay_is_paid($post_id, $user_id = 0)
{
    // 准备判断参数
    if (!$post_id) {
        return false;
    }

    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $posts_pay = get_post_meta($post_id, 'posts_zibpay', true);

    if (zibpay_post_is_points_modo($posts_pay)) {
        //积分商品
        if (empty($posts_pay['points_price'])) {
            $pay_order = array('paid_type' => 'free', 'modo' => 'points');
            return $pay_order;
        }
        $vip_level = zib_get_user_vip_level($user_id);
        if ($vip_level && empty($posts_pay['vip_' . $vip_level . '_points'])) {
            $pay_order = array('paid_type' => 'vip' . $vip_level . '_free', 'vip_level' => $vip_level, 'modo' => 'points');
            return $pay_order;
        }

    } else {
        if (empty($posts_pay['pay_price'])) {
            $pay_order = array('paid_type' => 'free');
            return $pay_order;
        }

        $vip_level = zib_get_user_vip_level($user_id);
        if ($vip_level && empty($posts_pay['vip_' . $vip_level . '_price'])) {
            $pay_order = array('paid_type' => 'vip' . $vip_level . '_free', 'vip_level' => $vip_level);
            return $pay_order;
        }
    }

    global $wpdb;

    if ($user_id) {
        // 如果已经登录，根据用户id查找数据库订单
        $pay_order = $wpdb->get_row("SELECT * FROM $wpdb->zibpay_order where user_id=$user_id and post_id=$post_id and status=1");
        if ($pay_order) {
            $pay_order              = (array) $pay_order;
            $pay_order['paid_type'] = 'paid';
            return $pay_order;
        }
    }

    //根据浏览器Cookie查找
    if (isset($_COOKIE['zibpay_' . $post_id])) {
        $pay_order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->zibpay_order} WHERE order_num = %s and post_id=%d and status=1", $_COOKIE['zibpay_' . $post_id], $post_id));

        if ($pay_order) {
            $pay_order              = (array) $pay_order;
            $pay_order['paid_type'] = 'paid';
            return $pay_order;
        }
    }

    return false;
}

/**
 * @description: 判断文章付费功能是不是积分支付
 * @param {*} $post_meta
 * @param {*} $post_id
 * @return {*}
 */
function zibpay_post_is_points_modo($pay_mate = array(), $post_id = 0)
{

    if (!isset($pay_mate['pay_type'])) {
        $pay_mate = get_post_meta($post_id, 'posts_zibpay', true);
    }

    if (!isset($pay_mate['pay_type'])) {
        return false;
    }

    return isset($pay_mate['pay_modo']) && $pay_mate['pay_modo'] === 'points';
}

//获取站点今日的订单统计
function zibpay_get_order_statistics_totime($time_type = 'today')
{
    $error = array(
        'count' => 0,
        'sum'   => 0,
        'ids'   => '',
    );

    if (!$time_type) {
        return $error;
    }

    //静态缓存
    static $this_data = null;
    if (isset($this_data[$time_type])) {
        return $this_data[$time_type];
    }

    global $wpdb;

    switch ($time_type) {
        case 'today':
            $like_time = current_time('Y-m-d');
            break;
        case 'yester':
            $todaytime = current_time('Y-m-d');
            $like_time = date("Y-m-d", strtotime("$todaytime -1 day"));
            break;
        case 'thismonth':
            $like_time = current_time('Y-m');
            break;
        case 'lastmonth': //上个月
            $thismonth_time = current_time('Y-m');
            $like_time      = date('Y-m', strtotime("$thismonth_time -1 month"));
            break;
        case 'thisyear': //今年
            $like_time = current_time('Y');
            break;
        case 'all': //今年
            $like_time = '';
            break;
        default:
            $like_time = current_time('Y-m-d');

    }

    $data = $wpdb->get_row("SELECT COUNT(*) as count,SUM(pay_price) as sum FROM {$wpdb->zibpay_order} WHERE pay_time LIKE '%$like_time%' and status=1 and pay_price > 0");
    $data = (array) $data;
    if (!isset($data['count'])) {
        $this_data[$time_type] = $error;
    } else {
        $this_data[$time_type] = array(
            'count' => $data['count'] ?: 0,
            'sum'   => $data['sum'] ? floatval($data['sum']) : 0,
            'ids'   => '',
        );
    }
    return $this_data[$time_type];
}
