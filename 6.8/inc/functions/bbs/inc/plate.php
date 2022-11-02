<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-05 20:25:29
 * @LastEditTime: 2022-10-08 22:32:27
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|版块类函数|plate
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//获取版块排序方式选项
function zib_bbs_get_plate_order_options()
{
    $args = array(
        'name'              => '标题名称',
        'date'              => '创建时间',
        'modified'          => '更新时间',
        'last_post'         => '最新发帖',
        'last_reply'        => '最新回帖',
        'posts_count'       => '最多帖子',
        'reply_count'       => '最多回复',
        'today_reply_count' => '今日最多回复',
        'views'             => '最多查看(热门)',
        'follow_count'      => '最多关注',
        'rand'              => '随机',
    );

    return apply_filters('bbs_plate_order_options', $args);
}

function zib_bbs_get_plate_type_options()
{
    //暂未启用
    return array();
    return array(
        ''      => __('标准', 'zib_language'),
        'image' => __('图片', 'zib_language'),
        'video' => __('视频', 'zib_language'),
    );
}

/**
 * @description: 获取版块的顶部header
 * @param {*} $plate_id
 * @param {*} $class
 * @return {*}
 */
function zib_bbs_get_plate_header($plate_id = 0, $class = '', $show_cat = true)
{

    $post = get_post($plate_id);
    if (empty($post->ID)) {
        return;
    }

    $plate_id = $post->ID;

    $class         = $class ? ' ' . $class : '';
    $badge_popover = zib_bbs_get_plate_badge_popover($post, 'badg badg-sm b-yellow ml6');
    $title         = esc_attr(get_the_title($post));
    $excerpt       = esc_attr(get_the_excerpt($post));
    $excerpt       = '<div class="desc px12-sm mt6">' . $excerpt . '</div>';
    $thumb         = zib_bbs_get_thumbnail($post);
    $permalink     = get_permalink($post);

    $blur_bg = '<div class="abs-blur-bg">' . $thumb . '</div><div class="absolute forum-mask"></div>';

    $hot             = zib_bbs_get_hot_badge($plate_id, 'img-badge top jb-red px12-sm');
    $ount_mate       = '';
    $all_posts_count = zib_bbs_get_plate_posts_cut_count($plate_id);
    $ount_mate .= '<item class="mate-posts mr10">帖子 ' . $all_posts_count . '</item>';

    $reply_count = zib_bbs_get_plate_reply_cut_count($plate_id);
    $ount_mate .= $reply_count ? '<item class="mate-reply mr10">互动 ' . zib_bbs_get_plate_reply_cut_count($plate_id) . '</item>' : '';

    $follow_count = zib_bbs_get_plate_follow_cut_count($plate_id);
    if ($follow_count) {
        $ount_mate .= '<item class="mate-follow mr10">关注 ' . $follow_count . '</item>';
    } else {
        $ount_mate .= '<item class="mate-views mr10">阅读 ' . zib_bbs_get_plate_views_cut_count($plate_id) . '</item>';
    }
    $ount_mate = '<div class="px12-sm mt10">' . $ount_mate . '</div>';

    $title_link     = '<h1 class="forum-title em14"><a title="' . esc_attr($title) . '" href="' . esc_url($permalink) . '">' . $title . '</a></h1>';
    $thumbnail_link = '<div class="plate-thumb flex0 ml10">' . $thumb . '</div>';
    $more_dropdown  = zib_bbs_get_plate_header_more_btn($plate_id, '', true);
    $moderator_btns = zib_bbs_get_plate_header_moderator_btns($post);
    $moderator_btns = $moderator_btns ? '<div class="mt10 moderator-btns em09 ">' . $moderator_btns . ' </div>' : '';
    $cat_link       = '';
    if ($show_cat) {
        $cat_link .= zib_bbs_get_plate_cat_link($post, 'badg badg-sm b-theme');
    }

    $cat_link .= $badge_popover;
    $cat_link = $cat_link ? '<div class="mb6">' . $cat_link . '</div>' : '';

    if ('trash' == $post->post_status) {
        $cat_link = '<div class="mb6"><span class="badg badg-sm b-red"><i class="fa fa-trash-o mr3"></i>已删除</span></div>';
    }

    $html = '<div class="forum-header blur-header relative-h mb20' . $class . '">';
    $html .= $blur_bg;
    $html .= $hot;
    $html .= $more_dropdown;

    $html .= '<div class="relative header-content">';
    $html .= '<div class="flex ac">';

    $html .= '<div class="item-info flex1">';
    $html .= $cat_link;
    $html .= $title_link;
    $html .= $ount_mate;
    $html .= $excerpt;
    $html .= '</div>';
    $html .= $thumbnail_link;
    $html .= '</div>';
    $html .= $moderator_btns;

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

function zib_bbs_get_plate_header_moderator_btns($post)
{

    //超级版主
    global $zib_bbs;
    $plate_author     = $post->post_author;
    $plate_id         = $post->ID;
    $btns             = '';
    $moderator_avatar = '';
    $moderator_link   = '';
    $class            = 'but c-white mr6 mt6';

    //版主
    $moderator = get_post_meta($plate_id, 'moderator', true);
    if (is_array($moderator)) {
        $i = 1;
        foreach ($moderator as $user_id) {
            $moderator_avatar .= '<span class="avatar-mini moderator-avatar">' . zib_get_data_avatar($user_id) . '</span>';
            if (3 === $i) {
                break;
            }
            ++$i;
        }
    }

    if ($moderator_avatar) {
        $moderator_html = '<span class="avatar-mini author-moderator-avatar">' . zib_get_data_avatar($plate_author) . '</span><span class="icon-spot"></span>';
        $moderator_html .= $moderator_avatar . (count($moderator) + 1) . '名' . $zib_bbs->plate_moderator_name . '<i class="ml6 fa fa-angle-right em12" style="margin-right: 0;"></i>';
        $moderator_link = zib_bbs_get_plate_moderator_modal_link($plate_id, $class, $moderator_html);
    } else {
        $btns .= '<a href="' . zib_get_user_home_url($plate_author) . '" class="' . $class . '"><span class="avatar-mini mr6">' . zib_get_data_avatar($plate_author) . '</span>' . $zib_bbs->plate_author_name . '</a>';
        $moderator_link = zib_bbs_get_apply_moderator_link($post, $class, '申请' . $zib_bbs->plate_moderator_name . '<i class="ml6 fa fa-angle-right em12" style="margin-right: 0;"></i>');
    }
    if (!$moderator_link) {
        $moderator_link = zib_bbs_get_add_plate_moderator_link($plate_id, $class, '添加' . $zib_bbs->plate_moderator_name . '<i class="ml6 fa fa-angle-right em12" style="margin-right: 0;"></i>');
    }
    $btns .= $moderator_link;

    $btns .= zib_bbs_get_posts_add_page_link(array('plate_id' => $plate_id), $class, zib_get_svg('add') . '发布');

    //  $btns .= zib_bbs_get_all_plate_follow_btn($plate_id, $class, zib_get_svg('add') . '关注');

    return $btns;
}

/**
 * @description: 获取添加版块的连接按钮
 * @param {*} $class
 * @return {*}
 */
function zib_bbs_get_plate_add_link($cat = 0, $class = 'ml20 but hollow c-blue p2-10 px12 flex0', $con = '', $tag = 'botton')
{

    if (!$con) {
        global $zib_bbs;
        $con = zib_get_svg('add') . '创建' . $zib_bbs->plate_name;
    }

    $class = 'plate-add ' . $class;
    $html  = zib_bbs_get_plate_edit_link(0, $cat, $class, $con, $tag);
    return $html;
}

/**
 * @description: 获取添加版块的连接按钮
 * @param {*} $plate_id
 * @param {*} $cat_id
 * @param {*} $class
 * @param {*} $con
 * @return {*}
 */
function zib_bbs_get_plate_edit_link($plate_id = 0, $cat_id = 0, $class = '', $con = '<i class="fa fa-edit"></i>版块设置', $tag = 'botton')
{

    //编辑权限判断
    if ($plate_id && !zib_bbs_current_user_can('plate_edit', $plate_id)) {
        return '';
    }

    //新建版块和选择此分类的权限判读
    if ((!$plate_id && !zib_bbs_current_user_can('plate_add')) || !zib_bbs_current_user_can('select_plate_cat', $cat_id, $plate_id)) {
        //未开启一直显示
        if (!_pz('bbs_show_new_plate', true)) {
            return '';
        }
    }

    if (!get_current_user_id()) {
        //如果未登录则显示登录按钮
        return '<' . $tag . ' class="signin-loader ' . esc_attr($class) . '" href="javascript:;">' . $con . '</' . $tag . '>';
    }

    $url_var = array(
        'action'   => 'plate_edit_modal',
        'plate_id' => $plate_id,
        'cat_id'   => $cat_id,
    );

    $args = array(
        'tag'           => $tag,
        'mobile_bottom' => true,
        'data_class'    => 'full-sm',
        'height'        => 400,
        'class'         => $class,
        'text'          => $con,
        'query_arg'     => $url_var,
    );

    //每次都刷新的modal
    return zib_get_refresh_modal_link($args);
}

//获取版主设置发帖权限的模态框链接
function zib_bbs_get_plate_set_add_limit_link($id = 0, $class = '', $con = '<i class="fa fa-fw fa-unlock-alt mr6"></i>设置发布权限', $tag = 'a')
{
    return zib_bbs_get_set_add_limit_link('plate', $id, $class, $con, $tag);
}

/**
 * @description: 获取版块设置阅读权限的链接按钮
 * @param {*} $plate_id
 * @param {*} $cat_id
 * @param {*} $class
 * @param {*} $con
 * @return {*}
 */
function zib_bbs_get_plate_allow_view_set_link($plate_id = 0, $class = '', $con = '<i class="fa fa-fw fa-eye-slash mr6"></i>设置查看权限', $tag = 'a')
{

    if (!$plate_id || !zib_bbs_current_user_can('plate_set_allow_view', $plate_id)) {
        return;
    }

    $class .= ' allow-view-set';

    $url_var = array(
        'action' => 'plate_allow_view_set_modal',
        'id'     => $plate_id,
    );

    $args = array(
        'tag'           => $tag,
        'class'         => $class,
        'data_class'    => 'modal-mini',
        'height'        => 268,
        'mobile_bottom' => true,
        'text'          => $con,
        'query_arg'     => $url_var,
    );

    //每次都刷新的modal
    return zib_get_refresh_modal_link($args);
}

/**
 * @description: 获取添加版块设置发布权限的链接按钮
 * @param {*} $plate_id
 * @param {*} $class
 * @param {*} $con
 * @return {*}
 */
function zib_bbs_get_set_add_limit_link($type = 'plate', $id = 0, $class = '', $con = '<i class="fa fa-fw fa-unlock-alt mr6"></i>设置发布权限', $tag = 'a')
{

    if (!$id || !zib_bbs_current_user_can($type . '_set_add_limit', $id) || !zib_bbs_get_add_limit_options($type)) {
        return;
    }

    $url_var = array(
        'action' => 'set_add_limit_modal',
        'type'   => $type,
        'id'     => $id,
    );

    $args = array(
        'tag'           => $tag,
        'mobile_bottom' => true,
        'data_class'    => 'modal-mini',
        'height'        => 370,
        'class'         => $class,
        'text'          => $con,
        'query_arg'     => $url_var,
    );

    //每次都刷新的modal
    return zib_get_refresh_modal_link($args);
}

//获取版块的发布限制图标
function zib_bbs_get_plate_add_limit_btn($id = 0, $class = '', $text = '')
{
    return zib_bbs_get_add_limit_btn('posts', $id, $class, $text);
}
//获取版块分类的发布限制图标
function zib_bbs_get_plate_cat_add_limit_btn($id = 0, $class = '', $text = '')
{
    return zib_bbs_get_add_limit_btn('plate', $id, $class, $text);
}
//获取内容阅读限制的图标按钮
function zib_bbs_get_add_limit_btn($type = 'plate', $id = 0, $class = '', $text = '')
{

    if (!$id) {
        return;
    }
    global $zib_bbs;
    if ('plate' === $type) {
        $add_limit = (int) get_term_meta($id, 'add_limit', true);
        if (!$add_limit) {
            return;
        }
        $desc = '可在此' . $zib_bbs->plate_name . '分类创建' . $zib_bbs->plate_name;

    } else {
        $add_limit = (int) get_post_meta($id, 'add_limit', true);
        if (!$add_limit) {
            return;
        }
        $desc = '可在此' . $zib_bbs->plate_name . '发布' . $zib_bbs->posts_name;
    }

    $_id         = 'bbs_' . $type . '_add_limit_' . $add_limit;
    $_name       = _pz('user_cap', array(), $_id);
    $name        = !empty($_name['name']) ? $_name['name'] : '限制' . $add_limit;
    $roles_lists = zib_bbs_get_cap_roles_lists($type . '_add_limit_' . $add_limit);
    $roles_lists = $roles_lists ? '<div class="mb6 muted-2-color">以下用户组' . $desc . '</div>' . zib_str_remove_lazy($roles_lists) : '<div class="c-yellow">仅管理员' . $desc . '</div>';
    if (!$text) {
        $text = '<i class="fa fa-unlock-alt mr3"></i>' . $name;
    }
    $con   = '<div class="text-center em09 add-limit-popover">' . $roles_lists . '</div>';
    $title = '<i class="fa fa-unlock-alt mr6"></i>权限限制';

    return '<span class="' . $class . '"  data-html="1" title="' . esc_attr($title) . '" data-content="' . esc_attr($con) . '" data-trigger="hover" data-placement="auto top" data-container="body" data-toggle="popover">' . $text . '</span>';
}

//获取版块权限限制的徽章
function zib_bbs_get_plate_badge_popover($post = null, $class = '', $text = '')
{
    global $zib_bbs;
    if (!is_object($post)) {
        $post = get_post($post);
    }
    $id            = $post->ID;
    $con           = '';
    $add_limit_con = '';
    $add_limit     = (int) get_post_meta($id, 'add_limit', true);
    if ($add_limit) {
        $desc = '可在此' . $zib_bbs->plate_name . '发布' . $zib_bbs->posts_name;
        if (!$text) {
            $_id   = 'bbs_posts_add_limit_' . $add_limit;
            $_name = _pz('user_cap', array(), $_id);
            $text  = !empty($_name['name']) ? $_name['name'] : '';
        }
        $roles_lists   = zib_bbs_get_cap_roles_lists('posts_add_limit_' . $add_limit);
        $roles_lists   = $roles_lists ? '<p class="separator muted-3-color mb6">以下用户组' . $desc . '</p>' . $roles_lists : '<div class="separator muted-3-color mb6 c-yellow">仅管理员' . $desc . '</div>';
        $add_limit_con = '<div class="em09">' . $roles_lists . '</div>';
    }

    $allow_view_con = zib_bbs_get_allow_view_data($post)['html'];
    $allow_view_con = str_replace('可查看', '可查看此' . $zib_bbs->plate_name . '内容', $allow_view_con);
    if (!$allow_view_con && !$add_limit_con) {
        return;
    }

    if ($allow_view_con && $add_limit_con) {
        $add_limit_con = '<div class="mb20">' . $add_limit_con . '</div>';
    }

    if (!$text) {
        $text = '<i class="fa fa-unlock-alt" style="width:1em;"></i>';
    }

    $con   = '<div class="text-center em09 add-limit-popover">' . zib_str_remove_lazy($add_limit_con . $allow_view_con) . '</div>';
    $title = '<i class="fa fa-unlock-alt mr6"></i>权限限制';

    return '<span class="' . $class . '"  data-html="1" title="' . esc_attr($title) . '" data-content="' . esc_attr($con) . '" data-trigger="hover" data-placement="auto top" data-container="body" data-toggle="popover">' . $text . '</span>';
}

/**
 * @description: 获取删除版块的连接按钮
 * @param {*} $plate_id
 * @param {*} $cat_id
 * @param {*} $class
 * @param {*} $con
 * @return {*}
 */
function zib_bbs_get_plate_delete_link($plate_id = 0, $class = '', $con = '<i class="fa fa-trash-o fa-fw"></i>删除', $revoke_con = '<i class="fa fa-trash-o fa-fw mr6"></i>撤销删除', $tag = 'a')
{

    if (!$plate_id || !zib_bbs_current_user_can('plate_delete', $plate_id)) {
        return '';
    }

    $get_post = get_post($plate_id);
    $class    = 'plate-delete ' . $class;

    if (empty($get_post->post_status) || 'trash' === $get_post->post_status) {
        if (!zib_bbs_current_user_can('plate_edit', $plate_id)) {
            return;
        }

        $class .= ' wp-ajax-submit';

        $url_var = array(
            'action'   => 'posts_delete_revoke',
            'id'       => $plate_id,
            '_wpnonce' => wp_create_nonce('posts_delete_revoke'),
        );

        $ajax = add_query_arg($url_var, admin_url('admin-ajax.php'));
        return '<' . $tag . ' class="' . $class . '" href="javascript:;" ajax-href="' . $ajax . '">' . $revoke_con . '</' . $tag . '>';
    }
    ;

    $url_var = array(
        'action' => 'plate_delete_modal',
        'id'     => $plate_id,
    );

    $args = array(
        'tag'           => $tag,
        'class'         => $class,
        'mobile_bottom' => true,
        'data_class'    => 'modal-mini',
        'height'        => 329,
        'text'          => $con,
        'query_arg'     => $url_var,
    );

    //每次都刷新的modal
    return zib_get_refresh_modal_link($args);
}

/**
 * @description: 获取版块的mates
 * @param {*} $plate_id
 * @return {*}
 */
function zib_bbs_get_plate_count_mates($plate_id = 0)
{
    if (!$plate_id) {
        global $post;
        $plate_id = $post->ID;
    }
    global $zib_bbs;

    // $add_limit     = zib_bbs_get_plate_add_limit_btn($plate_id, 'mr10', '<i class="fa fa-unlock-alt fa-fw"></i>');
    $badge_popover = zib_bbs_get_plate_badge_popover($plate_id, 'mr10', '<i class="fa fa-unlock-alt"></i>');

    $all_reply_count = zib_bbs_get_plate_reply_cut_count($plate_id);
    //有互动优先显示互动，没有互动显示帖子
    if ($all_reply_count) {
        $all_reply_count = '<item class="mate-posts mr10" data-toggle="tooltip" title="互动"><icon class="">' . zib_get_svg('comment') . '</icon>' . $all_reply_count . '</item>';
    } else {
        $posts_count     = zib_bbs_get_plate_posts_cut_count($plate_id);
        $all_reply_count = '<item class="mate-posts mr10" data-toggle="tooltip" title="' . $zib_bbs->posts_name . '"><icon class="">' . zib_get_svg('post') . '</icon>' . $posts_count . '</item>';
    }

    $follow_count = zib_bbs_get_plate_follow_cut_count($plate_id);
    $follow_count = $follow_count ? '<item class="mate-follow" data-toggle="tooltip" title="关注"><icon class=""><i class="fa fa-heart-o"></i></icon>' . $follow_count . '</item>' : '';

    $views_count = zib_bbs_get_plate_views_cut_count($plate_id);
    $views_count = '<item class="mate-views mr10" data-toggle="tooltip" title="热度"><icon class="">' . zib_get_svg('hot') . '</icon>' . $views_count . '</item>';

    $author_id = get_the_author_meta('ID');
    $author    = zib_get_avatar_box($author_id, 'avatar-mini');
    $author    = '<item class="mate-author" data-toggle="tooltip" title="热度">' . $author . '</item>';

    return '<div class="count-mates px12-sm em09 text-ellipsis flex ac jsb"><div>' . $badge_popover . $all_reply_count . $views_count . $follow_count . '</div></div>';
}

/**
 * @description: 获取卡片模式的版块列表
 * @param {*} $class
 * @return {*}
 */
function zib_bbs_get_plate_card($class = '', $follow = true, $today = false)
{
    global $post;
    $class = $class ? ' ' . $class : '';
    $title = esc_attr(get_the_title());

    $permalink    = get_permalink();
    $thumb        = zib_bbs_get_thumbnail();
    $target_blank = _pz('plate_target_blank') ? ' target="_blank"' : '';

    $views_count     = zib_bbs_get_plate_views_cut_count($post->ID);
    $follow_count    = zib_bbs_get_plate_follow_cut_count($post->ID);
    $all_reply_count = zib_bbs_get_plate_reply_cut_count($post->ID);

    $title_attr = $follow_count ? $follow_count . '人关注' : '';
    $title_attr .= $title_attr ? ' ' : '';
    $title_attr .= $all_reply_count ? $all_reply_count . '次互动' : '';
    $title_attr = $title_attr ? ' data-toggle="tooltip" title="' . $title_attr . '"' : '';

    $follow_btn = $follow ? zib_bbs_get_all_plate_follow_btn($post->ID, 'but jb-pink but-plate btn-block mt20') : '';

    if ($today) {
        $today_reply_count = zib_bbs_get_plate_reply_count($post->ID, true);
        $today             = $today_reply_count ? '<badge class="spot abs-tr" data-toggle="tooltip" title="今日有' . $today_reply_count . '次新互动"></badge>' : '';
    }

    $html = '<div class="plate-card' . $class . '">';
    $html .= '<a' . $target_blank . ' title="' . $title . '" href="' . $permalink . '">';
    $html .= '<div class="plate-thumb">' . $thumb . $today . '</div>';
    $html .= '<div class="title text-ellipsis mt10">' . $title . '</div>';
    $html .= '<div class="mt3 px12 muted-2-color text-ellipsis count"' . $title_attr . '>' . $views_count . '热度</div>';
    $html .= '</a>';
    $html .= $follow_btn;
    $html .= '</div>';

    return $html;
}

function zib_bbs_get_main_plate($class = '')
{
    global $post;
    //准备必要参数
    $class     = $class ? ' ' . $class : '';
    $title     = esc_attr(get_the_title());
    $excerpt   = esc_attr(get_the_excerpt());
    $permalink = get_permalink($post);
    $thumb     = zib_bbs_get_thumbnail();

    $today_reply_count = zib_bbs_get_plate_reply_cut_count($post->ID, 'today');
    $today_reply_count = $today_reply_count ? '<span class="badg but-plate hide-sm flex0" title="今日互动 ' . $today_reply_count . '">' . zib_get_svg('comment-fill', null, 'icon c-red em12') . ' 今日：' . $today_reply_count . '</span>' : '';
    $target_blank      = _pz('plate_target_blank') ? ' target="_blank"' : '';
    $hot               = zib_bbs_get_hot_badge($post->ID, 'img-badge top jb-red px12-sm');
    $ount_mate         = zib_bbs_get_plate_count_mates($post->ID);
    $excerpt           = '<a' . $target_blank . ' class="excerpt em09" href="' . $permalink . '">' . $excerpt . '</a>';

    $title_link     = '<h2 class="forum-title"><a class="flex ac"' . $target_blank . ' href="' . $permalink . '" title="' . $title . '"><span class="mr6 text-ellipsis">' . $title . '</span>' . $today_reply_count . '</a></h2>';
    $thumbnail_link = '<a' . $target_blank . ' class="plate-thumb" href="' . $permalink . '">' . $thumb . '</a>';
    $info_header    = '<div class="info-header">' . $title_link . '</div>';

    $html = '<div class="plate-item flex' . $class . '">';
    $html .= $thumbnail_link;
    $html .= '<div class="item-info flex1">';
    $html .= $hot;
    $html .= $info_header;
    $html .= $excerpt;
    $html .= $ount_mate;
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/**
 * @description: 获取关注版块的按钮
 * @param {*} $class
 * @param {*} $follow_id
 * @param {*} $text
 * @param {*} $ok_text
 * @return {*}
 */
function zib_bbs_get_all_plate_follow_btn($plate_id, $class = 'but jb-pink', $text = '关注', $ok_text = '已关注')
{

    $user_id = get_current_user_id();
    if (zib_is_my_meta_ed('follow_plate', $plate_id)) {
        $class .= ' active';
        $text = $ok_text;
    }
    $action = ' ajax-action="follow_plate"';

    if (!$user_id) {
        $action = '';
        $class .= ' signin-loader';
    }
    return '<a href="javascript:;"' . $action . ' class="btn-follow ' . $class . '" data-id="' . $plate_id . '"><text>' . $text . '</text></a>';
}

//移动版快
function zib_bbs_posts_plate_move($id, $new_id, $old_id)
{
    if (is_array($id)) {
        foreach ($id as $_id) {
            zib_bbs_posts_plate_move($_id, $new_id, $old_id);
        }
    } else {
        update_post_meta($id, 'plate_id', $new_id);
        do_action('save_post', $new_id, get_post($new_id), true);
        do_action('save_post', $old_id, get_post($old_id), true);
        do_action('posts_plate_move', $id, $new_id, $old_id);
    }
}

/**
 * @description: 批量切换版块
 * @param {*} $plate_id
 * @param {*} $posts_id
 * @return {*}
 */
function zib_bbs_plates_move($new_id, $old_id = null)
{
    global $wpdb;
    $WHERE = "meta_key = 'plate_id'";
    if ($old_id) {
        if (is_array($old_id)) {
            $WHERE .= " AND meta_value IN (" . implode(",", $old_id) . ")";
        } else {
            $WHERE .= " AND meta_value = $old_id";
        }
    }

    $sql = "UPDATE $wpdb->postmeta SET `meta_value` = $new_id WHERE $WHERE";

    $wpdb->query($sql);
    do_action('save_post', $new_id, get_post($new_id), true);

    if (is_array($old_id)) {
        foreach ($old_id as $_id) {
            do_action('save_post', $_id, get_post($old_id), true);
        }
    } else {
        do_action('save_post', $old_id, get_post($old_id), true);
    }
}

function zib_bbs_get_plate_slide_card($args = array(), $class = '', $follow = true, $today = false)
{

    $new_query = zib_bbs_get_plate_query($args);

    $lists = '';
    if ($new_query->have_posts()) {
        while ($new_query->have_posts()): $new_query->the_post();
            $lists .= '<div class="swiper-slide">';
            $lists .= zib_bbs_get_plate_card($class, $follow, $today);
            $lists .= '</div>';
        endwhile;
        wp_reset_query();
    }

    if (!$lists) {
        return;
    }

    $html = '';
    $html .= '<div class="swiper-container swiper-scroll scroll-plate">
                <div class="swiper-wrapper">
                    ' . $lists . '
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>';

    return $html;

}

/**
 * @description: 根据分类显示版块列表，可选排序方式
 * @param {*} $cat_id 分类ID
 * @param {*} $orderby 排序方式|last_reply:最后回复 | last_post:最后发帖 | reply_count:回复数量 | today_reply_count:今日回复数量 | view:查看总数（热门）|name 名称 | like:点赞总数 | follow_count:关注总数
 * @return {*}
 */
function zib_bbs_get_plate_main_lists($args = array())
{

    $new_query = zib_bbs_get_plate_query($args);
    $lists     = '';
    if ($new_query->have_posts()) {
        while ($new_query->have_posts()): $new_query->the_post();
            $lists .= zib_bbs_get_main_plate();
        endwhile;
    }
    wp_reset_query();

    return $lists;
}

function zib_bbs_get_plate_query($args = array())
{

    $query_args = array(
        'post_type'   => 'plate',
        'post_status' => array('publish'),
        'order'       => 'DESC',
        'orderby'     => 'modified',
        'showposts'   => -1,
    );

    if (isset($args['cat'])) {
        if ($args['cat']) {
            $query_args['tax_query'][] = array(
                'taxonomy'         => 'plate_cat',
                'field'            => 'id',
                'terms'            => (array) $args['cat'],
                'include_children' => true,
            );
        }
        unset($args['cat']);
    }

    //其它筛选
    if (isset($args['filter'])) {
        if (in_array($args['filter'], array('is_hot'))) {
            $query_args['meta_query'][] = array(
                'key'   => $args['filter'],
                'value' => 1,
            );
        }
        unset($args['filter']);
    }

    //排序方式
    if (isset($args['orderby'])) {
        $query_args = zib_bbs_query_orderby_filter($args['orderby'], $query_args);
        unset($args['orderby']);
    }

    //手动排序
    if (!empty($args['include'])) {
        $query_args['post__in'] = $args['include'];
        $query_args['orderby']  = 'post__in';
        unset($args['include']);
    }

    //版块无需审核和回收站
    $query_args = array_merge($query_args, $args);

    return new WP_Query($query_args);
}

/**
 * @description: 制作new WP_Query meta过滤
 * @param {*} $orderby
 * @param {*} $args
 * @return {*}
 */
function zib_bbs_get_meta_filter_query($metas, $args = array())
{

    $mate_orderbys = array('essence', 'question_status');
    foreach ($metas as $k => $v) {

    }

    return $args;
}

/**
 * @description: 根据排序方式获取全部版块分类列表
 * @param {*} $orderby 可选排序方式 last_reply:最后回复 | last_post:最后发帖 | count版块数量 | views:查看总数（热门）|name 名称
 * @param {*} $hide_empty 是否隐藏没有版块的分类
 * @return {*}
 */
function zib_bbs_get_plate_cats_orderby($orderby = 'count', $hide_empty = true, $include = false)
{
    //可选的排序方式
    //last_reply:最后回复 | last_post:最后发帖 | count:帖子数量 | views:查看总数（热门）|name 名称

    //$orderby = 'views';
    $query = array(
        'taxonomy'   => array('plate_cat'), //分类法
        'orderby'    => $orderby, //默认为版块数量
        'order'      => 'DESC',
        'count'      => true,
        'hide_empty' => $hide_empty,
    );

    if ($include) {
        $query['include'] = $include;
        $query['orderby'] = 'include';
    }

    return zib_bbs_get_term_query($query);
}

/**
 * @description: 获取文章的版块ID
 * @param {*} $posts_id
 * @return {*}
 */
function zib_bbs_get_plate_id($posts_id = 0)
{
    if (!$posts_id) {
        global $post;
        $posts_id = $post->ID;
    }

    $plate_id = get_post_meta($posts_id, 'plate_id', true);

    return (int) $plate_id;
}

/**
 * @description: 获取当前版块或帖子的版块ID
 * @param {*} $posts_id
 * @return {*}
 */
function zib_bbs_get_the_plate_id($post = null)
{

    if (!is_object($post)) {
        $post = get_post($post);
    }

    if (!isset($post->ID)) {
        return 0;
    }

    if ('plate' === $post->post_type) {
        return (int) $post->ID;
    }

    if ('forum_post' === $post->post_type) {
        return zib_bbs_get_plate_id($post->ID);
    }

    return 0;
}

/**
 * @description: 获取当前版块或帖子的版块OBJ数据
 * @param {*} $posts_id
 * @return {*}
 */
function zib_bbs_get_the_plate($post = null)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }

    if (!isset($post->ID)) {
        return null;
    }

    if ('plate' === $post->post_type) {
        return $post;
    }

    if ('forum_post' === $post->post_type) {
        $plate_id = zib_bbs_get_plate_id($post->ID);
        if ($plate_id) {
            return get_post($plate_id);
        }
    }

    return null;
}

