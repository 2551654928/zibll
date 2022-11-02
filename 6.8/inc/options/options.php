<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-11-11 10:19:48
 * @LastEditTime: 2022-11-01 12:18:53
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

$functions = array(
    'options-module',
    'admin-options',
    'metabox-options',
    'profile-options',
    'action',
);

foreach ($functions as $function) {
    $path = 'inc/options/' . $function . '.php';
    require get_theme_file_path($path);
}

//使用Font Awesome 4
add_filter('csf_fa4', '__return_true');

function my_custom_icons($icons)
{
    $icons[] = array(
        'title' => '主题内置SVG图标',
        'icons' => array(
            'zibsvg-like',
            'zibsvg-view',
            'zibsvg-comment',
            'zibsvg-time',
            'zibsvg-search',
            'zibsvg-money',
            'zibsvg-right',
            'zibsvg-left',
            'zibsvg-reply',
            'zibsvg-circle',
            'zibsvg-close',
            'zibsvg-add',
            'zibsvg-add-ring',
            'zibsvg-post',
            'zibsvg-posts',
            'zibsvg-huo',
            'zibsvg-favorite',
            'zibsvg-menu',
            'zibsvg-d-qq',
            'zibsvg-d-weibo',
            'zibsvg-d-wechat',
            'zibsvg-d-email',
            'zibsvg-user',
            'zibsvg-theme',
            'zibsvg-signout',
            'zibsvg-set',
            'zibsvg-signup',
            'zibsvg-user_rp',
            'zibsvg-pan_baidu',
            'zibsvg-lanzou',
            'zibsvg-onedrive',
            'zibsvg-tianyi',
            'zibsvg-menu_2',
            'zibsvg-alipay',
            'zibsvg-baidu',
            'zibsvg-dingtalk',
            'zibsvg-huawei',
            'zibsvg-gitee',
            'zibsvg-comment-fill',
            'zibsvg-private',
            'zibsvg-hot-fill',
            'zibsvg-hot',
            'zibsvg-topping',
            'zibsvg-topic',
            'zibsvg-plate-fill',
            'zibsvg-extra-points',
            'zibsvg-deduct-points',
            'zibsvg-points',
            'zibsvg-tags',
            'zibsvg-user-auth',
            'zibsvg-vip_1',
            'zibsvg-vip_2',
            'zibsvg-qzone-color',
            'zibsvg-qq-color',
            'zibsvg-weibo-color',
            'zibsvg-poster-color',
            'zibsvg-copy-color',
            'zibsvg-user-color',
            'zibsvg-user-color-2',
            'zibsvg-add-color',
            'zibsvg-home-color',
            'zibsvg-money-color',
            'zibsvg-order-color',
            'zibsvg-gift-color',
            'zibsvg-security-color',
            'zibsvg-trend-color',
            'zibsvg-msg-color',
            'zibsvg-tag-color',
            'zibsvg-comment-color',
            'zibsvg-wallet-color',
            'zibsvg-money-color-2',
            'zibsvg-merchant-color',
            'zibsvg-medal-color',
            'zibsvg-points-color',
            'zibsvg-book-color',
            'zibsvg-ontop-color',
        ),
    );

    $icons = array_reverse($icons);
    return $icons;
}
add_filter('csf_field_icon_add_icons', 'my_custom_icons');

//定义文件夹
function csf_custom_csf_override()
{
    return 'inc/csf-framework';
}
add_filter('csf_override', 'csf_custom_csf_override');

//自定义css、js
function csf_add_custom_wp_enqueue()
{
    // Style
    wp_enqueue_style('csf_custom_css', get_template_directory_uri() . '/inc/csf-framework/assets/css/style.min.css', array(), THEME_VERSION);
    // Script
    wp_enqueue_script('csf_custom_js', get_template_directory_uri() . '/inc/csf-framework/assets/js/main.min.js', array('jquery'), THEME_VERSION);
}
add_action('csf_enqueue', 'csf_add_custom_wp_enqueue');

