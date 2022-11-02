<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:37
 * @LastEditTime: 2022-10-14 20:35:09
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//输出顶部cover
function zib_author_header()
{
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }

    $author_id  = $curauth->ID;
    $cover_btns = apply_filters('author_header_more_btn', '', $author_id);
    $cover_btns = $cover_btns ? '<div class="abs-center right-bottom padding-6 cover-btns">' . $cover_btns . '</div>' : '';
    $cover_html = '<div class="page-cover">' . get_user_cover_img($author_id) . '<div class="absolute linear-mask"></div>' . zib_get_author_header_metas($author_id) . $cover_btns . '</div>';
    $avatar     = zib_get_avatar_box($author_id, 'avatar-img', true, false);
    $name       = '<div class="em12 name">' . zib_get_user_name("id=$author_id&vip=1&auth=0&medal=0") . '</div>';
    $desc       = '<div class="mt6 desc muted-2-color">' . get_user_desc($author_id) . '</div>';
    $btns       = '<div class="header-btns flex0 flex ac">' . zib_get_author_header_btns($author_id) . '</div>';
    $identity   = is_super_admin($author_id) ? '<span class="badg c-red">管理员</span>' : '';
    $identity   = apply_filters('author_header_identity', $identity, $author_id);

    $info_html = '<div class="flex header-info relative hh">';
    $info_html .= '<div class="flex0 header-avatar">';
    $info_html .= $avatar;
    $info_html .= '</div>';
    $info_html .= '<div class="flex1">';
    $info_html .= $name;
    $info_html .= $identity ? '<div class="user-identity flex ac hh">' . $identity . '</div>' : '';
    $info_html .= $desc;
    $info_html .= '</div>';
    $info_html .= $btns;
    $info_html .= '</div>';

    $html = '<div class="author-header mb20 radius8 main-shadow main-bg full-widget-sm">';
    $html .= $cover_html;
    $html .= '<div class="header-content">';
    $html .= $info_html;
    $html .= '</div>';
    $html .= '</div>';
    echo $html;
}

//获取顶部的按钮
function zib_get_author_header_btns($author_id)
{
    if (!$author_id) {
        return;
    }

    $current_id = get_current_user_id();

    $btns = '';
    if ($current_id && $current_id == $author_id) {
        //登录用户就是作者
        $btns .= zib_get_user_center_link('but c-blue ml10 pw-1em radius', zib_get_svg('user') . '用户中心'); //用户中心

        if (_pz('message_s')) {
            $btns .= zibmsg_nav_radius_button($current_id, 'ml10');
        }
    } else {
        $btns .= zib_get_user_follow('but jb-pink ml10 pw-1em', $author_id); //关注
        if (_pz('private_s', true) && _pz('message_s', true)) {
            $btns .= Zib_Private::get_but($author_id, zib_get_svg('private') . '私信', 'but jb-blue ml10 pw-1em'); //私信
        } else {
            $btns .= zib_get_rewards_button($author_id, 'rewards but jb-blue ml10 pw-1em'); //打赏
        }
    }

    return $btns;
}

//获取顶部mates
function zib_get_author_header_metas($author_id)
{
    if (!$author_id) {
        return;
    }

    $like_n     = get_user_posts_meta_count($author_id, 'like');
    $view_n     = get_user_posts_meta_count($author_id, 'views');
    $followed_n = _cut_count(get_user_followed_count($author_id));

    $items = $view_n ? '<item><a data-toggle="tooltip" data-original-title="人气值 ' . $view_n . '">' . zib_get_svg('hot') . $view_n . '</a></item>' : '';
    $items .= $like_n ? '<item><a data-toggle="tooltip" data-original-title="获得' . $like_n . '个点赞">' . zib_get_svg('like') . $like_n . '</a></item>' : '';
    $items .= $followed_n ? '<item><a data-toggle="tooltip" data-original-title="共' . $followed_n . '个粉丝"><i class="fa fa-heart em09"></i>' . $followed_n . '</a></item>' : '';

    $metas = $items ? '<div class="flex ac single-metabox cover-meta abs-right"><div class="post-metas">' . $items . '</div></div>' : '';
    return $metas;
}

/**
 * @description: 作者页主内容外框架
 * @param {*}
 * @return {*}
 */