/**
 * @description: 获取文章的版块obj资料
 * @param {*} $posts_id
 * @return {*}
 */
function zib_bbs_get_plate($posts_id = 0)
{
    if (!$posts_id) {
        global $post;
        $posts_id = $post->ID;
    }

    $plate_id = get_post_meta($posts_id, 'plate_id', true);

    return get_post($plate_id);
}

//获取tab导航
function zib_bbs_get_plate_tab_nav($tabs_options, $index = 1)
{
    return zib_bbs_get_tab_nav('nav', $tabs_options, 'plate', _pz('bbs_plate_tab_swiper', true), $index);
}

//获取栏目的tab内容
function zib_bbs_get_plate_tab_content($tabs_options, $index = 1)
{

    return zib_bbs_get_tab_nav('content', $tabs_options, 'plate', _pz('bbs_plate_tab_swiper', true), $index);
}

//获取版块自己显示的关注数量
function zib_bbs_get_plate_follow_cut_count($id = 0)
{
    if (!$id) {
        global $post;
        $id = $post->ID;
    }

    $count = get_post_meta($id, 'follow_count', true);

    return _cut_count($count);
}

//获取直接显示的文章数量
function zib_bbs_get_plate_posts_cut_count($id = 0, $today = false)
{
    return _cut_count(zib_bbs_get_plate_posts_count($id, $today));
}

