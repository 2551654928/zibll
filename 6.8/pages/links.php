<?php
/*
 * @Author: Qinver
 * @Url: zibll.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2022-04-20 19:58:09
 */

/**
 * Template name: Zibll-链接列表
 * Description:   sidebar page
 */

// 获取链接列表
function zib_page_links()
{
    $type          = 'card';
    $post_ID       = get_queried_object_id();
    $args_orderby  = get_post_meta($post_ID, 'page_links_orderby', true);
    $args_order    = get_post_meta($post_ID, 'page_links_order', true);
    $args_limit    = get_post_meta($post_ID, 'page_links_limit', true);
    $args_category = get_post_meta($post_ID, 'page_links_category', true);
    $args          = array(
        'orderby'  => $args_orderby ? $args_orderby : 'name', //排序方式
        'order'    => $args_order ? $args_order : 'ASC', //升序还是降序
        'limit'    => $args_limit ? $args_limit : -1, //最多显示数量
        'category' => $args_category, //以逗号分隔的类别ID列表
    );
    $links = get_bookmarks($args);

    $html = '';

    if ($links) {
        $html .= zib_links_box($links, $type, false);
    } elseif (is_super_admin()) {
        $html = '<a class="author-minicard links-card radius8" href="' . admin_url('link-manager.php') . '" target="_blank">添加链接</a>';
    } else {
        $html = '<div class="author-minicard links-card radius8">暂无链接</div>';
    }
    return $html;
}

get_header();
$post_id                     = get_queried_object_id();
$header_style                = zib_get_page_header_style();
$page_links_content_s        = get_post_meta($post_id, 'page_links_content_s', true);
$page_links_content_position = get_post_meta($post_id, 'page_links_content_position', true);
$page_links_submit_s         = get_post_meta($post_id, 'page_links_submit_s', true);

?>
<main class="container">
    <div class="content-wrap">
        <div class="content-layout">
            <?php while (have_posts()) : the_post(); ?>
                <?php echo zib_get_page_header(); ?>
                <?php
                if ($page_links_content_position != 'top') {
                    echo '<div class="zib-widget">' . zib_page_links() . '</div>';
                }
                if ($page_links_content_s) {
                    echo '<div class="zib-widget"><article class="article wp-posts-content">';
                    the_content();
                    echo '</article>';
                    wp_link_pages(
                        array(
                            'before' => '<p class="text-center post-nav-links radius8 padding-6">',
                            'after'  => '</p>',
                        )
                    );
                    echo '</div>';
                }
                if ($page_links_content_position == 'top') {
                    echo '<div class="zib-widget">' . zib_page_links() . '</div>';
                }
                if ($page_links_submit_s) {
                    $submit_args = array(
                        'title'    => get_post_meta($post_id, 'page_links_submit_title', true),
                        'subtitle' => get_post_meta($post_id, 'page_links_submit_subtitle', true),
                        'dec'      => get_post_meta($post_id, 'page_links_submit_dec', true),
                    );
                    echo zib_submit_links_card($submit_args);
                }
                ?>
                <?php ?>
            <?php endwhile; ?>
            <?php comments_template('/template/comments.php', true); ?>
        </div>
    </div>
    <?php get_sidebar(); ?>
</main>
<?php
get_footer();
