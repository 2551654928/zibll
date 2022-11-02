<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:38
 * @LastEditTime: 2022-11-02 20:34:50
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

function zib_seo_image()
{
    if (!_pz('seo_list_img_s')) {
        return;
    }

    $obj_id = get_queried_object_id();
    $pic    = '';
    if (is_tax() && $obj_id) {
        $pic = zib_get_taxonomy_img_url($obj_id, 'full');
    }

    if (is_single() && $obj_id) {
        $pic = zib_post_thumbnail('full', '', true, $obj_id);
    }

    if (is_author()) {
        global $wp_query;
        $curauth = $wp_query->get_queried_object();
        if (!empty($curauth->ID)) {
            $pic = get_user_meta($curauth->ID, 'custom_avatar', true);
        }
    }

    if (!$pic) {
        $pic = _pz('seo_list_img');
    }

    if (!$pic) {
        return;
    }

    echo '<div style="position: fixed;z-index: -999;left: -5000%;"><img src="' . $pic . '" alt="' . zib_title(false) . '"></div>';
}

function zib_header()
{
    $layout      = _pz('header_layout', '2');
    $m_nav_align = _pz('mobile_navbar_align', 'right');
    $m_layout    = _pz('mobile_header_layout', 'center');
    $show_slide  = zib_header_slide_is_show();
?>
    <header class="header header-layout-<?php echo $layout;
                                        echo $show_slide ? ' show-slide' : ''; ?>">
        <nav class="navbar navbar-top <?php echo $m_layout; ?>">
            <div class="container-fluid container-header">
                <?php zib_navbar_header(); ?>
                <div class="collapse navbar-collapse">
                    <?php
                    if (3 != $layout) {
                        zib_menu_items();
                    }
                    if (2 == $layout) {
                        zib_get_menu_search();
                    }
                    zib_menu_button($layout);
                    if (3 == $layout) {
                        echo '<div class="navbar-right">';
                        zib_menu_items();
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="mobile-header">
        <nav <?php echo $m_nav_align != 'top' ? 'mini-touch="mobile-nav" touch-direction="' . $m_nav_align . '"' : ''; ?> class="mobile-navbar visible-xs-block scroll-y mini-scrollbar <?php echo $m_nav_align; ?>">
            <?php
            if (!_pz('nav_fixed', true)) {
                zib_navbar_header();
            }
            zib_nav_mobile();
            if (function_exists('dynamic_sidebar')) {
                echo '<div class="mobile-nav-widget">';
                dynamic_sidebar('mobile_nav_fluid');
                echo '</div>';
            }
            ?>
        </nav>
        <div class="fixed-body" data-close=".mobile-navbar"></div>
    </div>
    <?php if ($show_slide) {
        zib_header_slide();
    } ?>
<?php }

function zib_menu_button($layout = 1)
{
    $li      = '';
    $button  = '';
    $user_id = get_current_user_id();

    if (_pz('nav_newposts')) {

        $nav_newposts_btns = _pz('nav_newposts_btns', array('post'));
        if ($nav_newposts_btns) {
            $show_class = 'but nowave ' . _pz('nav_newposts_class', 'jb-blue');
            $show_class .= _pz('nav_newposts_radius', true) ? ' radius' : '';
            $new_add_btns = zib_get_new_add_btns($nav_newposts_btns, $show_class, (_pz('nav_newposts_text') ?: '发布'));
            $button .= $new_add_btns;
        }
    }

    if (_pz('nav_pay_vip', true) && !zib_is_close_sign() && (_pz('pay_user_vip_1_s', true) || _pz('pay_user_vip_2_s', true))) {
        $hover_show = '<div class="sub-menu hover-show-con sub-vip-card">' . zibpay_get_vip_card(1) . zibpay_get_vip_card(2) . '</div>';
        if ($user_id) {
            if (!zib_get_user_vip_level($user_id)) {
                $vip_button = '<a class="pay-vip but jb-red radius payvip-icon ml10" href="javascript:;">' . zib_get_svg('vip_1', '0 0 1024 1024', 'em12 mr3') . '开通会员</a>';
                $button .= '<span class="hover-show inline-block">' . $vip_button . $hover_show . '</span>';
            }
        } else {
            $vip_button = '<a class="signin-loader but jb-red radius payvip-icon ml10" href="javascript:;">' . zib_get_svg('vip_1', '0 0 1024 1024', 'em12 mr3') . '开通会员</a>';
            $button .= '<span class="hover-show inline-block">' . $vip_button . $hover_show . '</span>';
        }
    }

    if ($button) {
        $button = '<div class="navbar-form navbar-right navbar-but">' . $button . '</div>';
    }

    $radius_but = in_array('pc_nav', zib_get_theme_mode_button_positions()) ? '<a href="javascript:;" class="toggle-theme toggle-radius"><i class="fa fa-toggle-theme"></i></a>' : '';
    $radius_but = apply_filters('zib_nav_radius_button', $radius_but, $user_id);

    if ($radius_but) {
        $button .= '<div class="navbar-form navbar-right">' . $radius_but . '</div>';
    }

    $sign_but = '';
    $user_sub = '';
    if (!zib_is_close_sign(true)) {
        $user_sub = '<div class="padding-10">' . zib_header_user_box() . '</div>';
        if (2 == $layout) {
            $sign_but = '<li><a href="javascript:;" class="signin-loader">登录</a></li>';
            $sign_but .= !zib_is_close_signup() ? '<li><a href="javascript:;" class="signup-loader">注册</a></li>' : '';

            if ($user_id) {
                $avatar   = zib_get_data_avatar($user_id);
                $sign_but = '<li><a href="javascript:;" class="navbar-avatar">' . $avatar . '</a><ul class="sub-menu">' . $user_sub . '</ul></li>';
            }
        } else {
            $sign_but = '<li><a href="javascript:;" class="btn' . ($user_id ? '' : ' signin-loader') . '">' . zib_get_svg('user', '50 0 924 924') . '</a>
							<ul class="sub-menu">
							' . $user_sub . '
							</ul>
						</li>';
        }
    }

    $search_but = (2 == $layout) ? '' : '<li class="relative">' . zib_get_search_link('class=btn nav-search-btn') . '</li>';
    $right_but  = '<div class="navbar-form navbar-right' . (!$user_id && 2 == $layout ? ' navbar-text' : '') . '">
					<ul class="list-inline splitters relative">
						' . $sign_but . $search_but . '
					</ul>
				</div>';

    $html = '<div class="navbar-form navbar-right hide show-nav-but" style="margin-right:-20px;"><a data-toggle-class data-target=".nav.navbar-nav" href="javascript:;" class="but">' . zib_get_svg('menu_2', '0 0 1024 1024', '') . '</a></div>';

    if (3 == $layout) {
        $html .= $right_but . $button;
    } else {
        $html .= $button . $right_but;
    }
    echo $html;
}

function zib_header_user_box()
{
    static $html = null;
    if (null != $html) {
        return $html;
    }

    if (zib_is_close_sign(true)) {
        $html = '';
        return $html;
    }

    $user_id = get_current_user_id();
    $con     = '';
    if ($user_id) {
        $display_name = zib_get_user_name("id=$user_id");
        $avatar       = zib_get_avatar_box($user_id, 'avatar-img', true, false);
        $desc         = get_user_desc($user_id);
        $payvip       = zib_get_header_payvip_icon($user_id);
        $msg_icon     = zibmsg_get_user_icon($user_id, 'abs-right');
        $items        = zib_get_user_badges($user_id);
        $wallet       = zib_get_user_wallet_mini_box($user_id);
        $href         = '<div class="flex jsa header-user-href">';
        $href .= '<a href="' . zib_get_user_center_url() . '" ><div class="badg mb6 toggle-radius c-blue">' . zib_get_svg('user', '50 0 924 924') . '</div><div class="c-blue">用户中心</div></a>';

        $user_newposts = _pz('nav_user_newposts_btn', 'post');
        if ($user_newposts) {
            $new_add_btns = zib_get_new_add_btns([$user_newposts], 'start-new-posts', '<div class="badg mb6 toggle-radius c-green"><i class="fa fa-fw fa-pencil-square-o"></i></div><div class="c-green">' . _pz('nav_user_newposts_btn_text', '发布文章') . '</div>');
            $href .= $new_add_btns;
        }

        $href .= '<a href="javascript:;" data-toggle="modal" data-target="#modal_signout" ><div class="badg mb6 toggle-radius c-red">' . zib_get_svg('signout') . '</div><div class="c-red">退出登录</div></a>';
        $href .= '</div>';

        if (is_super_admin()) {
            $href .= '<div class="flex jsa header-user-href">';
            $href .= '<a target="_blank" href="' . zib_get_admin_csf_url() . '"><div class="badg mb6 toggle-radius c-yellow">' . zib_get_svg('theme') . '</div><div class="c-yellow">主题设置</div></a>';
            $href .= '<a target="_blank" href="' . zib_get_customize_widgets_url() . '" ><div class="badg mb6 toggle-radius c-yellow"><i class="fa fa-pie-chart"></i></div><div class="c-yellow">模块配置</div></a>';
            $href .= '<a target="_blank" href="' . admin_url() . '" ><div class="badg mb6 toggle-radius c-yellow">' . zib_get_svg('set') . '</div><div class="c-yellow">后台管理</div></a>';
            $href .= '</div>';
        }

        $checkin_btn = '';
        if (_pz('checkin_s') && _pz('checkin_header_user_show')) {
            $class = _pz('checkin_header_user_option', 'c-yellow', 'class');
            $text  = _pz('checkin_header_user_option', '签到领取今日奖励', 'text');

            $checkin_btn = zib_get_user_checkin_btn('but block ' . $class, '<i class="fa fa-calendar-check-o"></i> ' . $text, '<i class="fa fa-calendar-check-o"></i> 今日已签到');
        }

        $con = '<div class="user-info flex ac relative">';
        $con .= $avatar;
        $con .= '<div class="user-right flex flex1 ac jsb ml10">';
        $con .= '<div class="flex1" style="max-width: calc(100% - 40px);"><b>' . $display_name . '</b><div class="px12 muted-2-color text-ellipsis">' . $desc . '</div></div>';
        $con .= '</div>';
        $con .= $msg_icon;
        $con .= '</div>';

        $con .= $payvip ? '<div class="mt10 em09" style="padding:2px;">' . $payvip . '</div>' : '';
        $con .= $checkin_btn ? '<div class="mt6 em09" style="padding:2px;">' . $checkin_btn . '</div>' : '';
        $con .= $wallet ? '<div class="mt6" style="padding:2px;">' . $wallet . '</div>' : '';
        $con .= '<div class="em09 author-tag mb10 mt6 flex jc">' . $items . '</div>';
        $con .= '<div class="relative opacity5"><i class="line-form-line"></i> </div>';
        $con .= '<div class="mt10 text-center">' . $href . '</div>';
    } else {
        $href = ((_pz('pay_user_vip_1_s', true) || _pz('pay_user_vip_2_s', true)) && _pz('nav_user_pay_vip', true)) ? '<div><a class="em09 signin-loader but jb-red radius4 payvip-icon btn-block mt10" href="javascript:;">' . zib_get_svg('vip_1', '0 0 1024 1024', 'em12 mr10') . '开通会员 尊享会员权益</a></div>' : '';

        $href .= '<div class="flex jsa header-user-href">';
        $href .= '<a href="javascript:;" class="signin-loader"><div class="badg mb6 toggle-radius c-blue">' . zib_get_svg('user', '50 0 924 924') . '</div><div class="c-blue">登录</div></a>';
        $href .= !zib_is_close_signup() ? '<a href="javascript:;" class="signup-loader"><div class="badg mb6 toggle-radius c-green">' . zib_get_svg('signup') . '</div><div class="c-green">注册</div></a>' : '';
        $href .= '<a target="_blank" href="' . add_query_arg('redirect_to', urlencode(zib_get_current_url()), zib_get_sign_url('resetpassword')) . '"><div class="badg mb6 toggle-radius c-purple">' . zib_get_svg('user_rp') . '</div><div class="c-purple">找回密码</div></a>';
        $href .= '</div>';

        $con .= '<div class="text-center">' . $href . '</div>';
        $ocial_login = zib_social_login(false);
        if ($ocial_login) {
            $con .= '<p class="social-separator separator muted-3-color em09 mt10">快速登录</p>';
            $con .= '<div class="social_loginbar">';
            $con .= $ocial_login;
            $con .= '</div>';
        }
    }

    $html = '<div class="sub-user-box">' . $con . '</div>';
    return $html;
}

//购买会员的按钮
function zib_get_header_payvip_icon($user_id = 0)
{
    if (!$user_id || (!_pz('pay_user_vip_1_s', true) && !_pz('pay_user_vip_2_s', true))) {
        return;
    }

    $vip_level = zib_get_user_vip_level($user_id);

    if ($vip_level) {
        return '<span class="radius4 payvip-icon btn-block text-center vipbg-v2 ' . $vip_level . '">' . zibpay_get_vip_card_icon($vip_level, 'em12 mr6') . '<span>' . _pz('pay_user_vip_' . $vip_level . '_name') . '</span>' . '<span class="ml10 badg jb-yellow vip-expdate-tag">' . zib_get_svg('time', null, 'mr3') . zib_get_user_vip_exp_date_text($user_id) . '</span></span>';
    } elseif (_pz('nav_user_pay_vip', true)) {
        $button = '<a class="pay-vip but radius4 payvip-icon btn-block" href="javascript:;">' . zib_get_svg('vip_1', '0 0 1024 1024', 'em12 mr6') . '开通会员 尊享会员权益</a>';
        return $button;
    }
    return '';
}

function zib_get_menu_search()
{
    if (is_search()) {
        return;
    }

    $more_cats = _pz('header_search_more_cat_obj', array());

    $args = array(
        'show_form'     => false,
        'show_keywords' => _pz('search_popular_key', true),
        'show_history'  => _pz('search_history', true),
        'show_posts'    => _pz('search_posts'),
        'show_more_cat' => false,
    );

    echo '<form method="get" class="navbar-form navbar-left hover-show" action="' . esc_url(home_url('/')) . '">';
    echo '<div class="form-group relative dropdown">';
    echo '<input type="text" class="form-control search-input focus-show" name="s" placeholder="搜索内容">';
    echo '<div class="abs-right muted-3-color"><button type="submit" tabindex="3" class="null">' . zib_get_svg('search') . '</button></div>';

    $ajaxpager = array(
        'class'  => '',
        'loader' => ' ', // 加载动画
        'query'  => array(
            'action' => 'menu_search',
        ),
    );
    //    echo '<div class="dropdown-menu hover-show-con">';
    echo zib_get_remote_box($ajaxpager);
    //    echo '</div>';

    echo '</div>';
    echo '</form>';
}

function zib_menu_items($location = 'topmenu', $echo = true)
{
    $args = array(
        'container'       => false,
        'container_class' => 'nav navbar-nav',
        'echo'            => false,
        'fallback_cb'     => false,
        'items_wrap'      => '<ul class="nav navbar-nav">%3$s</ul>',
        'theme_location'  => $location,
    );
    if (!wp_is_mobile()) {
        $args['depth'] = 0;
    }

    $menu = wp_nav_menu($args);

    if (!$menu && is_super_admin()) {
        $menu = '<ul class="nav navbar-nav"><li><a href="' . admin_url('nav-menus.php') . '" class="loaderbt">添加导航菜单</a></li></ul>';
    }
    if ($echo) {
        echo $menu;
    } else {
        return $menu;
    }
}

function zib_navbar_header()
{
    $m_layout = _pz('mobile_header_layout', 'center');

    $t    = _pz('hometitle') ? _pz('hometitle') : get_bloginfo('name') . (get_bloginfo('description') ? _get_delimiter() . get_bloginfo('description') : '');
    $logo = '<a class="navbar-logo" href="' . get_bloginfo('url') . '" title="' . $t . '">'
        . zib_get_adaptive_theme_img(_pz('logo_src'), _pz('logo_src_dark'), $t) . '
			</a>';
    $button = '<button type="button" data-toggle-class data-target=".mobile-navbar" class="navbar-toggle">' . zib_get_svg('menu', '0 0 1024 1024', 'icon em12') . '</button>';
    if ('center' == $m_layout) {
        $button .= zib_get_search_link('class=navbar-toggle');
    }

    echo '<div class="navbar-header">
			<div class="navbar-brand">' . $logo . '</div>
			' . $button . '
		</div>';
}

function zib_nav_mobile($location = 'mobilemenu')
{
    $menu = '';
    $args = array(
        'container'      => false,
        'echo'           => false,
        'fallback_cb'    => false,
        'depth'          => 2,
        'items_wrap'     => '<ul class="mobile-menus theme-box">%3$s</ul>',
        'theme_location' => $location,
    );

    $m_layout = _pz('mobile_header_layout', 'center');

    $menu .= in_array('m_menu', zib_get_theme_mode_button_positions()) ? '<a href="javascript:;" class="toggle-theme toggle-radius"><i class="fa fa-toggle-theme"></i></a>' : '';

    if ('center' != $m_layout) {
        $menu .= zib_get_search_link('class=toggle-radius');
    }
    $wp_nav_menu = wp_nav_menu($args);

    if (!$wp_nav_menu) {
        $args['theme_location'] = 'topmenu';
        $wp_nav_menu            = wp_nav_menu($args);
    }

    if (!$wp_nav_menu && is_super_admin()) {
        $wp_nav_menu = '<ul class="mobile-menus theme-box"><li><a href="' . admin_url('nav-menus.php') . '" class="loaderbt">添加导航菜单</a></li></ul>';
    }
    $menu .= $wp_nav_menu;

    if (_pz('article_nav', true) && _pz('article_nav_mobile_nav_s', true)) {
        $menu .= '<div class="posts-nav-box" data-title="文章目录"></div>';
    }

    $sub = zib_header_user_box('mobile');

    echo $menu . $sub;
}

function zib_header_slide_is_show()
{
    global $wp_query;
    if (!isset($wp_query)) {
        return false;
    }
    $show_slide     = false;
    $wp_query_array = (array) $wp_query;

    //不是第一页不显示
    if (!empty($wp_query_array['query_vars']['paged'])) {
        return false;
    }

    $show_type = _pz('header_slider_show_type');
    if ('only_pc' == $show_type && wp_is_mobile()) {
        return false;
    }

    if ('only_sm' == $show_type && !wp_is_mobile()) {
        return false;
    }

    $header_slider_show = (array) _pz('header_slider_show');
    if ($header_slider_show) {
        foreach ($header_slider_show as $show) {
            if (!empty($wp_query_array['is_' . $show])) {
                $show_slide = true;

                //其他页面的部分页面
                if ($show === 'page') {
                    if (is_page_template('pages/user-sign.php')) {
                        $show_slide = false;
                    }
                }

                continue;
            }
        }
    }
    if (!$show_slide && is_tax('topics') && $header_slider_show && in_array('topics', $header_slider_show)) {
        $show_slide = true;
    }

    return $show_slide;
}
/**
 * @description: 导航栏侧边栏
 * @param {*}
 * @return {*}
 */
function zib_header_slide()
{

    $header_slider        = _pz('header_slider');
    $header_slider_option = _pz('header_slider_option');

    //判断配置是否为空
    if (!is_array($header_slider) || !is_array($header_slider_option) || empty($header_slider[0])) {
        return;
    }

    $header_slider_option['class']  = 'slide-index slide-header mb20';
    $header_slider_option['slides'] = $header_slider;
    $show_type                      = _pz('header_slider_show_type');
    if ('only_pc' == $show_type) {
        $header_slider_option['class'] .= ' hidden-xs';
    }

    if ('only_sm' == $show_type) {
        $header_slider_option['class'] .= ' visible-xs-block';
    }

    //echo json_encode($header_slider_option);
    zib_new_slider($header_slider_option);
}

//编辑菜单-清理菜单缓存
function zib_cache_delete_nav_menu()
{
    wp_cache_delete('mobilemenu', 'nav_menu');
    wp_cache_delete('topmenu', 'nav_menu');
}
add_action('wp_update_nav_menu_item', 'zib_cache_delete_nav_menu');