//获取版块直接显示的总阅读数量
function zib_bbs_get_plate_views_cut_count($id = 0)
{
    return _cut_count(zib_bbs_get_posts_meta('views', $id));
}

//获取版块直接显示的总阅读数量
function zib_bbs_get_plate_reply_cut_count($id = 0, $today = false)
{
    return _cut_count(zib_bbs_get_plate_reply_count($id, $today));
}

//获取版块下面的帖子数量
function zib_bbs_get_plate_posts_count($post = null, $today = false)
{

    $post = get_post($post);

    if (empty($post->ID)) {
        return;
    }

    $id = $post->ID;

    $type = $today ? 'today' : 'all';
    //第一步通过缓存获取
    $cache_num = wp_cache_get($id, 'plate_posts_count_' . $type, true);
    if (false !== $cache_num) {
        return $cache_num;
    }

    //第二步通过全局变量获取
    global $wpdb, $bbs_plate_posts_count;

    if (!isset($bbs_plate_posts_count[$type])) {
        //第三步通过数据库查询
        $time     = current_time('Y-m-d 00:00:00');
        $sql_time = $today ? "AND $wpdb->posts.post_date_gmt >= '$time'" : '';
        $sql      = "SELECT $wpdb->postmeta.meta_value as `id` , count(id) as `count` FROM $wpdb->posts
        INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = 'plate_id' )
        WHERE $wpdb->posts.post_type = 'forum_post' AND $wpdb->posts.post_status = 'publish' $sql_time
        GROUP BY $wpdb->postmeta.meta_value";
        $counts = $wpdb->get_results($sql);

        //保存到全局变量
        $GLOBALS['bbs_plate_posts_count'][$type] = $counts;
    } else {
        $counts = $bbs_plate_posts_count[$type];
    }

    $count = (int) zib_array_search($counts, $id);
    //添加缓存，长期有效
    wp_cache_set($id, $count, 'plate_posts_count_' . $type);

    return $count;
}

