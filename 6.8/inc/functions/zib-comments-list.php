<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:37
 * @LastEditTime: 2022-09-27 00:11:34
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */
function zib_comments_list($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    global $commentcount, $wpdb, $post;
    zib_get_comments_list($comment, $depth);
}

//获取主评论的评论用户姓名
function zib_get_comments_user_name($comment)
{
    if (!$comment) {
        return;
    }

    $user_id   = $comment->user_id;
    $user_name = '';
    if ($user_id) {
        $user = get_userdata($user_id);
        if (isset($user->display_name)) {
            $display_name = $user->display_name;
            $url          = zib_get_user_home_url($user_id);
            $user_name    = '<a class="text-ellipsis font-bold" href="' . $url . '">' . $display_name . '</a>';
            $user_name .= zib_get_user_auth_badge($user_id, 'ml3');
            $user_name .= zib_get_medal_wear_icon($user_id, 'ml3');
            $user_name .= zib_get_user_level_badge($user_id, 'ml3');
        }
    }

    $user_name = $user_name ? $user_name : '<b class="mr6">' . $comment->comment_author . '</b>';
    $badge     = '';
    if ($user_id && _pz('comment_author_tag', true)) {
        $post = get_post($comment->comment_post_ID);
        if ($user_id == $post->post_author) {
            $badge .= '<span class="badg c-green badg-sm flex0 ml3">作者</span>';
        }
    }
    $badge = apply_filters('comments_user_name_badge', $badge, $comment);

    return '<name class="flex ac flex1">' . $user_name . $badge . '</name>';
}

//获取主评论的头部
function zib_get_comment_header($comment)
{
    if (!$comment) {
        return;
    }

    $user_id       = $comment->user_id;
    $comment_id    = $comment->comment_ID;
    $c_like        = zib_get_comment_like('action action-comment-like muted-2-color flex0 ml10', $comment_id);
    $author_avatar = zib_get_avatar_box($user_id, 'avatar-img comt-avatar');
    $user_neme     = zib_get_comments_user_name($comment);

    $html = '<div class="author-box flex ac">';
    $html .= $author_avatar;
    $html .= $user_neme;
    $html .= $c_like;
    $html .= '</div>';

    return '<div class="comment-header mb10">' . apply_filters('comment_header', $html, $comment) . '</div>';
}

//获取主评论的底部
function zib_get_comment_footer($comment, $depth = 0)
{
    if (!$comment) {
        return;
    }

    $is_mobile = wp_is_mobile();

    //状态标签
    $badg_approve = '';
    if ('0' == $comment->comment_approved) {
        $badg_approve = '<span class="badg c-red badg-sm">待审核</span>';
    }
    $badg_approve = '<span class="badge-approve">' . $badg_approve . '</span>';

    //时间
    $time_html = '<span class="comt-author"' . ($is_mobile ? ' data-toggle="tooltip"' : '') . ' title="' . date('Y年m月d日 H:i:s', strtotime($comment->comment_date)) . '">' . zib_get_time_ago($comment->comment_date) . '</span>';

    //回复@
    $comment_parent_html = '';
    if ($comment->comment_parent > 0) {
        $comment_parent_html = '<span>@<a rel="nofollow" class="url" href="javascript:(scrollTo(\'#comment-' . $comment->comment_parent . '\',-70));">' . get_comment_author($comment->comment_parent) . '</a></span>';
    }

    //回复按钮
    $replyText_html = '';
    $max_depth      = get_option('thread_comments_depth');
    if ('0' != $comment->comment_approved && $depth && !zib_is_close_sign()) {
        $replyText = get_comment_reply_link(array('add_below' => 'div-comment', 'reply_text' => '回复', 'login_text' => '回复', 'depth' => $depth, 'max_depth' => $max_depth), $comment->comment_ID);
        if (strstr($replyText, 'reply-login')) {
            $replyText = preg_replace('# class="[\s\S]*?" href="[\s\S]*?"#', ' class="signin-loader" href="javascript:;"', $replyText);
        } else {
            $replyText = preg_replace('# aria-label=#', $is_mobile ? ' title=' : ' data-toggle="tooltip" title=', $replyText);
        }
        $replyText_html = '<span class="reply-link">' . $replyText . '</span>';
    }

    //操作按钮
    $action_btn = zib_get_comments_action_list($comment, $depth);
    $info       = $time_html . $comment_parent_html . $badg_approve . $replyText_html;
    $info       = apply_filters('comment_footer_info', $info, $comment, $depth);

    return '<div class="comt-meta muted-2-color">' . $info . $action_btn . '</div>';
}

