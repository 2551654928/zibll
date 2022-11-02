<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-11-10 17:48:49
 * @LastEditTime: 2022-09-15 09:13:30
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

/**购买给用户发送邮件、消息 */
add_action('payment_order_success', 'zibpay_mail_payment_order');
function zibpay_mail_payment_order($values)
{

    /**根据订单号查询订单 */
    $pay_order = (array) $values;
    $user_id   = $pay_order['user_id'];

    $udata = get_userdata($user_id);
    if (!$user_id || !$udata) {
        return;
    }

    $user_name = $udata->display_name;

    if ($pay_order['pay_type'] === 'points') {
        $pay_price = zibpay_get_order_pay_points($pay_order) . '积分';
    } else {
        $pay_price = zibpay_get_order_effective_amount($pay_order);
        $pay_price = $pay_price ? '￥' . $pay_price : '';
    }
    $pay_price = $pay_price ? '-金额：' . $pay_price : '';
    $pay_time  = $pay_order['pay_time'];
    $blog_name = get_bloginfo('name');

    $_link      = zib_get_user_center_url('order');
    $order_name = zibpay_get_pay_type_name($pay_order['order_type']);

    $m_title = '订单支付成功' . $pay_price . '，订单号[' . $pay_order['order_num'] . ']';
    $title   = '[' . $blog_name . '] ' . $m_title;

    $message = '您好！ ' . $user_name . "<br>";
    $message .= '您在【' . $blog_name . '】购买的商品已支付成功' . "<br>";
    $post_title = '';
    if ($pay_order['post_id']) {
        $post = get_post($pay_order['post_id']);
        if (isset($post->post_title)) {
            $post_title = zib_str_cut($post->post_title, 0, 20, '...');
        }
    }
    $message .= '类型：' . $order_name . '<br>';
    $message .= $post_title ? '商品：<a target="_blank" href="' . get_permalink($post) . '">' . $post_title . '</a>' . "<br>" : '';
    $message .= '订单号：' . $pay_order['order_num'] . "<br>";
    $message .= '付款明细：' . zibpay_get_order_pay_detail_lists($pay_order) . "<br>";
    $message .= '付款时间：' . $pay_time . "<br>";
    $message .= "<br>";
    $message .= '您可以打开下方链接查看订单详情' . "<br>";
    $message .= '<a target="_blank" style="margin-top: 20px" href="' . esc_url($_link) . '">' . $_link . '</a>' . "<br>";

    $msg_arge = array(
        'send_user'    => 'admin',
        'receive_user' => $user_id,
        'type'         => 'pay',
        'title'        => $m_title,
        'content'      => $message,
        'meta'         => '',
        'other'        => '',
    );

    //创建新消息
    if (_pz('message_s', true)) {
        ZibMsg::add($msg_arge);
    }

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('payment_order');
    if ($wechat_template_id) {
        /**
         * {{first.DATA}}
         * 购买服务：{{keyword1.DATA}}
         * 支付金额：{{keyword2.DATA}}
         * 支付时间：{{keyword3.DATA}}
         * {{remark.DATA}}
         */

        $remark    = '您可以登录网站后在用户中心查看订单详细信息';
        $send_data = array(
            'first'    => array(
                'value' => '[' . $blog_name . '] 订单支付成功！',
            ),
            'keyword1' => array(
                'value' => $order_name . ($post_title ? '-' . $post_title : ''),
            ),
            'keyword2' => array(
                'value' => implode("\n", zibpay_get_order_pay_detail_text_args($pay_order)),
            ),
            'keyword3' => array(
                'value' => $pay_order['pay_time'],
            ),
            'remark'   => array(
                'value' => $remark,
            ),
        );
        $send_url = $_link;
        //发送消息
        zib_send_wechat_template_msg($user_id, $wechat_template_id, $send_data, $send_url);
    }

    if (_pz('email_payment_order', true)) {
        /**获取用户邮箱 */
        $user_email = !empty($udata->user_email) ? $udata->user_email : '';
        /**如果没有 email或者email无效则终止*/
        if (!$user_email || stristr($user_email, '@no')) {
            return false;
        }
        /**发送邮件 */
        @wp_mail($user_email, $title, $message);
    }
}