//获取主题设置链接
function zib_get_admin_csf_url($tab = '')
{
    $tab                = trim(strip_tags($tab));
    $tab_array          = explode("/", $tab);
    $tab_array_sanitize = array();
    foreach ($tab_array as $tab_i) {
        $tab_array_sanitize[] = sanitize_title($tab_i);
    }
    $tab_attr = esc_attr(implode("/", $tab_array_sanitize));
    $url      = add_query_arg('page', 'zibll_options', admin_url('admin.php'));
    $url      = $tab ? $url . '#tab=' . $tab_attr : $url;
    return esc_url($url);
}

// 获取及设置主题配置参数
function _pz($name, $default = false, $subname = '')
{
    //声明静态变量，加速获取
    static $options = null;
    if ($options === null) {
        $options = get_option('zibll_options');
    }

    if (isset($options[$name])) {
        if ($subname) {
            return isset($options[$name][$subname]) ? $options[$name][$subname] : $default;
        } else {
            return $options[$name];
        }
    }
    return $default;
}

function _spz($name, $value)
{
    $get_option        = get_option('zibll_options');
    $get_option        = is_array($get_option) ? $get_option : array();
    $get_option[$name] = $value;
    return update_option('zibll_options', $get_option);
}

//获取及设置压缩后的posts_meta
if (!function_exists('of_get_posts_meta')) {
    function of_get_posts_meta($name, $key, $default = false, $post_id = '')
    {
        global $post;
        $post_id  = $post_id ? $post_id : $post->ID;
        $get_mate = get_post_meta($post_id, $name, true);
        if (isset($get_mate[$key])) {
            return $get_mate[$key];
        }
        return $default;
    }
}

if (!function_exists('of_set_posts_meta')) {
    function of_set_posts_meta($post_id = '', $name, $key, $value)
    {
        if (!$name) {
            return false;
        }
        global $post;
        $post_id        = $post_id ? $post_id : $post->ID;
        $get_mate       = get_post_meta($post_id, $name, true);
        $get_mate       = (array) $get_mate;
        $get_mate[$key] = $value;
        return update_post_meta($post_id, $name, $get_mate);
    }
}

//备份主题数据
function zib_options_backup($type = '自动备份')
{
    $prefix  = 'zibll_options';
    $options = get_option($prefix);

    $options_backup = get_option($prefix . '_backup');
    if (!$options_backup) {
        $options_backup = array();
    }

    $time                  = current_time('Y-m-d H:i:s');
    $options_backup[$time] = array(
        'time' => $time,
        'type' => $type,
        'data' => $options,
    );
    return update_option($prefix . '_backup', $options_backup);
}

function zib_csf_reset_to_backup()
{
    zib_options_backup('重置全部 自动备份');
}
add_action('csf_zibll_options_reset_before', 'zib_csf_reset_to_backup');

function zib_csf_reset_section_to_backup()
{
    zib_options_backup('重置选区 自动备份');
}
add_action('csf_zibll_options_reset_section_before', 'zib_csf_reset_section_to_backup');

//主题更新自动备份
function zib_new_zibll_to_backup()
{
    $prefix         = 'zibll_options';
    $options_backup = get_option($prefix . '_backup');
    $time           = false;

    if ($options_backup) {
        $options_backup = array_reverse($options_backup);
        foreach ($options_backup as $key => $val) {
            if ('更新主题 自动备份' == $val['type']) {
                $time = $key;
                break;
            }
        }
    }
    if (!$time || (floor((strtotime(current_time("Y-m-d H:i:s")) - strtotime($time)) / 3600) > 240)) {
        zib_options_backup('更新主题 自动备份');
        //更新主题刷新所有缓存
        wp_cache_flush();
    }
}
add_action('zibll_update_notices', 'zib_new_zibll_to_backup');

function zib_csf_save_section_to_backup()
{
    $prefix         = 'zibll_options';
    $options_backup = get_option($prefix . '_backup');
    $time           = false;

    if ($options_backup) {
        $options_backup = array_reverse($options_backup);
        foreach ($options_backup as $key => $val) {
            if ('定期自动备份' == $val['type']) {
                $time = $key;
                break;
            }
        }
    }
    if (!$time || (floor((strtotime(current_time("Y-m-d H:i:s")) - strtotime($time)) / 3600) > 600)) {
        zib_options_backup('定期自动备份');
    }
}
add_action('csf_zibll_options_saved', 'zib_csf_save_section_to_backup');

