<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:36
 * @LastEditTime: 2022-10-26 21:46:06
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */
?>
<!DOCTYPE HTML>
<html <?php echo 'lang="' . esc_attr(get_bloginfo('language')) . '"'; ?>>
<head>
	<meta charset="UTF-8">
	<link rel="dns-prefetch" href="//apps.bdimg.com">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=0.0, viewport-fit=cover">
	<meta http-equiv="Cache-Control" content="no-transform" />
	<meta http-equiv="Cache-Control" content="no-siteapp" />
	<?php wp_head();?>
	<?php tb_xzh_head_var();?>
</head>
<body <?php body_class(_bodyclass());?>>
	<?php echo qj_dh_nr(); ?>
	<?php zib_seo_image();?>
	<?php zib_header();?>