//获取版块总计回复数量
function zib_bbs_get_plate_reply_count($id = 0, $today = false)
{
    if (!$id) {
        global $post;
        $id = $post->ID;
    }
    $type = $today ? 'today' : 'all';
    //第一步通过缓存获取
    $cache_num = wp_cache_get($id, 'plate_reply_count_' . $type, true);
    if (false !== $cache_num) {
        return $cache_num;
    }

    //第二步通过全局变量获取
    global $wpdb, $bbs_plate_reply_count;

    if (!isset($bbs_plate_reply_count[$type])) {
        //第三步通过数据库查询
        $time     = current_time('Y-m-d 00:00:00');
        $sql_time = $today ? "AND $wpdb->comments.comment_date_gmt >= '$time'" : '';
        $sql      = "SELECT $wpdb->postmeta.meta_value as `id`,COUNT(*) as `count` FROM $wpdb->comments
        INNER JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID AND $wpdb->comments.comment_approved='1')
        INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = 'plate_id' )
        WHERE $wpdb->posts.post_type = 'forum_post' AND $wpdb->posts.post_status = 'publish' $sql_time
        GROUP BY $wpdb->postmeta.meta_value";
        $counts = $wpdb->get_results($sql);

        //保存到全局变量
        $GLOBALS['bbs_plate_reply_count'][$type] = $counts;
    } else {
        $counts = $bbs_plate_reply_count[$type];
    }

    $count = (int) zib_array_search($counts, $id);
    //添加缓存，长期有效
    wp_cache_set($id, $count, 'plate_reply_count_' . $type);

    return $count;
}