//老数据转新数据
function zib_pz_to_csf()
{
    $prefix = 'zibll_options';
    //老板数据迁移
    if (!get_option($prefix)) {
        $old_db = get_option('Zibll');
        if (!$old_db || !is_array($old_db)) {
            return;
        }

        //转换参数
        //切换主题按钮
        $old_db['theme_mode_button'] = !empty($old_db['theme_mode_button']) ? array('pc_nav', 'm_menu') : array();
        //通知按钮
        $old_db['system_notice_button'] = array();
        for ($i = 1; $i <= 2; $i++) {
            if (!empty($old_db['system_notice_b' . $i . '_t']) && !empty($old_db['system_notice_b' . $i . '_h'])) {
                $old_db['system_notice_button'][] = array(
                    'link'  => array(
                        'url'  => @$old_db['system_notice_b' . $i . '_h'],
                        'text' => @$old_db['system_notice_b' . $i . '_t'],
                    ),
                    'class' => @$old_db['system_notice_b' . $i . '_c'],
                );
            }
        }
        //底部二维码
        $old_db['footer_mini_img'] = array();
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($old_db['footer_mini_img_' . $i])) {
                $old_db['footer_mini_img'][] = array(
                    'image' => @$old_db['footer_mini_img_' . $i],
                    'text'  => @$old_db['footer_mini_img_t_' . $i],
                );
            }
        }
        //首页多栏目
        @$home_list_num        = $old_db['home_list_num'] ? $old_db['home_list_num'] : 4;
        @$old_db['home_lists'] = array();
        for ($i = 2; $i <= $home_list_num; $i++) {
            if (!empty($old_db['home_list' . $i . '_s']) && !empty($old_db['home_list' . $i . '_cat'])) {
                $old_db['home_lists'][] = array(
                    'title'   => @$old_db['home_list' . $i . '_t'],
                    'term_id' => @$old_db['home_list' . $i . '_cat'],
                );
            }
        }
        //老多选转新多选
        $multicheck_kay = array(
            'header_search_more_cat_obj',
            'post_article_cat',
            'pay_rebate_user_s',
            'pay_rebate_user_s_1',
            'pay_rebate_user_s_2',
        );

        foreach ($multicheck_kay as $key) {
            $old_db[$key] = array_keys($old_db[$key], true);
        }

        //排序方式
        @$orderby                                      = array_keys($old_db['option_list_orderby'], true);
        @$old_db['cat_orderby_option']['lists']        = $orderby;
        @$old_db['tag_orderby_option']['lists']        = $orderby;
        @$old_db['topics_orderby_option']['lists']     = $orderby;
        @$old_db['home_list1_orderby_option']['lists'] = $orderby;

        //ajax菜单
        $ajax_list_page = array(
            'cat', 'topics', 'tag',
        );
        //ajax按钮列表
        $ajax_but_cats   = array_keys($old_db['option_list_cats'], true);
        $ajax_but_topics = array_keys($old_db['option_list_topics'], true);
        $ajax_but_tags   = array_keys($old_db['option_list_tags'], true);

        foreach ($ajax_list_page as $page) {
            @$old_db['ajax_list_' . $page . '_cat']                    = $old_db['option_list_' . $page . '_cat'];
            @$old_db['ajax_list_' . $page . '_topics']                 = $old_db['option_list_' . $page . '_top'];
            @$old_db['ajax_list_' . $page . '_tag']                    = $old_db['option_list_' . $page . '_tag'];
            @$old_db[$page . '_orderby_s']                             = $old_db['option_list_' . $page . '_orderby'];
            @$old_db['ajax_list_option_' . $page . '_cat']['lists']    = $ajax_but_cats;
            @$old_db['ajax_list_option_' . $page . '_topics']['lists'] = $ajax_but_topics;
            @$old_db['ajax_list_option_' . $page . '_tag']['lists']    = $ajax_but_tags;
        }

        //封面图
        @$old_db['cat_default_cover']    = $old_db['page_cover_img'];
        @$old_db['topics_default_cover'] = $old_db['page_cover_img'];
        @$old_db['tag_default_cover']    = $old_db['page_cover_img'];

        //列表卡片模式-分类id
        @$old_db['list_card_cat'] = array_keys($old_db['list_card'], true);

        //列表多图模式
        if ($old_db['mult_thumb']) {
            @$old_db['mult_thumb_cat'] = array($old_db['mult_thumb_cat']);
        } else {
            @$old_db['mult_thumb_cat'] = '';
        }

        //文章幻灯片封面
        @$old_db['article_slide_cover_option']['button']     = $old_db['article_cover_slide_show_button'];
        @$old_db['article_slide_cover_option']['pagination'] = $old_db['article_cover_slide_show_pagination'];
        @$old_db['article_slide_cover_option']['effect']     = $old_db['article_cover_slide_effect'];
        @$old_db['article_slide_cover_option']['pc_height']  = $old_db['article_cover_slide_height'];
        @$old_db['article_slide_cover_option']['m_height']   = $old_db['article_cover_slide_height_m'];

        //用，号分割数据
        @$old_db['home_exclude_posts'] = preg_split("/,|，|\s|\n/", $old_db['home_exclude_posts']);
        @$old_db['home_exclude_cats']  = preg_split("/,|，|\s|\n/", $old_db['home_exclude_cats']);

        //社交登录
        $oauth_type = array(
            'qq', 'weixin', 'weibo', 'github', 'gitee', 'baidu', 'alipay',
        );
        foreach ($oauth_type as $oauth) {
            @$old_db['oauth_' . $oauth . '_option']['appid']  = $old_db['oauth_' . $oauth . '_appid'];
            @$old_db['oauth_' . $oauth . '_option']['appkey'] = !empty($old_db['oauth_' . $oauth . '_appkey']) ? $old_db['oauth_' . $oauth . '_appkey'] : '';
        }
        @$old_db['oauth_alipay_option']['appkrivatekey'] = $old_db['oauth_alipay_appkrivatekey'];

        //会员产品
        for ($i = 1; $i <= 2; $i++) {
            @$old_db['vip_opt']['pay_user_vip_' . $i . '_equity'] = $old_db['pay_user_vip_' . $i . '_equity'];
            for ($x = 0; $x <= 3; $x++) {
                if ($old_db['vip_product_' . $i . '_' . ($x + 1) . '_s']) {
                    @$old_db['vip_opt']['vip_' . $i . '_product'][$x]['price']      = $old_db['vip_product_' . $i . '_' . ($x + 1) . '_price'];
                    @$old_db['vip_opt']['vip_' . $i . '_product'][$x]['show_price'] = $old_db['vip_product_' . $i . '_' . ($x + 1) . '_show_price'];
                    @$old_db['vip_opt']['vip_' . $i . '_product'][$x]['tag']        = $old_db['vip_product_' . $i . '_' . ($x + 1) . '_tag'];
                    @$old_db['vip_opt']['vip_' . $i . '_product'][$x]['time']       = $old_db['vip_product_' . $i . '_' . ($x + 1) . '_time'];
                }
            }
        }

        //收款接口
        @$old_db['official_alipay']['appid']         = $old_db['official_alipay_appid'];
        @$old_db['official_alipay']['privatekey']    = $old_db['official_alipay_privatekey'];
        @$old_db['official_alipay']['publickey']     = $old_db['official_alipay_publickey'];
        @$old_db['official_alipay']['webappid']      = $old_db['enterprise_alipay_appid'];
        @$old_db['official_alipay']['webprivatekey'] = $old_db['enterprise_alipay_privatekey'];
        @$old_db['official_alipay']['h5']            = $old_db['enterprise_alipay_h5'];

        @$old_db['xunhupay']['wechat_appid']     = $old_db['xunhupay_wechat_appid'];
        @$old_db['xunhupay']['wechat_appsecret'] = $old_db['xunhupay_wechat_appsecret'];
        @$old_db['xunhupay']['alipay_appid']     = $old_db['xunhupay_alipay_appid'];
        @$old_db['xunhupay']['alipay_appsecret'] = $old_db['xunhupay_alipay_appsecret'];

        @$old_db['official_wechat']['merchantid'] = $old_db['official_wechat_merchantid'];
        @$old_db['official_wechat']['appid']      = $old_db['official_wechat_appid'];
        @$old_db['official_wechat']['key']        = $old_db['official_wechat_appkey'];
        @$old_db['official_wechat']['jsapi']      = $old_db['official_wechat_jsapi'];
        @$old_db['official_wechat']['h5']         = $old_db['official_wechat_h5'];

        @$old_db['codepay']['id']    = $old_db['codepay_id'];
        @$old_db['codepay']['key']   = $old_db['codepay_key'];
        @$old_db['codepay']['token'] = $old_db['codepay_token'];

        @$old_db['payjs']['mchid'] = $old_db['payjs_mchid'];
        @$old_db['payjs']['key']   = $old_db['payjs_key'];
        @$old_db['xhpay']['mchid'] = $old_db['xhpay_mchid'];
        @$old_db['xhpay']['key']   = $old_db['xhpay_key'];

        //执行数据更新
        update_option($prefix, $old_db);
    }
}

