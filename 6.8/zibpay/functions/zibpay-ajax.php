<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:50
 * @LastEditTime: 2022-11-01 12:44:21
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//微信支付读取session数据
function zibpay_initiate_order_session_data()
{
    @session_start();
    if (!empty($_REQUEST['openid'])) {
        //微信跳转兼容
        if (!empty($_SESSION['ZIBPAY_POST'])) {
            $_POST = array_merge($_SESSION['ZIBPAY_POST'], $_POST);
        } else {
            zib_send_json_error('PHP session 数据获取失败');
        }
    }
}
add_action('wp_ajax_initiate_pay', 'zibpay_initiate_order_session_data', 5);
add_action('wp_ajax_nopriv_initiate_pay', 'zibpay_initiate_order_session_data', 5);

//挂钩输出订单信息
function zibpay_initiate_order_data($post_data = array())
{
    $order_type     = !empty($post_data['order_type']) ? (int) $post_data['order_type'] : 0;
    $payment_method = !empty($post_data['payment_method']) ? $post_data['payment_method'] : '';
    $user_id        = get_current_user_id();

    $__data = array(
        'user_id'    => $user_id,
        'order_type' => $order_type,
        'post_id'    => !empty($post_data['post_id']) ? (int) $post_data['post_id'] : 0,
    );

    switch ($order_type) {
        case 9: //购买积分
            if (!$user_id) {
                zib_send_json_error('请先登录');
            }
            $product_id    = isset($post_data['product']) ? $post_data['product'] : 'custom';
            $custom_points = !empty($post_data['custom']) ? (int) $post_data['custom'] : 0;
            if ($product_id === 'custom') {
                //自定义数额
                $custom_limit = zibpay_get_pay_points_product_custom_limit();
                if (!empty($custom_limit['min']) && $custom_points < $custom_limit['min']) {
                    zib_send_json_error('最低购买' . $custom_limit['min'] . '积分');
                }
                if (!empty($custom_limit['max']) && $custom_points > $custom_limit['max']) {
                    zib_send_json_error('最高可购买' . $custom_limit['max'] . '积分');
                }

                $__data['order_price'] = round(($custom_points / _pz('pay_points_rate')), 2);
            } else {
                $product               = _pz('pay_points_product');
                $__data['order_price'] = $product[$product_id]['pay_price'];
                $__data['product_id']  = 'points_' . $product_id;
            }
            break;

        case 8: //余额充值
            if (!$user_id) {
                zib_send_json_error('请先登录');
            }
            if ($payment_method === 'card_pass') {
                if (_pz('pay_balance_pass_charge_s')) {
                    //卡密支付
                    $password_card     = isset($post_data['card_pass']['card']) ? trim($post_data['card_pass']['card']) : ''; //卡号
                    $password_password = isset($post_data['card_pass']['password']) ? trim($post_data['card_pass']['password']) : ''; //密码

                    if (!$password_card && !$password_password) {
                        zib_send_json_error('请输入卡号及卡密');
                    }

                    //卡密查询
                    $recharge_card = zibpay_get_recharge_card($password_card, $password_password);

                    if (empty($recharge_card->id)) {
                        zib_send_json_error('卡号或密码错误');
                    }

                    if ($recharge_card->status != '0') {
                        zib_send_json_error('该卡密已使用');
                    }

                    $card_price = zibpay_get_recharge_card_price($recharge_card);

                    if (!$card_price) {
                        zib_send_json_error('当前卡密可充值金额为0');
                    }

                    $__data['order_price']       = $card_price;
                    $GLOBALS['zibpay_card_pass'] = $recharge_card; //保存到全局变量
                } else {
                    zib_send_json_error('该功能已关闭');
                }
            } else {
                $balance_product = isset($post_data['balance_product']) ? $post_data['balance_product'] : 'custom';
                $custom_price    = !empty($post_data['custom_price']) ? round((float) $post_data['custom_price'], 2) : 0;
                if ($balance_product === 'custom') {
                    //自定义数额
                    $custom_limit = zibpay_get_pay_balance_product_custom_limit();
                    if (!empty($custom_limit['min']) && $custom_price < $custom_limit['min']) {
                        zib_send_json_error('最低充值' . $custom_limit['min']);
                    }
                    if (!empty($custom_limit['max']) && $custom_price > $custom_limit['max']) {
                        zib_send_json_error('最高充值' . $custom_limit['max']);
                    }
                    $__data['order_price'] = $custom_price;
                } else {
                    $product               = _pz('pay_balance_product');
                    $price                 = round((float) $product[$balance_product]['price'], 2);
                    $pay_price             = round((float) $product[$balance_product]['pay_price'], 2);
                    $__data['order_price'] = $pay_price ?: $price;
                    $__data['product_id']  = 'balance_' . $balance_product;
                }
            }

            break;

        case 4: //会员开通、升级、续费
            if (!$user_id) {
                zib_send_json_error('请先登录');
            }

            $vip_product_id = !empty($post_data['vip_product_id']) ? explode("_", $post_data['vip_product_id']) : '';
            if (empty($vip_product_id[0]) || !isset($vip_product_id[1]) || !isset($vip_product_id[2])) {
                zib_send_json_error('会员数据数据传入错误');
            }
            $vip_action  = $vip_product_id[0];
            $vip_level   = (int) $vip_product_id[1];
            $vip_product = (int) $vip_product_id[2];
            if (!_pz('pay_user_vip_' . $vip_level . '_s', true)) {
                zib_send_json_error('暂未提供此功能');
            }

            if ('renewvip' == $vip_action) {
                //续费
                $vip_product_args      = zibpay_get_vip_renew_product($vip_level);
                $__data['order_price'] = round($vip_product_args[$vip_product]['price'], 2);
                $__data['product_id']  = 'vip_' . $vip_level . '_' . $vip_product . '_renew';
            } elseif ('upgradevip' == $vip_action) {
                //升级
                $vip_product_args      = zibpay_get_vip_upgrade_product($user_id);
                $__data['order_price'] = round($vip_product_args[$vip_product]['price'], 2);
                $__data['product_id']  = 'vip_' . $vip_level . '_' . $vip_product . '_upgrade';
            } else {
                //购买
                $vip_product_args      = (array) _pz('vip_opt', '', 'vip_' . $vip_level . '_product');
                $__data['order_price'] = round($vip_product_args[$vip_product]['price'], 2);
                $__data['product_id']  = 'vip_' . $vip_level . '_' . $vip_product . '_pay';
            }

            break;

        default: //文章类型的
            $post_id = !empty($post_data['post_id']) ? (int) $post_data['post_id'] : 0;

            $post = get_post($post_id);
            if (empty($post->post_author)) {
                zib_send_json_error('商品数据获取错误');
            }

            if (!$post_id) {
                zib_send_json_error('商品数据获取错误');
            }

            if (!$user_id && !_pz('pay_no_logged_in', true)) {
                zib_send_json_error('请先登录');
            }

            $pay_mate              = get_post_meta($post_id, 'posts_zibpay', true);
            $__data['post_author'] = $post->post_author;
            $__data['order_type']  = !empty($pay_mate['pay_type']) ? $pay_mate['pay_type'] : '';
            $__data['product_id']  = !empty($pay_mate['product_id']) ? $pay_mate['product_id'] : '';
            $__data['order_price'] = isset($pay_mate['pay_price']) ? round((float) $pay_mate['pay_price'], 2) : 0;

            if ($user_id) {
                //会员价格
                $vip_level = zib_get_user_vip_level($user_id);
                if ($vip_level && _pz('pay_user_vip_' . $vip_level . '_s', true) && isset($pay_mate['vip_' . $vip_level . '_price'])) {

                    if (!$pay_mate['vip_' . $vip_level . '_price']) {
                        zib_send_json_error('会员免费，请刷新页面', 'info');
                    }
                    $vip_price = round((float) $pay_mate['vip_' . $vip_level . '_price'], 2);
                    //会员金额和正常金额取更小值
                    $__data['order_price'] = $vip_price < $__data['order_price'] ? $vip_price : $__data['order_price'];
                }
            }
    }

    //订单没有金额
    if ($__data['order_price'] <= 0) {
        zib_send_json_error('订单金额异常');
    }

    $pay_detail = array('payment_method' => $payment_method, $payment_method => $__data['order_price']);

    // 推荐返佣、让利功能----充值不返利。积分消费不返利
    $rebate_rule = 0;
    if (_pz('pay_rebate_s')) {
        $get_referrer_id = zibpay_get_referrer_id($user_id);
        if ($get_referrer_id) {
            //查询到推荐人
            $rebate_rule = zibpay_get_referrer_rebate_ratio($get_referrer_id, $order_type); //返利比例
            if ($rebate_rule) {
                //推广优惠
                $__data['referrer_id'] = $get_referrer_id;
                if (!empty($pay_mate['pay_rebate_discount'])) {
                    $pay_detail['rebate_discount'] = round((float) $pay_mate['pay_rebate_discount'], 2);
                    $pay_detail[$payment_method] -= $pay_detail['rebate_discount'];
                    $pay_detail[$payment_method] = $pay_detail[$payment_method] < 0 ? 0 : $pay_detail[$payment_method];
                    //订单最小值
                }
            }
        }
    }

    //积分抵扣 //待处理
    /**
     * 积分抵扣，以及余额组合付款方式涉及到时差问题，可能会导致数据差错
     * 暂无有效方法，故关闭
     */
    if ($user_id && !empty($post_data['points_deduction']) && zibpay_is_allow_points_deduction($order_type)) {
        $points_deduction_rate  = _pz('points_deduction_rate', 30); //抵扣比例
        $user_points            = zibpay_get_user_points($user_id); //我的积分
        $points_deduction_price = round(($user_points / $points_deduction_rate), 2); //我的积分最高可抵扣金额

        if ($points_deduction_price >= $pay_detail[$payment_method]) {
            //足够全额抵扣
            $pay_detail['points_deduction'] = $pay_detail[$payment_method];
            //积分冻结
        } else {
            $pay_detail['points_deduction'] = $points_deduction_price;
        }
        $pay_detail[$payment_method] -= $pay_detail['points_deduction'];
    }

    $__data['pay_detail'] = $pay_detail;

    //保存推广佣金
    if (!empty($__data['referrer_id']) && $rebate_rule) {
        $effective_amount       = zibpay_get_order_effective_amount($__data);
        $__data['rebate_price'] = $effective_amount ? round($effective_amount * $rebate_rule / 100, 2) : 0;
    }

    //保存创作分成
    if (!empty($__data['post_author']) && _pz('pay_income_s')) {
        //现金分成数据保存
        $income_ratio     = zibpay_get_user_income_ratio($__data['post_author']);
        $effective_amount = zibpay_get_order_effective_amount($__data);
        $income_price     = $effective_amount ? round($effective_amount * $income_ratio / 100, 2) : 0;

        $__data['income_price'] = $income_price;
    }

    //数据处理，避免精度问题
    $pay_detail[$payment_method] = sprintf("%.2f", round($pay_detail[$payment_method], 2));
    return $__data;
}

