<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-09-22 10:30:38
 * @LastEditTime: 2022-09-27 12:38:09
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//为用户授予徽章
function zib_ajax_user_medal_manually_add()
{
    $medal_name = isset($_REQUEST['name']) ? strip_tags(trim($_REQUEST['name'])) : '';
    $user_id    = isset($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;

    if (!_pz('user_medal_s', true) || !zib_current_user_can('medal_manually_set')) {
        zib_send_json_error('暂无此权限');
    }

    $medal_args = zib_get_single_medal_args($medal_name);
    if (!$medal_args) {
        zib_send_json_error('没有此徽章或参数错误');
    }

    //添加徽章
    zib_add_user_medal($user_id, $medal_name, '');

    zib_send_json_success(array('msg' => '已授予徽章【' . $medal_name . '】', 'hide_modal' => true));
}
add_action('wp_ajax_user_medal_manually_add', 'zib_ajax_user_medal_manually_add');

//收回用户徽章
function zib_ajax_user_medal_manually_remove()
{
    $medal_name = isset($_REQUEST['name']) ? strip_tags(trim($_REQUEST['name'])) : '';
    $user_id    = isset($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;

    if (!_pz('user_medal_s', true) || !zib_current_user_can('medal_manually_set')) {
        zib_send_json_error('暂无此权限');
    }

    //收回徽章
    zib_remove_user_medal($user_id, $medal_name);

    zib_send_json_success(array('msg' => '已收回徽章【' . $medal_name . '】', 'hide_modal' => true));
}
add_action('wp_ajax_user_medal_manually_remove', 'zib_ajax_user_medal_manually_remove');

//为用户授予徽章的模态框
function zib_ajax_user_medal_manually_add_modal()
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $id     = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

    $userdata = get_userdata($id);
    if (empty($userdata->ID)) {
        zib_ajax_notice_modal('danger', '用户不存在或参数传入错误');
    }

    if (!_pz('user_medal_s', true) || !zib_current_user_can('medal_manually_set')) {
        zib_ajax_notice_modal('danger', '您没有此权限');
    }

    $html = zib_get_user_medal_manually_add_modal($id);

    echo $html;
    exit;
}
add_action('wp_ajax_user_medal_manually_add_modal', 'zib_ajax_user_medal_manually_add_modal');

//保存用户佩戴徽章
function zib_ajax_user_medal_wear()
{
    $name = isset($_REQUEST['name']) ? strip_tags(trim($_REQUEST['name'])) : '';

    if (!$name) {
        zib_send_json_error('danger', '参数传入错误');
    }

    $user_id = get_current_user_id();
    if (!$user_id) {
        zib_send_json_error('登录失效，请刷新页面后重试');
    }
    $user_medal = zib_get_user_medal_details($user_id);

    if (!isset($user_medal[$name])) {
        zib_send_json_error('暂未获得此徽章');
    }

    update_user_meta($user_id, 'wear_medal', $name);

    zib_send_json_success(array('msg' => '已佩戴徽章【' . $name . '】', 'hide_modal' => true));
}
add_action('wp_ajax_user_medal_wear', 'zib_ajax_user_medal_wear');

//用户徽章信息的模态框
function zib_ajax_single_medal_info_modal()
{
    $name    = isset($_REQUEST['name']) ? strip_tags(trim($_REQUEST['name'])) : '';
    $user_id = isset($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;

    if (!$name) {
        zib_ajax_notice_modal('danger', '参数传入错误');
    }

    $html = zib_get_single_medal_info_modal($name, $user_id);

    echo $html;
    exit;
}
add_action('wp_ajax_single_medal_info_modal', 'zib_ajax_single_medal_info_modal');
add_action('wp_ajax_nopriv_single_medal_info_modal', 'zib_ajax_single_medal_info_modal');

//用户徽章信息的模态框
function zib_ajax_user_medal_info_modal()
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $id     = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

    $userdata = get_userdata($id);
    if (empty($userdata->ID)) {
        zib_ajax_notice_modal('danger', '用户不存在或参数传入错误');
    }

    $html = zib_get_user_medal_info_modal($id);

    echo $html;
    exit;
}
add_action('wp_ajax_user_medal_info_modal', 'zib_ajax_user_medal_info_modal');
add_action('wp_ajax_nopriv_user_medal_info_modal', 'zib_ajax_user_medal_info_modal');

//认证信息的模态框
function zib_ajax_user_auth_info_modal()
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $id     = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

    $userdata = get_userdata($id);
    if (empty($userdata->ID)) {
        zib_ajax_notice_modal('danger', '用户不存在或参数传入错误');
    }

    $html = zib_get_user_auth_info_modal($id);

    echo $html;
    exit;
}
add_action('wp_ajax_user_auth_info_modal', 'zib_ajax_user_auth_info_modal');
add_action('wp_ajax_nopriv_user_auth_info_modal', 'zib_ajax_user_auth_info_modal');