//版块信息的侧边栏模块
function zib_bbs_get_plate_info_mini_box($post = null, $class = '')
{

    if (!is_object($post)) {
        $post = get_post($post);
    }

    global $zib_bbs;
    $plate_id = $post->ID;
    $class    = $class ? ' ' . $class : '';

    $title     = esc_attr(get_the_title($plate_id));
    $excerpt   = esc_attr(get_the_excerpt($plate_id));
    $excerpt   = '<div class="em09 muted-2-color mt10">' . $excerpt . '</div>';
    $thumb     = zib_bbs_get_thumbnail($plate_id);
    $permalink = get_permalink($plate_id);
    //  $add_limit     = zib_bbs_get_plate_add_limit_btn($plate_id, 'c-yellow ', '<i class="fa fa-unlock-alt fa-fw"></i>');
    $badge_popover = zib_bbs_get_plate_badge_popover($post, 'c-yellow ', '<span class="badg badg-sm c-yellow mr3"><i class="fa fa-unlock-alt"></i></span>');

    $hot             = zib_bbs_get_hot_badge($plate_id, 'img-badge top jb-red px12-sm');
    $ount_mate       = '';
    $all_posts_count = zib_bbs_get_plate_posts_cut_count($plate_id);
    $ount_mate .= '<item class="mate-views"><div class="em09 opacity5 mb6">' . $zib_bbs->posts_name . '</div><div class="em14"> ' . $all_posts_count . '</div></item>';
    $ount_mate .= '<item class="mate-views"><div class="em09 opacity5 mb6">互动</div><div class="em14"> ' . zib_bbs_get_plate_reply_cut_count($plate_id) . '</div></item>';
    $ount_mate .= '<item class="mate-views"><div class="em09 opacity5 mb6">阅读</div><div class="em14"> ' . zib_bbs_get_plate_views_cut_count($plate_id) . '</div></item>';
    $ount_mate = '<div class="count-mates text-center">' . $ount_mate . '</div>';

    $follow_count = zib_bbs_get_plate_follow_cut_count($plate_id);
    $follow_html  = $follow_count ? '<badge class="img-badge px12 follow-info">' . $follow_count . '人已关注</badge>' : '';

    $follow_btn = zib_bbs_get_all_plate_follow_btn($plate_id, 'but hollow c-red', zib_get_svg('add') . '关注');
    $new_btn    = zib_bbs_get_posts_add_page_link(array('plate_id' => $plate_id), 'but hollow c-blue mr10');

    $btn = '<div class="mt20 mb20 buts">' . $new_btn . $follow_btn . '</div>';

    $status_badge = '';
    if ('trash' == $post->post_status) {
        $status_badge = zib_bbs_get_status_badge('', $post);
    }

    $title_link     = '<h1 class="forum-title em14">' . $badge_popover . $status_badge . '<a title="' . esc_attr($title) . '" href="' . esc_url($permalink) . '">' . $title . '</a></h1>';
    $thumbnail_link = '<div class="plate-thumb flex0 mb20">' . $thumb . '</div>';

    $more_dropdown = zib_bbs_get_plate_header_more_btn($plate_id);

    $html = '<div class="forum-header relative-h plate-info-sidebar' . $class . '">';
    $html .= $hot;
    $html .= $follow_html;
    $html .= $more_dropdown;
    $html .= '<div class="relative text-center header-content">';
    $html .= $thumbnail_link;
    $html .= '<div class="item-info">';
    $html .= $title_link;
    $html .= $excerpt;
    $html .= $btn;
    $html .= $ount_mate;
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

function zib_bbs_get_plate_share_btn($plate_id, $class = '')
{
    return zib_get_post_share_btn($plate_id, 'btn-share ' . $class, true);
}

function zib_bbs_get_plate_search_btn($plate_id, $class = '')
{
    global $zib_bbs;
    $plate      = get_post($plate_id);
    $post_title = esc_attr($plate->post_title);

    $args = array(
        'class'       => $class,
        'trem'        => 'plate_' . $plate->ID,
        'trem_name'   => zib_str_cut($post_title, 0, 8),
        'type'        => 'forum',
        'placeholder' => '在' . $zib_bbs->plate_name . '[' . $post_title . ']中搜索' . $zib_bbs->posts_name,
    );

    return zib_get_search_link($args);
}

function zib_bbs_get_plate_header_more_btn($plate_id, $class = '', $show_follow = false)
{

    $html = '';
    if ($show_follow) {
        $follow_btn = zib_bbs_get_all_plate_follow_btn($plate_id, $class, '<i title="关注" class="fa fa-heart-o mr6 em12"></i>', '<i title="已关注" class="fa fa-heart mr6 em12"></i>');
        $html .= $follow_btn ? $follow_btn : '';
    }

    //搜索
    $search = zib_bbs_get_plate_search_btn($plate_id, 'item');
    $html .= $search ? $search : '';

    //分享
    $share = zib_bbs_get_plate_share_btn($plate_id, 'item');
    $html .= $share ? $share : '';

    //更多按钮
    $dropdown = zib_bbs_get_plate_more_dropdown($plate_id, 'pull-right', 'item mr3');
    $html .= $dropdown ? $dropdown : '';

    if (!$html) {
        return;
    }

    $class = $class ? ' ' . $class : '';
    return '<div class="flex ac more-btns' . $class . '">' . $html . '</div>';
}

/**
 * @description: 获取版块更多按钮的dropdown下拉框
 * @param {*} $term_id
 * @param {*} $class
 * @param {*} $con
 * @return {*}
 */
function zib_bbs_get_plate_more_dropdown($plate_id, $class = '', $con_class = '', $con = '')
{

    if (!$plate_id) {
        return;
    }

    $con       = $con ? $con : zib_get_svg('menu_2');
    $class     = $class ? ' ' . $class : '';
    $con_class = $con_class ? ' class="' . $con_class . '"' : '';

    global $zib_bbs;
    $action = '';
    $name   = $zib_bbs->plate_name;

    $add = zib_bbs_get_plate_add_link(false, '', zib_get_svg('add', null, 'icon mr6 fa-fw') . '创建新' . $name, 'a');
    $action .= $add ? '<li>' . $add . '</li>' : '';

    $edit = zib_bbs_get_plate_edit_link($plate_id, false, '', zib_get_svg('set', null, 'icon mr6 fa-fw') . '编辑此' . $name, 'a');
    $action .= $edit ? '<li>' . $edit . '</li>' : '';

    $add_limit = zib_bbs_get_plate_set_add_limit_link($plate_id);
    $action .= $add_limit ? '<li>' . $add_limit . '</li>' : '';

    $allow_view = zib_bbs_get_plate_allow_view_set_link($plate_id);
    $action .= $allow_view ? '<li>' . $allow_view . '</li>' : '';

    $edit = zib_bbs_get_edit_plate_moderator_link($plate_id, '', zib_get_svg('circle', null, 'icon mr6 fa-fw') . '管理' . $zib_bbs->plate_moderator_name);
    $action .= $edit ? '<li>' . $edit . '</li>' : '';

    $del = zib_bbs_get_plate_delete_link($plate_id, 'c-red', '<i class="fa fa-trash-o mr6 fa-fw"></i>删除此' . $name);
    $action .= $del ? '<li>' . $del . '</li>' : '';

    if (!$action) {
        return;
    }

    $html = '<span class="dropdown' . $class . '">';
    $html .= '<a href="javascript:;"' . $con_class . ' data-toggle="dropdown">' . $con . '</a>';
    $html .= '<ul class="dropdown-menu">';
    $html .= $action;
    $html .= '</ul>';
    $html .= '</span>';
    return $html;
}

//挂钩搜索功能
function zib_bbs_get_search_plate($html = '', $s, $cat, $user)
{

    if (!have_posts() || !$s) {
        return '';
    }

    //开始构建内容
    $lists = '<div class="plate-lists ajax-item">';
    while (have_posts()): the_post();
        $lists .= zib_bbs_get_main_plate();
    endwhile;
    $lists .= '</div>';

    $paginate = zib_paging(false, false);
    if ($paginate) {
        $lists .= $paginate;
        $lists .= '<div class="post_ajax_loader" style="display:none;"><div class="plate-lists"><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div></div><div class="plate-lists"><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div><div class="plate-item"><div class="plate-thumb"><div style="height: 100%;" class="placeholder radius"></div></div><div class="item-info"><div class="placeholder k1"></div><div style="height: 30px;" class="placeholder k2 hide-sm mt10"></div><div class="placeholder s1 mt10"></div></div></div></div></div>';
    }

    return $lists;
}
add_filter('search_content_plate', 'zib_bbs_get_search_plate', 10, 4);

//获取所有版块的选择明细||未启用
function bbs_plate_options()
{

    //第一步通过缓存获取
    $cache = wp_cache_get('bbs_plate_options', '', true);
    if (false !== $cache) {
        return $cache;
    }

    //第二步通过全局变量获取
    global $bbs_plate_options, $wpdb;
    if (null !== $bbs_plate_options) {
        return $bbs_plate_options;
    }

    $sql_posts    = $wpdb->posts;
    $sql_term_rel = $wpdb->term_relationships;
    $sql_terms    = $wpdb->terms;
    $sql_taxonomy = $wpdb->term_taxonomy;
    $options      = array();

    //待处理，部分环境无效
    $sql = "SELECT $sql_posts.ID,$sql_posts.post_title,$sql_terms.name FROM $sql_posts
    INNER JOIN $sql_term_rel ON $sql_posts.ID = $sql_term_rel.object_id and $sql_posts.post_type = 'plate'
    INNER JOIN $sql_taxonomy ON  $sql_taxonomy.term_taxonomy_id = $sql_term_rel.term_taxonomy_id and $sql_taxonomy.taxonomy = 'plate_cat'
    INNER JOIN $sql_terms ON $sql_terms.term_id = $sql_term_rel.term_taxonomy_id
    WHERE $sql_posts.post_status = 'publish'
    GROUP BY $sql_posts.ID ORDER BY $sql_posts.post_date DESC";

    $query = $wpdb->get_results($sql);
    if (!empty($query[0])) {
        foreach ($query as $item) {
            $options[$item->name][$item->ID] = $item->post_title;
        }
    }

    //添加缓存，长期有效
    wp_cache_set('bbs_plate_options', $options);
    return $bbs_plate_options = $options;
}

/**---------------以下为版块页面内容------------------ */
//版块页面加载时候，如果版块已经删除则404提示
function zib_bbs_plate_page_locate()
{
    global $post;
    $post_status = $post->post_status;

    if ('trash' === $post_status) {
        global $zib_bbs;
        $plate_id = $post->ID;
        $name     = $zib_bbs->plate_name;
        $con      = '<p class="muted-2-color box-body separator" style="margin:60px 0;">当前' . $zib_bbs->plate_name . '已删除</p>';
        $edit     = zib_bbs_get_plate_edit_link($plate_id, false, 'but hollow c-blue padding-lg', '重新发布此' . $name . '<b><i class="fa fa-angle-right ml10 em12"></i></b>', 'a');

        $con .= $edit ? '<div class="">' . $edit . '</div>' : '';
        bbs_locate_template_nocan_edit($con);
    }
}
add_action('bbs_locate_template_plate', 'zib_bbs_plate_page_locate');

//版块顶部模块
function zib_bbs_plate_page_mobile_header()
{
    $is_mobile = wp_is_mobile();
    $show      = _pz('bbs_plate_top_info_s');
    if (!$show || (!in_array('pc_s', $show) && !$is_mobile) || (!in_array('m_s', $show) && $is_mobile)) {
        return;
    }

    $plate_header = zib_bbs_get_plate_header();

    $html = '<div class="">';
    $html .= $plate_header;
    $html .= '</div>';
    echo $html;
}
add_action('bbs_plate_page_content', 'zib_bbs_plate_page_mobile_header');

//页面主要内容
function zib_bbs_plate_page_content()
{
    global $post;

    //如果内容被隐藏
    $is_hide = zib_bbs_get_plate_not_allow_view($post);
    if ($is_hide) {
        echo '<div class="plate-tab zib-widget">' . zib_get_null('抱歉！您暂无查看此版块内容的权限', 10, 'null-cap.svg', '') . $is_hide . '</div>';
    } else {
        zib_bbs_plate_page_tab_content();
    }

}
add_action('bbs_plate_page_content', 'zib_bbs_plate_page_content');

//获取版块不能查看内容的html，可以作为判断函数
function zib_bbs_get_plate_not_allow_view($post = null)
{
    $data = zib_bbs_get_allow_view_data($post);
    return $data['open'] && !$data['allow_reason'] ? $data['not_html'] : '';
}

//获取内容阅读限制的图标按钮
function zib_bbs_get_plate_allow_view_btn($post = 0, $class = '', $not_show = true)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    global $zib_bbs;
    $type_name = '';
    $data      = zib_bbs_get_allow_view_data($post);

    if (!$data['open'] || !$data['html'] || ($not_show && !$data['allow_reason'])) {
        return;
    }

    $text = !empty($data['btn_icon']) ? $data['btn_icon'] : '<span class="badg badg-sm c-yellow"><i class="fa fa-unlock-alt"></i></span>';
    $con  = zib_str_remove_lazy($data['html']);

    $con .= $data['allow_reason'] ? '<div class="c-blue em09 mt10 text-center">' . $data['allow_reason'] . '</div>' : '';
    $title = '<i class="fa fa-unlock-alt mr6"></i>' . $type_name . '阅读限制';

    return '<span class="' . $class . '"  data-html="1" title="' . esc_attr($title) . '" data-content="' . esc_attr($con) . '" data-trigger="hover" data-placement="auto top" data-container="body" data-toggle="popover">' . $text . '</span>';
}