function zibpay_initiate_order()
{
    $order_type     = !empty($_POST['order_type']) ? $_POST['order_type'] : 0;
    $payment_method = !empty($_POST['payment_method']) ? $_POST['payment_method'] : 0;
    if (!$order_type || !$payment_method) {
        zib_send_json_error('请选择支付方式');
    }

    $add_order_data = apply_filters('initiate_order_data', $_POST);
    if (!$add_order_data) {
        zib_send_json_error('数据获取失败');
    }

    //创建新订单
    $order = ZibPay::add_order($add_order_data);
    if (!$order) {
        zib_send_json_error('订单创建失败');
    }

    //设置浏览器缓存
    if (!empty($_POST['post_id']) && !$order['user_id']) {
        $expire = time() + 3600 * 24 * _pz('pay_cookie_day', '15');
        setcookie('zibpay_' . $order['post_id'], $order['order_num'], $expire, '/', '', false);
    }

    //准备支付数据
    $_pay_detail = maybe_unserialize($order['pay_detail']);
    $order_price = isset($_pay_detail[$payment_method]) ? $_pay_detail[$payment_method] : $order['order_price'];

    $order_data = array(
        'user_id'        => $order['user_id'],
        'payment_method' => $payment_method,
        'order_num'      => $order['order_num'],
        'order_price'    => $order_price,
        'ip_address'     => $order['ip_address'],
        'order_name'     => !empty($_POST['order_name']) ? $_POST['order_name'] : zibpay_get_pay_type_name($order_type) . '-' . get_bloginfo('name'),
        'return_url'     => !empty($_POST['return_url']) ? $_POST['return_url'] : '', //回调链接判断
    );

    $initiate_pay = zibpay_initiate_pay($order_data);

    /**添加发起支付成功挂钩 */
    do_action('zibpay_initiate_pay', $initiate_pay);

    /**返回数据 */
    echo (json_encode($initiate_pay));
    exit();

}
add_action('wp_ajax_initiate_pay', 'zibpay_initiate_order');
add_action('wp_ajax_nopriv_initiate_pay', 'zibpay_initiate_order');