//申请认证模态框
function zib_ajax_user_auth_apply_modal()
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $id     = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

    if (zib_is_user_auth()) {
        zib_ajax_notice_modal('warning', '您已是认证用户');
    }

    $html = zib_get_user_auth_apply_modal($id);

    echo $html;
    exit;
}
add_action('wp_ajax_user_auth_apply_modal', 'zib_ajax_user_auth_apply_modal');

//提交认证申请
function zib_ajax_user_auth_apply()
{

    $action  = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $user_id = get_current_user_id();
    if (!$user_id) {
        zib_send_json_error('登录失效，请刷新页面后重试');
    }

    //执行安全验证检查，验证不通过自动结束并返回提醒
    zib_ajax_verify_nonce();

    //判断已经有申请
    if (zib_get_user_auth_apply_ing($user_id)) {
        zib_send_json_error('您的申请已提交，请耐心等待');
    }

    $name = isset($_REQUEST['name']) ? strip_tags(trim($_REQUEST['name'])) : '';
    $desc = isset($_REQUEST['desc']) ? strip_tags(trim($_REQUEST['desc'])) : '';
    $msg  = isset($_REQUEST['msg']) ? strip_tags(trim($_REQUEST['msg'])) : ''; //留言

    if (!$name) {
        zib_send_json_error('请输入申请认证的身份名称');
    }

    if (zib_new_strlen($name) < 4) {
        zib_send_json_error('身份名称过短，不能低于4个字符');
    }

    if (zib_new_strlen($name) > 16) {
        zib_send_json_error('身份名称过长，不能超过16个字符');
    }

    $img = array();

    //图片处理
    if (!empty($_FILES['file'])) {
        //开始上传图像
        $img_ids = zib_php_upload('file', 0, false);
        if (!empty($img_ids['error'])) {
            zib_send_json_error($img_ids['msg']);
        }

        if (!is_array($img_ids)) {
            $img_ids = array($img_ids);
        }

        foreach ($img_ids as $imgid) {
            $image_urls = wp_get_attachment_image_src($imgid, 'full');
            if (isset($image_urls[0])) {
                $img[] = $image_urls[0];
            }
        }
    }

    $user_display_name = get_userdata($user_id)->display_name;

    $process_url = zib_get_user_auth_apply_process_url($user_id);

    $msg_con = '';
    $msg_con .= '用户：' . zib_get_user_name_link($user_id) . '，正在申请身份认证' . "<br>";
    $msg_con .= '身份名称：' . $name . "<br>";
    $msg_con .= $desc ? '身份简介：' . $desc . "<br>" : '';
    $msg_con .= $msg ? '申请说明：' . $msg . "<br>" : '';

    $msg_con .= '申请时间：' . current_time("Y-m-d H:i:s") . "<br>";
    $msg_con .= "<br>";
    $msg_con .= '您可以点击下方按钮快速处理此申请' . "<br>";
    $msg_con .= '<a target="_blank" style="margin-top: 20px;" class="but jb-blue padding-lg" href="' . esc_url($process_url) . '">立即处理</a>' . "<br>";

    $msg_args = array(
        'send_user'    => $user_id,
        'receive_user' => 'admin',
        'type'         => 'auth_apply',
        'title'        => '有新的身份认证申请待处理-用户：' . $user_display_name,
        'content'      => $msg_con,
        'meta'         => array(
            'name' => $name,
            'desc' => $desc,
            'msg'  => $msg,
            'img'  => $img,
            'time' => current_time("Y-m-d H:i:s"),
        ),
    );

    //创建消息
    $add_msg = ZibMsg::add($msg_args);
    if (!$add_msg) {
        zib_send_json_error('提交失败，请稍候再试');
    }

    //用户申请身份认证 向管理员发送邮件
    if (_pz('email_auth_apply_to_admin', true)) {
        $blog_name = get_bloginfo('name');
        $title     = '[' . $blog_name . '] ' . $msg_args['title'];
        $message   = $msg_args['content'];
        /**发送邮件 */
        zib_mail_to_admin($title, $message);
    }

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('auth_apply_admin');
    if ($wechat_template_id) {
        /**
        {{first.DATA}}
        认证名称：{{keyword1.DATA}}
        认证时间：{{keyword2.DATA}}
        认证状态：{{keyword3.DATA}}
        {{remark.DATA}}
         */

        $send_data = array(
            'first'    => array(
                'value' => '[' . $blog_name . '] 用户：' . $user_display_name . '正在申请身份认证，请及时处理！',
            ),
            'keyword1' => array(
                'value' => $name,
            ),
            'keyword2' => array(
                'value' => current_time("Y-m-d H:i:s"),
            ),
            'keyword3' => array(
                'value' => '待审核',
            ),
            'remark'   => array(
                'value' => ($msg ? '申请说明：' . $msg . "\n" : '') . '请登录网站后台处理该用户的身份认证申请',
            ),
        );
        $send_url = $process_url;
        //发送消息
        zib_send_wechat_template_msg_to_admin($wechat_template_id, $send_data, $send_url);
    }

    zib_send_json_success(array('msg' => '申请提交成功，请耐心等待', 'hide_modal' => true));
}
add_action('wp_ajax_user_auth_apply', 'zib_ajax_user_auth_apply');

