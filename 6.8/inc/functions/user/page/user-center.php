<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-10-09 22:12:52
 * @LastEditTime: 2022-09-14 16:56:13
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|用户中心的页面模板
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

$page_type = 'user_center';
do_action('locate_template_' . $page_type);
get_header();
?>
<main class="main-min-height">
    <?php do_action($page_type . '_page_header'); ?>
    <div class="container">
        <?php do_action($page_type . '_page_content'); ?>
    </div>
    <?php do_action($page_type . '_page_footer'); ?>
</main>
<?php
get_footer();