function zib_author_content()
{
    global $wp_query;
    $curauth   = $wp_query->get_queried_object();
    $author_id = $curauth->ID;

    do_action('zib_author_main_content');
    $post_count = zib_get_user_post_count($author_id, 'publish');

    $tabs_array['post'] = array(
        'title'         => '文章<count class="opacity8 ml3">' . $post_count . '</count>',
        'content_class' => '',
        'route'         => true,
        'loader'        => zib_get_author_tab_loader('post'),
    );
    $tabs_array['favorite'] = array(
        'title'         => '收藏<count class="opacity8 ml3">' . get_user_favorite_post_count($author_id) . '</count>',
        'content_class' => '',
        'route'         => true,
        'loader'        => zib_get_author_tab_loader('post'),
    );

    if (!_pz('close_comments')) {
        $comment_count         = get_user_comment_count($author_id);
        $tabs_array['comment'] = array(
            'title'         => '评论<count class="opacity8 ml3">' . $comment_count . '</count>',
            'content_class' => '',
            'route'         => true,
            'loader'        => zib_get_author_tab_loader('comment'),
        );
    }

    $tabs_array = apply_filters('author_main_tabs_array', $tabs_array, $author_id);

    $tabs_array['follow'] = array(
        'title'         => '粉丝<count class="opacity8 ml3">' . _cut_count(get_user_meta($author_id, 'followed-user-count', true)) . '</count>',
        'content_class' => 'text-center',
        'route'         => true,
        'loader'        => zib_get_author_tab_loader('follow'),
    );

    $tab_nav     = zib_get_main_tab_nav('nav', $tabs_array, 'author', false);
    $tab_content = zib_get_main_tab_nav('content', $tabs_array, 'author', false);
    if ($tab_nav && $tab_content) {
        $html = '<div class="author-tab zib-widget">';
        $html .= '<div class="affix-header-sm" offset-top="6">';
        $html .= $tab_nav;
        $html .= '</div>';
        $html .= $tab_content;
        $html .= '</div>';
        echo $html;
    }
}

function zib_get_author_tab_loader($key)
{
    $args = array(
        'post'    => str_repeat('<div class="posts-item flex"><div class="post-graphic"><div class="radius8 item-thumbnail placeholder"></div> </div><div class="item-body flex xx flex1 jsb"> <p class="placeholder t1"></p> <h4 class="item-excerpt placeholder k1"></h4><p class="placeholder k2"></p><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>', 4),
        'comment' => str_repeat('<div class="posts-item no_margin"><div class="author-set-left"><div class="placeholder k2"></div></div><div class="author-set-right"><div class="placeholder t1 mb10"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>', 6),
        'follow'  => str_repeat('<div class="author-minicard radius8 flex ac" style="display: inline-flex;margin:5px 8px;"><div class="avatar-img mr10"><div class="avatar placeholder"></div></div><div class="flex1"><div class="placeholder k1 mb6"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>', 6),
    );
    return isset($args[$key]) ? $args[$key] : $args['post'];
}

//作者页面->favorite收藏的文章
function zib_main_author_tab_content_favorite()
{
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }
    $author_id = $curauth->ID;
    $paged     = isset($_GET['favorite_paged']) ? $_GET['favorite_paged'] : 1;

    return zib_get_favorite_posts_lists($author_id, $paged);
}

//获取用户收藏的文章明细
function zib_get_favorite_posts_lists($user_id, $paged = 1)
{
    if (!$user_id) {
        return;
    }
    $ice_perpage = get_option('posts_per_page');
    $args        = array(
        'no_margin' => true,
        'is_author' => true,
    );
    $post_args = array(
        'ignore_sticky_posts' => true,
        'post_status'         => 'publish',
        'order'               => 'DESC',
        'paged'               => $paged,
        'posts_per_page'      => $ice_perpage,
    );

    $ajax_url = false;

    $lists        = '';
    $count_all    = 0;
    $favorite_ids = maybe_unserialize(get_user_meta($user_id, 'favorite-posts', true));
    if ($favorite_ids) {
        $post_args['post__in'] = (array) $favorite_ids;
        $the_query             = new WP_Query($post_args);
        $count_all             = $the_query->found_posts;
        $lists .= zib_posts_list($args, $the_query, false);
    }

    if (!$lists) {
        $lists = zib_get_ajax_null('暂无收藏内容', 60, 'null-2.svg');
    }
    if (_pz('paging_ajax_s', true)) {
        $paginate = zib_get_ajax_next_paginate($count_all, $paged, $ice_perpage, $ajax_url, 'text-center theme-pagination ajax-pag', 'next-page ajax-next', '', 'favorite_paged');
    } else {
        $paginate = zib_get_ajax_number_paginate($count_all, $paged, $ice_perpage, $ajax_url, 'ajax-pag', 'next-page ajax-next', 'favorite_paged');
    }

    return $lists . $paginate;
}

