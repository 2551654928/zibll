<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-05 20:25:29
 * @LastEditTime: 2022-03-17 20:37:29
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|用户中心显示函数
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//用户个人主页显示版块
function zib_bbs_author_main_tabs_array($tabs_array, $author_id)
{
    global $zib_bbs;

    $plate_count = zib_bbs_get_user_plate_count($author_id);
    $plate_count = $plate_count ? $plate_count : zib_bbs_get_user_moderator_plate_count($author_id);
    $posts_count = zib_bbs_get_user_posts_count($author_id);

    $tabs_array['plate'] = array(
        'title'         => $zib_bbs->plate_name . '<count class="opacity8 ml3">' . $plate_count . '</count>',
        'content_class' => '',
        'loader'        => '<div class="plate-lists"><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div></div><div class="plate-lists"><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div></div>',
        'route'         => true,
    );
    $tabs_array['forum'] = array(
        'title'         => $zib_bbs->posts_name . '<count class="opacity8 ml3">' . $posts_count . '</count>',
        'content_class' => '',
        'route'         => true,
        'loader'        => zib_bbs_get_placeholder('posts_detail'),
    );

    return $tabs_array;
}
add_filter('author_main_tabs_array', 'zib_bbs_author_main_tabs_array', 10, 2);

//用户个人主页显示版块
function zib_bbs_author_content_plate()
{
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }
    $author_id = $curauth->ID;

    $orderby = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'date';
    $status  = !empty($_REQUEST['status']) ? $_REQUEST['status'] : 'publish';

    return zib_bbs_get_author_filter('plate', $orderby, $status) . zib_bbs_get_user_plate_lists($author_id, 1, $orderby, $status);
}
add_filter('main_author_tab_content_plate', 'zib_bbs_author_content_plate');

//用户个人主页显示帖子
function zib_bbs_author_content_forum()
{
    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }
    $author_id = $curauth->ID;

    $orderby = !empty($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'date';
    $status  = !empty($_REQUEST['status']) ? $_REQUEST['status'] : 'publish';

    return zib_bbs_get_author_filter('forum_post', $orderby, $status) . zib_bbs_get_user_posts_lists($author_id, 1, $orderby, $status);
}
add_filter('main_author_tab_content_forum', 'zib_bbs_author_content_forum');

//为用户中心添加论坛标识
function zib_bbs_filter_author_header_desc_identity($desc, $user_id)
{
    return $desc . zib_bbs_get_user_identity_badge($user_id);
}
add_filter('author_header_identity', 'zib_bbs_filter_author_header_desc_identity', 10, 3);

//个人中中心筛选过滤
function zib_bbs_get_author_filter($type = 'plate', $orderby = 'date', $status = 'publish')
{

    global $wp_query;
    $curauth = $wp_query->get_queried_object();
    if (empty($curauth->ID)) {
        return;
    }
    $author_id = $curauth->ID;

    $current_id = get_current_user_id();
    $this_url   = zib_url_del_paged(zib_get_current_url());

    global $zib_bbs;

    $orderby_array['plate'] = array(
        'date'         => '最新创建',
        'last_post'    => '最新发布',
        'last_reply'   => '最新回复',
        'posts_count'  => '最多' . $zib_bbs->posts_name,
        'reply_count'  => '最多回复',
        'views'        => '最高热度',
        'follow_count' => '最多关注',
    );

    $orderby_array['forum_post'] = array(
        'date'           => '最新发布',
        'modified'       => '最近更新',
        'last_reply'     => '最新回复',
        'views'          => '最多查看',
        'score'          => '最高评分',
        'comment_count'  => '最多回复',
        'favorite_count' => '最多收藏',
    );

    //左边状态
    $status_html  = '';
    $status_class = 'mb6';
    if ('plate' === $type) {
        $follow_count = zib_bbs_get_user_follow_plate_count($author_id);
        $status_array = array(
            'publish'   => '创建' . '<count class="ml3 em09">' . zib_get_user_post_count($author_id, 'publish', $type) . '</count>',
            'moderator' => '管理' . '<count class="ml3 em09">' . zib_bbs_get_user_moderator_plate_count($author_id) . '</count>',
            'follow'    => '关注' . '<count class="ml3 em09">' . $follow_count . '</count>',
        );
    } else {
        $favorite_count = zib_bbs_get_user_favorite_posts_count($author_id);

        $status_array = array(
            'publish'  => '发布' . '<count class="ml3 em09">' . zib_get_user_post_count($author_id, 'publish', $type) . '</count>',
            'favorite' => '收藏' . '<count class="ml3 em09">' . $favorite_count . '</count>',
        );
    }

    if ('forum_post' === $type && (($current_id && $current_id == $author_id) || is_super_admin())) {
        $status_array['pending'] = '待审核' . '<count class="ml3 em09">' . zib_get_user_post_count($author_id, 'pending', $type) . '</count>';
        $status_array['draft']   = '草稿' . '<count class="ml3 em09">' . zib_get_user_post_count($author_id, 'draft', $type) . '</count>';
        //    $status_array['trash']   = '回收站' . '<count class="ml3 em09">' . zib_get_user_post_count($author_id, 'trash', $type) . '</count>';
    }

    foreach ($status_array as $k => $v) {
        $active_class = $k === $status ? ' c-blue badg mr6' : ' ajax-next but mr6';
        $active_attr  = $k === $status ? ' href="javascript:;"' : ' ajax-replace="true"  href="' . add_query_arg('status', $k, $this_url) . '"';
        $status_html .= '<a' . $active_attr . ' class="' . $status_class . $active_class . '">' . $v . '</a>';
    }

    //右边排序
    $orderby_html = '';
    if ($status_html || zib_get_user_post_count($author_id, 'publish', $type)) {
        $orderby_class        = 'ajax-next';
        $orderby_dropdown_but = '';
        foreach ($orderby_array[$type] as $k => $v) {
            $active_class = $k == $orderby ? ' class="active"' : '';
            $orderby_dropdown_but .= '<li' . $active_class . '><a ajax-replace="true" class="' . $orderby_class . '" href="' . add_query_arg(array('orderby' => $k), $this_url) . '">' . $v . '</a></li>';
        }

        $orderby_html = '<div class="dropdown flex0 pull-right">';
        $orderby_html .= '<a class="but" href="javascript:;" data-toggle="dropdown">排序<i class="fa fa-caret-down opacity5 ml6" aria-hidden="true" style="margin-right: 0;"></i></a>';
        $orderby_html .= '<ul class="dropdown-menu">' . $orderby_dropdown_but . '</ul>';
        $orderby_html .= '</div>';
    }

    $header = '<div class="ajax-item flex jsb mb10 px12-sm"><div class="mr10">' . $status_html . '</div>' . $orderby_html . '</div>';

    return $header;
}