/***************禁封功能 */
function zib_ajax_set_user_ban_modal()
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $id     = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

    $userdata = get_userdata($id);
    if (empty($userdata->ID)) {
        zib_ajax_notice_modal('danger', '用户不存在或参数传入错误');
    }

    //权限判断
    if (!zib_current_user_can('set_user_ban')) {
        zib_ajax_notice_modal('warning', '抱歉，您暂时没有此权限');
    }

    if ('user_ban_log_modal' === $action) {
        echo zib_get_ban_log_modal($id);
    } else {
        echo zib_get_set_user_ban_modal($id);
    }
    exit;
}
add_action('wp_ajax_set_user_ban_modal', 'zib_ajax_set_user_ban_modal');
add_action('wp_ajax_user_ban_log_modal', 'zib_ajax_set_user_ban_modal');

//禁封模态框
function zib_ajax_user_ban_info_modal()
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $id     = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

    if ((!$id || get_current_user_id() != $id) && (!zib_current_user_can('set_user_ban'))) {
        zib_ajax_notice_modal('warning', '抱歉，您暂时没有此权限');
    }

    if ('user_ban_info_modal' === $action) {
        echo zib_get_ban_info_modal($id);
    } else {
        echo zib_get_user_ban_appeal_modal($id);
    }
    exit;
}
add_action('wp_ajax_user_ban_info_modal', 'zib_ajax_user_ban_info_modal');
add_action('wp_ajax_user_ban_appeal_modal', 'zib_ajax_user_ban_info_modal');