/**购买给管理员发送邮件、消息 */
add_action('payment_order_success', 'zibpay_mail_payment_order_to_admin');
function zibpay_mail_payment_order_to_admin($values)
{
    /**根据订单号查询订单 */
    $pay_order = (array) $values;
    $user_id   = $pay_order['user_id'];
    $udata     = get_userdata($user_id);
    $user_name = !empty($udata->display_name) ? $udata->display_name : '';

    //积分不发||管理员购买不发
    if (in_array('administrator', $udata->roles) || $pay_order['pay_type'] === 'points') {
        return;
    }

    $pay_price              = '￥' . zibpay_get_order_effective_amount($pay_order);
    $pay_time               = $pay_order['pay_time'];
    $blog_name              = get_bloginfo('name');
    $order_type             = zibpay_get_pay_type_name($pay_order['order_type']);
    $today_data             = zibpay_get_order_statistics_totime('today');
    $thismonth_data         = zibpay_get_order_statistics_totime('thismonth');
    $pay_detail_lists       = implode("，", zibpay_get_order_pay_detail_text_args($pay_order));
    $post_title             = '';
    $wechat_template_remark = '';
    $wechat_template_remark .= '今日收入：￥' . $today_data['sum'] . '(' . $today_data['count'] . '笔)' . "\n";
    $wechat_template_remark .= '本月收入：￥' . $thismonth_data['sum'] . '(' . $thismonth_data['count'] . '笔)' . "\n";

    $m_title = '有新的订单已支付-' . $order_type . '，金额：' . $pay_price . '，订单号[' . $pay_order['order_num'] . ']';
    $title   = '[' . $blog_name . '] ' . $m_title;

    $message = '您的网站【' . $blog_name . '】有新的订单已支付！' . "<br>";
    $message .= '订单号：' . $pay_order['order_num'] . "<br>";
    $message .= '商品类型：' . $order_type . "<br>";
    if ($pay_order['post_id']) {
        $post = get_post($pay_order['post_id']);
        if (isset($post->post_title)) {
            $post_title = zib_str_cut($post->post_title, 0, 20, '...');
            $message .= '商品：<a target="_blank" href="' . get_permalink($post) . '">' . $post_title . '</a>' . "<br>";
        }
    }
    $message .= '付款明细：' . $pay_detail_lists . "<br>";
    if ($user_name) {
        $message .= '购买用户：' . zib_get_user_name_link($user_id) . "<br>";
    } else {
        $message .= '购买用户：未登录购买' . "<br>";
    }
    $message .= '付款时间：' . $pay_time . "<br>";
    $message .= '<br />今日有效收入：' . $today_data['count'] . '笔订单 ￥' . $today_data['sum'] . "<br>";
    $message .= '本月有效收入：' . $thismonth_data['count'] . '笔订单 ￥' . $thismonth_data['sum'] . "（有效收入不含余额、积分支付）<br />";

    if (!empty($pay_order['referrer_id']) && !empty($pay_order['rebate_price']) && $pay_order['rebate_price'] > 0) {
        $all_rebate_price      = zibpay_get_user_rebate_data($pay_order['referrer_id'])['sum'];
        $rebate_effective_data = zibpay_get_user_rebate_data($pay_order['referrer_id'], 'effective')['sum'];

        $message .= '<br />推荐人：' . zib_get_user_name_link($pay_order['referrer_id']) . "<br>";
        $message .= '推荐佣金：￥' . $pay_order['rebate_price'] . "<br>";
        $message .= '该推荐人累计佣金：￥' . $all_rebate_price . "<br>";
        $message .= '该推荐人待提现佣金：￥' . $rebate_effective_data . "<br>";

        $referrer = get_userdata($pay_order['referrer_id']);
        $wechat_template_remark .= '推荐佣金：￥' . $pay_order['rebate_price'] . (isset($referrer->display_name) ? '(' . $referrer->display_name . ')' : '') . "\n";
    }

    if (!empty($pay_order['post_author']) && !empty($pay_order['income_price']) && $pay_order['income_price'] > 0) {
        $income_price_all       = zibpay_get_user_income_data($pay_order['post_author'], 'all')['sum'];
        $income_price_effective = zibpay_get_user_income_data($pay_order['post_author'], 'effective')['sum'];

        $message .= '<br />分成作者：' . zib_get_user_name_link($pay_order['post_author']) . "<br>";
        $message .= '分成金额：￥' . $pay_order['income_price'] . "<br>";
        $message .= '该作者累计分成：￥' . $income_price_all . "<br>";
        $message .= '该作者待提现分成：￥' . $income_price_effective . "<br>";

        $post_author = get_userdata($pay_order['post_author']);
        $wechat_template_remark .= '创作分成：￥' . $pay_order['income_price'] . (isset($post_author->display_name) ? '(' . $post_author->display_name . ')' : '') . "\n";
    }

    $message .= "<br>";
    $message .= '您可以打开下方链接查看订单详情' . "<br>";
    $_link = admin_url('admin.php?page=zibpay_order_page&s=' . $pay_order['order_num']);
    $message .= '<a target="_blank" style="margin-top: 20px" href="' . esc_url($_link) . '">' . $_link . '</a>' . "<br>";

    $msg_arge = array(
        'send_user'    => 'admin',
        'receive_user' => 'admin',
        'type'         => 'pay',
        'title'        => $m_title,
        'content'      => $message,
        'meta'         => '',
        'other'        => '',
    );
    //创建新消息
    if (_pz('message_s', true)) {
        ZibMsg::add($msg_arge);
    }

    /**发送邮件 */
    if (_pz('email_payment_order_to_admin', true)) {
        zib_mail_to_admin($title, $message);
    }

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('payment_order_admin');
    if ($wechat_template_id) {
        /**
         * {{first.DATA}}
         * 商品：{{keyword1.DATA}}
         * 金额：{{keyword2.DATA}}
         * 购买人昵称：{{keyword3.DATA}}
         * 交易时间：{{keyword4.DATA}}
         * 交易流水号：{{keyword5.DATA}}
         * {{remark.DATA}}
         */

        $send_data = array(
            'first'    => array(
                'value' => '[' . get_bloginfo('name') . '] 有新的订单已支付，金额：' . $pay_price,
            ),
            'keyword1' => array(
                'value' => $order_type . ($post_title ? '-' . $post_title : ''),
            ),
            'keyword2' => array(
                'value' => $pay_detail_lists,
            ),
            'keyword3' => array(
                'value' => $user_name ?: '未登录用户',
            ),
            'keyword4' => array(
                'value' => $pay_order['pay_time'],
            ),
            'keyword5' => array(
                'value' => $pay_order['order_num'],
            ),
            'remark'   => array(
                'value' => $wechat_template_remark . '您可以登录网站后台，查看订单详细信息',
            ),
        );
        $send_url = $_link;
        //发送消息
        zib_send_wechat_template_msg_to_admin($wechat_template_id, $send_data, $send_url);
    }

}