//作者页面->关注|粉丝
function zib_main_author_tab_content_follow()
{
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }
    $author_id = $curauth->ID;

    return zib_get_follow_user_list($author_id);
}

//获取关注、粉丝的用户列表
function zib_get_follow_user_list($user_id, $type = 'followed', $paged = 1)
{
    if (!$user_id) {
        return;
    }

    $ice_perpage = 12;

    $follow         = maybe_unserialize(get_user_meta($user_id, 'follow-user', true));
    $followed       = maybe_unserialize(get_user_meta($user_id, 'followed-user', true));
    $follow_count   = is_array($follow) ? count(array_unique($follow)) : 0;
    $followed_count = is_array($followed) ? count(array_unique($followed)) : 0;

    if ('followed' == $type) {
        $text  = '粉丝';
        $meta  = $followed;
        $count = $followed_count;
    } else {
        $text  = '关注用户';
        $meta  = $follow;
        $count = $follow_count;
    }

    $header   = '';
    $ajax_url = add_query_arg(array('user_id' => $user_id, 'type' => $type, 'action' => 'author_follow'), admin_url('admin-ajax.php'));

    if (1 == $paged) {
        $header_but = '<li><a ajax-replace="true" class="ajax-next ' . ('followed' == $type ? 'focus-color' : 'muted-color') . '" href="' . add_query_arg(array('type' => 'followed', 'paged' => false), $ajax_url) . '">粉丝 ' . $followed_count . '</a></li>';
        $header_but .= '<li><a ajax-replace="true" class="ajax-next ' . ('followed' != $type ? 'focus-color' : 'muted-color') . '" href="' . add_query_arg(array('type' => 'follow', 'paged' => false), $ajax_url) . '">关注 ' . $follow_count . '</a></li>';
        $header = '<ul class="ajax-item list-inline splitters relative mb10 mt10">' . $header_but . '</ul>';
    }

    $lists = '';
    if ($meta && is_array($meta)) {
        $meta      = array_unique($meta);
        $meta_show = $meta;
        if ($count > $ice_perpage) {
            $meta      = array_chunk($meta, $ice_perpage);
            $meta_show = isset($meta[$paged - 1]) ? $meta[$paged - 1] : array();
        }

        foreach ($meta_show as $_id) {
            $lists .= zib_author_card($_id, 'ajax-item');
        }
    }
    if (!$lists) {
        $lists = zib_get_ajax_null('暂无' . $text, '40', 'null-love.svg');
    }
    $paginate = zib_get_ajax_next_paginate($count, $paged, $ice_perpage, $ajax_url);

    return $header . $lists . $paginate;
}

//作者页面-评论tab
function zib_main_author_tab_content_comment()
{
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }
    $author_id = $curauth->ID;

    return zib_get_author_comment($author_id);
}

