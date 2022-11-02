<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-11-03 00:09:44
 * @LastEditTime: 2022-09-27 01:25:47
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

if (_pz('pay_rebate_s')) {
    add_action('admin_notices', 'zib_withdraw_admin_notice', 1, 1);
}
function zib_withdraw_admin_notice()
{
    if (isset($_GET['page']) && 'zibpay_withdraw' == $_GET['page']) {
        return;
    }

    $withdraw_count = ZibMsg::get_count(array(
        'type'   => 'withdraw',
        'status' => 0,
    ));
    if ($withdraw_count > 0) {
        $html = '<div class="notice notice-info is-dismissible">';
        $html .= '<h3>提现申请待处理</h3>';
        $html .= '<p>您有' . $withdraw_count . '个提现申请待处理</p>';
        $html .= '<p><a class="button" href="' . add_query_arg(array('page' => 'zibpay_withdraw', 'status' => 0), admin_url('admin.php')) . '">立即处理</a></p>';
        $html .= '</div>';
        echo $html;
    }
    ;
}

/**
 * @description: 后台用户列表添加会员筛选
 * @param {*}
 * @return {*}
 */
add_filter('views_users', 'zib_admin_user_views');
function zib_admin_user_views($views)
{

    $vip = isset($_REQUEST['vip']) ? $_REQUEST['vip'] : '';
    if (!$views) {
        $views = array();
    }

    for ($i = 1; $i <= 2; $i++) {
        if (_pz('pay_user_vip_' . $i . '_s', true)) {
            $views['vip' . $i] = '<a' . ($vip == $i ? ' class="current"' : '') . ' href="users.php?vip=' . $i . '">' . _pz('pay_user_vip_' . $i . '_name') . '</a>（' . zib_get_vip_user_count($i) . '）';
        }
    }
    return $views;
}

add_filter('users_list_table_query_args', 'zib_admin_users_list_table_query_args');
function zib_admin_users_list_table_query_args($args)
{
    $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';
    if (in_array($orderby, array('balance', 'points', 'last_login', 'phone_number', 'vip_level', 'level', 'referrer_id'))) {
        $args['orderby']  = 'meta_value';
        $args['meta_key'] = $orderby;
    }
    //默认排序方式为注册时间
    if (!isset($_REQUEST['orderby'])) {
        $args['order']   = 'desc';
        $args['orderby'] = 'user_registered';
    }
    $vip = isset($_REQUEST['vip']) ? $_REQUEST['vip'] : '';
    for ($i = 1; $i <= 2; $i++) {
        if ($vip == $i && _pz('pay_user_vip_' . $i . '_s', true)) {
            $args['meta_key']   = 'vip_level';
            $args['meta_value'] = $i;
        }
    }
    return $args;
}

/**挂钩后台用户中心-用户列表 */
function zib_users_columns($columns)
{
    $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';
    $order   = isset($_REQUEST['order']) && 'desc' == $_REQUEST['order'] ? 'asc' : 'desc';

    unset($columns['role']);
    unset($columns['name']);
    unset($columns['posts']);
    unset($columns['email']);

    $columns['show_name'] = '<a href="' . add_query_arg(array('orderby' => 'display_name', 'order' => $order)) . '"><span>昵称</span></a>';
    $columns['show_name'] .= ' · <a href="' . add_query_arg(array('orderby' => 'email', 'order' => $order)) . '"><span>邮箱</span></a>';
    if (_pz('user_level_s')) {
        $columns['show_name'] .= ' · <a href="' . add_query_arg(array('orderby' => 'level', 'order' => $order)) . '"><span>等级</span></a>';
    }
    $columns['oauth'] = __('社交登录');
    $columns['oauth'] .= ' · <a href="' . add_query_arg(array('orderby' => 'phone_number', 'order' => $order)) . '"><span>手机号</span></a>';

    $points_s      = _pz('points_s');
    $pay_balance_s = _pz('pay_balance_s');
    if ($pay_balance_s || $points_s) { //资产
        $columns['assets'] = '<a href="' . add_query_arg(array('orderby' => 'balance', 'order' => $order)) . '"><span>余额</span></a>';
        $columns['assets'] .= ' · <a href="' . add_query_arg(array('orderby' => 'points', 'order' => $order)) . '"><span>积分</span></a>';
    }

    $columns['vip_type'] = '<a href="' . add_query_arg(array('orderby' => 'vip_level', 'order' => $order)) . '"><span>VIP会员</span></a>';
    if (_pz('pay_rebate_s')) {
        $columns['rebate_info'] = __('推广返利');
        $columns['referrer']    = '<a href="' . add_query_arg(array('orderby' => 'referrer_id', 'order' => $order)) . '"><span>推荐人</span></a>';
    }

    if (_pz('pay_income_s')) {
        $columns['income'] = __('创作分成');
    }

    $columns['all_time'] = '<a href="' . add_query_arg(array('orderby' => 'user_registered', 'order' => $order)) . '"><span>注册</span></a> · <a href="' . add_query_arg(array('orderby' => 'last_login', 'order' => $order)) . '"><span>登录</span></a>';
    $columns['count']    = '文章 · 评论';

    return $columns;
}