/**
 * @description: 订单支付成功给文章作者发送分成明细
 * @param array $values 订单数组
 * @return {*}
 */
add_action('payment_order_success', 'zibpay_mail_payment_order_to_income');
function zibpay_mail_payment_order_to_income($values)
{

    $pay_order   = (array) $values;
    $post_author = $pay_order['post_author'];
    //如果没有推荐人或者返利金额则退出
    if (!$post_author) {
        return;
    }

    if ($pay_order['pay_type'] === 'points') {
        $income_val      = zibpay_get_order_income_points($pay_order);
        $income_val_text = $income_val . '积分';
    } else {
        $income_val      = $pay_order['income_price'];
        $income_val_text = '￥' . $income_val . '元';
    }

    if ($income_val <= 0 || !$income_val) {
        return;
    }

    $udata      = get_userdata($post_author);
    $post       = get_post($pay_order['post_id']);
    $post_title = zib_str_cut($post->post_title, 0, 16, '...');
    $user_name  = $udata->display_name;
    $pay_time   = $pay_order['pay_time'];
    $blog_name  = get_bloginfo('name');

    $title = '恭喜您！获得一笔创作分成：' . $income_val_text;

    $message = '您好！ ' . $user_name . "<br>";
    $message .= '您在【' . $blog_name . '】发布的付费内容有订单已支付，并获得一笔创作分成' . "<br>";
    $message .= '订单号：' . $pay_order['order_num'] . "<br>";
    $message .= '分成金额：' . $income_val_text . "<br>";
    $message .= '分成商品：<a target="_blank" href="' . get_permalink($post) . '">' . $post_title . '</a>' . "<br>";
    $message .= '订单时间：' . $pay_time . "<br>";
    $message .= "<br>";

    if ($pay_order['pay_type'] !== 'points') {
        $income_price_all       = zibpay_get_user_income_data($post_author, 'all')['sum'];
        $income_price_effective = zibpay_get_user_income_data($post_author, 'effective')['sum'];
        $message .= '累计分成：￥ <span style="font-size: 18px;color: #f94a4a;">' . $income_price_all . '</span>' . "<br>";
        $message .= '待提现：￥ <span style="font-size: 18px;color: #2193f7;">' . $income_price_effective . '</span>' . "<br>";
        $message .= "<br>";
    }
    $message .= '您可以打开下方链接查看您的分成详情' . "<br>";
    $_link = zib_get_user_center_url('income');
    $message .= '<a target="_blank" style="margin-top: 20px" href="' . esc_url($_link) . '">' . $_link . '</a>' . "<br>";

    $msg_arge = array(
        'send_user'    => 'admin',
        'receive_user' => $post_author,
        'type'         => 'pay',
        'title'        => $title,
        'content'      => $message,
        'meta'         => '',
        'other'        => '',
    );

    //创建新消息
    if (_pz('message_s', true)) {
        ZibMsg::add($msg_arge);
    }

    //发送邮件
    if (_pz('email_payment_order_to_income', true)) {
        $title      = '[' . $blog_name . '] ' . $title;
        $user_email = $udata->user_email;

        /**判断邮箱状态 */
        if (!is_email($user_email) || stristr($user_email, '@no')) {
            return false;
        }

        /**发送邮件 */
        @wp_mail($user_email, $title, $message);
    }

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('payment_order_to_income');
    if ($wechat_template_id) {
        /**
        {{first.DATA}}
        收入类型：{{keyword1.DATA}}
        收入金额：{{keyword2.DATA}}
        收入时间：{{keyword3.DATA}}
        {{remark.DATA}}
         */

        $send_data = array(
            'first'    => array(
                'value' => '[' . $blog_name . '] 您发布的付费内容[' . $post_title . ']有订单已支付，并获得一笔创作分成',
            ),
            'keyword1' => array(
                'value' => '创作分成',
            ),
            'keyword2' => array(
                'value' => $income_val_text,
            ),
            'keyword3' => array(
                'value' => $pay_order['pay_time'],
            ),
            'remark'   => array(
                'value' => '您可以登录网站后在用户中心查看分成详细信息',
            ),
        );
        $send_url = $_link;
        //发送消息
        zib_send_wechat_template_msg($post_author, $wechat_template_id, $send_data, $send_url);
    }
}