//作者页面-文章tab
function zib_main_author_tab_content_post()
{
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }

    $type       = 'post';
    $author_id  = $curauth->ID;
    $current_id = get_current_user_id();
    $page       = zib_get_the_paged();

    $post_args = array(
        'no_margin' => true,
        'no_author' => true,
    );

    $header   = '';
    $this_url = zib_get_current_url();

    if (1 == $page || !_pz('paging_ajax_s', true)) {
        $this_url = zib_url_del_paged($this_url);

        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';
        $status  = isset($_REQUEST['post_status']) ? $_REQUEST['post_status'] : 'publish';

        $status_html  = '';
        $status_class = '';

        $status_array = array(
            'publish' => '发布',
        );

        if (($current_id && $current_id == $author_id) || (is_super_admin())) {
            $status_array['pending'] = '待审核';
            $status_array['draft']   = '草稿';
            // $status_array['trash']   = '回收站';
        }

        foreach ($status_array as $k => $v) {
            $active_class = $k === $status ? ' c-blue badg mr6' : ' ajax-next but mr6';
            $active_attr  = $k === $status ? ' href="javascript:;"' : ' ajax-replace="true"  href="' . add_query_arg('post_status', $k, $this_url) . '"';
            $status_html .= '<a' . $active_attr . ' class="' . $status_class . $active_class . '">' . $v . '<count class="ml3 em09">' . zib_get_user_post_count($author_id, $k, $type) . '</count></a>';
        }

        //右边排序
        $orderby_html = '';
        if ($status_html || zib_get_user_post_count($author_id, 'publish')) {

            $orderby_array = array(
                'date'           => '最新发布',
                'modified'       => '最近更新',
                'views'          => '最多查看',
                'like'           => '最多点赞',
                'comment_count'  => '最多回复',
                'favorite_count' => '最多收藏',
            );

            $orderby_class        = 'ajax-next';
            $orderby_dropdown_but = '';

            foreach ($orderby_array as $k => $v) {
                $active_class = $k == $orderby ? ' class="active"' : '';
                $orderby_dropdown_but .= '<li' . $active_class . '><a ajax-replace="true" class="' . $orderby_class . '" href="' . add_query_arg(array('orderby' => $k), $this_url) . '">' . $v . '</a></li>';
            }

            $orderby_html = '<div class="dropdown flex0 pull-right">';
            $orderby_html .= '<a href="javascript:;" class="but" data-toggle="dropdown">排序<i class="ml6 fa fa-caret-down opacity5" aria-hidden="true" style="margin-right:0;"></i></a>';
            $orderby_html .= '<ul class="dropdown-menu">' . $orderby_dropdown_but . '</ul>';
            $orderby_html .= '</div>';
        }
        $header = '<div class="ajax-item flex ac jsb mb10 px12-sm"><div class="scroll-x mini-scrollbar mr10">' . $status_html . '</div>' . $orderby_html . '</div>';
    }

    $posts_list = zib_posts_list($post_args, false, false);
    $paging     = zib_paging(false, false);

    if ($paging) {
        $posts_list .= $paging;
        $posts_list .= '<div class="post_ajax_loader" style="display: none;">' . zib_get_author_tab_loader('post') . '</div>';
    }
    if (!$posts_list) {
        $posts_list = zib_get_ajax_null('暂无文章');
        //if ($page == 1) $header = '';
        if (1 == $page && $current_id && $current_id == $author_id) {
            $add = zib_get_write_posts_button('ml10 mr10 mt10 but hollow c-blue padding-lg', '发布文章');
            $posts_list .= '<div class="text-center mb20">';
            $posts_list .= $add;
            $posts_list .= '</div>';
        }
    }

    $html = $header;
    $html .= $posts_list;

    return $html;
}

/**
 * @description: 获取用户文章不同状态的数量
 * @param int $user_id 用户ID
 * @param int $poststatus 文章状态 ：
 * @return int 数量
 */
//获取文章数量
function zib_get_user_post_count($user_id, $poststatus = 'publish', $post_type = 'post')
{
    $cache_num = wp_cache_get($user_id . '_' . $poststatus, 'post_count_' . $post_type, true);
    if (false !== $cache_num) {
        return $cache_num;
    }

    global $wpdb;
    $cuid       = esc_sql($user_id);
    $poststatus = esc_sql($poststatus);
    $post_type  = esc_sql($post_type);

    if ('all' == $poststatus) {
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_author) FROM $wpdb->posts WHERE post_author=%d AND post_type='$post_type'", $cuid));
    } else {
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_author) FROM $wpdb->posts WHERE post_author=%d AND post_type='$post_type' AND post_status=%s", $cuid, $poststatus));
    }

    //添加缓存，长期有效
    wp_cache_set($user_id . '_' . $poststatus, $count, 'post_count_' . $post_type);

    return (int) $count;
}

//挂钩清空缓存
function zib_get_user_post_count_cache_delete($post_ID, $post)
{
    $post_type = $post->post_type;
    $user_id   = $post->post_author;
    foreach (['all', 'publish', 'pending', 'draft'] as $poststatus) {
        wp_cache_delete($user_id . '_' . $poststatus, 'post_count_' . $post_type);
    }
}
add_action('save_post', 'zib_get_user_post_count_cache_delete', 10, 2);