//保存禁封数据
function zib_ajax_set_user_ban()
{
    if (!isset($_REQUEST['ban']) || empty($_REQUEST['user_id'])) {
        zib_send_json_error('用户不存在或参数传入错误');
    }

    $ban     = $_REQUEST['ban'];
    $user_id = $_REQUEST['user_id'];
    $desc    = isset($_REQUEST['desc']) ? trim(strip_tags($_REQUEST['desc'])) : '';

    //执行安全验证检查，验证不通过自动结束并返回提醒
    zib_ajax_verify_nonce();

    //执行权限检查
    if (!zib_current_user_can('set_user_ban')) {
        zib_send_json_error('抱歉，您暂时没有此权限');
    }

    //解封
    if (0 == $ban) {
        zib_updata_user_ban($user_id, 0, array('desc' => $desc));
        zib_send_json_success(array('msg' => '已解封此用户', 'hide_modal' => true));
    }

    //原因
    $reason   = isset($_REQUEST['reason']) ? $_REQUEST['reason'] : '';
    $time_day = isset($_REQUEST['time_day']) ? (int) $_REQUEST['time_day'] : 0;
    if (!$reason || 'other' == $reason) {
        $reason = isset($_REQUEST['reason_other']) ? trim(strip_tags($_REQUEST['reason_other'])) : '';
    }
    if (!$reason) {
        zib_send_json_error('请输入禁封原因');
    }

    $current_time = current_time('Y-m-d H:i:s');
    if ($time_day) {
        $time_day = date('Y-m-d H:i:s', strtotime($current_time . ' +' . $time_day . ' day'));
    }

    $no_appeal_desc = isset($_REQUEST['no_appeal_desc']) ? strip_tags(trim($_REQUEST['no_appeal_desc'])) : '';
    $no_appeal      = !empty($_REQUEST['no_appeal']);
    if ($no_appeal && !$no_appeal_desc) {
        zib_send_json_error('请输入禁止用户申诉的说明或原因');
    }

    //整理数据
    $data = array(
        'type'           => $ban,
        'time'           => $current_time,
        'banned_time'    => $time_day,
        'reason'         => $reason, //原因
        'desc'           => $desc, //说明
        'no_appeal'      => $no_appeal, //禁止申诉
        'no_appeal_desc' => $no_appeal_desc, //禁止申诉说明
    );

    zib_updata_user_ban($user_id, $ban, $data);
    $msg_args = array(
        0 => '已解封此用户',
        1 => '已将此用户拉入小黑屋',
        2 => '已禁封此用户',
    );

    zib_send_json_success(array('msg' => $msg_args[$ban], 'hide_modal' => true));
}
add_action('wp_ajax_set_user_ban', 'zib_ajax_set_user_ban');