/**
 * @description: 订单支付成功给推荐人发邮件
 * @param array $values 订单数组
 * @return {*}
 */
add_action('payment_order_success', 'zibpay_mail_payment_order_to_referrer');
function zibpay_mail_payment_order_to_referrer($values)
{

    $pay_order    = (array) $values;
    $referrer_id  = $pay_order['referrer_id'];
    $rebate_price = $pay_order['rebate_price'];
    //如果没有推荐人或者返利金额则退出
    if (!$referrer_id || $rebate_price < 0.1) {
        return false;
    }

    $all_rebate_price      = zibpay_get_user_rebate_data($referrer_id)['sum'];
    $rebate_effective_data = zibpay_get_user_rebate_data($referrer_id, 'effective')['sum'];

    $udata = get_userdata($referrer_id);
    if (!$udata) {
        return;
    }

    $user_name = $udata->display_name;
    $pay_time  = $pay_order['pay_time'];
    $blog_name = get_bloginfo('name');

    $m_title = '恭喜您！获得一笔推广佣金：￥' . $rebate_price . '元';
    $title   = '[' . $blog_name . '] ' . $m_title;

    $message = '您好！ ' . $user_name . "<br>";

    $message .= '恭喜您！在【' . $blog_name . '】获得一笔推荐佣金' . "<br>";
    $message .= '订单号：' . $pay_order['order_num'] . "<br>";
    $message .= '佣金金额：￥' . $rebate_price . "<br>";
    $message .= '时间：' . $pay_time . "<br>";
    $message .= "<br>";
    $message .= '累计佣金：￥ <span style="font-size: 18px;color: #f94a4a">' . $all_rebate_price . '</span>' . "<br>";
    $message .= '待提现佣金：￥ <span style="font-size: 18px;color: #2193f7;">' . $rebate_effective_data . '</span>' . "<br>";
    $message .= "<br>";
    $message .= '您可以打开下方链接查看佣金详情' . "<br>";
    $_link = zib_get_user_center_url('rebate');
    $message .= '<a target="_blank" style="margin-top: 20px" href="' . esc_url($_link) . '">' . $_link . '</a>' . "<br>";

    $msg_arge = array(
        'send_user'    => 'admin',
        'receive_user' => $referrer_id,
        'type'         => 'pay',
        'title'        => $m_title,
        'content'      => $message,
        'meta'         => '',
        'other'        => '',
    );
    //创建新消息
    if (_pz('message_s', true)) {
        ZibMsg::add($msg_arge);
    }
    if (_pz('email_payment_order_to_referrer', true)) {
        $user_email = $udata->user_email;

        /**判断邮箱状态 */
        if (!is_email($user_email) || stristr($user_email, '@no')) {
            return false;
        }

        /**发送邮件 */
        @wp_mail($user_email, $title, $message);
    }

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('payment_order_to_referrer');
    if ($wechat_template_id) {
        /**
        {{first.DATA}}
        收入类型：{{keyword1.DATA}}
        收入金额：{{keyword2.DATA}}
        收入时间：{{keyword3.DATA}}
        {{remark.DATA}}
         */

        $send_data = array(
            'first'    => array(
                'value' => '[' . $blog_name . '] 恭喜您！获得一笔推广佣金！',
            ),
            'keyword1' => array(
                'value' => '推荐奖励',
            ),
            'keyword2' => array(
                'value' => $rebate_price . '元',
            ),
            'keyword3' => array(
                'value' => $pay_order['pay_time'],
            ),
            'remark'   => array(
                'value' => '您当前累计佣金' . $all_rebate_price . '元，待提现' . $rebate_effective_data . '元' . "\n" . '您可以登录网站后在用户中心查看佣金详情',
            ),
        );
        $send_url = $_link;
        //发送消息
        zib_send_wechat_template_msg($referrer_id, $wechat_template_id, $send_data, $send_url);
    }
}