/**
 * @description: 获取作者、用户卡片列表
 * @param {*}
 * @return {*}
 */
function zib_author_card_lists($args = array(), $users_args = array())
{

    if (!$users_args) {
        $users_args = array(
            'include' => array(),
            'exclude' => array('1'),
            'order'   => 'DESC',
            'orderby' => 'user_registered',
            'number'  => 8,
        );
    }
    $users = get_users($users_args);

    if ($users) {
        foreach ($users as $user) {
            echo zib_author_card($user->ID);
        }
    } else {
        echo '未找到用户!';
    }
}

/**
 * @description: 作者页面获取作者卡片
 * @param {*} $user_id
 * @param {*} $class
 * @return {*}
 */
function zib_author_card($user_id = '', $class = '')
{
    if (!$user_id) {
        return;
    }

    $user_data = get_userdata($user_id);
    if (!$user_data) {
        return;
    }

    $avatar = zib_get_data_avatar($user_id);
    $follow = zib_get_user_follow('focus-color px12 ml10 follow flex0', $user_id);
    $desc   = get_user_desc($user_id);

    $avatar_box        = zib_get_avatar_box($user_id);
    $display_name_link = zib_get_user_name("id=$user_id");

    return '
    <div class="author-minicard radius8 relative-h ' . $class . '">
    <div class="abs-blur-bg blur">' . $avatar . '</div>
        <ul class="list-inline relative">
            <li>' . $avatar_box . '
            </li>
            <li>
                <dl>
                    <dt class="flex ac">' . $display_name_link . $follow . '</dt>
                    <dd class="mt6 em09 muted-color text-ellipsis">' . $desc . '</dd>
                </dl>
            </li>
        </ul></div>';
}

function zib_get_author_header_dropup_btn()
{
    global $wp_query;
    $author = $wp_query->get_queried_object();
    if (empty($author->ID)) {
        return;
    }
    $author_id = $author->ID;

    $lists = '<li>' . zib_get_user_details_data_link($author_id, '', zib_get_svg('user', null, 'mr6') . '更多资料') . '<li>';
    $lists .= zib_get_user_search_link($author_id, '', '', zib_get_svg('search', null, 'mr6') . '搜索内容');
    if (get_current_user_id() === $author_id) {
        $lists .= '<li>' . zib_get_user_cover_set_link('', '<i class="fa fa-camera mr6" aria-hidden="true"></i>修改封面') . '<li>';
    }
    $lists = apply_filters('author_header_drop_lists', $lists, $author_id);

    if (!$lists) {
        return;
    }

    return '<span class="dropup pull-right"><a href="javascript:;" class="item mr3 toggle-radius" data-toggle="dropdown">' . zib_get_svg('menu_2') . '</a><ul class="dropdown-menu">' . $lists . '</ul></span>';
}
add_filter('author_header_more_btn', 'zib_get_author_header_dropup_btn', 11, 2);

