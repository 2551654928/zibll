<?php

/**
 * Template name: Zibll-资源下载
 * Description:   download page
 */

if (empty($_GET['post'])) {
    get_header();
    get_template_part('template/content-404');
    get_footer();
    exit;
}
get_header();
$post_id = $_GET['post'];

function zibpay_get_down_html($post_id)
{

    $pay_mate = get_post_meta($post_id, 'posts_zibpay', true);
    $html     = '';
    if (empty($pay_mate['pay_type']) || empty($pay_mate['pay_type']) || $pay_mate['pay_type'] != '2') {
        return get_template_part('template/content-404');
    }
    ;

    // 查询是否已经购买
    $paid_obj    = zibpay_is_paid($post_id);
    $posts_title = get_the_title($post_id) . get_post_meta($post_id, 'subtitle', true);
    $pay_title   = !empty($pay_mate['pay_title']) ? $pay_mate['pay_title'] : $posts_title;
    $pay_doc     = $pay_mate['pay_doc'];
    $pay_details = $pay_mate['pay_details'];
    if ($paid_obj) {
        //已经购买直接显示下载盒子

        $paid_name = zibpay_get_paid_type_name($paid_obj['paid_type']);
        $paid_name = '<b class="badg jb-red mr6" style="font-size: 12px; padding: 2px 10px; line-height: 1.4; "><i class="fa fa-check mr6" aria-hidden="true"></i>' . $paid_name . '</b>';

        $pay_extra_hide = !empty($pay_mate['pay_extra_hide']) ? '<div class="pay-extra-hide">' . $pay_mate['pay_extra_hide'] . '</div>' : '';

        $dowmbox = '<div style="margin-bottom:3em;">' . zibpay_get_post_down_buts($pay_mate, $paid_obj['paid_type'], $post_id) . '</div>';
        if ($paid_obj['paid_type'] == 'free' && _pz('pay_free_logged_show') && !is_user_logged_in()) {
            $dowmbox        = '<div class="alert jb-yellow em12" style="margin: 2em 0;"><b>免费资源，请登录后下载</b></div>';
            $pay_extra_hide = zib_get_user_singin_page_box('pay-extra-hide', 'Hi！请先登录');
        }

        $html = '<div class="article-header theme-box"><div class="article-title"><a href="' . get_permalink($post_id) . '#posts-pay">' . $pay_title . '</a></div>' . $paid_name . '</div>';

        $html .= '<div>' . $pay_doc . '</div>';
        $html .= '<div class="muted-2-color em09" style="margin: 2em 0;">' . $pay_details . '</div>';

        $html .= '<div style="margin-bottom: 2em;">' . $dowmbox . $pay_extra_hide . '</div>';
    } else {
        //未购买
        $html = '<div class="article-header theme-box"><div class="article-title"><a href="' . get_permalink($post_id) . '#posts-pay">' . $pay_title . '</a></div></div>';

        $html .= '<div>' . $pay_doc . '</div>';
        $html .= '<div class="muted-2-color em09" style="margin: 2em 0;">' . $pay_details . '</div>';
        $html .= '<div class="alert jb-red em12" style="margin: 2em 0;"><b>暂无下载权限</b></div>';
        $html .= '<a style="margin-bottom: 2em;" href="' . get_permalink($post_id) . '#posts-pay" class="but jb-yellow padding-lg"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><span class="ml10">返回文章</span></a>';
    }

    return $html;
}

?>
<style>
	.but-download>.but,
	.but-download>span {

		min-width: 200px;
		padding: .5em;
		margin-top: 10px;
	}

	.pay-extra-hide {
		background: var(--muted-border-color);
		display: block;
		margin: 10px;
		padding: 20px;
		color: var(--muted-color);
		border-radius: 4px;
	}
</style>
<main class="container">
	<div class="content-wrap">
		<div class="content-layout">


			<?php while (have_posts()): the_post();?>
						<?php echo zib_get_page_header(); ?>

						<div class="zib-widget article" style=" min-height: 600px; ">

							<?php
    echo zibpay_get_down_html($post_id);
    $page_links_content_s = get_post_meta(get_queried_object_id(), 'page_show_content', true);
    if ($page_links_content_s) {
        the_content();
        wp_link_pages(
            array(
                'before' => '<p class="text-center post-nav-links radius8 padding-6">',
                'after'  => '</p>',
            )
        );
        echo '</div>';
    }
    ?>
							<?php ?>
						<?php endwhile;?>
				</div>
				<?php comments_template('/template/comments.php', true);?>
		</div>
	</div>
	<?php get_sidebar();?>
</main>

<?php

get_footer();