//提交禁封申诉
function zib_ajax_user_ban_appeal()
{
    $user_id = isset($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;

    //执行安全验证检查，验证不通过自动结束并返回提醒
    zib_ajax_verify_nonce();

    //判断已经有申请
    if (zib_get_user_ban_appeal_ing($user_id)) {
        zib_send_json_error('您的申诉正在处理中，请耐心等待', 'warning');
    }

    //判断用户存在
    $user_data = get_userdata($user_id);
    if (!isset($user_data->display_name)) {
        zib_send_json_error('用户不存在或参数错误');
    }

    $data      = array();
    $data_html = '';
    foreach ($_REQUEST['data'] as $k => $v) {
        $v = esc_sql(trim(strip_tags($v)));
        if (!$v) {
            zib_send_json_error('请输入' . $k);
        }
        $data[$k] = $v;
        $data_html .= $k . '：' . $v . '<br>';
    }

    $process_url       = zib_get_user_ban_appeal_process_url($user_id);
    $user_display_name = $user_data->display_name;
    $is_ban            = zib_get_user_ban_info($user_id);
    $in_type           = $is_ban['type'];

    $msg_con = '用户：' . zib_get_user_name_link($user_id) . '，正在进行帐号禁封申诉<br>';
    $msg_con .= '<div style="border-radius:8px;margin:10px 0;color: #959595;font-size: 13px;">';
    $msg_con .= '禁封状态：' . (2 == $in_type ? '禁封中(禁止登录)' : '小黑屋禁封中') . '<br>';
    $msg_con .= '开始时间：' . $is_ban['time'] . '<br>';
    $msg_con .= '结束时间：' . ($is_ban['banned_time'] ? $is_ban['banned_time'] : '永久') . '<br>';
    $msg_con .= '禁封原因：' . $is_ban['reason'] . '<br>';
    $msg_con .= ($is_ban['desc'] ? '说明：' . $is_ban['desc'] . '<br>' : '');
    $msg_con .= '</div>';
    $msg_con .= $data_html;
    $msg_con .= '提交时间：' . current_time("Y-m-d H:i:s") . '<br><br>';
    $msg_con .= '您可以点击下方按钮快速处理此申请<br>';
    $msg_con .= '<a target="_blank" style="margin-top: 20px;" class="but jb-blue padding-lg" href="' . esc_url($process_url) . '">立即处理</a>';
    $title    = '有新的帐号禁封申诉待处理-用户：' . $user_display_name;
    $msg_args = array(
        'send_user'    => $user_id,
        'receive_user' => 'admin',
        'type'         => 'ban_appeal',
        'title'        => $title,
        'content'      => $msg_con,
        'meta'         => array(
            'data' => $data,
            'time' => current_time("Y-m-d H:i:s"),
        ),
    );

    //创建消息
    $add_msg = ZibMsg::add($msg_args);
    if (!$add_msg) {
        zib_send_json_error('提交失败，请稍候再试');
    }

    //给管理员发送邮件
    if (_pz('email_ban_appeal_to_admin', true)) {
        //发送邮件
        $blog_name  = get_bloginfo('name');
        $mail_title = '[' . $blog_name . '] ' . $title;
        zib_mail_to_admin($mail_title, $msg_con);
    }

    zib_send_json_success(array('msg' => '申诉已提交，请耐心等待', 'hide_modal' => true));
}
add_action('wp_ajax_user_ban_appeal', 'zib_ajax_user_ban_appeal');
add_action('wp_ajax_nopriv_user_ban_appeal', 'zib_ajax_user_ban_appeal');

//举报模态框
function zib_ajax_report_modal()
{
    $user_id = isset($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;
    $url     = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';

    //执行安全验证检查
    if (!isset($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'report_modal')) {
        zib_ajax_notice_modal('danger', '安全验证失败，请刷新页面稍候再试');
    }

    //执行用户检查
    $userdata = get_userdata($user_id);
    if (empty($userdata->ID)) {
        zib_ajax_notice_modal('danger', '用户不存在或参数传入错误');
    }

    //用户状态检查
    if (zib_user_is_ban($user_id)) {
        zib_ajax_notice_modal('success', '用户已禁封，感谢您的反馈');
    }

    //权限判断
    if (!zib_current_user_can('user_report')) {
        zib_ajax_notice_modal('warning', '抱歉，您暂时没有举报权限');
    }

    echo zib_get_report_modal($user_id, $url);
    exit;
}
add_action('wp_ajax_report_modal', 'zib_ajax_report_modal');
add_action('wp_ajax_nopriv_report_modal', 'zib_ajax_report_modal');

//提交举报
function zib_ajax_report_user()
{

    //执行安全验证检查，验证不通过自动结束并返回提醒
    zib_ajax_verify_nonce();

    //执行用户检查
    $user_id   = isset($_REQUEST['user_id']) ? (int) $_REQUEST['user_id'] : 0;
    $user_data = get_userdata($user_id);
    if (empty($user_data->display_name)) {
        zib_send_json_error('用户不存在或参数传入错误');
    }

    //用户状态检查
    if (zib_user_is_ban($user_id)) {
        zib_send_json_error('用户已禁封，感谢您的反馈');
    }

    //判断已经有举报
    if (zib_get_user_report_ing($user_id)) {
        zib_send_json_error('您的举报已提交，感谢您的反馈', 'warning');
    }

    //权限判断
    if (!zib_current_user_can('user_report')) {
        zib_send_json_error('抱歉，您暂时没有举报权限');
    }

    $reason = isset($_REQUEST['reason']) ? $_REQUEST['reason'] : ''; //原因
    if (!$reason || 'other' == $reason) {
        $reason = isset($_REQUEST['reason_other']) ? trim(strip_tags($_REQUEST['reason_other'])) : '';
    }
    if (!$reason) {
        zib_send_json_error('请选择或输入举报原因');
    }

    //图片处理
    $img_ids = array();
    if (!empty($_FILES['file'])) {
        //开始上传图像
        $img_ids = zib_php_upload('file', 0, false);
        if (!empty($img_ids['error'])) {
            zib_send_json_error($img_ids['msg']);
        }

        if (!is_array($img_ids)) {
            $img_ids = array($img_ids);
        }
    }

    //开始发送消息
    $desc              = isset($_REQUEST['desc']) ? trim(strip_tags($_REQUEST['desc'])) : '';
    $url               = isset($_REQUEST['url']) ? trim(strip_tags($_REQUEST['url'])) : ''; //链接
    $current_user_id   = get_current_user_id();
    $process_url       = zib_get_user_report_process_url($user_id);
    $user_display_name = $user_data->display_name;
    $title             = '收到新的不良信息举报-被举报用户：' . $user_display_name;
    $current_time      = current_time("Y-m-d H:i:s");

    $msg_con = '收到新的不良信息举报<br>';
    $msg_con .= '被举报用户：' . zib_get_user_name_link($user_id) . '<br>';
    $msg_con .= '举报原因：' . $reason . '<br>';
    $msg_con .= ($url ? '违规链接：<a target="_blank" href="' . esc_url($url) . '">' . esc_url($url) . '</a><br>' : '');
    $msg_con .= ($desc ? '举报详情：' . $desc . '<br>' : '');
    $msg_con .= '提交时间：' . $current_time . '<br><br>';
    $msg_con .= '您可以点击下方按钮快速处理此申请<br>';
    $msg_con .= '<a target="_blank" style="margin-top: 20px;" class="but jb-blue padding-lg" href="' . esc_url($process_url) . '">立即处理</a>';
    $msg_args = array(
        'send_user'    => ($current_user_id ? $current_user_id : 'admin'),
        'receive_user' => 'admin',
        'type'         => 'user_report',
        'title'        => $title,
        'content'      => $msg_con,
        'meta'         => array(
            'report_user'    => 'report_user_id_' . $user_id,
            'report_user_id' => $user_id,
            'desc'           => $desc,
            'reason'         => $reason,
            'url'            => $url,
            'img'            => $img_ids,
            'time'           => $current_time,
        ),
    );

    //创建消息
    $add_msg = ZibMsg::add($msg_args);
    if (!$add_msg) {
        zib_send_json_error('提交失败，请稍候再试');
    }

    //给管理员发送邮件
    if (_pz('email_report_to_admin', true)) {
        //发送邮件
        $blog_name  = get_bloginfo('name');
        $mail_title = '[' . $blog_name . '] ' . $title;
        zib_mail_to_admin($mail_title, $msg_con);
    }

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('report_user_admin');
    if ($wechat_template_id) {
        /**
        {{first.DATA}}
        举报事项：{{keyword1.DATA}}
        举报时间：{{keyword2.DATA}}
        {{remark.DATA}}
         */

        $send_data = array(
            'first'    => array(
                'value' => '[' . get_bloginfo('name') . '] ' . $title . '，请及时处理',
            ),
            'keyword1' => array(
                'value' => $reason,
            ),
            'keyword2' => array(
                'value' => $current_time,
            ),
            'remark'   => array(
                'value' => ($desc ? '举报详情：' . $desc . "\n" : '') . '请登录网站后台进行处理',
            ),
        );
        $send_url = $process_url;
        //发送消息
        zib_send_wechat_template_msg_to_admin($wechat_template_id, $send_data, $send_url);
    }

    zib_send_json_success(array('msg' => '您的举报已提交，我们会尽快处理，感谢您的反馈', 'hide_modal' => true));
}
add_action('wp_ajax_report_user', 'zib_ajax_report_user');
add_action('wp_ajax_nopriv_report_user', 'zib_ajax_report_user');

//签到模态框
function zib_ajax_checkin_details_modal()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        zib_ajax_notice_modal('danger', '请先登录');
    }
    if (!_pz('checkin_s')) {
        zib_ajax_notice_modal('danger', '此功能已关闭');
    }

    $html = zib_get_user_checkin_details_modal($user_id);

    echo $html;
    exit;

}
add_action('wp_ajax_checkin_details_modal', 'zib_ajax_checkin_details_modal');

