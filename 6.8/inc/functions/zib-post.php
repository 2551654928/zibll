<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2022-04-10 20:18:56
 * @LastEditTime: 2022-04-30 18:21:47
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

function zib_get_post_more_dropdown($post, $class = '', $con_class = 'but cir post-drop-meta', $con = '', $direction = 'down')
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    if (empty($post->ID)) {
        return;
    }

    $con       = $con ? $con : zib_get_svg('menu_2');
    $class     = $class ? ' ' . $class : '';
    $con_class = $con_class ? ' class="' . $con_class . '"' : '';
    $action    = '';

    //编辑
    $edit = zib_get_post_edit_link($post, '', '<i class="fa fa-fw fa-edit mr6"></i>编辑文章');
    $action .= $edit ? '<li>' . $edit . '</li>' : '';

    //删除
    $del = zib_bbs_post_delete_link($post, 'c-red', '<i class="fa fa-trash-o mr6 fa-fw"></i>删除文章');
    $action .= $del ? '<li>' . $del . '</li>' : '';

    if (!$action) {
        return;
    }

    $html = '<div class="drop' . $direction . ' more-dropup' . $class . '">';
    $html .= '<a href="javascript:;"' . $con_class . ' data-toggle="dropdown">' . $con . '</a>';
    $html .= '<ul class="dropdown-menu">';
    $html .= $action;
    $html .= '</ul>';
    $html .= '</div>';
    return $html;
}

/**
 * @description: 获取前台编辑文章的按钮
 * @param {*} $post
 * @param {*} $class
 * @param {*} $con
 * @return {*}
 */
function zib_get_post_edit_link($post = null, $class = "but c-blue", $con = '编辑文章')
{
    if (!_pz('post_article_s', true)) {
        return;
    }

    if (!is_object($post)) {
        $post = get_post($post);
    }
    if (empty($post->ID)) {
        return;
    }

    if (!zib_current_user_can('new_post_edit', $post)) {
        return;
    }

    $url = zib_get_new_post_url($post->ID);

    return '<a href="' . $url . '" class="' . $class . '">' . $con . '</a>';
}

/**
 * @description: 获取前台投稿链接
 * @param {*}
 * @return {*}
 */
function zib_get_new_post_url($edit = 0)
{

    if (!_pz('post_article_s', true)) {
        return;
    }

    $url = zib_get_template_page_url('pages/newposts.php');
    if ($edit) {
        $url = add_query_arg('edit', $edit, $url);
    }

    return $url;
}

/**
 * @description: 获取删除帖子的连接按钮
 * @param {*} $posts_id
 * @param {*} $class
 * @param {*} $con
 * @param {*} $tag
 * @return {*} zib_get_refresh_modal_link()
 */
function zib_bbs_post_delete_link($post = null, $class = '', $con = '<i class="fa fa-trash-o fa-fw"></i>删除', $tag = 'a')
{
    if (!_pz('post_article_s', true)) {
        return;
    }

    if (!is_object($post)) {
        $post = get_post($post);
    }

    if (empty($post->ID)) {
        return;
    }

    if (!zib_current_user_can('new_post_delete', $post) || 'trash' === $post->post_status) {
        return;
    }

    $url_var = array(
        'action' => 'post_delete_modal',
        'id'     => $post->ID,
    );

    $args = array(
        'tag'           => $tag,
        'class'         => $class,
        'data_class'    => 'modal-mini',
        'height'        => 240,
        'mobile_bottom' => true,
        'text'          => $con,
        'query_arg'     => $url_var,
    );

    //每次都刷新的modal
    return zib_get_refresh_modal_link($args);
}
