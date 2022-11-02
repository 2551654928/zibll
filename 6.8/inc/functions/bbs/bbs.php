<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-05 20:25:29
 * @LastEditTime: 2022-09-04 13:25:02
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//定义常量
define('ZIB_BBS_ASSETS_URI', ZIB_TEMPLATE_DIRECTORY_URI . '/inc/functions/bbs/assets'); //本主题
define('ZIB_BBS_REQUIRE_URI', '/inc/functions/bbs/'); //本主题require_once的地址前缀
//引入资源文件
require get_theme_file_path(ZIB_BBS_REQUIRE_URI . 'inc/functions.php');
require get_theme_file_path(ZIB_BBS_REQUIRE_URI . 'action/action.php');

if (is_admin()) {
    foreach (array(
        'option',
        'meta-option',
    ) as $require) {
        $path = ZIB_BBS_REQUIRE_URI . 'admin/' . $require . '.php';
        require get_theme_file_path($path);
    }
}

//启动论坛系统
zib_bbs();

if (!_pz('bbs_s', true)) {
    //关闭论坛功能，则后台不显示论坛的评论
    add_filter('comments_list_table_query_args', function ($args) {
        if (empty($args['post_type'])) {
            //默认不显示帖子的评论
            $args['post_type'] = ['post', 'page'];
        }
        return $args;
    });
} else {
    //引入小工具
    require get_theme_file_path(ZIB_BBS_REQUIRE_URI . 'widgets/widgets.php');
    //引入用户中心
    require get_theme_file_path(ZIB_BBS_REQUIRE_URI . 'inc/user-page.php');

    //为搜索添加新的tpye
    add_filter('search_types', function ($types) {
        global $zib_bbs;
        $types['plate'] = $zib_bbs->plate_name;
        $types['forum'] = $zib_bbs->posts_name;
        return $types;
    });

    //添加add新建按钮-版块
    function zib_bbs_new_add_btns_filter_bbs_plate()
    {
        global $zib_bbs;
        return zib_bbs_get_plate_add_link(0, 'btn-newadd', '<icon class="jb-yellow">' . zib_get_svg('plate-fill') . '</icon><text>创建' . $zib_bbs->plate_name . '</text>', 'a');
    }
    add_filter('new_add_btns_bbs_plate', 'zib_bbs_new_add_btns_filter_bbs_plate');

    //添加add新建按钮-话题
    function zib_bbs_new_add_btns_filter_bbs_topic()
    {
        global $zib_bbs;
        return zib_bbs_get_topic_edit_link(0, 'btn-newadd', '<icon class="jb-pink">' . zib_get_svg('topic') . '</icon><text>创建' . $zib_bbs->topic_name . '</text>', 'a');
    }
    add_filter('new_add_btns_bbs_topic', 'zib_bbs_new_add_btns_filter_bbs_topic');

    //添加add新建按钮-话题
    function zib_bbs_new_add_btns_filter_bbs_posts()
    {
        global $zib_bbs;
        return zib_bbs_get_posts_add_page_link(0, 'btn-newadd', '<icon class="jb-blue">' . zib_get_svg('posts') . '</icon><text>发布' . $zib_bbs->posts_name . '</text>', 'a');
    }
    add_filter('new_add_btns_bbs_posts', 'zib_bbs_new_add_btns_filter_bbs_posts');

    function zib_bbs_user_count_badges_filter($html, $user_id)
    {
        $post_n = _cut_count(zib_get_user_post_count($user_id, 'publish', 'forum_post'));
        global $zib_bbs;
        $html = '<a class="but c-blue-2 tag-forum-post" data-toggle="tooltip" title="共' . $post_n . '篇' . $zib_bbs->posts_name . '" href="' . zib_get_user_home_url($user_id, array('tab' => 'forum')) . '">' . zib_get_svg('posts') . $post_n . '</a>' . $html;

        return $html;
    }
    add_filter('user_count_badges', 'zib_bbs_user_count_badges_filter', 10, 2);

    function zib_bbs_user_sidebar_statistics_filter($args, $user_id)
    {
        global $zib_bbs;

        $data = array(array(
            'name'  => $zib_bbs->posts_name,
            'count' => zib_get_user_post_count($user_id, 'publish', 'forum_post'),
            'link'  => zib_get_user_home_url($user_id, array('tab' => 'forum')),
        ));
        return array_merge($data, $args);
    }
    add_filter('user_sidebar_statistics_args', 'zib_bbs_user_sidebar_statistics_filter', 10, 2);

    //过滤必要的付费参数
    function zibpay_post_pay_meta_sanitize($meta_value)
    {
        if (!isset($meta_value['pay_type'])) {
            $allow_view = !empty($_POST['forum_allow_view']['allow_view']) ? $_POST['forum_allow_view']['allow_view'] : '';
            if (in_array($allow_view, ['points', 'pay'])) {
                $meta_value['pay_type'] = '1';
                $meta_value['pay_modo'] = $allow_view === 'points' ? 'points' : '0';
            }
        }

        $meta_value = array_merge(
            array(
                'pay_type'            => 'no',
                'pay_limit'           => '0',
                'pay_modo'            => '0',
                'points_price'        => '',
                'vip_1_points'        => '',
                'vip_2_points'        => '',
                'pay_price'           => '',
                'vip_1_price'         => '',
                'vip_2_price'         => '',
                'pay_rebate_discount' => 0,
                'pay_cuont'           => 0,
            ), (array) $meta_value);

        return $meta_value;
    }
    add_filter("sanitize_post_meta_posts_zibpay_for_forum_post", 'zibpay_post_pay_meta_sanitize', 10);
}

//关闭前台设置
add_action('bbs_locate_template', function () {
    add_filter('frontend_set_switch', '__return_false');
});

//添加add新建按钮-选项
function zib_bbs_new_add_btns_options($options)
{
    global $zib_bbs;
    $options['bbs_plate'] = '[' . $zib_bbs->forum_name . ']创建' . $zib_bbs->plate_name;
    $options['bbs_topic'] = '[' . $zib_bbs->forum_name . ']创建' . $zib_bbs->topic_name;
    $options['bbs_posts'] = '[' . $zib_bbs->forum_name . ']发布' . $zib_bbs->posts_name;
    return $options;
}
add_filter('new_add_btns_options', 'zib_bbs_new_add_btns_options');