/**
 * @description: 后台用户表格添加自定义内容
 * @param {*}
 * @return {*}
 */
function zib_output_users_columns($var, $column_name, $user_id)
{

    $user = get_userdata($user_id);

    switch ($column_name) {
        case "show_name":
            $html = '<a title="在前台查看此用户" href="' . zib_get_user_home_url($user_id) . '">' . $user->display_name . '</a>';

            if (_pz('user_level_s')) {
                $user_level = zib_get_user_level($user_id);
                $title      = esc_attr(_pz('user_level_opt', 'LV' . $user_level, 'name_' . $user_level));
                $html .= ' · ' . $title;
            }
            if (_pz('user_ban_s')) {
                $is_ban = zib_user_is_ban($user_id);
                if ($is_ban) {
                    $is_ban = 2 === $is_ban ? '已封号' : '小黑屋';
                    $html .= ' · <code>' . $is_ban . '</code>';
                }
            }
            if (_pz('user_auth_s')) {
                $is_ban = zib_is_user_auth($user_id);
                if ($is_ban) {
                    $html .= ' · <code>已认证</code>';
                }
            }

            $html .= '<br><a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';
            if (_pz('user_medal_s')) {
                $medal_details = zib_get_user_medal_details($user_id);
                if ($medal_details) {
                    $i         = 0;
                    $icon_html = '';
                    $max_icon  = 3;
                    $count     = count($medal_details);

                    foreach ($medal_details as $k => $v) {
                        if ($i >= $max_icon) {
                            break;
                        }
                        $i++;
                        $icon_url  = $v['icon'];
                        $icon_html .= '<img src="' . $v['icon'] . '" style="vertical-align: -3px;width: 14px;height: 14px;margin-right: 1px;margin-top: 2px;" data-toggle="tooltip" title="' . esc_attr($k) . '" alt="徽章-' . esc_attr($k) . '">';
                    }

                    $html .=  '<div>'.$icon_html.'<span>徽章[' . $count . ']</span></div>';
                }

            }

            return '<div style="font-size: 12px;">' . $html . '</div>';
            break;
        case "vip_type":
            $level = zib_get_user_vip_level($user_id);
            return $level ? _pz('pay_user_vip_' . $level . '_name') . '<br>' . zib_get_user_vip_exp_date_text($user_id) : '普通用户';
            break;
        case "rebate_info":
            $rebate_ratio = zibpay_get_user_rebate_rule($user->ID);
            if (!$rebate_ratio['type'] || !is_array($rebate_ratio['type'])) {
                return '不返佣';
            }

            $rebate_type = zibpay_get_user_rebate_type($rebate_ratio['type'], '/');
            $html        = $rebate_type . '：' . $rebate_ratio['ratio'] . '%';

            $all     = zibpay_get_user_rebate_data($user_id, 'all')['sum'];
            $invalid = zibpay_get_user_rebate_data($user_id, 'effective')['sum'];
            $invalid = $invalid ? $invalid : 0;

            $html .= $all ? '<br>佣金累计：' . $all . ' · 待提现：' . $invalid : '<br>暂无佣金';
            return '<div style="font-size: 12px;">' . $html . '</div>';

            break;

        case "income":

            $income_points_ratio    = zibpay_get_user_income_points_ratio($user_id);
            $income_ratio           = zibpay_get_user_income_ratio($user_id);
            $income_price_all       = zibpay_get_user_income_data($user_id, 'all');
            $income_price_effective = zibpay_get_user_income_data($user_id, 'effective');

            $html = '<div style="font-size: 12px;" title="分成比例">现金' . $income_ratio . '% · 积分' . $income_points_ratio . '%</div>';
            $html .= floatval($income_price_all['sum']) ? '累计：' . (floatval($income_price_all['sum']) ?: '0') . ' · 待提现：' . (floatval($income_price_effective['sum']) ?: '0'): '';
            return '<div style="font-size: 12px;">' . $html . '</div>';

            break;

        case "assets":
            $user_points  = zibpay_get_user_points($user_id);
            $user_balance = _pz('pay_balance_s') ? zibpay_get_user_balance($user_id) : '';
            $html         = '';

            return '<div style="font-size: 12px;">余额：' . $user_balance . ' <br>积分：' . $user_points . '</div>';
            break;

        case "referrer":
            $referrer_id = get_user_meta($user_id, 'referrer_id', true);
            if ($referrer_id) {
                $referrer_name = get_userdata($referrer_id)->display_name;
                $level         = zib_get_user_vip_level($referrer_id);
                return '<a title="查看此用户" href="' . add_query_arg('s', $referrer_name, admin_url('users.php')) . '">' . $referrer_name . '</a>' . ($level ? '<br>' . _pz('pay_user_vip_' . $level . '_name') : '');
            }
            return '无';
            break;

        case "all_time":
            $last_login = get_user_meta($user->ID, 'last_login', true);
            $last_login = $last_login ? '<span title="' . $last_login . '">' . zib_get_time_ago($last_login) . '登录</span>' : '--';

            $reg_time = get_date_from_gmt($user->user_registered);
            $reg_time = $reg_time ? '<span title="' . $reg_time . '">' . zib_get_time_ago($reg_time) . '注册</span>' : '--';

            return '<div style="font-size: 12px;">' . $reg_time . '<br>' . $last_login . '</div>';
            break;

        case "oauth":

            $args   = array();
            $args[] = array(
                'name' => 'QQ',
                'type' => 'qq',
            );
            $args[] = array(
                'name' => '微信',
                'type' => 'weixin',
            );
            $args[] = array(
                'name' => '微信',
                'type' => 'weixingzh',
            );
            $args[] = array(
                'name' => '微博',
                'type' => 'weibo',
            );
            $args[] = array(
                'name' => 'GitHub',
                'type' => 'github',
            );
            $args[] = array(
                'name' => '码云',
                'type' => 'gitee',
            );
            $args[] = array(
                'name' => '百度',
                'type' => 'baidu',
            );
            $args[] = array(
                'name' => '支付宝',
                'type' => 'alipay',
            );
            $oauth = array();
            foreach ($args as $arg) {
                $name = $arg['name'];
                $type = $arg['type'];

                $bind_href = zib_get_oauth_login_url($type);
                if ($bind_href) {
                    $oauth_info = get_user_meta($user_id, 'oauth_' . $type . '_getUserInfo', true);
                    $oauth_id   = get_user_meta($user_id, 'oauth_' . $type . '_openid', true);
                    if ($oauth_info && $oauth_id) {
                        $oauth[] = $name;
                    }
                }
            }

            $html         = $oauth ? '已绑定' . implode('、', $oauth) : '未绑定社交账号';
            $phone_number = get_user_meta($user->ID, 'phone_number', true);
            $html .= $phone_number ? $phone_number : '<br>未绑定手机号';
            return '<div style="font-size: 12px;">' . $html . '</div>';
            break;
        case "count":
            $com_n  = (int) get_user_comment_count($user_id);
            $post_n = (int) count_user_posts($user_id, 'post', true);

            $html = '';
            $html .= $post_n ? '<div><a href="edit.php?author=' . $user_id . '">文章[' . $post_n . ']</a></div>' : '<div>暂无文章</div>';
            $html .= $com_n ? '<div><a href="edit-comments.php?user_id=' . $user_id . '">评论[' . $com_n . ']</a></div>' : '<div>暂无评论</div>';

            return $html;
            break;
    }

    return $var;
}
add_filter('manage_users_columns', 'zib_users_columns');
add_filter('manage_users_custom_column', 'zib_output_users_columns', 10, 3);

