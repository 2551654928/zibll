<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:36
 * @LastEditTime: 2022-10-30 11:13:44
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

get_header();
?>

<main>
	<div class="container">
		<?php
		zib_author_header();
		if (function_exists('dynamic_sidebar')) {
			echo '<div class="fluid-widget">';
			dynamic_sidebar('all_top_fluid');
			dynamic_sidebar('author_top_fluid');
			echo '</div>';
		}
		zib_author_content();
		?>
	</div>
	<?php if (function_exists('dynamic_sidebar')) {
		echo '<div class="container fluid-widget">';
		dynamic_sidebar('author_bottom_fluid');
		dynamic_sidebar('all_bottom_fluid');
		echo '</div>';
	}
	?>
</main>
<?php get_footer(); ?>