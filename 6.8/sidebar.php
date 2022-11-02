<?php 
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:36
 * @LastEditTime: 2022-10-30 11:13:09
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

if (!zib_is_show_sidebar() || wp_is_mobile()) return; ?>
<div class="sidebar">
	<?php
	if (function_exists('dynamic_sidebar')) {
		dynamic_sidebar('all_sidebar_top');
		if (is_home()) {
			dynamic_sidebar('home_sidebar');
		} elseif (is_category()||is_tax( 'topics' )) {
			dynamic_sidebar('cat_sidebar');
		} elseif (is_tag()) {
			dynamic_sidebar('tag_sidebar');
		} elseif (is_search()) {
			dynamic_sidebar('search_sidebar');
		} elseif (is_single()) {
			dynamic_sidebar('single_sidebar');
		} elseif (is_page_template('pages/sidebar.php')) {
			dynamic_sidebar('page_sidebar');
		}
		dynamic_sidebar('all_sidebar_bottom');
	}
	?>
</div>