//后台用户资料修改
if (is_super_admin()) {
    function zib_csf_user_vip_fields()
    {
        $args       = array();
        $profile_id = !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;
        if (!$profile_id && defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE) {
            $profile_id = get_current_user_id();
        }

        $vip_dec = '<h3>会员设置</h3><p>修改用户的会员信息，请确保主题设置中的<code>VIP会员功能</code>已开启</p>';

        if ($profile_id) {
            $vip_exp_date = get_user_meta($profile_id, 'vip_exp_date', true);
            $vip_level    = zib_get_user_vip_level($profile_id);

            $vip_dec .= '当前用户：';
            if ($vip_level) {
                if ('Permanent' == $vip_exp_date) {
                    $vip_dec .= '已开通<code>' . _pz('pay_user_vip_' . $vip_level . '_name') . '</code>，永久有效';
                } else {
                    $vip_dec .= '已开通<code>' . _pz('pay_user_vip_' . $vip_level . '_name') . '</code>，到期时间：' . date("Y年m月d日", strtotime($vip_exp_date));
                }
            } else {
                $vip_level_expired = (int) get_user_meta($profile_id, 'vip_level_expired', true);
                if ($vip_level_expired) {
                    $vip_dec .= '开通的<code>' . _pz('pay_user_vip_' . $vip_level_expired . '_name') . '</code>已过期，过期时间：' . date("Y年m月d日", strtotime($vip_exp_date));
                } else {
                    $vip_dec .= '未开通会员';
                }
            }
        }

        $args[] = array(
            'type'    => 'content',
            'content' => $vip_dec,
        );

        $args[] = array(
            'id'      => 'vip_level',
            'type'    => 'radio',
            'title'   => 'VIP会员设置',
            'default' => '0',
            'desc'    => '在此直接修改此用户的会员信息，涉及到用户权益请谨慎修改',
            'options' => array(
                '0' => '普通用户',
                '1' => _pz('pay_user_vip_1_name'),
                '2' => _pz('pay_user_vip_2_name'),
            ),
        );
        $args[] = array(
            'id'         => 'vip_exp_date',
            'dependency' => array('vip_level', '>=', '1'),
            'type'       => 'date',
            'title'      => '会员有效期',
            'desc'       => '<p>请输入或选择有效期，请确保格式正确，例如：<code>2020-10-10 23:59:59</code></p>如果需要设置为“永久有效会员”，请手动设置为：<code>Permanent</code>',
            'settings'   => array(
                'dateFormat'  => 'yy-mm-dd 23:59:59',
                'changeMonth' => true,
                'changeYear'  => true,
            ),
        );
        return $args;
    }

    function zib_csf_user_points_balance_fields($type = 'points')
    {

        $user_id = !empty($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;

        if (!$user_id && defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE) {
            $user_id = get_current_user_id();
        }

        $user_points  = zibpay_get_user_points($user_id);
        $user_balance = _pz('pay_balance_s') ? zibpay_get_user_balance($user_id) : '';

        $text       = $type === 'balance' ? '余额' : '积分';
        $type_class = $type === 'balance' ? 'c-blue-2' : 'c-green';
        $action     = 'admin_update_user_' . $type; //管理
        $val        = $type === 'balance' ? zibpay_get_user_balance($user_id) : zibpay_get_user_points($user_id); //
        if ($type === 'balance') {
        }

        $con = '<div class="options-notice">
        <div class="explain">
        <p><b>您可以在此处手动为用户添加或扣除' . $text . '</b></p>
        <ajaxform class="ajax-form" ajax-url="' . admin_url("admin-ajax.php") . '">
            <p class="flex ac"><select ajax-name="type">
                    <option value="">请选择添加或扣除</option>
                    <option value="add">添加</option>
                    <option value="delete">扣除</option>
                </select>
                <input style="max-width:120px;" ajax-name="val" type="number" placeholder="请输入数额"></p>
            <p class="">
                <div class="">请填写添加或扣除的简短说明</div>
                <input type="text" placeholder="添加或扣除的简短说明" style="width: 95%;" ajax-name="decs">
            </p>
            <div class="ajax-notice"></div>
            <p><a href="javascript:;" class="but ajax-submit ' . $type_class . '"> 确认提交</a></p>
            <input type="hidden" ajax-name="action" value="' . $action . '">
            <input type="hidden" ajax-name="user_id" value="' . $user_id . '">
        </ajaxform>
    </div></div>';

        $args[] = array(
            'type'    => 'content',
            'content' => '<h3>用户' . $text . '</h3>',
        );
        $args[] = array(
            'title'   => '当前' . $text,
            'type'    => 'content',
            'content' => '<span class="but ' . $type_class . '">' . $val . '</span>',
        );
        $args[] = array(
            'class'   => 'compact',
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => $con,
        );

        return $args;

    }

    $points_s      = _pz('points_s');
    $pay_balance_s = _pz('pay_balance_s');

    if ($pay_balance_s) {
        CSF::createProfileOptions('user_balance', array(
            'data_type' => 'unserialize',
        ));
        CSF::createSection('user_balance', array(
            'fields' => zib_csf_user_points_balance_fields('balance'),
        ));
    }
    if ($points_s) {
        CSF::createProfileOptions('user_points', array(
            'data_type' => 'unserialize',
        ));
        CSF::createSection('user_points', array(
            'fields' => zib_csf_user_points_balance_fields('points'),
        ));
    }

    CSF::createProfileOptions('user_vip', array(
        'data_type' => 'unserialize',
    ));
    CSF::createSection('user_vip', array(
        'fields' => zib_csf_user_vip_fields(),
    ));
    if (_pz('pay_income_s')) {
        CSF::createProfileOptions('income_rule');
        CSF::createSection('income_rule', array(
            'fields' => array(
                array(
                    'type'    => 'content',
                    'content' => '<h3>创作分成</h3>在此处您可以单独为此用户设置创作分成比例',
                ),
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '独立设置',
                ),
                array(
                    'dependency' => array('switch', '!=', ''),
                    'id'         => 'ratio',
                    'type'       => 'spinner',
                    'title'      => '现金分成比例',
                    'desc'       => '为0则不参与分成',
                    'min'        => 0,
                    'max'        => 100,
                    'step'       => 5,
                    'unit'       => '%',
                    'default'    => 0,
                ),
                array(
                    'dependency' => array('switch', '!=', ''),
                    'id'         => 'points_ratio',
                    'type'       => 'spinner',
                    'title'      => '积分分成比例',
                    'desc'       => '为0则不参与分成（用户采用积分支付的订单给与作者的分成比例）',
                    'min'        => 0,
                    'max'        => 100,
                    'step'       => 5,
                    'unit'       => '%',
                    'default'    => 0,
                ),
            ),
        ));
    }
    if (_pz('pay_rebate_s')) {

        CSF::createProfileOptions('rebate_rule');
        CSF::createSection('rebate_rule', array(
            'fields' => array(
                array(
                    'type'    => 'content',
                    'content' => '<h3>推广返利</h3>在此处您可以单独为此用户设置返利规则。为用户开启独立设置后，则不受主题设置的规则约束',
                ),
                array(
                    'id'    => 'switch',
                    'type'  => 'switcher',
                    'title' => '独立设置',
                ),
                array(
                    'dependency' => array('switch', '!=', ''),
                    'id'         => 'type',
                    'type'       => 'checkbox',
                    'title'      => '返利订单',
                    'desc'       => '给用户返利的订单类型<br>全部关闭，则代表此用户不参与推广返佣',
                    'options'    => CFS_Module::rebate_type(),
                    'default'    => array('all'),
                ),
                array(
                    'dependency' => array('switch', '!=', ''),
                    'id'         => 'ratio',
                    'type'       => 'spinner',
                    'title'      => '佣金比例',
                    'min'        => 0,
                    'max'        => 100,
                    'step'       => 5,
                    'unit'       => '%',
                    'default'    => 10,
                ),
            ),
        ));
    }
}