/**
 * @description: 用户申请佣金提现给管理员发送邮件
 * @param array $values 订单数组
 * @return {*}
 */
add_action('user_apply_withdraw', 'zibpay_msg_apply_withdraw_to_admin');
function zibpay_msg_apply_withdraw_to_admin($msg_args)
{
    $blog_name = get_bloginfo('name');
    if (_pz('email_apply_withdraw_to_admin', true)) {
        $title   = '[' . $blog_name . '] ' . $msg_args['title'];
        $message = $msg_args['content'];

        /**发送邮件 */
        zib_mail_to_admin($title, $message);
    }

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('apply_withdraw_admin');
    if ($wechat_template_id) {
        /**
        {{first.DATA}}
        申请人：{{keyword1.DATA}}
        提现金额：{{keyword2.DATA}}
        申请时间：{{keyword3.DATA}}
        {{remark.DATA}}
         */

        $send_data = array(
            'first'    => array(
                'value' => '[' . $blog_name . '] 收到新的提现申请，请及时处理',
            ),
            'keyword1' => array(
                'value' => get_userdata($msg_args['send_user'])->display_name,
            ),
            'keyword2' => array(
                'value' => floatval($msg_args['meta']['withdraw_price']) . '元',
            ),
            'keyword3' => array(
                'value' => current_time("Y-m-d H:i:s"),
            ),
            'remark'   => array(
                'value' => '请登录网站后台处理该提现申请',
            ),
        );
        $send_url = add_query_arg(array('page' => 'zibpay_withdraw', 'status' => '0'), admin_url('admin.php'));
        //发送消息
        zib_send_wechat_template_msg_to_admin($wechat_template_id, $send_data, $send_url);
    }

}