//获取评论执行的按钮
function zib_get_comments_action_list($comment)
{
    if (!$comment) {
        return;
    }

    $user_id        = $comment->user_id;
    $comment_id     = $comment->comment_ID;
    $is_super_admin = is_super_admin();
    $lists          = '';

    if (zib_current_user_can('comment_edit', $comment)) {
        $edit_but = '<a class="comment-edit-link" data-commentid="' . $comment->comment_ID . '" data-postid="' . $comment->comment_post_ID . '" href="javascript:;"><i class="fa fa-edit mr10 fa-fw" aria-hidden="true"></i>编辑</a>';
        $lists .= '<li>' . $edit_but . '</li>';
    }

    if (zib_current_user_can('comment_audit', $comment)) {
        $text           = '0' == $comment->comment_approved ? '批准' : '驳回';
        $approved_class = '0' == $comment->comment_approved ? 'approve' : 'unapprove';

        $lists .= '<li><a class="comment-approve-link ' . $approved_class . '" data-commentid="' . $comment->comment_ID . '" data-postid="' . $comment->comment_post_ID . '" href="javascript:;">' . zib_get_svg('approve', null, 'mr10 icon fa-fw') . '<text>' . $text . '</text></a></li>';
    }

    if (zib_current_user_can('comment_delete', $comment)) {
        $trash_but = '<a class="comment-trash-link c-red" data-commentid="' . $comment->comment_ID . '" data-postid="' . $comment->comment_post_ID . '" href="javascript:;"><i class="fa fa-trash-o mr10 fa-fw" aria-hidden="true"></i>删除</a>';
        $lists .= '<li>' . $trash_but . '</li>';
    }

    $lists = apply_filters('comments_action_lists', $lists, $comment);
    if (!$lists) {
        return;
    }

    $icon_a = '<a href="javascript:;" class="muted-color padding-6" data-toggle="dropdown">';
    $icon_a .= zib_get_svg('menu_2');
    $icon_a .= '</a>';

    return '<span class="dropdown drop-fixed-sm padding-6">' . $icon_a . '<ul class="dropdown-menu">' . $lists . '</ul></span>';
}