/**
 * @description: 文章付费设置的数据转换
 * @param {*}
 * @return {*}
 */
function zibpay_post_meta_to_csf($post_type, $post)
{
    $post_id = !empty($post->ID) ? $post->ID : '';

    if (!$post_id) {
        return;
    }

    $pay_mate = get_post_meta($post_id, 'posts_zibpay', true);

    if (!empty($pay_mate['pay_download']) && !is_array($pay_mate['pay_download'])) {
        $pay_download_args        = zibpay_get_post_down_array($pay_mate);
        $pay_mate['pay_download'] = $pay_download_args;
        update_post_meta($post_id, 'posts_zibpay', $pay_mate);
    }
}
add_action('add_meta_boxes', 'zibpay_post_meta_to_csf', 1, 2);

//添加文章付费参数
CSF::createMetabox('posts_zibpay', zibpay_post_mate_csf_meta());
CSF::createSection('posts_zibpay', array(
    'fields' => zibpay_post_mate_csf_fields(),
));

function zibpay_post_mate_csf_meta()
{
    $meta = array(
        'title'     => '付费功能',
        'post_type' => array('post'),
        'data_type' => 'serialize',
    );
    return apply_filters('zib_add_pay_meta_box_meta', $meta);
}

/**
 * @description: 文章post_mate的设置数据
 * @param {*}
 * @return {*}
 */
