<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:36
 * @LastEditTime: 2022-10-09 23:17:32
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//后台设置微信公众号的自定义菜单
function zib_weixin_gzh_create_menu()
{
    $json = $_REQUEST['json'];
    if (!$json) {
        zib_send_json_error('输入自定义菜单的json配置代码');
    }

    $data = json_decode(wp_unslash(trim($json)), true);

    if (!$data || !is_array($data)) {
        zib_send_json_error('json格式错误');
    }

    $wxConfig = get_oauth_config('weixingzh');
    require_once get_theme_file_path('/oauth/sdk/weixingzh.php');
    if (!$wxConfig['appid'] || !$wxConfig['appkey']) {
        zib_send_json_error('微信公众号配置错误，请检查AppID或AppSecret');
    }

    try {
        $wxOAuth    = new \Weixin\GZH\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
        $CreateMenu = $wxOAuth->CreateMenu($data);

        if (isset($CreateMenu['errcode'])) {
            if (0 == $CreateMenu['errcode']) {
                zib_send_json_success('设置成功，5-10分钟后生效，请耐心等待');
            } else {
                zib_send_json_error('设置失败，请对照一下错误检查<br>错误码：' . $CreateMenu['errcode'] . '<br>错误消息：' . $CreateMenu['errmsg']);
            }
        }

    } catch (\Exception $e) {
        zib_send_json_error($e->getMessage());
    }

}
add_action('wp_ajax_weixin_gzh_menu', 'zib_weixin_gzh_create_menu');

//后台配置ajax提交内容审核测试
function zib_audit_test()
{

    $action     = $_REQUEST['action'];
    $option_key = 'audit_baidu_access_token';

    //刷新数据库保存的access_token
    update_option($option_key, false);

    switch ($action) {
        case 'text_audit_test':
            if (empty($_POST['content'])) {
                echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请输入需要测试的内容')));
                exit();
            }
            $rel = ZibAudit::text($_POST['content']);
            break;
        case 'img_audit_test':

            $test_img = get_theme_file_path('/inc/csf-framework/assets/images/audit_test.jpg');

            $rel = ZibAudit::image($test_img);
            break;
    }

    if (!empty($rel['error'])) {
        echo (json_encode(array('error' => $rel['error'], 'ys' => 'danger', 'msg' => $rel['msg'])));
        exit();
    }

    if (!empty($rel['conclusion'])) {
        $msg = '审核结果：' . $rel['conclusion'] . '<br/>结果代码：' . $rel['conclusion_type'] . '<br/>消息：' . $rel['msg'];
        echo (json_encode(array('error' => 0, 'msg' => $msg, 'data' => $rel['data'])));
        exit();
    }

    echo (json_encode(array('error' => 0, 'msg' => $rel)));
    exit();
}
add_action('wp_ajax_text_audit_test', 'zib_audit_test');
add_action('wp_ajax_img_audit_test', 'zib_audit_test');

/**
 * @description: 后台AJAX发送测试邮件
 * @param {*}
 * @return {*}
 */