//显示版块页面的tab内容
function zib_bbs_plate_page_tab_content()
{
    global $post;
    $tabs_options = false;
    $active_index = 0;

    //优先显示版块独立tab
    if (get_post_meta($post->ID, 'plate_tab_alone_s', true)) {
        $tabs_options = get_post_meta($post->ID, 'plate_tab', true);
        $active_index = (int) get_post_meta($post->ID, 'tab_active_index', true);
    }
    if (!$tabs_options) {
        $tabs_options = apply_filters('bbs_plate_tab_options', _pz('bbs_plate_tab', array()));
    }
    if (!$active_index) {
        $active_index = _pz('bbs_plate_tab_active_index') ?: 1;
    }

    if (!is_array($tabs_options)) {
        $tabs_options = array();
    }

    //查询是否有管理版块的权限，有则显示管理版块
    if ($post && (zib_bbs_current_user_can('posts_audit', $post) || zib_bbs_current_user_can('posts_edit_other', $post))) {
        if (zib_bbs_current_user_can('posts_audit', $post)) {
            global $zib_bbs;
            $tabs_options['pending'] = array(
                'title'       => zib_get_svg('approve', null, 'icon mr3') . '待审核',
                'post_status' => 'pending',
                'show'        => array('pc_s', 'm_s'),
            );
        }
        if (zib_bbs_current_user_can('posts_edit_other', $post)) {
            global $zib_bbs;
            $tabs_options['trash'] = array(
                'title'       => '<i class="fa fa-trash-o mr3"></i>回收站',
                'post_status' => 'trash',
                'show'        => array('pc_s', 'm_s'),
            );
        }
    }

    if (!$tabs_options) {
        return;
    }

    $tab_nav     = zib_bbs_get_plate_tab_nav($tabs_options, $active_index);
    $tab_content = zib_bbs_get_plate_tab_content($tabs_options, $active_index);
    // echo json_encode($tabs_options);
    if ($tab_nav && $tab_content) {
        $html = '<div class="plate-tab zib-widget">';
        $html .= '<div class="affix-header-sm" offset-top="6">';
        $html .= $tab_nav;
        $html .= '</div>';
        $html .= $tab_content;
        $html .= '</div>';
        echo $html;
    }
}