// 挂钩AJAX-确认支付订单
function zibpay_check_pay($order_data = array())
{
    header("Content-type:application/json;character=utf-8");

    if (empty($_POST['order_num'])) {
        echo (json_encode(array('error' => 1, 'msg' => '还未生成订单')));
        exit();
    }
    $check_order_num = $_POST['order_num'];
    /**根据订单号查询订单 */
    global $wpdb;
    $order_check = $wpdb->get_row($wpdb->prepare("SELECT id,order_num,status FROM `$wpdb->zibpay_order` WHERE `order_num` = %d", $check_order_num));
    echo (json_encode($order_check));
    exit();
}
add_action('wp_ajax_check_pay', 'zibpay_check_pay');
add_action('wp_ajax_nopriv_check_pay', 'zibpay_check_pay');

/**发起支付函数 */
function zibpay_initiate_pay($order_data)
{
    //初始化默认数据
    $defaults = array(
        'user_id'        => 0,
        'payment_method' => 'wechat',
        'order_num'      => '',
        'order_price'    => 0,
        'ip_address'     => '',
        'order_name'     => get_bloginfo('name') . '支付',
        'return_url'     => home_url(),
    );
    $order_data = wp_parse_args($order_data, $defaults);
    //实例化sdk
    new ZibPaySDK();

    if (empty($order_data['order_num'])) {
        return array('error' => 1, 'msg' => '订单创建失败');
    }

    //价格为0，直接付款
    if (!$order_data['order_price']) {
        $pay = array(
            'order_num' => $order_data['order_num'],
            'pay_type'  => '',
            'pay_price' => 0,
            'pay_num'   => $order_data['order_num'],
            'other'     => '',
        );
        // 更新订单状态
        ZibPay::payment_order($pay);

        return array('error' => 0, 'reload' => true, 'msg' => '支付成功', 'payok' => 1, 'return_url' => $order_data['return_url']);
    }

    /**准备付款接口 */
    $pay_sdk = '';
    switch ($order_data['payment_method']) {
        case 'balance':
        case 'card_pass':
            $pay_sdk = $order_data['payment_method'];
            break;

        case 'wechat':
            $pay_sdk = _pz('pay_wechat_sdk_options');
            break;

        case 'alipay':
            $pay_sdk = _pz('pay_alipay_sdk_options');
            break;
    }

    //支付接口挂钩
    $pay_sdk = apply_filters('zibpay_initiate_paysdk', $pay_sdk, $order_data);

    if (!$pay_sdk || 'null' == $pay_sdk) {
        return array('error' => 1, 'msg' => '当前订单不支持此方式支付，请联系客服');
    }

    //支付结果挂钩
    $payresult = apply_filters('zibpay_initiate_' . $pay_sdk, $order_data);

    $payresult = array_merge($order_data, $payresult);
    return $payresult;
}

//V免签发起支付
function zibpay_zibpay_initiate_vmqphp($order_data)
{
    //获取参数
    $config = zibpay_get_payconfig('vmqphp');
    if (empty($config['apiurl']) || empty($config['key'])) {
        return array('error' => 1, 'msg' => 'V免签接口缺少配置参数');
    }

    require_once get_theme_file_path('/zibpay/sdk/vmq/vmq.class.php');

    //建立请求
    $PaySubmit = new vmqphpPay($config);

    $payment_method = 'alipay' == $order_data['payment_method'] ? 2 : 1;
    $return_url     = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url();
    $param          = $order_data['payment_method'] . '|' . $return_url;

    $parameter = array(
        "payId"     => $order_data['order_num'], //本地订单号
        "type"      => $payment_method,
        "price"     => $order_data['order_price'],
        'notifyUrl' => get_template_directory_uri() . '/zibpay/shop/vmq/notify.php',
        'returnUrl' => get_template_directory_uri() . '/zibpay/shop/vmq/return.php',
        "param"     => $param,
        "isHtml"    => 0,
    );

    if (empty($config['no_open'])) {
        $parameter['isHtml'] = 1;
        $url                 = $PaySubmit->buildURL($parameter);
        return array('url' => $url, 'open_url' => true);
    }

    $get_json = $PaySubmit->get($parameter);

    if (isset($get_json['code']) && 1 == $get_json['code'] && !empty($get_json['data']['payUrl'])) {
        $result['url_qrcode']  = zib_get_qrcode_base64($get_json['data']['payUrl']);
        $reallyPrice           = !empty($get_json['data']['reallyPrice']) ? round($get_json['data']['reallyPrice'], 2) : $order_data['order_price'];
        $result['order_price'] = $reallyPrice;
        $result['more_html']   = '<div class="badg btn-block c-yellow em09 padding-h10">请扫码后支付' . $reallyPrice . '元，为了确保支付成功，请注意付款金额请勿出错</div>';
        return $result;
    }
    $msg = !empty($get_json['msg']) ? $get_json['msg'] : '接口请求错误';
    return array('error' => 1, 'msg' => $msg);
}

//易支付发起支付
function zibpay_initiate_epay($order_data)
{
    //获取参数
    $config = zibpay_get_payconfig('epay');
    if (empty($config['apiurl']) || empty($config['partner']) || empty($config['key'])) {
        return array('error' => 1, 'msg' => '易支付缺少配置参数');
    }
    $config['sign_type']     = strtoupper('MD5');
    $config['input_charset'] = strtolower('utf-8');
    $config['transport']     = (is_ssl()) ? 'https' : 'http';
    $config['apiurl']        = rtrim($config['apiurl'], '/') . '/';

    require_once get_theme_file_path('/zibpay/sdk/epay/epay.class.php');

    $payment_method = 'alipay' == $order_data['payment_method'] ? 'alipay' : 'wxpay';

    $parameter = array(
        "pid"          => trim($config['partner']),
        "type"         => $payment_method,
        'notify_url'   => get_template_directory_uri() . '/zibpay/shop/epay/notify.php',
        'return_url'   => !empty($order_data['return_url']) ? $order_data['return_url'] : home_url(),
        "out_trade_no" => $order_data['order_num'], //本地订单号
        "name"         => $order_data['order_name'],
        "money"        => $order_data['order_price'],
        "sitename"     => get_bloginfo('name'),
    );

    //建立请求
    $alipaySubmit = new AlipaySubmit($config);

    if (wp_is_mobile() || empty($config['qrcode'])) {
        $html_text = $alipaySubmit->buildRequestForm($parameter);
        return array('url_qrcode' => get_template_directory_uri() . '/zibpay/assets/img/pay-qrcode.png', 'more_html' => '<div class="hide">' . $html_text . '</div>');
    } else {
        $get_url  = $alipaySubmit->buildRequestUrl($parameter);
        $http     = new Yurun\Util\HttpRequest;
        $response = $http->timeout(15000)->get($get_url);
        if (empty($response->success)) {
            return array('error' => 1, 'msg' => '易支付接口连接超时');
        }

        $result = $response->json();
        $result = (array) $result;

        if (isset($result['code']) && 1 == $result['code'] && !empty($result['code_url'])) {
            $result['url_qrcode'] = zib_get_qrcode_base64($result['code_url']);
        } else {
            $result['error'] = 1;
            $result['msg']   = !empty($result['msg']) ? $result['msg'] : '收款码请求失败';
        }

        $result['body']    = $response->body();
        $result['get_url'] = $get_url;
        return $result;
    }
}