function zib_test_send_mail()
{
    if (empty($_POST['email'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请输入邮箱帐号')));
        exit();
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo (json_encode(array('error' => 1, 'msg' => '邮箱格式错误')));
        exit();
    }
    $blog_name = get_bloginfo('name');
    $blog_url  = get_bloginfo('url');
    $title     = '[' . $blog_name . '] 测试邮件';

    $message = '您好！ <br />';
    $message .= '这是一封来自' . $blog_name . '[' . $blog_url . ']的测试邮件<br />';
    $message .= '该邮件由网站后台发出，如果非您本人操作，请忽略此邮件 <br />';
    $message .= current_time("Y-m-d H:i:s");

    try {
        $test = wp_mail($_POST['email'], $title, $message);
    } catch (\Exception $e) {
        echo array('error' => 1, 'msg' => $e->getMessage());
        exit();
    }
    if ($test) {
        echo (json_encode(array('error' => 0, 'msg' => '后台已操作')));
    } else {
        echo (json_encode(array('error' => 1, 'msg' => '发送失败')));
    }
    exit();
}
add_action('wp_ajax_test_send_mail', 'zib_test_send_mail');

//后台下载老数据
function zib_export_old_options()
{

    $nonce = (!empty($_GET['nonce'])) ? sanitize_text_field(wp_unslash($_GET['nonce'])) : '';

    if (!wp_verify_nonce($nonce, 'export_nonce')) {
        die(esc_html__('安全效验失败！', 'csf'));
    }
    // Export
    header('Content-Type: application/json');
    header('Content-disposition: attachment; filename=zibll-old-options-' . date('Y-m-d') . '.json');
    header('Content-Transfer-Encoding: binary');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo json_encode(get_option('Zibll'));
    die();
}
add_action('wp_ajax_export_old_options', 'zib_export_old_options');

function zib_test_send_sms()
{
    if (empty($_POST['phone_number'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请输入手机号码')));
        exit();
    }

    echo json_encode(ZibSMS::send($_POST['phone_number'], '888888'));
    exit();
}
add_action('wp_ajax_test_send_sms', 'zib_test_send_sms');

//重置用户徽章数据
function zib_ajax_reset_user_medal()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }

    global $wpdb;

    $sql   = "DELETE FROM `$wpdb->usermeta` WHERE `meta_key` = 'medal_details'";
    $query = $wpdb->query($sql);

    //刷新所有缓存
    wp_cache_flush();

    echo (json_encode(array('error' => 0, 'query' => $query, 'msg' => '已重置全部用户的徽章数据')));
    exit();
}
add_action('wp_ajax_reset_user_medal', 'zib_ajax_reset_user_medal');

//导入主题设置
function zib_ajax_options_import()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }

    $data = !empty($_REQUEST['import_data']) ? $_REQUEST['import_data'] : '';

    if (!$data) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请粘贴需导入配置的json代码')));
        exit();
    }

    $import_data = json_decode(wp_unslash(trim($data)), true);

    if (empty($import_data) || !is_array($import_data)) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => 'json代码格式错误，无法导入')));
        exit();
    }

    zib_options_backup('导入配置 自动备份');

    $prefix = 'zibll_options';
    update_option($prefix, $import_data);
    echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '主题设置已导入，请刷新页面')));
    exit();
}
add_action('wp_ajax_options_import', 'zib_ajax_options_import');

//备份主题设置
function zib_ajax_options_backup()
{
    $type   = !empty($_REQUEST['type']) ? $_REQUEST['type'] : '手动备份';
    $backup = zib_options_backup($type);
    echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '当前配置已经备份')));
    exit();
}
add_action('wp_ajax_options_backup', 'zib_ajax_options_backup');

function zib_ajax_options_backup_delete()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }
    if (empty($_REQUEST['key'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '参数传入错误')));
        exit();
    }

    $prefix = 'zibll_options';
    if ('options_backup_delete_all' == $_REQUEST['action']) {
        update_option($prefix . '_backup', false);
        echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '已删除全部备份数据')));
        exit();
    }

    $options_backup = get_option($prefix . '_backup');

    if ('options_backup_delete_surplus' == $_REQUEST['action']) {
        if ($options_backup) {
            $options_backup = array_reverse($options_backup);
            update_option($prefix . '_backup', array_reverse(array_slice($options_backup, 0, 3)));
            echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '已删除多余备份数据，仅保留最新3份')));
            exit();
        }
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '暂无可删除的数据')));
    }

    if (isset($options_backup[$_REQUEST['key']])) {
        unset($options_backup[$_REQUEST['key']]);

        update_option($prefix . '_backup', $options_backup);
        echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '所选备份已删除')));
    } else {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '此备份已删除')));
    }
    exit();
}
add_action('wp_ajax_options_backup_delete', 'zib_ajax_options_backup_delete');
add_action('wp_ajax_options_backup_delete_all', 'zib_ajax_options_backup_delete');
add_action('wp_ajax_options_backup_delete_surplus', 'zib_ajax_options_backup_delete');

function zib_ajax_options_backup_restore()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }
    if (empty($_REQUEST['key'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '参数传入错误')));
        exit();
    }

    $prefix         = 'zibll_options';
    $options_backup = get_option($prefix . '_backup');
    if (isset($options_backup[$_REQUEST['key']]['data'])) {
        update_option($prefix, $options_backup[$_REQUEST['key']]['data']);
        echo (json_encode(array('error' => 0, 'reload' => 1, 'msg' => '主题设置已恢复到所选备份[' . $_REQUEST['key'] . ']')));
    } else {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '备份恢复失败，未找到对应数据')));
    }
    exit();
}
add_action('wp_ajax_options_backup_restore', 'zib_ajax_options_backup_restore');