//获取管理帖子的列表
function zib_bbs_get_plate_tab_content_manage($html = '', $option = array())
{
    global $post;
    $lists = '';

    $args = array(
        'plate'       => $post->ID,
        'post_status' => $option['post_status'],
    );
    $posts = zib_bbs_get_posts_query($args);

    if ($posts->have_posts()) {
        while ($posts->have_posts()): $posts->the_post();
            $lists .= zib_bbs_get_posts_manage_list();
        endwhile;

        //帖子分页paginate
        $paginate = zib_bbs_get_paginate($posts->found_posts);
        if ($paginate) {
            $lists .= $paginate;
            $lists .= '<div class="post_ajax_loader" style="display:none;">' . zib_bbs_get_placeholder('plate') . '</div>';
        }

        wp_reset_query();
    }

    if (!$lists) {
        $msg_args = array(
            'trash'   => '回收站暂无内容',
            'pending' => '暂无待审核内容',
        );
        $lists = zib_get_ajax_null($msg_args[$option['post_status']]);
    }

    $html = $lists;

    return $html;

}
add_filter('bbs_plate_tab_content_trash', 'zib_bbs_get_plate_tab_content_manage', 10, 2);
add_filter('bbs_plate_tab_content_pending', 'zib_bbs_get_plate_tab_content_manage', 10, 2);