/**支付宝官方发起支付 */
function zibpay_initiate_official_alipay($order_data = array(), $payment = 'alipay')
{

    //获取参数
    $config = zibpay_get_payconfig('official_alipay');

    // 判断是否开启H5
    if (wp_is_mobile() && $config['h5'] && $config['webappid'] && $config['webprivatekey']) {
        if (empty($config['publickey'])) {
            return array('error' => 1, 'msg' => '缺少支付宝公钥参数');
        }
        /**支付宝企业支付-手机网站支付产品 */
        // 公共配置
        $params        = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
        $params->appID = $config['webappid'];
        /**网站应用-APPID */
        $params->appPrivateKey = $config['webprivatekey'];
        /**网站应用-应用私钥 */

        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);

        // 支付接口
        $request                               = new \Yurun\PaySDK\AlipayApp\Wap\Params\Pay\Request;
        $request->notify_url                   = get_template_directory_uri() . '/zibpay/shop/alipay/notify.php'; // 支付后通知地址（作为支付成功回调，这个可靠）
        $request->return_url                   = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url(); // 支付后跳转返回地址
        $request->businessParams->out_trade_no = $order_data['order_num']; // 商户订单号
        $request->businessParams->total_amount = $order_data['order_price']; // 价格
        $request->businessParams->subject      = $order_data['order_name']; // 商品标题

        $pay->prepareExecute($request, $url, $data);
        if (empty($data['sign'])) {
            return array('error' => 1, 'msg' => 'APPID或应用私钥错误，导致签名失败');
        }

        return array('open_url' => 1, 'url' => $url);
    } elseif ($config['webappid'] && $config['webprivatekey'] && (empty($config['privatekey']) || empty($config['appid']))) {
        /**支付宝企业支付-电脑网站支付 */
        if (empty($config['publickey'])) {
            return array('error' => 1, 'msg' => '缺少支付宝公钥参数');
        }
        // 公共配置
        $params        = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
        $params->appID = $config['webappid'];
        /**网站应用-APPID */
        $params->appPrivateKey = $config['webprivatekey'];
        /**网站应用-应用私钥 */
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);

        // 支付接口
        $request                               = new \Yurun\PaySDK\AlipayApp\Page\Params\Pay\Request;
        $request->notify_url                   = get_template_directory_uri() . '/zibpay/shop/alipay/notify.php'; // 支付后通知地址（作为支付成功回调，这个可靠）
        $request->return_url                   = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url(); // 支付后跳转返回地址
        $request->businessParams->out_trade_no = $order_data['order_num']; // 商户订单号
        $request->businessParams->total_amount = $order_data['order_price']; // 价格
        $request->businessParams->subject      = $order_data['order_name']; // 商品标题

        $pay->prepareExecute($request, $url, $data);
        if (empty($data['sign'])) {
            return array('error' => 1, 'msg' => 'APPID或应用私钥错误，导致签名失败');
        }

        return array('open_url' => 1, 'url' => $url);
    } else {
        /**支付宝当面付 */
        if (empty($config['privatekey']) || empty($config['appid']) || empty($config['publickey'])) {
            return array('error' => 1, 'msg' => '支付宝后台配置无效');
        }

        // 配置文件
        $params                = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
        $params->appID         = $config['appid'];
        $params->appPrivateKey = $config['privatekey'];
        $params->appPublicKey  = $config['publickey'];
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        // 支付接口
        $request                               = new \Yurun\PaySDK\AlipayApp\FTF\Params\QR\Request;
        $request->notify_url                   = get_template_directory_uri() . '/zibpay/shop/alipay/notify.php'; // 支付后通知地址
        $request->businessParams->out_trade_no = $order_data['order_num']; // 商户订单号
        $request->businessParams->total_amount = $order_data['order_price']; // 价格
        $request->businessParams->subject      = $order_data['order_name']; // 商品标题

        // 调用接口
        try {
            $data = $pay->execute($request);
        } catch (Exception $e) {
            return array('error' => 1, 'msg' => $e->getMessage());
            //  var_dump($pay->response->body());
        }

        if (!empty($data['alipay_trade_precreate_response']['qr_code'])) {
            $data['alipay_trade_precreate_response']['url_qrcode'] = zib_get_qrcode_base64($data['alipay_trade_precreate_response']['qr_code']);
            $data['alipay_trade_precreate_response']['msg']        = '处理完成，请扫码支付';
            if (wp_is_mobile()) {
                $data['alipay_trade_precreate_response']['more_html'] = '<a href="' . esc_url($data['alipay_trade_precreate_response']['qr_code']) . '" class="but btn-block c-blue em09 padding-h10">跳转到支付宝APP付款</a>';
            }
            return $data['alipay_trade_precreate_response'];
        } else {
            return array('error' => 1, 'msg' => $pay->getError() . ' ' . $pay->getErrorCode());
        }
    }
}