//新版的用户小工具
function zib_get_user_card_box($args = array(), $echo = false)
{
    $defaults = array(
        'user_id'            => 0,
        'class'              => '',
        'show_posts'         => true,
        'show_checkin'       => false,
        'show_img_bg'        => false,
        'show_button'        => true,
        'button_1'           => 'post',
        'button_1_text'      => '发布文章',
        'button_1_class'     => 'jb-pink',
        'button_2'           => 'home',
        'button_2_text'      => '用户中心',
        'button_2_class'     => 'jb-blue',
        'show_payvip_button' => false,
        'limit'              => 6,
        'orderby'            => 'views',
        'post_type'          => 'post',
        'post_style'         => 'card',
    );

    $args = wp_parse_args((array) $args, $defaults);

    if (!$args['user_id']) {
        return;
    }

    $user_id   = $args['user_id'];
    $cuid      = get_current_user_id();
    $avatar    = zib_get_avatar_box($user_id, 'avatar-img avatar-lg');
    $name      = zib_get_user_name("id=$user_id&class=flex1 flex ac&follow=true");
    $tag_metas = zib_get_user_badges($user_id);
    $btns      = '';
    $post      = '';
    $checkin   = '';

    if (!$cuid || $cuid != $user_id) {
        $args['show_button'] = false;
    }
    if ($args['show_posts'] && $args['limit'] > 0) {
        $post = zib_get_user_card_post($args);
    }
    if ($args['show_checkin'] && get_current_user_id() == $user_id) {
        $checkin = zib_get_user_checkin_btn('img-badge jb-yellow', '<i class="fa fa-calendar-check-o ml3 mr6"></i>签到', '<i class="ml3 mr6 fa fa-calendar-check-o"></i>已签到');
    }

    if ($args['show_button']) {
        $btns .= zib_get_new_add_btns([$args['button_1']], 'but pw-1em mr6 ' . $args['button_1_class'], $args['button_1_text']);
        if ('home' === $args['button_2']) {
            $btns .= zib_get_user_home_link($user_id, 'but pw-1em ml6 ' . $args['button_2_class'], $args['button_2_text']);
        } else {
            $btns .= zib_get_user_center_link('but pw-1em ml6 ' . $args['button_2_class'], $args['button_2_text']);
        }
        $btns = '<div class="user-btns mt20">' . $btns . '</div>';
    }

    $cover = $args['show_img_bg'] ? '<div class="user-cover graphic" style="padding-bottom: 50%;">' . get_user_cover_img($user_id) . '</div>' : '';

    $html = '<div class="user-card zib-widget ' . $args['class'] . '">' . $cover . '
        <div class="card-content mt10 relative">
            <div class="user-content">
                ' . $checkin . '
                <div class="user-avatar">' . $avatar . '</div>
                <div class="user-info mt20 mb10">
                    <div class="user-name flex jc">' . $name . '</div>
                    <div class="author-tag mt10 mini-scrollbar">' . $tag_metas . '</div>
                    <div class="user-desc mt10 muted-2-color em09">' . get_user_desc($user_id) . '</div>
                    ' . $btns . '
                </div>
            </div>
            ' . $post . '
        </div>
    </div>';

    if (!$echo) {
        return $html;
    }

    echo $html;
}

//获取用户卡片的文章模块
function zib_get_user_card_post($args = array())
{
    $defaults = array(
        'user_id'    => 0,
        'limit'      => 6,
        'orderby'    => 'views',
        'post_type'  => 'post',
        'post_style' => 'card',
    );

    $args = wp_parse_args((array) $args, $defaults);

    if (!$args['user_id']) {
        return;
    }

    $query_args = array(
        'post_type'           => $args['post_type'],
        'author'              => $args['user_id'],
        'showposts'           => $args['limit'],
        'ignore_sticky_posts' => 1,
    );
    $the_ID = get_the_ID();
    if ($the_ID) {
        $query_args['post__not_in'] = [$the_ID];
    }
    $query_args = zib_query_orderby_filter($args['orderby'], $query_args);
    $new_query  = new WP_Query($query_args);
    $lists      = '';

    while ($new_query->have_posts()) {
        $new_query->the_post();
        if ('card' === $args['post_style']) {
            $time_ago = zib_get_time_ago(get_the_time('U'));
            $info     = '<item>' . $time_ago . '</item><item class="pull-right">' . zib_get_svg('view') . ' ' . get_post_view_count($before = '', $after = '') . '</item>';
            $img      = zib_post_thumbnail('', 'fit-cover', true);
            $img      = $img ? $img : zib_get_spare_thumb();
            $title    = get_the_title() . get_the_subtitle(false);
            $card     = array(
                'type'         => 'style-3',
                'class'        => 'em09',
                'img'          => $img,
                'alt'          => $title,
                'link'         => array(
                    'url'    => get_permalink(),
                    'target' => '',
                ),
                'text1'        => $title,
                'text2'        => zib_str_cut($title, 0, 32, '...'),
                'text3'        => $info,
                'lazy'         => true,
                'height_scale' => 70,
            );
            $lists .= '<div class="swiper-slide mr10">' . zib_graphic_card($card, false) . '</div>';
        } else {
            $lists .= '<div class="item"><a class="icon-circle text-ellipsis" href="' . get_permalink() . '">' . get_the_title() . get_the_subtitle() . '</a></div>';
        }
    }
    wp_reset_query();

    if ('card' === $args['post_style']) {
        $html = '<div class="swiper-container more-posts swiper-scroll"><div class="swiper-wrapper">' . $lists . '</div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>';
    } else {
        $html = '<div class="more-posts-mini">' . $lists . '</div>';
    }

    return $html;
}