//后台重新导入老数据
function zib_admin_option_to_csf()
{
    //管理员权限判断
    if (!is_super_admin()) {
        echo json_encode(array('error' => true, 'msg' => '权限不足，请用管理员账号登录！'));
        exit();
    }

    //二次确认操作
    if (empty($_COOKIE['option_to_csf'])) {
        echo (json_encode(array('error' => 1, 'msg' => '导入老数据之后会完全覆盖现有数据，此操作不可恢复！请再次确认！')));
        //设置浏览器缓存限制提交的间隔时间
        $expire = time() + 10;
        setcookie('option_to_csf', time(), $expire, '/', '', false);
        exit();
    }

    //执行删除数据
    delete_option('zibll_options');
    //执行导入老数据数据
    zib_pz_to_csf();

    echo json_encode(array('error' => 0, 'reload' => true, 'msg' => '配置数据已导入，请刷新页面'));
    exit();
}
add_action('wp_ajax_option_to_csf', 'zib_admin_option_to_csf');

//保存主题时候保存必要的wp设置
function zib_save_zibll_wp_options()
{
    update_option('default_comments_page', 'oldest');
    update_option('comment_order', 'asc');

    $theme_data = wp_get_theme();
    update_option('Zibll_version', $theme_data['Version']);

    /**
     * 刷新固定连接
     */
    flush_rewrite_rules();
}
add_action("csf_zibll_options_saved", 'zib_save_zibll_wp_options');