/**微信官方企业支付发起支付 */
function zibpay_initiate_official_wechat($order_data = array())
{

    //获取参数
    $config = zibpay_get_payconfig('official_wechat');
    if (empty($config['merchantid']) || empty($config['appid']) || empty($config['key'])) {
        return array('error' => 1, 'msg' => '微信支付后台配置无效');
    }

    $params = new \Yurun\PaySDK\Weixin\Params\PublicParams;

    $params->appID  = $config['appid'];
    $params->mch_id = $config['merchantid'];
    $params->key    = $config['key'];
    // SDK实例化，传入公共配置
    $pay = new \Yurun\PaySDK\Weixin\SDK($params);

    //JSAPI判断
    $zibpay_is_wechat_app = zib_is_wechat_app();

    $gzh_appid  = $config['appid'];
    $open_id    = false;
    $return_url = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url();

    // 判断是否开启手机版跳转
    if (wp_is_mobile() && $config['h5'] && !$zibpay_is_wechat_app) {
        // H5支付接口
        $request                   = new \Yurun\PaySDK\Weixin\H5\Params\Pay\Request;
        $request->body             = $order_data['order_name']; // 商品描述
        $request->out_trade_no     = $order_data['order_num']; // 订单号
        $request->total_fee        = round($order_data['order_price'] * 100); // 订单总金额，单位为：分
        $request->spbill_create_ip = !empty($order_data['ip_address']) ? $order_data['ip_address'] : '127.0.0.1'; // 客户端ip，必须传正确的用户ip，否则会报错
        $request->notify_url       = get_template_directory_uri() . '/zibpay/shop/weixin/notify.php'; // 异步通知地址
        $request->scene_info       = new \Yurun\PaySDK\Weixin\H5\Params\SceneInfo;
        //场景信息
        $request->scene_info->type     = 'Wap'; // 可选值：IOS、Android、Wap
        $request->scene_info->wap_url  = home_url(); //h5支付返回地址
        $request->scene_info->wap_name = zib_str_cut(get_bloginfo('name'), 0, 12); //WAP 网站名
        // 调用接口
        $result = $pay->execute($request);
        if ($pay->checkResult()) {
            /**支付订单成功 */
            $result['open_url'] = 1;
            $result['url']      = add_query_arg('redirect_url', urlencode($return_url), $result['mweb_url']);

            return $result;
        } else {
            return array('error' => 1, 'msg' => $pay->getError() . ' ' . $pay->getErrorCode());
        }
    } elseif ($config['jsapi'] && $zibpay_is_wechat_app) {
        //1.从已经微信登录的用户中获取openid
        if ($order_data['user_id']) {
            $open_id = get_user_meta($order_data['user_id'], 'oauth_weixingzh_openid', true);
        }
        //2.从跳转连接中获取openid
        if (!$open_id && !empty($_REQUEST['openid'])) {
            $open_id = $_REQUEST['openid']; //用户微信openid
        }
        //仍然没有openid则使用接口跳转获取
        if (!$open_id) {
            //获取openid
            $redirect_uri = add_query_arg(array(
                'zippay'     => 'wechat',
                'return_url' => $return_url,
                'action'     => 'get_gzh_open_id',
            ), admin_url('admin-ajax.php'));

            $api_url  = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
            $api_data = array(
                'appid'         => $gzh_appid,
                'redirect_uri'  => $redirect_uri,
                'response_type' => 'code',
                'scope'         => 'snsapi_base',
                'state'         => 'zib_pay_wechat',
            );

            $url                     = $api_url . http_build_query($api_data) . '#wechat_redirect';
            $_SESSION['ZIBPAY_POST'] = $_POST;
            return array('open_url' => 1, 'url' => $url);
        }

        //JSAPI模式，在微信APP内调用
        $request                   = new \Yurun\PaySDK\Weixin\JSAPI\Params\Pay\Request;
        $request->body             = $order_data['order_name']; // 商品描述
        $request->out_trade_no     = $order_data['order_num']; // 订单号
        $request->total_fee        = round($order_data['order_price'] * 100); // 订单总金额，单位为：分
        $request->spbill_create_ip = !empty($order_data['ip_address']) ? $order_data['ip_address'] : '127.0.0.1'; // 客户端ip，必须传正确的用户ip，否则会报错
        $request->notify_url       = get_template_directory_uri() . '/zibpay/shop/weixin/notify.php'; // 异步通知地址

        $request->openid = $open_id; // 必须设置openid

        // 调用接口
        $result = $pay->execute($request);
        if ($pay->checkResult()) {
            $request            = new \Yurun\PaySDK\Weixin\JSAPI\Params\JSParams\Request;
            $request->prepay_id = $result['prepay_id'];
            $jsapiParams        = $pay->execute($request);
            // 最后需要将数据传给js，使用WeixinJSBridge进行支付
            $result['jsapiParams'] = $jsapiParams;
            return $result;
        } else {
            return array('error' => 1, 'msg' => $pay->getError() . ' ' . $pay->getErrorCode());
        }
    } else {
        // PC扫码支付接口
        $request                   = new \Yurun\PaySDK\Weixin\Native\Params\Pay\Request;
        $request->body             = $order_data['order_name']; // 商品描述
        $request->out_trade_no     = $order_data['order_num']; // 订单号
        $request->total_fee        = round($order_data['order_price'] * 100); // 订单总金额，单位为：分
        $request->spbill_create_ip = empty($order_data['ip_address']) ? $order_data['ip_address'] : '127.0.0.1'; // 客户端ip，必须传正确的用户ip，否则会报错
        $request->notify_url       = get_template_directory_uri() . '/zibpay/shop/weixin/notify.php'; // 异步通知地址
        // 调用接口
        $result   = $pay->execute($request);
        $shortUrl = $result['code_url'];
        if (is_array($result) && $shortUrl) {
            $result['url_qrcode'] = zib_get_qrcode_base64($shortUrl);
            return $result;
        } else {
            return array('error' => 1, 'msg' => $pay->getError() . ' ' . $pay->getErrorCode());
        }
    }
}

//微信官方支付获取openid
function zib_ajax_get_gzh_open_id()
{
    $return_url = !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : '';
    $code       = !empty($_REQUEST['code']) ? $_REQUEST['code'] : '';

    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';

    $config = zibpay_get_payconfig('official_wechat');
    if (!$config['appsecret']) {
        $wxConfig            = get_oauth_config('weixingzh');
        $config['appid']     = $wxConfig['appid'];
        $config['appsecret'] = $wxConfig['appkey'];
    }

    $url_data = array(
        'appid'      => $config['appid'],
        'secret'     => $config['appsecret'],
        'code'       => $code,
        'grant_type' => 'authorization_code',
    );
    $http     = new Yurun\Util\HttpRequest;
    $response = $http->timeout(10000)->get($url, $url_data);
    $result   = $response->json(true);

    if (!empty($result['openid'])) {
        $return_url = add_query_arg(array('zippay' => 'wechat', 'openid' => $result['openid']), $return_url);
        header('location:' . $return_url);
        exit();
    } else {
        wp_die(
            '<h3>' . __('微信支付错误：') . '</h3>' .
            '<p>' . json_encode($result) . '</p>',
            403
        );
        exit;
    }
}
add_action('wp_ajax_get_gzh_open_id', 'zib_ajax_get_gzh_open_id');
add_action('wp_ajax_nopriv_get_gzh_open_id', 'zib_ajax_get_gzh_open_id');