function zib_get_comments_list($comment, $depth = 0, $echo = true)
{

    if (!$comment) {
        return false;
    }
    $comment_id = $comment->comment_ID;

    $con    = zib_comment_filters(get_comment_text($comment));
    $header = zib_get_comment_header($comment);
    $footer = zib_get_comment_footer($comment, $depth);
    $html   = '<li ' . comment_class('', $comment, null, false) . ' id="comment-' . $comment_id . '">';
    $html .= '<ul class="list-inline">';
    $html .= '<li class="comt-main" id="div-comment-' . $comment_id . '">';
    $html .= $header;
    $html .= '<div class="comment-footer">';
    $html .= '<div class="mb10 comment-content" id="comment-content-' . $comment_id . '">' . $con . '</div>';
    $html .= $footer;
    $html .= '</div>';
    $html .= '</li>';
    $html .= '</ul>';

    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

//作者页面的评论列表
function zib_get_author_comment($author_id = 0, $paged = 1)
{

    if (!$author_id) {
        return;
    }

    $current_id      = get_current_user_id();
    $ice_perpage     = 10;
    $comments_status = $current_id == $author_id ? 'all' : 'approve';
    $args            = array(
        'user_id' => $author_id,
        'number'  => $ice_perpage,
        'status'  => $comments_status,
        'offset'  => ($paged - 1) * $ice_perpage,
    );
    $comments = get_comments($args);

    $count_all = get_user_comment_count($author_id, $comments_status, false);

    $lists = '';
    if (!$comments) {
        $lists = zib_get_ajax_null('暂无评论内容');
    } else {
        foreach ($comments as $comment) {
            $lists .= '<div class="ajax-item posts-item no_margin">';
            $lists .= zib_comments_author_list($comment);
            $lists .= '</div>';
        }
    }
    $ajax_url = add_query_arg(array('user_id' => $author_id, 'action' => 'author_comment'), admin_url('admin-ajax.php'));
    $paginate = zib_get_ajax_next_paginate($count_all, $paged, $ice_perpage, $ajax_url);

    return $lists . $paginate;
}

function zib_comments_author_list($comment)
{
    if (!$comment) {
        return false;
    }

    $cont = zib_comment_filters(get_comment_text($comment->comment_ID), 'noimg');

    $_link      = get_comment_link($comment->comment_ID);
    $post_title = get_the_title($comment->comment_post_ID);
    $post_tlink = get_the_permalink($comment->comment_post_ID);

    $time     = $comment->comment_date;
    $approved = '';
    if ('0' == $comment->comment_approved) {
        $approved = '<span class="badg c-red badg-sm mr6">待审核</span>';
    }
    $parent = '';
    $post   = '<a class="muted-color" href="' . $post_tlink . '">' . $post_title . '</a>';
    $cont   = '<a class="muted-color text-ellipsis-5" href="' . $_link . '">' . $approved . $cont . '</a>';
    if ($comment->comment_parent > 0) {
        $parent = '<span class="mr10" >@' . get_comment_author($comment->comment_parent) . '</span>';
    }

    $time = zib_get_time_ago($comment->comment_date);

    $html = '<div class="author-set-left" title="' . $comment->comment_date . '">';
    $html .= $time;
    $html .= '</div>';

    $html .= '<div class="author-set-right">';
    $html .= '<div class="mb10 comment-content">';
    $html .= $cont;

    $html .= '</div>';
    $html .= '<div class="muted-2-color em09 text-ellipsis">';
    $html .= $parent . '评论于：' . $post;

    $html .= '</div>';

    $html .= '</div>';
    return $html;
}

function zib_widget_comments($limit, $outpost, $outer)
{
    global $wpdb;
    $args = array(
        'orderby'        => 'comment_date',
        'number'         => $limit,
        'status'         => 'approve',
        'author__not_in' => preg_split("/,|，|\s|\n/", $outer),
        'post__not_in'   => preg_split("/,|，|\s|\n/", $outpost),
    );

    $comments = get_comments($args);

    $output = '';
    foreach ($comments as $comment) {
        $cont  = zib_comment_filters(get_comment_text($comment->comment_ID), 'noimg');
        $_link = get_comment_link($comment->comment_ID);
        //$post_title = $comment->post_title;
        //$post_link = get_the_permalink($comment->ID);
        $time      = zib_get_time_ago($comment->comment_date);
        $user_name = get_comment_author($comment->comment_ID);
        $user_id   = $comment->user_id;
        $c_like    = zib_get_comment_like('action action-comment-like pull-right muted-2-color', $comment->comment_ID);
        $vip_icon  = '';

        if ($user_id) {
            $user_name = '<a href="' . zib_get_user_home_url($user_id) . '">' . $user_name . '</a>';
            $user_name = zib_get_user_name('id=' . $user_id . '&level=0&class=inflex ac relative-h');
        }

        $avatar = zib_get_avatar_box($user_id);

        echo '<div class="posts-mini">';
        echo $avatar;
        echo '<div class="posts-mini-con em09 ml10 flex xx jsb">';
        echo '<p class="flex jsb">';
        echo '<span class="flex1 flex">';
        echo $user_name;
        echo '<span class="flex0 icon-spot muted-3-color" title="' . $comment->comment_date . '">' . $time . '</span>';
        echo '</span>';

        echo '<span class="ml10 flex0">' . $c_like . '</span>';
        echo '</p>';

        echo '<a class="muted-color text-ellipsis-5" href="' . $_link . '">' . $cont . '</a>';
        echo '</div>';
        echo '</div>';
    }
};

function zib_comment_filters($cont, $type = '', $lazy = true)
{
    $cont = convert_smilies($cont);

    $cont = preg_replace('/\[img=(.*?)\]/', '<img class="box-img lazyload" src="$1" alt="评论图片' . zib_get_delimiter_blog_name() . '">', $cont);

    if ('noimg' == $type) {
        $cont = preg_replace('/\<img(.*?)\>/', '[图片]', $cont);
        $cont = preg_replace('/\[code]([\s\S]*)\[\/code]/', '[代码]', $cont);
    } else {
        $cont = str_replace('[code]', '<pre><code>', $cont);
        $cont = str_replace('[/code]', '</code></pre>', $cont);
    }

    $cont = preg_replace('/\[g=(.*?)\]/', '<img class="smilie-icon" src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/smilies/$1.gif" alt="表情[$1]' . zib_get_delimiter_blog_name() . '">', $cont);
    if (zib_is_lazy('lazy_comment') && $lazy) {
        $cont = str_replace(' src=', ' src="' . zib_get_lazy_thumb() . '" data-src=', $cont);
    }
    //

    $cont = wp_kses_post($cont);
    return $cont;
}

//解决评论链接定位问题(由order=DESC引起)
add_filter('get_page_of_comment_query_args', function ($comment_args) {
    $comment_args['date_query'][0]['after'] = $comment_args['date_query'][0]['before'];
    unset($comment_args['date_query'][0]['before']);
    return $comment_args;
});

//为评论添加排序方式
add_filter('comments_template_query_args', 'zib_comments_template_query_args');
function zib_comments_template_query_args($comment_args)
{
    $comment_args['order'] = 'DESC';
    if (is_super_admin()) {
        $comment_args['status'] = array('hold', 'approve');
    }

    if (isset($_GET['corderby'])) {
        if (in_array($_GET['corderby'], array('comment_like'))) {
            $comment_args['orderby']  = 'meta_value_num';
            $comment_args['meta_key'] = $_GET['corderby'];
        } else {
            $comment_args['orderby'] = $_GET['corderby'];
        }
    }

    if (!empty($_GET['only_author'])) {
        $comment_args['author__in']   = [$_GET['only_author']];
        $comment_args['hierarchical'] = false;
    }
    return $comment_args;
}

//新建评论时候，为评论添加参数
add_action('wp_insert_comment', function ($id) {
    add_comment_meta($id, 'comment_like', '0');
});

//获取评论翻页按钮
function zib_paginate_comments_links()
{
    echo zib_get_comment_paginate(_pz('comment_paginate_type'), _pz('comment_paging_ajax_ias_s'), _pz('comment_paging_ajax_ias_max', 3));
}

//获取评论翻页按钮
function zib_get_comment_paginate($type = '', $ias = true, $ias_auto = 3, $ajax_text = '')
{
    if (!is_singular()) {
        return;
    }

    if ('ajax_lists' == $type) {
        //ias自动加载
        $paged = get_query_var('cpage');
        $paged = $paged ? $paged : 1;

        $nextpage = (int) $paged + 1;
        global $wp_query;
        $max_page = $wp_query->max_num_comment_pages;
        if (empty($max_page)) {
            $max_page = get_comment_pages_count();
        }
        if ($nextpage > $max_page) {
            return;
        }
        $next = get_comments_pagenum_link($nextpage, $max_page);
        if (!$next) {
            return;
        }

        $ajax_trigger = $ajax_text ? $ajax_text : _pz("ajax_trigger", '加载更多');
        if (isset($_GET['corderby'])) {
            $next = add_query_arg('corderby', $_GET['corderby'], $next);
        }
        if (!empty($_GET['only_author'])) {
            $next = add_query_arg('only_author', $_GET['only_author'], $next);
        }

        $ias_max  = (int) $ias_auto;
        $ias_attr = ($ias && ($paged <= $ias_max || !$ias_max)) ? ' class="theme-pagination lazyload ias-pagenav pagenav" lazyload-action="ias"' : '  class="theme-pagination ias-pagenav pagenav"';

        $pag_html = '<div' . $ias_attr . '><div class="order-ajax-next"><a href="' . esc_url($next) . '" class="ias-btn" no-replace="true">' . $ajax_trigger . '</a></div></div>';
    } else {
        $args = array(
            'type'      => 'array',
            'prev_text' => '<i class="fa fa-angle-left em12"></i><span class="hide-sm ml6">上一页</span>',
            'next_text' => '<span class="hide-sm mr6">下一页</span><i class="fa fa-angle-right em12"></i>',
        );
        $array = paginate_comments_links($args);
        if (!$array) {
            return;
        }

        $pag_html = '<div class="pagenav">';
        $pag_html .= implode("", $array);
        $pag_html .= '</div>';
    }

    return $pag_html;
}

function zib_get_respond_mobile($input_s = '#respond', $placeholder = '', $class = '', $is_lognin = false)
{

    $user_id = get_current_user_id();
    if (!$user_id && $is_lognin) {
        $class .= ' signin-loader';
    }

    $avatar_img = zib_get_data_avatar($user_id);

    $html = '<div class="flex ac jsb virtual-input ' . $class . '" fixed-input="' . $input_s . '">'; //虚拟
    $html .= '<div class="flex flex1 ac">';
    $html .= $avatar_img; //头像
    $html .= '<div class="text-ellipsis simulation mr10">'; //模拟input
    $html .= $placeholder;
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<span class="but c-blue">提交</span>';
    $html .= '</div>';

    return $html;
}

/**
 * @description: 获取用户的具有特殊meta_query查询的评论数量
 * @param {*} $user_id
 * @param {*} $type adopted|is_hot
 * @return {*}
 */
function zib_get_user_meta_query_comment_count($user_id, $type = 'is_hot')
{
    if (!$user_id) {
        return;
    }

    $cache = wp_cache_get($user_id, 'user_' . $type . '_comment_count', true);
    if (false !== $cache) {
        return $cache;
    }

    $query_args = array(
        'author__in' => $user_id,
        'count'      => true,
        'status'     => 'approve',
    );

    switch ($type) {

        case 'is_hot': //热门
        case 'adopted': //已采纳

            $query_args['meta_query'][] = array(
                'key'   => $type,
                'value' => 1,
            );
            break;

        default:
            return 0;
    }

    $count = (int) get_comments($query_args);

    //设置缓存
    wp_cache_set($user_id, $count, 'user_' . $type . '_comment_count');

    return $count;
}

//刷新缓存
function zib_user_meta_query_comment_count_cache_delete($meta_id, $comment_id, $meta_key, $_meta_value)
{
    if (in_array($meta_key, array('is_hot', 'adopted'))) {
        $comment = get_comment($comment_id);
        if (!empty($comment->user_id)) {
            wp_cache_delete($comment->user_id, 'user_' . $meta_key . '_comment_count');
        }
    }
}
add_action('updated_comment_meta', 'zib_user_meta_query_comment_count_cache_delete', 99, 4);
add_action("added_comment_meta", 'zib_user_meta_query_comment_count_cache_delete', 99, 4);