function zibpay_post_mate_csf_fields()
{
    //对老板数据做兼容处理
    $post_id = !empty($_REQUEST['post']) ? $_REQUEST['post'] : 0;
    if ($post_id) {
        $pay_mate = get_post_meta($post_id, 'posts_zibpay', true);
        if (!empty($pay_mate['pay_download']) && !is_array($pay_mate['pay_download'])) {
            $pay_mate['pay_download'] = zibpay_get_post_down_array($pay_mate);
            update_post_meta($post_id, 'posts_zibpay', $pay_mate);
        }
    }
    $pay_cuont_default = zib_get_mt_rand_number(_pz('pay_cuont_default', 0));

    $fields = array(
        array(
            'title'   => '付费模式',
            'id'      => 'pay_type',
            'type'    => 'radio',
            'default' => 'no',
            'inline'  => true,
            'options' => array(
                'no' => __('关闭', 'zib_language'),
                '1'  => __('付费阅读', 'zib_language'),
                '2'  => __('付费下载', 'zib_language'),
                '5'  => '付费图片',
                '6'  => '付费视频',
            ),
        ),
        //显示购买用户权限
        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'title'      => '购买权限',
            'id'         => 'pay_limit',
            'type'       => 'radio',
            'default'    => '0',
            'desc'       => '设置此处可实现会员专享资源功能，配置对应的会员价格可实现专享免费资源<br/><i class="fa fa-fw fa-info-circle fa-fw"></i> 使用此功能，请确保付费会员功能已开启，否则会出错',
            'options'    => array(
                '0' => __('所有人可购买', 'zib_language'),
                '1' => _pz('pay_user_vip_1_name') . '及以上会员可购买',
                '2' => '仅' . _pz('pay_user_vip_2_name') . '可购买',
            ),
        ),
        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'title'      => '支付类型',
            'id'         => 'pay_modo',
            'type'       => 'radio',
            'default'    => _pz('pay_modo_default', 0),
            'options'    => array(
                '0'      => __('普通商品（金钱购买）', 'zib_language'),
                'points' => __('积分商品（积分购买，依赖于用户积分功能）', 'zib_language'),
            ),
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '==', 'points'),
            ),
            'id'         => 'points_price',
            'title'      => '积分售价',
            'class'      => '',
            'default'    => _pz('points_price_default'),
            'type'       => 'number',
            'unit'       => '积分',
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '==', 'points'),
            ),
            'title'      => _pz('pay_user_vip_1_name') . '积分售价',
            'id'         => 'vip_1_points',
            'class'      => 'compact',
            'subtitle'   => '填0则为' . _pz('pay_user_vip_1_name') . '免费',
            'default'    => _pz('vip_1_points_default'),
            'type'       => 'number',
            'unit'       => '积分',
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '==', 'points'),
            ),
            'title'      => _pz('pay_user_vip_2_name') . '积分售价',
            'id'         => 'vip_2_points',
            'class'      => 'compact',
            'subtitle'   => '填0则为' . _pz('pay_user_vip_2_name') . '免费',
            'default'    => _pz('vip_2_points_default'),
            'type'       => 'number',
            'unit'       => '积分',
            'desc'       => '会员价格不能高于售价',
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '!=', 'points'),
            ),
            'id'         => 'pay_price',
            'title'      => '执行价',
            'default'    => _pz('pay_price_default', '0.01'),
            'type'       => 'number',
            'unit'       => '元',
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '!=', 'points'),
            ),
            'id'         => 'pay_original_price',
            'title'      => '原价',
            'class'      => 'compact',
            'subtitle'   => '显示在执行价格前面，并划掉',
            'default'    => _pz('pay_original_price_default'),
            'type'       => 'number',
            'unit'       => '元',
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_original_price', '!=', ''),
                array('pay_modo', '!=', 'points'),
            ),
            'title'      => ' ',
            'subtitle'   => '促销标签',
            'class'      => 'compact',
            'id'         => 'promotion_tag',
            'type'       => 'textarea',
            'default'    => _pz('pay_promotion_tag_default', '<i class="fa fa-fw fa-bolt"></i> 限时特惠'),
            'attributes' => array(
                'rows' => 1,
            ),
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '!=', 'points'),
            ),
            'title'      => _pz('pay_user_vip_1_name') . '价格',
            'id'         => 'vip_1_price',
            'class'      => 'compact',
            'subtitle'   => '填0则为' . _pz('pay_user_vip_1_name') . '免费',
            'default'    => _pz('vip_1_price_default'),
            'type'       => 'number',
            'unit'       => '元',
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '!=', 'points'),
            ),
            'title'      => _pz('pay_user_vip_2_name') . '价格',
            'id'         => 'vip_2_price',
            'class'      => 'compact',
            'subtitle'   => '填0则为' . _pz('pay_user_vip_2_name') . '免费',
            'default'    => _pz('vip_2_price_default'),
            'type'       => 'number',
            'unit'       => '元',
            'desc'       => '会员价格不能高于执行价',
        ),
        array(
            'dependency' => array(
                array('pay_type', '!=', 'no'),
                array('pay_modo', '!=', 'points'),
            ),
            'title'      => '推广折扣',
            'id'         => 'pay_rebate_discount',
            'class'      => 'compact',
            'subtitle'   => __('通过推广链接购买，额外优惠的金额', 'zib_language'),
            'desc'       => __('1.需开启推广返佣功能  2.注意此金不能超过实际购买价，避免出现负数', 'zib_language'),
            'default'    => _pz('pay_rebate_discount', 0),
            'type'       => 'number',
            'unit'       => '元',
        ),
        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'title'      => '销量浮动',
            'id'         => 'pay_cuont',
            'subtitle'   => __('为真实销量增加或减少的数量', 'zib_language'),
            'default'    => $pay_cuont_default,
            'type'       => 'number',
        ),
        array(
            'dependency'  => array('pay_type', '==', 5),
            'title'       => '付费图片',
            'id'          => 'pay_gallery',
            'type'        => 'gallery',
            'add_title'   => '新增图片',
            'edit_title'  => '编辑图片',
            'clear_title' => '清空图片',
            'default'     => false,
        ),
        array(
            'dependency' => array('pay_type|pay_gallery', '==|!=', '5|'),
            'title'      => ' ',
            'subtitle'   => '免费查看',
            'class'      => 'compact',
            'id'         => 'pay_gallery_show',
            'default'    => _pz('pay_gallery_show_default', 1),
            'min'        => 0,
            'step'       => 1,
            'unit'       => '张',
            'desc'       => '设置可免费查看前几张图片的数量，不能大于付费图片数量，否则无效',
            'type'       => 'spinner',
        ),
        array(
            'dependency' => array('pay_type', '==', 6),
            'title'      => '视频资源',
            'id'         => 'video_url',
            'type'       => 'upload',
            'library'    => 'video',
            'preview'    => false,
            'default'    => '',
            'desc'       => '输入视频地址或选择、上传本地视频',
        ),
        array(
            'dependency' => array('pay_type|video_url', '==|!=', '6|'),
            'title'      => ' ',
            'subtitle'   => '视频封面(可选)',
            'class'      => 'compact',
            'id'         => 'video_pic',
            'type'       => 'upload',
            'library'    => 'image',
            'default'    => '',
        ),
        array(
            'dependency'  => array('pay_type|video_url', '==|!=', '6|'),
            'id'          => 'video_title',
            'title'       => ' ',
            'subtitle'    => '本集标题(如需添加剧集则需填写此处)',
            'placeholder' => '第1集',
            'default'     => '',
            'class'       => 'compact',
            'type'        => 'text',
        ),
        array(
            'dependency'   => array('pay_type|video_url', '==|!=', '6|'),
            'id'           => 'video_episode',
            'type'         => 'group',
            'button_title' => '添加剧集',
            'class'        => 'compact',
            'title'        => '更多剧集',
            'subtitle'     => '为付费视频添加更多剧集',
            'default'      => array(),
            'fields'       => array(
                array(
                    'id'       => 'title',
                    'title'    => ' ',
                    'subtitle' => '剧集标题',
                    'default'  => '',
                    'type'     => 'text',
                ),
                array(
                    'title'       => ' ',
                    'subtitle'    => '视频地址',
                    'id'          => 'url',
                    'class'       => 'compact',
                    'type'        => 'upload',
                    'preview'     => false,
                    'library'     => 'video',
                    'placeholder' => '选择视频或填写视频地址',
                    'default'     => false,
                ),

            ),
        ),
        array(
            'dependency' => array('pay_type', '==', '6'),
            'id'         => 'video_scale_height',
            'title'      => '视频设置',
            'subtitle'   => '固定长宽比例',
            'default'    => 0,
            'max'        => 200,
            'min'        => 0,
            'step'       => 5,
            'unit'       => '%',
            'type'       => 'spinner',
            'desc'       => '为0则不固定长宽比例',
        ),
        array(
            'dependency'   => array('pay_type', '==', 2),
            'id'           => 'pay_download',
            'type'         => 'group',
            'button_title' => '添加资源',
            'title'        => '资源下载',
            'fields'       => array(
                array(
                    'title'       => __('下载地址', 'zib_language'),
                    'id'          => 'link',
                    'placeholder' => '上传文件或输入下载地址',
                    'preview'     => false,
                    'type'        => 'upload',
                ),
                array(
                    'dependency' => array('link', '!=', ''),
                    'title'      => '更多内容',
                    'desc'       => '按钮旁边的额外内容，例如：提取密码、解压密码等',
                    'class'      => 'compact',
                    'id'         => 'more',
                    'type'       => 'textarea',
                    'attributes' => array(
                        'rows' => 1,
                    ),
                ),
                array(
                    'dependency'   => array('link', '!=', ''),
                    'id'           => 'icon',
                    'type'         => 'icon',
                    'title'        => '自定义按钮图标',
                    'button_title' => '选择图标',
                    'default'      => 'fa fa-download',
                ),
                array(
                    'dependency' => array('link', '!=', ''),
                    'title'      => '自定义按钮文案',
                    'class'      => 'compact',
                    'id'         => 'name',
                    'type'       => 'textarea',
                    'attributes' => array(
                        'rows' => 1,
                    ),
                ),
                array(
                    'dependency' => array('link', '!=', ''),
                    'title'      => '自定义按钮颜色',
                    'class'      => 'compact skin-color',
                    'desc'       => '按钮图标、文案、颜色默认均会自动获取，建议为空即可。<br>上方的按钮图标为主题自带的fontawesome 4图标库，如需添加其它图标可采用HTML代码，请注意代码规范！<br><a href="https://www.zibll.com/547.html" target="_blank">使用阿里巴巴Iconfont图标详细图文教程</a>',
                    'id'         => 'class',
                    'type'       => "palette",
                    'options'    => CFS_Module::zib_palette(),
                ),
            ),
        ),
        array(
            'dependency'   => array('pay_type', '==', 2),
            'id'           => 'attributes',
            'type'         => 'group',
            'button_title' => '添加属性',
            'title'        => '资源属性',
            'default'      => _pz('pay_attributes_default', array()),
            'fields'       => array(
                array(
                    'title'   => '属性名称',
                    'default' => '',
                    'id'      => 'key',
                    'type'    => 'text',
                ),
                array(
                    'title'   => '属性内容',
                    'class'   => 'compact',
                    'default' => '',
                    'id'      => 'value',
                    'type'    => 'text',
                ),
            ),
        ),
        array(
            'dependency'   => array('pay_type', '==', 2),
            'title'        => '演示地址',
            'id'           => 'demo_link',
            'default'      => array(),
            'add_title'    => '添加演示',
            'edit_title'   => '编辑地址',
            'remove_title' => '移除演示地址',
            'type'         => 'link',
        ),
        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'title'      => '商品信息',
            'subtitle'   => __('商品标题', 'zib_language'),
            'desc'       => __('（可选）如需要单独显示商品标题请填写此项', 'zib_language'),
            'id'         => 'pay_title',
            'type'       => 'text',
        ),
        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'title'      => ' ',
            'subtitle'   => __('商品简介', 'zib_language'),
            'id'         => 'pay_doc',
            'desc'       => __('（可选）如需要单独显示商品介绍请填写此项', 'zib_language'),
            'class'      => 'compact',
            'type'       => 'textarea',
            'attributes' => array(
                'rows' => 1,
            ),
        ),
        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'title'      => ' ',
            'subtitle'   => '更多详情',
            'id'         => 'pay_details',
            'desc'       => __('（可选）显示在商品卡片下方的内容（支持HTML代码，请注意代码规范）', 'zib_language'),
            'class'      => 'compact',
            'default'    => _pz('pay_details_default'),
            'type'       => 'textarea',
            'attributes' => array(
                'rows' => 3,
            ),
        ),
        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'title'      => ' ',
            'subtitle'   => '额外隐藏内容',
            'id'         => 'pay_extra_hide',
            'desc'       => __('（可选）付费后显示的额外隐藏内容（支持HTML代码，请注意代码规范）', 'zib_language'),
            'class'      => 'compact',
            'default'    => _pz('pay_extra_hide_default'),
            'type'       => 'textarea',
            'attributes' => array(
                'rows' => 3,
            ),
        ),

        array(
            'dependency' => array('pay_type', '!=', 'no'),
            'content'    => '<li><qc style="color:#fb2121;background:undefined">付费阅读</qc>功能需要配合<qc style="color:#fb2121;background:undefined">短代码</qc>或者古腾堡<qc style="color:#fb2121;background:undefined">隐藏内容块</qc>使用 </li><li>古腾堡编辑器：添加块-zibll主题模块-隐藏内容块-设置隐藏模式为：付费阅读 </li><li>经典编辑器：插入短代码： <code>[hidecontent type="payshow"]</code> 隐藏内容 <code>[/hidecontent]</code> </li><li><a href="https://www.zibll.com/580.html" target="_blank">官方教程</a> | <a href="' . zib_get_admin_csf_url('商城配置') . '" target="_blank">商城设置</a></li>',
            'style'      => 'warning',
            'type'       => 'submessage',
        ),
    );
    return apply_filters('zib_add_pay_meta_box_args', $fields);
}
