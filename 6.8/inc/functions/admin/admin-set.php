<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-10-11 12:28:08
 * @LastEditTime  : 2020-11-11 17:01:36
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//链接管理++
add_action('admin_bar_menu', 'zib_add_link_toolbar', 999);
function zib_add_link_toolbar($wp_admin_bar)
{
    $count = zib_get_visible_link_count();
    $title = '<span class="ab-icon dashicons-before dashicons-admin-links"></span>'.$count;
    $url = $count ? '?orderby=visible&order=asc' :'';
    $args = array(
        'id' => 'zib_libk_toolbar',
        'title' => $title,
        'href' => admin_url('link-manager.php'.$url),
        'meta' => array(
            'title' => ($count?'有'.$count.'个链接需要审核':'链接管理'),
        )
    );
    $wp_admin_bar->add_node($args);
}


add_filter('manage_link-manager_columns', 'zib_link_columns');
function zib_link_columns($columns)
{

    $columns['visible'] = '可见性 <c style=" display: inline-block; font-size: 10px; color: #fff; background: #f45c74; border-radius: 50px; line-height: 1.4; padding: 0 5px; ">待审 '.zib_get_visible_link_count().'</c>';
    unset($columns['rel']);
    $columns['link_description'] = '简介(图像描述)';
    $columns['link_image'] = 'LOGO图像';
  //  $columns['rel_aa'] = 'LOGO图像';

    return $columns;
}

add_action('manage_link_custom_column', 'zib_output_link_columns', 10, 2);
function zib_output_link_columns($column, $link_id)
{
    $link = get_bookmark($link_id);
    switch ($column) {
        case "link_description":
            echo $link->link_description;
            break;
        case "link_image":
            $img = $link->link_image;
            $$img = $img ? '<img alt="图像地址失效" src="' . $img . '" height="50">' : '无';
            echo $$img;
            break;
        case "rel_aa":
            echo json_encode($link);
            break;
    }
}
//add_meta_box( 'linksubmitdiv', '提醒说明', 'zib_link_submit_meta_box', null, 'side', 'core' );
add_action('add_meta_boxes_link', 'zib_link_submit_meta_box_fun');
function zib_link_submit_meta_box_fun()
{
    add_meta_box('zib_link_box', '提醒说明', 'zib_link_submit_meta_box', null, 'side', 'core');
}
function zib_link_submit_meta_box()
{
    $html = '<p>请注意配置对应项目：</p>';
    $html .= '<li>图像描述->链接简介</li>';
    $html .= '<li>图像地址->链接LOGO图像</li>';
    $html .= '<li>私密链接->勾选私密链接视为未审核，不会在前台显示</li>';
    $html .= '<li>评分->可利用评分进行手动排序</li>';
    echo $html;
}
//获取未审核链接数量
function zib_get_visible_link_count()
{
    global $wpdb;
    $count = $wpdb->get_var("SELECT COUNT(link_id) FROM $wpdb->links WHERE link_visible <> 'Y'");
    return $count;
}

//菜单设置按钮
add_action('wp_nav_menu_item_custom_fields','zib_get_menu_set',10,5);
function zib_get_menu_set($item_id, $item, $depth, $args, $id)
{
    $html = '<style>
    .widefat.edit-menu-item-title {
        display: none
    }
    </style>';
    $html .= '<p class="description description-wide"><label>导航名称：支持HTML,可以插入图标或徽章或其它样式 <a target="_blank" href="https://www.zibll.com/?s=%E8%8F%9C%E5%8D%95">查看教程</a><br><textarea id="edit-menu-item-title-'.$item_id.'" class="widefat" rows="3" name="menu-item-title['.$item_id.']">'.esc_textarea( $item->title ).'</textarea></label></p>';
    //$html .='<code>'. json_encode($item).'</code>';
    echo $html;
}

//隐藏多余的用户设置项目
function zib_user_profile_css($user)
{

    $html = '<style>
    .user-first-name-wrap,
    .user-last-name-wrap,
    .user-admin-bar-front-wrap,
    .user-comment-shortcuts-wrap,
    .user-admin-color-wrap,
    .user-syntax-highlighting-wrap,
    .user-rich-editing-wrap,
    .user-profile-picture .description,
    .user-language-wrap
     {
        display: none
    }
    </style>';
    echo $html;

}
if(_pz('admin_user_del_fields',true)){
    add_action('show_user_profile', 'zib_user_profile_css');
    add_action('edit_user_profile', 'zib_user_profile_css');
}