function zib_ajax_test_wechat_template_test()
{
    if (!is_super_admin()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '操作权限不足')));
        exit();
    }
    if (empty($_REQUEST['type'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请选择需要测试的模板')));
        exit();
    }

    if (!_pz('wechat_template_msg_s')) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '微信公众号模板消息暂未开启，请开启并保存主题设置后，再次进行测试')));
        exit();
    }

    $_pz = _pz('wechat_template_ids');
    if (empty($_pz[$_REQUEST['type'] . '_s']) || empty($_pz[$_REQUEST['type']])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '未开启该类型消息或未设置该类型消息的模板ID')));
        exit();
    }

    $wxConfig = get_oauth_config('weixingzh');
    if (!$wxConfig['appid'] || !$wxConfig['appkey']) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '依赖的微信公众号登录功能未开启，或微信公众号的appid或appkey参数错误')));
        exit();
    }

    $user_id = get_current_user_id();
    $open_id = zib_get_user_wechat_open_id($user_id);
    if (!$open_id) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '您的账号当前暂为通过微信公众号登录功能绑定微信账号')));
        exit();
    }

    $template_id  = $_pz[$_REQUEST['type']];
    $current_time = current_time('Y-m-d H:i:s');

    switch ($_REQUEST['type']) {
        case 'payment_order':
            $data = array(
                'first'    => array(
                    'value' => '订单支付成功',
                ),
                'keyword1' => array(
                    'value' => '付费阅读-测试商品',
                ),
                'keyword2' => array(
                    'value' => '余额：888元，微信：999元',
                ),
                'keyword3' => array(
                    'value' => current_time('Y-m-d H:i:s'),
                ),
                'remark'   => array(
                    'value' => '这是一个订单支付成功后给用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
        case 'payment_order_admin':
            $data = array(
                'first'    => array(
                    'value' => '有新的订单已支付',
                ),
                'keyword1' => array(
                    'value' => '付费阅读-测试商品',
                ),
                'keyword2' => array(
                    'value' => '余额：99元，支付宝：99元',
                ),
                'keyword3' => array(
                    'value' => '张三',
                ),
                'keyword4' => array(
                    'value' => current_time('Y-m-d H:i:s'),
                ),
                'keyword5' => array(
                    'value' => '2022010154541288888888',
                ),
                'remark'   => array(
                    'value' => '这是一个订单支付成功后给管理员发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'payment_order_to_income':
            $data = array(
                'first'    => array(
                    'value' => '您发布的付费内容[XXX]有订单已支付，并获得一笔创作分成',
                ),
                'keyword1' => array(
                    'value' => '创作分成',
                ),
                'keyword2' => array(
                    'value' => '100元',
                ),
                'keyword3' => array(
                    'value' => current_time('Y-m-d H:i:s'),
                ),
                'remark'   => array(
                    'value' => '这是一个用户获得创作分成后给用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'payment_order_to_referrer':
            $data = array(
                'first'    => array(
                    'value' => '恭喜您！获得一笔推广佣金！',
                ),
                'keyword1' => array(
                    'value' => '推荐奖励',
                ),
                'keyword2' => array(
                    'value' => '100元',
                ),
                'keyword3' => array(
                    'value' => current_time('Y-m-d H:i:s'),
                ),
                'remark'   => array(
                    'value' => '这是一个用户获得推广佣金后给用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'apply_withdraw_admin':
            $data = array(
                'first'    => array(
                    'value' => '收到新的提现申请，请及时处理',
                ),
                'keyword1' => array(
                    'value' => '张三',
                ),
                'keyword2' => array(
                    'value' => '800元',
                ),
                'keyword3' => array(
                    'value' => current_time('Y-m-d H:i:s'),
                ),
                'remark'   => array(
                    'value' => '这是一个用户发起提现申请后给管理员发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'withdraw_process':
            $data = array(
                'first'    => array(
                    'value' => '您的提现申请已处理完成',
                ),
                'keyword1' => array(
                    'value' => current_time('Y-m-d H:i:s'),
                ),
                'keyword2' => array(
                    'value' => current_time('Y-m-d H:i:s'),
                ),
                'keyword3' => array(
                    'value' => '已处理完成',
                    "color" => '#1a7dfd',
                ),
                'keyword4' => array(
                    'value' => '888',
                ),
                'keyword5' => array(
                    'value' => '888',
                ),
                'remark'   => array(
                    'value' => '这是一个处理用户提现后给用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'auth_apply_admin':
            $data = array(
                'first'    => array(
                    'value' => '用户张三正在申请身份认证，请及时处理！',
                ),
                'keyword1' => array(
                    'value' => 'xx企业',
                ),
                'keyword2' => array(
                    'value' => current_time("Y-m-d H:i:s"),
                ),
                'keyword3' => array(
                    'value' => '待审核',
                ),
                'remark'   => array(
                    'value' => '这是一个收到申请身份认证后向管理员发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'auth_apply_process':
            $data = array(
                'first'    => array(
                    'value' => '恭喜您成为认证用户',
                ),
                'keyword1' => array(
                    'value' => '已通过',
                    "color" => '#1a7dfd',
                ),
                'keyword2' => array(
                    'value' => 'xx企业',
                ),
                'keyword3' => array(
                    'value' => 'xx企业的法人',
                ),
                'keyword4' => array(
                    'value' => current_time("Y-m-d H:i:s"),
                ),
                'remark'   => array(
                    'value' => '这是一个处理用户认证申请后向用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'report_user_admin':
            $data = array(
                'first'    => array(
                    'value' => '收到新的不良信息举报，请及时处理',
                ),
                'keyword1' => array(
                    'value' => '发送恶意广告',
                ),
                'keyword2' => array(
                    'value' => $current_time,
                ),
                'remark'   => array(
                    'value' => '这是一个收到新的不良信息举报后向管理员发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'report_process':
            $data = array(
                'first'    => array(
                    'value' => '收到新的不良信息举报，请及时处理',
                ),
                'keyword1' => array(
                    'value' => $current_time,
                ),
                'keyword2' => array(
                    'value' => '不良信息举报',
                ),
                'keyword3' => array(
                    'value' => '已对该用户做封号处理',
                ),
                'remark'   => array(
                    'value' => '这是一个处理举报信息后向举报人发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'bind_phone':
            $data = array(
                'first'    => array(
                    'value' => '您好！您的账号已成功绑定手机号',
                ),
                'keyword1' => array(
                    'value' => '张三',
                ),
                'keyword2' => array(
                    'value' => $current_time,
                ),
                'remark'   => array(
                    'value' => '手机号：138xxxx8888' . "\n" . '这是一个绑定手机号或修改手机号向用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'bind_email':
            $data = array(
                'first'    => array(
                    'value' => '您好！您的账号已完成邮箱绑定',
                ),
                'keyword1' => array(
                    'value' => '张三',
                ),
                'keyword2' => array(
                    'value' => $current_time,
                ),
                'remark'   => array(
                    'value' => '绑定邮箱：****zib@qq.com' . "\n" . '这是一个绑定邮箱或修改邮箱向用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'comment_to_postauthor':
            $data = array(
                'first'    => array(
                    'value' => '您发布的[这是一篇测试帖子]，有新的评论',
                ),
                'keyword1' => array(
                    'value' => '张三',
                ),
                'keyword2' => array(
                    'value' => $current_time,
                ),
                'keyword3' => array(
                    'value' => '您好，世界！',
                ),
                'remark'   => array(
                    'value' => '这是一个文章被评论后向作者发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;

        case 'comment_to_parent':
            $data = array(
                'first'    => array(
                    'value' => '您发表的评论有新的回复',
                ),
                'keyword1' => array(
                    'value' => '张三',
                ),
                'keyword2' => array(
                    'value' => $current_time,
                ),
                'keyword3' => array(
                    'value' => '您好，世界！',
                ),
                'remark'   => array(
                    'value' => '这是一个评论有新的回复向用户发送的微信公众号消息示例，由管理员后台操作',
                ),
            );
            break;
    }

    $send = zib_send_wechat_template_msg($user_id, $template_id, $data);

    if (isset($send['errcode'])) {
        if ($send['errcode'] == 0) {
            echo (json_encode(array('error' => 0, 'msg' => '消息发送成功')));
            exit();
        } else {
            echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '消息发送失败，错误码：' . $send['errcode'] . '，错误信息：' . $send['errmsg'] . '')));
            exit();
        }
    }

    echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '消息发送失败，网络链接超时')));
    exit();
}
add_action('wp_ajax_test_wechat_template_test', 'zib_ajax_test_wechat_template_test');