//主题更新后发送通知
function zib_notice_update()
{
    $version    = get_option('Zibll_version');
    $theme_data = wp_get_theme();
    if ($version && version_compare($version, $theme_data['Version'], '<')) {
        $up = get_option('zibll_new_version');
        do_action('zibll_update_notices', $theme_data['Version']);
        $up_desc = !empty($up['update_description']) ? '<p>' . $up['update_description'] . '</p>' : '';
        $con     = '<div class="notice notice-success is-dismissible">
				<h2 style="color:#fd4c73;"><i class="fa fa-heart fa-fw"></i> 恭喜您！Zibll子比主题已更新</h2>
                ' . $up_desc . '
                <p>欢迎使用zibll子比主题V6，使用全新V6请务必先配置好伪静态和固定链接，否则会出现404错误！<a target="_bank" style="color:#217ff9;" href="https://www.zibll.com/3025.html">查看官网教程</a></p>
                <p><a target="_bank" style="color:#217ff9;box-shadow:none !important;" href="https://www.zibll.com/375.html">看一下更新了哪些新功能？</a></p>
                <p>更新主题请务必<b style="color:#ff321d;">清空缓存、刷新CDN</b>，再保存一下<a href="' . zib_get_admin_csf_url() . '">主题设置</a>，保存主题设置后此通知会自动关闭</p>
                <p><a class="button" style="margin: 2px;" href="' . zib_get_admin_csf_url() . '">体验新功能</a><a class="button" style="margin: 2px;" href="' . zib_get_admin_csf_url('文档更新') . '">查看主题文档</a><a target="_blank" class="button" style="margin: 2px;" href="https://www.zibll.com/375.html">查看更新日志</a></p>
			</div>';
        echo $con;
    }
}
add_action('admin_notices', 'zib_notice_update');