function zib_bbs_get_plate_tab_content_plate($html = '', $option = array())
{

    $paged                = zib_get_the_paged();
    $style                = !empty($option['style']) ? $option['style'] : 'mini';
    $html                 = '';
    $lists                = '';
    $index                = $option['index'];
    $default_active_index = _pz('bbs_plate_tab_active_index', 1);

    if ($default_active_index == $index && 1 == $paged) {
        //第一页，第一个tab，需要显示置顶文章
        $topping_posts = zib_bbs_get_topping_posts_query(true);
        if ($topping_posts->have_posts()) {
            while ($topping_posts->have_posts()): $topping_posts->the_post();
                $lists .= zib_bbs_get_posts_mini_list('ajax-item', true);
            endwhile;
            wp_reset_query();
        }
        $topping_posts = zib_bbs_get_topping_posts_query();
        if ($topping_posts->have_posts()) {
            while ($topping_posts->have_posts()): $topping_posts->the_post();
                $lists .= zib_bbs_get_posts_mini_list('ajax-item', true);
            endwhile;
            wp_reset_query();
        }
    }

    //正常文章循环
    $args_2 = array();
    if (isset($option['orderby'])) {
        $args_2['orderby'] = $option['orderby'];
    }

    if (isset($option['plate'])) {
        $args_2['plate'] = $option['plate'];
    }
    //不显示置顶文章
    if ($default_active_index == $index) {
        //第一个tab,排除置顶内容
        $args_2['topping'] = false;
    }
    if (!empty($option['include_tag'])) {
        //标签
        $args_2['tag'] = $option['include_tag'];
    }
    if (!empty($option['include_topic'])) {
        //话题
        $args_2['topic'] = $option['include_topic'];
    }
    if (isset($option['bbs_type'])) {
        //类型
        $args_2['bbs_type'] = $option['bbs_type'];
    }
    if (isset($option['filter'])) {
        $args_2['filter'] = $option['filter'];
    }

    $posts = zib_bbs_get_posts_query($args_2);
    //$lists .= json_encode($posts);

    if ($posts->have_posts()) {
        while ($posts->have_posts()): $posts->the_post();
            // $lists .= zib_bbs_get_posts_mini_list();
            if ('detail' == $style) {
                $lists .= zib_bbs_get_posts_list();
            } else {
                $lists .= zib_bbs_get_posts_mini_list();
            }
        endwhile;

        //帖子分页paginate
        $paginate = zib_bbs_get_paginate($posts->found_posts);
        if ($paginate) {
            $lists .= $paginate;
            $lists .= '<div class="post_ajax_loader" style="display:none;">' . zib_bbs_get_placeholder('plate') . '</div>';
        }

        wp_reset_query();
    }

    if (!$lists) {
        $lists = zib_get_ajax_null('内容空空如也');
        if (1 == $index && 1 == $paged) {
            global $zib_bbs;
            $posts_add = zib_bbs_get_posts_add_page_link(array('plate_id' => get_queried_object_id()), 'ml10 mr10 mt10 but hollow c-green padding-lg', zib_get_svg('add') . '发布' . $zib_bbs->posts_name);
            $lists .= '<div class="text-center mb20">';
            $lists .= $posts_add;
            $lists .= '</div>';
        }
    }

    $html = $lists;

    return $html;
}
add_filter('bbs_plate_tab_content_other', 'zib_bbs_get_plate_tab_content_plate', 10, 2);