/**讯虎虎皮椒V3发起支付 */
function zibpay_initiate_xunhupay($order_data = array())
{

    $payment = 'alipay' == $order_data['payment_method'] ? 'alipay' : 'wechat';

    //获取参数
    $config = zibpay_get_payconfig('xunhupay');
    if ('wechat' == $payment && empty($config['wechat_appid']) && empty($config['wechat_appsecret'])) {
        return array('error' => 1, 'msg' => '未设置appid或者appsecret');
    }
    if ('alipay' == $payment && empty($config['alipay_appid']) && empty($config['alipay_appsecret'])) {
        return array('error' => 1, 'msg' => '未设置appid或者appsecret');
    }

    require_once get_theme_file_path('/zibpay/sdk/xunhupay/api.php');

    $trade_order_id = $order_data['order_num'];

    if ('wechat' == $payment) {
        $appid     = $config['wechat_appid'];
        $appsecret = $config['wechat_appsecret'];
        $payment   = 'wechat';
    } else {
        $appid     = $config['alipay_appid'];
        $appsecret = $config['alipay_appsecret'];
        $payment   = 'alipay';
    }
    //支付方式：wechat(微信接口)|alipay(支付宝接口)
    $my_plugin_id = 'zibpay_xunhupay';
    $home_url     = home_url();

    $data = array(
        'version'        => '1.1', //固定值，api 版本，目前暂时是1.1
        'lang'           => 'zh-cn', //必须的，zh-cn或en-us 或其他，根据语言显示页面
        'plugins'        => $my_plugin_id, //必须的，根据自己需要自定义插件ID，唯一的，匹配[a-zA-Z\d\-_]+
        'appid'          => $appid, //必须的，APPID
        'trade_order_id' => $trade_order_id, //必须的，网站订单ID，唯一的，匹配[a-zA-Z\d\-_]+
        'payment'        => $payment, //必须的，支付接口标识：wechat(微信接口)|alipay(支付宝接口)
        'total_fee'      => $order_data['order_price'], //人民币，单位精确到分(测试账户只支持0.1元内付款)
        'title'          => $order_data['order_name'], //必须的，订单标题，长度32或以内
        'time'           => time(), //必须的，当前时间戳，根据此字段判断订单请求是否已超时，防止第三方攻击服务器
        'notify_url'     => get_template_directory_uri() . '/zibpay/shop/xunhupay/notify.php', //必须的，支付成功异步回调接口
        'return_url'     => !empty($order_data['return_url']) ? $order_data['return_url'] : $home_url, //必须的，支付成功后的跳转地址
        'callback_url'   => !empty($order_data['return_url']) ? $order_data['return_url'] : $home_url, //必须的，支付发起地址（未支付或支付失败，系统会会跳到这个地址让用户修改支付信息）
        'modal'          => null, //可空，支付模式 ，可选值( full:返回完整的支付网页; qrcode:返回二维码; 空值:返回支付跳转链接)
        'nonce_str'      => str_shuffle(time()), //必须的，随机字符串，作用：1.避免服务器缓存，2.防止安全密钥被猜测出来
    );
    if ('wechat' == $payment) {
        $data['type']     = "WAP";
        $data['wap_url']  = $home_url;
        $data['wap_name'] = $home_url;
    }

    $hashkey      = $appsecret;
    $data['hash'] = XH_Payment_Api::generate_xh_hash($data, $hashkey);

    $url = 'https://api.xunhupay.com/payment/do.html';
    if (!empty($config['api_url'])) {
        $url = $config['api_url'];
    }

    try {
        $response = XH_Payment_Api::http_post($url, json_encode($data));
        /**
         * 支付回调数据
         * @var array(
         *      order_id,//支付系统订单ID
         *      url//支付跳转地址
         *  )
         */
        $result = $response ? json_decode($response, true) : null;
        if (!$result) {
            throw new Exception('Internal server error', 500);
        }

        $hash = XH_Payment_Api::generate_xh_hash($result, $hashkey);
        if (!isset($result['hash']) || $hash != $result['hash']) {
            throw new Exception('Invalid sign!', 500);
        }

        if (0 != $result['errcode']) {
            throw new Exception($result['errmsg'], $result['errcode']);
        }

        $pay_url = $result['url'];

        $result['open_url'] = wp_is_mobile();
        return $result;
    } catch (Exception $e) {
        //echo "errcode:{$e->getCode()},errmsg:{$e->getMessage()}";
        return array('error' => 1, 'errcode' => $e->getCode(), 'msg' => $e->getMessage());
        //TODO:处理支付调用异常的情况
    }
}

//PAYJS发起支付
function zibpay_initiate_payjs($order_data)
{
    //获取参数
    $config = zibpay_get_payconfig('payjs');
    if (empty($config['mchid']) || empty($config['key'])) {
        return array('error' => 1, 'msg' => '未设置mchid或者key');
    }

    require_once get_theme_file_path('/zibpay/sdk/payjs/payjs.class.php');

    $mchid          = $config['mchid'];
    $key            = $config['key'];
    $payment_method = 'alipay' == $order_data['payment_method'] ? 'alipay' : '';
    $data           = [
        "mchid"        => $mchid, //商户号
        "total_fee"    => round($order_data['order_price'] * 100), //金额。单位：分
        "out_trade_no" => $order_data['order_num'], //本地订单号
        "body"         => $order_data['order_name'], //订单标题
        "notify_url"   => get_template_directory_uri() . '/zibpay/shop/payjs/notify.php', //异步通知的回调地址
        "type"         => $payment_method, //支付宝交易传值：alipay ，微信支付无需此字段
        "attach"       => 'zibpay_payjs', //用户自定义数据，在notify的时候会原样返回
    ];

    $payjs = new Payjs($mchid, $key);

    if (zib_is_wechat_app() && 'wechat' == $order_data['payment_method']) {
        //微信内使用收银台模式
        $data["callback_url"] = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url(); //用户支付成功后，前端跳转地址。
        $data["auto"]         = 1; //auto=1：无需点击支付按钮，自动发起支付。
        $data["logo"]         = _pz('iconpng'); //auto=1：无需点击支付按钮，自动发起支付。
        $url                  = $payjs->cashier($data);
        if (isset($result['status']) && 0 == $result['status']) {
            return array('error' => 1, 'msg' => $result['return_msg']);
        }
        return array('open_url' => 1, 'url' => $url);
    }

    $result = $payjs->native($data);

    if ($result['return_code'] && $result['qrcode']) {
        $result['url_qrcode'] = $result['qrcode'];
    } else {
        $result = array('error' => 1, 'msg' => $result['return_msg']);
    }
    return $result;
}