//发起签到
function zib_ajax_user_checkin()
{
    $user_id = get_current_user_id();
    if (!$user_id) {
        zib_send_json_error('请先登录');
    }
    if (!_pz('checkin_s')) {
        zib_send_json_error('此功能已关闭');
    }
    //小黑屋禁封判断
    if (_pz('user_ban_s', true) && zib_user_is_ban($user_id)) {
        zib_send_json_error('您已被拉入小黑屋，暂时无法签到');
    }

    //函数节流
    zib_ajax_debounce('user_checkin', $user_id);

    //今日已经签到
    if (zib_user_is_checkined($user_id)) {
        zib_send_json_error('今日已签到', 'info');
    }

    $reward    = zib_get_user_checkin_should_reward($user_id); //先获取本次加分值
    $checkined = zib_user_checkin($user_id);

    $msg = $reward['continuous_day'] > 1 ? '连续' . $reward['continuous_day'] . '天签到成功！' : '签到成功！';
    $msg .= $checkined['points'] ? ' 积分+' . $checkined['points'] : '';
    $msg .= $checkined['integral'] ? ' 经验值+' . $checkined['integral'] : '';

    zib_send_json_success(array('msg' => $msg, 'data' => $checkined, 'continuous_day' => $reward['continuous_day'], 'details_link' => zib_get_user_checkin_details_link()));

}
add_action('wp_ajax_user_checkin', 'zib_ajax_user_checkin');
