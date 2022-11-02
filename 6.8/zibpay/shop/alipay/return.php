<?php

/**
 * 支付宝同步回调
 */

header('Content-type:text/html; Charset=utf-8');

ob_start();
require dirname(__FILE__) . '/../../../../../../wp-load.php';
ob_end_clean();

$user_id = get_current_user_id();
if ($user_id) {
    wp_safe_redirect(zib_get_user_center_url('order'));
    return;
}

wp_safe_redirect(home_url());