//讯虎迅虎PAY发起支付（虎皮椒V4）
function zibpay_initiate_xhpay($order_data)
{
    //获取参数
    $config = zibpay_get_payconfig('xhpay');
    if (empty($config['mchid']) || empty($config['key'])) {
        return array('error' => 1, 'msg' => '未设置商户号或者API秘钥');
    }

    $is_mobile    = wp_is_mobile();
    $is_alipay_v2 = !empty($config['alipay_v2']);
    $mchid        = $config['mchid'];
    $key          = $config['key'];

    //引入资源文件
    require_once get_theme_file_path('/zibpay/sdk/xhpay/xhpay.class.php');

    $payment_method = 'alipay' === $order_data['payment_method'] ? 'alipay' : 'wechat';

    $order_data['order_name'] = strtolower($order_data['order_name']); //订单名称转小写，避免出错
    $data                     = [
        "mchid"        => $mchid, //商户号
        "total_fee"    => round($order_data['order_price'] * 100), //金额。单位：分
        "out_trade_no" => $order_data['order_num'], //本地订单号
        "body"         => $order_data['order_name'], //订单标题
        "goods_detail" => $order_data['order_name'], //订单标题
        "notify_url"   => get_template_directory_uri() . '/zibpay/shop/xhpay/notify.php', //异步通知的回调地址
        "type"         => $payment_method, //支付宝交易传值：alipay ，微信支付无需此字段
        "attach"       => 'zibpay_xhpay_' . $payment_method, //用户自定义数据，在notify的时候会原样返回
    ];

    $xhpay = new Xhpay($config);
    if (zib_is_wechat_app() && 'wechat' === $payment_method) {
        //微信内JSAPI支付
        if (empty($_REQUEST['openid'])) {
            //第一步，跳转到获取openid的页面，储存_POST信息
            $return_url              = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url();
            $return_url              = add_query_arg('zippay', 'wechat', $return_url);
            $url                     = 'https://admin.xunhuweb.com/pay/openid?mchid=' . $mchid . '&redirect_url=' . urlencode($return_url);
            $_SESSION['ZIBPAY_POST'] = $_POST;
            return array('open_url' => 1, 'url' => $url);
        } else {
            //第二步，发起JSAPI支付
            $data["openid"]       = $_REQUEST['openid']; //用户微信openid
            $return_url           = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url();
            $data["redirect_url"] = $return_url;

            $result = $xhpay->jsapi($data);
            if (strtolower($result['return_code']) == 'success' && $result['jsapi']) {
                $result['jsapiParams'] = json_decode($result['jsapi']);
                return $result;
            } else {
                return array('error' => 1, 'msg' => $result['return_msg'] . ':' . $result['err_msg']);
            }
        }
    }

    if ($is_mobile && $payment_method === 'wechat') {
        //手机端微信H5支付
        $data["wap_url"]  = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url(); //用户支付成功后，前端跳转地址。
        $data["wap_name"] = get_bloginfo('name'); //网站名称（建议与网站名称一致）。
        $result           = $xhpay->h5($data);
        if ($result['return_code'] == 'SUCCESS' && $result['mweb_url']) {
            return array('open_url' => 1, 'url' => add_query_arg('redirect_url', urlencode($data["wap_url"]), $result['mweb_url']));
        } else {
            return array('error' => 1, 'msg' => $result['return_msg'] . ':' . $result['err_msg']);
        }
    }

    if ($is_mobile && $payment_method === 'alipay') {
        $data["redirect_url"] = !empty($order_data['return_url']) ? $order_data['return_url'] : home_url(); //用户支付成功后，前端跳转地址。

        if (!empty($config['alipay_v2'])) {
            //手机端支付宝2.0WAP新接口
            $result = $xhpay->wap($data);
            if ($result['return_code'] == 'SUCCESS' && $result['mweb_url']) {
                return array('open_url' => 1, 'url' => $result['mweb_url']);
            } else {
                return array('error' => 1, 'msg' => $result['return_msg'] . ':' . $result['err_msg']);
            }
        }

        //收银台模式
        $url = $xhpay->cashier($data);
        if ($url) {
            return array('open_url' => 1, 'url' => $url);
        }
    }

    //支付宝新2.0接口
    if (!empty($config['alipay_v2']) && $payment_method === 'alipay') {
        $data['trade_type'] = "WEB";
    }

    $result = $xhpay->native($data);
    if ('SUCCESS' == $result['return_code'] && $result['code_url']) {
        $result['url_qrcode'] = zib_get_qrcode_base64($result['code_url']);
    } else {
        $result = array('error' => 1, 'msg' => $result['return_msg'] . ':' . $result['err_msg']);
    }
    return $result;
}

/**码支付发起支付 */
function zibpay_initiate_codepay($order_data = array())
{

    $payment = 'alipay' == $order_data['payment_method'] ? 'alipay' : 'wechat';

    //获取参数
    $config = zibpay_get_payconfig('codepay');
    if (empty($config['id']) || empty($config['key']) || empty($config['token'])) {
        return array('error' => 1, 'msg' => '码支付配置错误');
    }

    if ('wechat' == $payment) {
        $type = 3;
    } else {
        $type = 1;
    }

    $codepay_id  = $config['id']; //这里改成码支付ID
    $codepay_key = $config['key']; //这是您的通讯密钥

    $data = array(
        "id"         => $codepay_id, //你的码支付ID
        "token"      => $config['token'], //你的码支付token
        "pay_id"     => $order_data['order_num'], //唯一标识 订单号
        "type"       => $type, //1支付宝支付 3微信支付 2QQ钱包
        "price"      => $order_data['order_price'], //金额
        "param"      => "zibpay", //自定义参数
        "notify_url" => get_template_directory_uri() . '/zibpay/shop/codepay/notify.php', //通知地址
        "return_url" => !empty($order_data['return_url']) ? $order_data['return_url'] : home_url(), //跳转地址
    ); //构造需要传递的参数

    ksort($data); //重新排序$data数组
    reset($data); //内部指针指向数组中的第一个元素

    $sign = ''; //初始化需要签名的字符为空
    $urls = ''; //初始化URL参数为空

    foreach ($data as $key => $val) {
        //遍历需要传递的参数
        if ('' == $val || 'sign' == $key) {
            continue;
        } //跳过这些不参数签名
        if ('' != $sign) {
            //后面追加&拼接URL
            $sign .= "&";
            $urls .= "&";
        }
        $sign .= "$key=$val"; //拼接为url参数形式
        $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
    }
    $query = $urls . '&sign=' . md5($sign . $codepay_key) . '&page=4'; //创建订单所需的参数
    //    $query = $urls.'&page=4'; //创建订单所需的参数
    $api_url = !empty($config['apiurl']) ? $config['apiurl'] : 'https://api.xiuxiu888.com/';
    $url     = rtrim($api_url, '/') . "/creat_order/?{$query}"; //支付页面

    $http     = new Yurun\Util\HttpRequest;
    $response = $http->ua('YurunHttp')->get($url);

    $result     = $response->body();
    $resultData = json_decode($result, true);

    if (isset($resultData['status']) && 0 == $resultData['status']) {
        //返回真实金额
        $money = !empty($resultData['money']) ? round($resultData['money'], 2) : $order_data['order_price'];
        return array('url_qrcode' => $resultData['qrcode'], 'order_price' => $money, 'more_html' => '<div class="badg btn-block c-yellow em09 padding-h10">请扫码后支付' . $money . '元，为了确保支付成功，请注意付款金额请勿出错</div>');
    }

    $msg = !empty($resultData['msg']) ? $resultData['msg'] : '码支付接口请求错误';
    return array('error' => 1, 'msg' => $msg);
}

