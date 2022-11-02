<?php
/*
 * @Author: Qinver
 * @Url: zibll.com
 * @Date: 2021-04-12 00:20:44
 * @LastEditTime: 2022-07-18 13:20:50
 */

header('Content-type:text/html; Charset=utf-8');

ob_start();
require dirname(__FILE__) . '/../../../../../../wp-load.php';
ob_end_clean();

if (empty($_REQUEST["sign"])) {
    echo '非法请求';
    exit();
}

$config = zibpay_get_payconfig('epay');
if (empty($config['apiurl']) || empty($config['partner']) || empty($config['key'])) {
    exit('fail');
}

if (_pz('pay_wechat_sdk_options') != 'epay' && _pz('pay_alipay_sdk_options') != 'epay') {
    //判断是否开启此支付接口
    // exit('fail');
}

require_once get_theme_file_path('/zibpay/sdk/epay/epay.class.php');
$alipayNotify  = new AlipayNotify($config);
$verify_result = $alipayNotify->verifyNotify();

//file_put_contents(__DIR__ . '/notify_result.txt', '//verify_result:' . $verify_result . PHP_EOL . '$_POST:' . json_encode($_REQUEST));

if ($verify_result) { //验证成功
    //本地订单处理
    $type = str_replace("zibpay_", "", $result['attach']);
    $pay  = array(
        'order_num' => $_GET['out_trade_no'],
        'pay_type'  => 'epay_' . $_GET['type'],
        'pay_price' => $_GET['money'],
        'pay_num'   => $_GET['trade_no'],
        'other'     => '',
    );
    // 更新订单状态
    $order = ZibPay::payment_order($pay);
    /**返回不在发送异步通知 */
    echo 'success';
}

exit();