/**
 * @description: 处理用户提现之后给用户发送邮件
 * @param array $data 消息数组
 * @return {*}
 */
if (_pz('email_withdraw_process', true)) {
    add_action('withdraw_process_newmsg', 'zibpay_mail_withdraw_process');
}
function zibpay_mail_withdraw_process($data)
{

    $user_id = $data['receive_user'];
    $udata   = get_userdata($user_id);
    /**判断邮箱状态 */
    $user_email = $udata->user_email;
    if (!is_email($user_email) || stristr($user_email, '@no')) {
        return false;
    }

    $blog_name = get_bloginfo('name');
    $title     = '[' . $blog_name . '] ' . $data['title'];

    $message = $udata->display_name . "<br>" . $data['content'];

    /**发送邮件 */
    @wp_mail($udata->user_email, $title, $message);
}

//处理完提现后向用户发送微信模板消息
add_action('withdraw_process', 'zibpay_wechat_template_msg_withdraw_process', 10, 3);
function zibpay_wechat_template_msg_withdraw_process($msg_db, $is_allow, $msg = '')
{
    /**
    {{first.DATA}}
    申请时间：{{keyword1.DATA}}
    处理时间：{{keyword2.DATA}}
    处理状态：{{keyword3.DATA}}
    提现金额：{{keyword4.DATA}}
    到账金额：{{keyword5.DATA}}
    {{remark.DATA}}
     */

    //发送微信模板消息
    $wechat_template_id = zib_get_wechat_template_id('withdraw_process');
    if (!$wechat_template_id) {
        return;
    }
    $blog_name = get_bloginfo('name');

    $send_data = array(
        'first'    => array(
            'value' => '[' . $blog_name . '] 您的提现申请' . ($is_allow ? '已处理完成' : '被拒绝'),
            "color" => ($is_allow ? '#1a7dfd' : '#fd4343'),
        ),
        'keyword1' => array(
            'value' => $msg_db['create_time'],
        ),
        'keyword2' => array(
            'value' => current_time('Y-m-d H:i:s'),
        ),
        'keyword3' => array(
            'value' => ($is_allow ? '已处理完成' : '被拒绝'),
            "color" => ($is_allow ? '#1a7dfd' : '#fd4343'),
        ),
        'keyword4' => array(
            'value' => floatval(round((float) $msg_db['meta']['withdraw_price'], 2)),
        ),
        'keyword5' => array(
            'value' => floatval(round((float) (($is_allow ? ($msg_db['meta']['withdraw_price'] - (isset($msg_db['meta']['service_price']) ? $msg_db['meta']['service_price'] : 0)) : '0')))),
        ),
        'remark'   => array(
            'value' => ($msg ? '处理留言：' . $msg . "\n" : '') . '如有疑问，请与客服联系',
        ),
    );
    $send_url = zib_get_user_center_url('rebate');
    //发送消息
    zib_send_wechat_template_msg($msg_db['send_user'], $wechat_template_id, $send_data, $send_url);
}