//余额支付
function zibpay_initiate_balance($order_data = array())
{

    if (empty($order_data['user_id'])) {
        return array('error' => 1, 'msg' => '请先登录');
    }

    //函数节流
    zib_ajax_debounce('balance_initiate_pay', $order_data['user_id']);

    $user_balance = zibpay_get_user_balance($order_data['user_id']);

    if ($user_balance < $order_data['order_price']) {
        return array('error' => 1, 'msg' => '余额不足，请先充值');
    }

    $order_type = !empty($_POST['order_type']) ? $_POST['order_type'] : 0;
    if ($order_type && !zibpay_is_allow_balance_pay($order_type)) {
        return array('error' => 1, 'msg' => '当前交易不支持余额支付');
    }

    //余额变动
    $blog_name = get_bloginfo('name');
    $data      = array(
        'order_num' => $order_data['order_num'], //订单号
        'value'     => -$order_data['order_price'], //值 整数为加，负数为减去
        'type'      => '余额支付',
        'desc'      => str_replace('-' . $blog_name, '', str_replace($blog_name . '-', '', $order_data['order_name'])), //说明
    );
    zibpay_update_user_balance($order_data['user_id'], $data);

    //订单变动
    $pay = array(
        'order_num' => $order_data['order_num'],
        'pay_type'  => 'balance',
        'pay_price' => 0,
        'pay_num'   => $order_data['order_num'],
    );
    // 更新订单状态
    ZibPay::payment_order($pay);
    return array('error' => 0, 'reload' => true, 'msg' => '支付成功', 'payok' => 1, 'return_url' => $order_data['return_url']);
}

//卡密支付
function zibpay_initiate_card_pass($order_data = array())
{
    global $zibpay_card_pass;
    if (!isset($zibpay_card_pass->id)) {
        return array('error' => 1, 'msg' => '异常错误');
    }
    // 更新卡密状态
    zibpay_use_recharge_card($zibpay_card_pass, $order_data);

    // 更新订单状态
    $pay = array(
        'order_num' => $order_data['order_num'],
        'pay_type'  => 'card_pass',
        'pay_price' => 0,
        'pay_num'   => $zibpay_card_pass->card,
    );
    ZibPay::payment_order($pay);

    return array('error' => 0, 'reload' => true, 'msg' => '卡密充值成功，充值金额：' . $order_data['order_price'], 'payok' => 1, 'return_url' => $order_data['return_url']);
}

//积分支付
function zibpay_points_initiate_pay()
{

    $post_id = !empty($_REQUEST['post_id']) ? (int) $_REQUEST['post_id'] : 0;
    $user_id = get_current_user_id();

    if (!$user_id) {
        zib_send_json_error('请先登录');
    }

    //函数节流
    zib_ajax_debounce('points_initiate_pay', $user_id);

    $current_time = current_time("Y-m-d H:i:s");
    $pay_mate     = get_post_meta($post_id, 'posts_zibpay', true);
    $post         = get_post($post_id);

    if (empty($post->ID) || empty($pay_mate['pay_type']) || 'no' == $pay_mate['pay_type'] || !zibpay_post_is_points_modo($pay_mate)) {
        zib_send_json_error('商品数据获取错误');
    }

    $order_type = $pay_mate['pay_type'];
    $price      = (int) $pay_mate['points_price'];
    $vip_level  = zib_get_user_vip_level($user_id);
    if ($vip_level && _pz('pay_user_vip_' . $vip_level . '_s', true)) {
        $vip_price = isset($pay_mate['vip_' . $vip_level . '_points']) ? (int) $pay_mate['vip_' . $vip_level . '_points'] : 0;
        //会员金额和正常金额取更小值
        $price = $vip_price < $price ? $vip_price : $price;
    }

    if ($price <= 0) {
        zib_send_json_error('商品售价错误');
    }

    //我的积分
    $user_points = zibpay_get_user_points($user_id);
    if ($price > $user_points) {
        zib_send_json_error('积分不足，暂时无法支付');
    }

    $post_author    = $post->post_author;
    $add_order_data = array(
        'user_id'     => $user_id,
        'post_id'     => $post_id,
        'post_author' => $post_author,
        'order_price' => 0,
        'order_type'  => $order_type,
        'pay_type'    => 'points',
        'pay_price'   => 0,
        'pay_detail'  => array(
            'points' => $price,
        ),
        'pay_time'    => $current_time,
    );

    //分成数据
    if (_pz('pay_income_s')) {
        $points_ratio  = zibpay_get_user_income_points_ratio($post_author);
        $income_points = (int) (($price * $points_ratio) / 100);
        if ($income_points > 0) {
            $add_order_data['income_detail'] = array(
                'points' => $income_points,
            );
        }
    }

    //创建新订单
    $order = ZibPay::add_order($add_order_data);
    if (!$order) {
        zib_send_json_error('订单创建失败');
    }

    //更新用户积分
    $update_points_data = array(
        'order_num' => $order['order_num'], //订单号
        'value'     => -$price, //值 整数为加，负数为减去
        'type'      => '积分支付', //类型说明
        'desc'      => zibpay_get_pay_type_name($order_type), //说明
        'time'      => current_time('Y-m-d H:i'),
    );

    $update_points = zibpay_update_user_points($user_id, $update_points_data);

    if (!$update_points) {
        zib_send_json_error('数据更新失败');
    }

    $pay = array(
        'order_num' => $order['order_num'],
        'pay_type'  => 'points',
        'pay_price' => 0,
        'pay_num'   => $order['order_num'],
    );

    // 更新订单状态
    ZibPay::payment_order($pay);

    zib_send_json_success(['reload' => true, 'msg' => '购买成功']);
}
add_action('wp_ajax_points_initiate_pay', 'zibpay_points_initiate_pay');
