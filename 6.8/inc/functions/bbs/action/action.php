<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-05 20:25:29
 * @LastEditTime: 2021-12-16 20:40:28
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|AJAX执行类函数
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

foreach (array(
    'posts',
    'plate',
    'term',
    'user',
    'comment',
    'other',
) as $function) {
    $path = ZIB_BBS_REQUIRE_URI . 'action/ajax-' . $function . '.php';
    require_once get_theme_file_path($path);
}

//加分
function zib_bbs_bbs_user_score_extra_max($max)
{
    return _pz('bbs_score_extra_max') ?: 5;
}
add_filter('bbs_user_score_extra_max', 'zib_bbs_bbs_user_score_extra_max');

//减分
function zib_bbs_bbs_user_score_deduct_max($max)
{
    return _pz('bbs_score_deduct_max') ?: 3;
}
add_filter('bbs_user_score_deduct_max', 'zib_bbs_bbs_user_score_deduct_max');