//主题首次新安装通知
function zib_notice_new_install()
{
    $version = get_option('Zibll_version');
    if (!$version) {
        $theme_version = wp_get_theme()['Version'];
        do_action('zibll_new_install_notices', $theme_version);

        $con = '<div class="notice notice-success is-dismissible">
				<h2 style="color:#fd4c73;"><i class="fa fa-heart fa-fw"></i> 感谢您使用子比主题</h2>
                <p>使用zibll子比主题请务必先配置好伪静态和固定链接，否则会出现404错误！<a target="_bank" style="color:#217ff9;" href="https://www.zibll.com/3025.html">查看官网教程</a></p>
                <p><a class="button" style="margin: 2px;" href="' . zib_get_admin_csf_url('文档更新') . '">查看主题文档</a><a target="_blank" class="button" style="margin: 2px;" href="https://www.zibll.com/375.html">查看更新日志</a></p>
			</div>';
        echo $con;
    }
}
add_action('admin_notices', 'zib_notice_update');

//伪静态检测通知
function zib_notice_permalink_structure()
{
    if (!get_option('permalink_structure')) {
        $con = '<div class="notice notice-error is-dismissible">
            <h2 style="color:#f73d3f;"><i class="dashicons-before dashicons-admin-settings"></i> 请完成固定链接设置</h2>
            <p>您的网站还未完成固定链接配置，部分页面会出现404错误，请先完成伪静态和固定链接设置</p>
            <p><a class="button button-primary" style="margin: 2px;" href="' . admin_url('options-permalink.php') . '">立即设置</a><a target="_blank" class="button" style="margin: 2px;" href="https://www.zibll.com/3025.html">查看官网教程</a></p>
        </div>';
        echo $con;
    }
}
add_action('admin_notices', 'zib_notice_permalink_structure');

//自动授权
function zib_auto_aut()
{
    $auto_key = 'autoaut_63';
    $option   = (int) get_option($auto_key);
    $index    = 2; //自动尝试的次数

    if (aut_required() && $option < $index) {
        $ajax_url = admin_url('admin-ajax.php');
        $con      = '<script type="text/javascript">
        (function ($, window, document) {
            $(document).ready(function ($) {
                console.log("开始自动授权验证");
                var html = \'<div><div style="position: fixed;top: 0;right: 0;z-index: 9999999;width: 100%;height: 100%;background: rgba(0, 0, 0, 0.2);"></div><div style="position: fixed;top: 6em;right: -1px;z-index: 10000000;"><div style="background: linear-gradient(135deg,rgb(255, 163, 180),rgb(253, 54, 110));margin-bottom: .6em;color: #fff;padding: 1em 3em;box-shadow: -3px 3px 15px rgba(0, 0, 0, 0.2);" class="a-bg"><i><i class="fa fa-spinner fa-spin fa-fw" style="position: absolute;left: 10px;top: 24px;font-size: 20px;"></i></i><div style="font-size: 1.1em;margin-bottom: 6px;">感谢您使用子比主题</div><div class="a-text">正在为您自动授权，请稍后...</div></div></div></div>\';
                var _html = $(html);
                $("body").append(_html);
                $.post("' . $ajax_url . '", {
                    action: "admin_curl_aut"
                }, function (n) {
                    var msg = n.msg;
                    var error = n.error;
                    _html.find(".a-text").html(msg);
                    if (!error) {
                        _html.find(".a-bg").css("background", "linear-gradient(135deg,rgb(124, 191, 251),rgb(10, 105, 227))");
                    }
                    setTimeout(function () {
                        location.reload();
                    }, 100);
                }, "json");
            });
        })(jQuery, window, document);
        </script>';
        echo $con;
        update_option($auto_key, $option + 1);
    }
}
add_action('admin_notices', 'zib_auto_aut', 99);
