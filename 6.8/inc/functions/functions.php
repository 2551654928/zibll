<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:37
 * @LastEditTime: 2022-11-01 12:25:09
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

$functions = array(
    'zib-tool',
    'zib-theme',
    'zib-head',
    'zib-header',
    'zib-content',
    'zib-footer',
    'zib-index',
    'zib-category',
    'zib-author',
    'zib-post',
    'zib-posts-list',
    'zib-share',
    'zib-search',
    'zib-share-wechat',
    'user/user',
    'zib-user',
    'zib-page',
    'zib-single',
    'zib-comments-list',
    'zib-svg-icon',
    'zib-baidu',
    'zib-email',
    'zib-frontend-set',
    'message/functions',
    'bbs/bbs',
);

foreach ($functions as $function) {
    $path = 'inc/functions/' . $function . '.php';
    require get_theme_file_path($path);
}

if (is_admin()) {
    require get_theme_file_path('inc/functions/admin/admin-main.php');
    require get_theme_file_path('inc/functions/admin/admin-set.php');
}

//老版slider
function zib_get_img_slider($args)
{
    $defaults = array(
        'class'        => '',
        'type'         => '',
        'lazy'         => false,
        'pagination'   => true,
        'effect'       => 'slide',
        'button'       => true,
        'loop'         => true,
        'auto_height'  => false,
        'm_height'     => '',
        'pc_height'    => '',
        'autoplay'     => true,
        'interval'     => 4000,
        'spaceBetween' => 15,
        'echo'         => true,
    );
    $args         = wp_parse_args((array) $args, $defaults);
    $class        = $args['class'];
    $type         = $args['type'];
    $lazy         = $args['lazy'];
    $pagination   = $args['pagination'];
    $effect       = ' data-effect="' . $args['effect'] . '"';
    $button       = $args['button'];
    $loop         = $args['loop'] ? ' data-loop="true"' : '';
    $auto_h       = $args['auto_height'] ? ' auto-height="true"' : '';
    $interval     = $args['interval'] < 999 ? $args['interval'] * 1000 : $args['interval'];
    $interval     = $args['autoplay'] ? ' data-autoplay="' . $args['autoplay'] . '"' : '';
    $interval     = $args['interval'] && $args['autoplay'] ? ' data-interval="' . $interval . '"' : '';
    $spaceBetween = $args['spaceBetween'] ? ' data-spaceBetween="' . $args['spaceBetween'] . '"' : '';

    $style = '';
    if (!$auto_h) {
        $_h = !empty($args['m_height']) ? '--m-height :' . (int) $args['m_height'] . 'px;' : '';
        $_h .= !empty($args['pc_height']) ? '--pc-height :' . (int) $args['pc_height'] . 'px;' : '';
        $style = ' style="' . $_h . '"';
    }

    if (!$lazy && zib_is_lazy('lazy_sider')) {
        $lazy = true;
    }
    if (empty($args['slides'])) {
        return;
    }
    $slides           = '';
    $pagination_rigth = '';
    foreach ($args['slides'] as $slide) {
        $lazy_src         = ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail-lg.svg';
        $s_class          = isset($slide['class']) ? $slide['class'] : '';
        $s_href           = isset($slide['href']) ? $slide['href'] : '';
        $s_image          = isset($slide['image']) ? $slide['image'] : '';
        $s_blank          = !empty($slide['blank']) ? ($s_href ? ' target="_blank"' : '') : '';
        $s_caption        = isset($slide['caption']) ? $slide['caption'] : '';
        $s_desc           = !empty($slide['desc']) ? '<div class="s-desc">' . $slide['desc'] . '</div>' : '';
        $pagination_rigth = !empty($slide['desc']) ? ' kaoyou' : ' kaoyou';
        $slides .= '<div class="swiper-slide' . ' ' . $s_class . '">' . $s_desc .
            '<' . ($s_href ? 'a' : 'span') . $s_blank . ($s_href ? ' href="' . $s_href . '"' : '') . '>
				<img class="lazyload swiper-lazy radius8" ' . ($lazy ? ' data-src="' . $s_image . '" src="' . $lazy_src . '"' : ' src="' . $s_image . '"') . '></' . ($s_href ? 'a' : 'span') . '>'
            . ($s_caption ? '<div class="carousel-caption">' . $s_caption . '</div>' : '') . '</div>';
    }
    $pagination = $pagination ? '<div class="swiper-pagination' . $pagination_rigth . '"></div>' : '';
    $button     = $button ? '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>' : '';

    $con = '<div class="new-swiper swiper-c ' . $class . '" ' . $effect . $loop . $auto_h . $interval . $spaceBetween . $style . '>
            <div class="swiper-wrapper">' . $slides . '</div>' .
        $button . $pagination . '</div>';
    if ($args['echo']) {
        echo '<div class="relative zib-slider theme-box">' . $con . '</div>';
    } else {
        return '<div class="relative zib-slider">' . $con . '</div>';
    }
}

/**
 * @description: slider构建函数
 * @param {*}
 * @return {*}
 */
function zib_new_slider($args, $echo = true)
{
    $defaults = array(
        'class'        => 'mb20',
        'type'         => '',
        'direction'    => 'horizontal',
        'lazy'         => false,
        'pagination'   => true,
        'effect'       => 'slide',
        'button'       => true,
        'loop'         => true,
        'scale_height' => false,
        'scale'        => 40,
        'auto_height'  => false,
        'm_height'     => '',
        'pc_height'    => '',
        'autoplay'     => true,
        'interval'     => 4000,
        'speed'        => 0,
        'slides'       => array(),
        'html'         => '',
    );
    $args = wp_parse_args((array) $args, $defaults);
    if (empty($args['slides'][0])) {
        return;
    }

    $class      = $args['class'];
    $type       = $args['type'];
    $lazy       = $args['lazy'];
    $pagination = $args['pagination'];
    $effect     = ' data-effect="' . $args['effect'] . '"';
    $button     = $args['button'];
    $loop       = $args['loop'] ? ' data-loop="true"' : '';
    $auto_h     = ($args['auto_height'] && 'vertical' !== $args['direction']) ? ' auto-height="true"' : '';
    $autoplay   = $args['autoplay'] ? ' data-autoplay="' . $args['autoplay'] . '"' : '';
    $interval   = $args['interval'] < 999 ? $args['interval'] * 1000 : $args['interval'];
    $autoplay .= ($interval && $args['autoplay']) ? ' data-interval="' . $interval . '"' : '';
    $speed        = $args['speed'] && $args['speed'] > 299 ? ' data-speed="' . $args['speed'] . '"' : '';
    $direction    = $args['direction'] ? ' data-direction="' . $args['direction'] . '"' : '';
    $spaceBetween = isset($args['spacebetween']) ? ' data-spaceBetween="' . $args['spacebetween'] . '"' : '';

    $style = '';
    if ($args['scale_height'] && 'vertical' !== $args['direction']) {
        $_h = '--scale-height :' . (int) $args['scale'] . '%';
        $class .= ' scale-height';
    } elseif (!$auto_h) {
        $_h = !empty($args['m_height']) ? '--m-height :' . (int) $args['m_height'] . 'px;' : '';
        $_h .= !empty($args['pc_height']) ? '--pc-height :' . (int) $args['pc_height'] . 'px;' : '';
    } else {
        $_h = !empty($args['max_height']) ? '--max-height :' . (int) $args['max_height'] . 'px;' : '';
        $_h .= !empty($args['min_height']) ? '--min-height :' . (int) $args['min_height'] . 'px;' : '';
    }
    $style = ' style="' . $_h . '"';

    if (!$lazy && zib_is_lazy('lazy_sider')) {
        $lazy = true;
    }

    $slides  = '';
    $seo_alt = _pz('hometitle') ? _pz('hometitle') : '幻灯片' . zib_get_delimiter_blog_name();

    foreach ($args['slides'] as $slide) {
        $lazy_src = ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail-lg.svg';
        //图片
        $s_background = isset($slide['background']) ? $slide['background'] : '';
        if (!$s_background) {
            continue;
        }

        $img_alt = !empty($slide['text']['title']) ? $slide['text']['title'] . zib_get_delimiter_blog_name() : '';
        if (!$img_alt) {
            $img_alt = !empty($slide['link']['text']) ? $slide['link']['text'] . zib_get_delimiter_blog_name() : '';
        }

        if (!$img_alt) {
            $img_alt = $seo_alt;
        }

        //背景图
        $s_background = '<img class="swiper-lazy radius8' . ($lazy ? ' lazyload ' : '') . '" ' . ($lazy ? ' data-src="' . $s_background . '" src="' . $lazy_src . '"' : ' src="' . $s_background . '"') . ' alt="' . $img_alt . '">';

        //更多图层
        $s_layers = '';
        if (!empty($slide['image_layer'][0]['image'])) {
            foreach ($slide['image_layer'] as $layer) {
                $layer_image = isset($layer['image']) ? $layer['image'] : '';
                if (!$layer_image) {
                    continue;
                }

                $layer_image = $layer_image ? '<img class="swiper-lazy radius8' . ($lazy ? ' lazyload ' : '') . '" ' . ($lazy ? ' data-src="' . $layer_image . '" src="' . $lazy_src . '"' : ' src="' . $layer_image . '"') . ' alt="' . $img_alt . '">' : '';

                //视差滚动
                $layer_parallax = isset($layer['parallax']) ? (int) $layer['parallax'] : 0;
                $layer_parallax = $layer_parallax ? ' data-swiper-parallax="' . $layer_parallax . '%"' : '';

                //视差透明度
                $layer_parallax_opacity = isset($layer['parallax_opacity']) ? (int) $layer['parallax_opacity'] / 100 : 0;
                $layer_parallax .= ($layer_parallax && $layer_parallax_opacity && 1 !== $layer_parallax_opacity) ? ' data-swiper-parallax-opacity="' . $layer_parallax_opacity . '"' : '';

                //视差缩放
                $layer_parallax_scale = isset($layer['parallax_scale']) ? (int) $layer['parallax_scale'] / 100 : 0;
                $layer_parallax_scale = ($layer_parallax_scale && 1 !== $layer_parallax_scale) ? ' data-swiper-parallax-scale="' . $layer_parallax_scale . '"' : '';

                //前景图对齐
                $layer_class = '';
                if (!empty($layer['free_size'])) {
                    $layer_class = ' slide-layer';
                    $layer_class .= isset($layer['align']) ? ' text-' . $layer['align'] : '';
                }
                //图层动画
                $animate_attr = '';
                /**
                $animate = array(
                array(
                'value' => 'rubberBand',
                'duration' => '',
                'loop' => '',
                'delay' => '',
                ),
                );
                if (!empty($animate[0]['value'])) {
                $animate_attr = ' swiper-animate-effect="' . esc_attr(json_encode($animate)) . '"';
                $layer_class .= ' ani';
                }
                 */
                $s_layers .= '<div' . $animate_attr . ' class="absolute' . $layer_class . '"' . $layer_parallax . $layer_parallax_scale . '>' . $layer_image . '</div>';
            }
        }
        $s_class = isset($slide['class']) ? $slide['class'] : '';
        //链接
        $s_href  = isset($slide['link']['url']) ? $slide['link']['url'] : '';
        $s_blank = !empty($slide['link']['target']) ? ($s_href ? ' target="_blank"' : '') : '';
        //文案
        $slide_text = !empty($slide['text']['title']) ? $slide['text'] : '';
        $s_text     = !empty($slide_text['title']) ? '<div class="slide-title">' . $slide_text['title'] . '</div>' : '';
        $s_text .= !empty($slide_text['desc']) ? '<div class="slide-desc">' . $slide_text['desc'] . '</div>' : '';

        if ($s_text) {
            //控制位置class
            $s_text_class = 'abs-center slide-text';
            $s_text_class .= isset($slide_text['text_align']) ? ' ' . $slide_text['text_align'] : '';
            //字体大小
            $s_text_size = !empty($slide_text['text_size_pc']) ? '--text-size-pc:' . (int) $slide_text['text_size_pc'] . 'px;' : '';
            $s_text_size .= !empty($slide_text['text_size_m']) ? '--text-size-m:' . (int) $slide_text['text_size_m'] . 'px;' : '';

            $s_text_style = $s_text_size ? ' style="' . $s_text_size . '"' : '';
            $s_text       = '<div class="' . $s_text_class . '"' . $s_text_style . '>' . $s_text . '</div>';
            //视差滚动
            $s_text_parallax = isset($slide_text['parallax']) ? $slide_text['parallax'] : 0;
            if ($s_text_parallax) {
                $s_text_parallax = $s_text_parallax ? ' data-swiper-parallax="' . $s_text_parallax . '"' : '';
                $s_text          = '<div class="absolute"' . $s_text_parallax . '>' . $s_text . '</div>';
            }
        }
        $slides .= '<div class="swiper-slide' . ' ' . $s_class . '">';
        $slides .= $s_href ? '<a' . $s_blank . ' href="' . $s_href . '">' : '<span>';
        $slides .= $s_background;
        $slides .= $s_layers;
        $slides .= $s_text;
        $slides .= $s_href ? '</a>' : '</span>';
        $slides .= '</div>';
    }
    if (!$slides) {
        return;
    }

    $slides = '<div class="swiper-wrapper">' . $slides . '</div>';

    $pagination = $pagination ? '<div class="swiper-pagination kaoyou"></div>' : '';
    $button     = $button ? '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>' : '';

    $con = '<div class="new-swiper ' . $class . '" ' . $direction . $effect . $loop . $speed . $auto_h . $autoplay . $spaceBetween . $style . '>';
    $con .= $slides;
    $con .= $button;
    $con .= $pagination;
    $con .= $args['html'];
    $con .= '</div>';

    if ($echo) {
        echo '<div class="relative zib-slider">' . $con . '</div>';
    } else {
        return '<div class="relative zib-slider">' . $con . '</div>';
    }
}

//dplayer简单的视频构建
function zib_get_dplayer($url, $pic = '', $scale_height = 0)
{
    $args = array(
        'url'          => $url,
        'pic'          => $pic,
        'scale_height' => $scale_height,
    );
    return zib_new_dplayer($args, false);
}

//dplayer视频构建
function zib_new_dplayer($args, $echo = true)
{

    $defaults = array(
        'class'        => '',
        'autoplay'     => false,
        'loop'         => false,
        'preload'      => 'auto', //values: 'none', 'metadata', 'auto'
        'volume'       => 1,
        'scale_height' => 0,
        'mutex'        => true, //开始播放时暂停其他播放器
        'type'         => 'auto',
        'url'          => '',
        'pic'          => '',
    );
    $args = wp_parse_args((array) $args, $defaults);
    if (empty($args['url'])) {
        return;
    }

    $option = array();
    if ($args['autoplay']) {
        $option['autoplay'] = true;
    }

    if ($args['loop']) {
        $option['loop'] = true;
    }

    if ('auto' != $args['preload']) {
        $option['preload'] = $args['preload'];
    }

    if (1 != $args['volume']) {
        $option['volume'] = $args['volume'];
    }

    if (!$args['mutex']) {
        $option['mutex'] = false;
    }

    $option_attr = $option ? ' video-option=\'' . json_encode($option) . '\'' : '';

    $attr = 'video-url="' . $args['url'] . '"';
    $attr .= $args['pic'] ? ' video-pic="' . $args['pic'] . '"' : '';
    $attr .= $args['type'] ? ' video-type="' . $args['type'] . '"' : '';
    $attr .= $option_attr;

    if ($args['scale_height'] > 0) {
        $args['class'] .= ' dplayer-scale-height';
        $attr .= ' style="--scale-height:' . $args['scale_height'] . '%;"';
    }
    $img  = '<div class="graphic" style="padding-bottom:50%;"><div class="abs-center text-center"><i class="fa fa-play-circle fa-4x muted-3-color opacity5" aria-hidden="true"></i></div></div>';
    $html = '<div class="new-dplayer ' . $args['class'] . '" ' . $attr . '>' . $img . '</div>';

    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * @description: 获取用户点赞、查看数量
 * @param {*}
 * @return {*}
 */
function zib_get_user_badges($user_id)
{
    if (!$user_id) {
        return;
    }

    $like_n     = get_user_posts_meta_count($user_id, 'like');
    $view_n     = get_user_posts_meta_count($user_id, 'views');
    $com_n      = get_user_comment_count($user_id);
    $followed_n = _cut_count(get_user_followed_count($user_id));
    $post_n     = _cut_count(zib_get_user_post_count($user_id, 'publish'));

    $html = '';
    $html .= '<a class="but c-blue tag-posts" data-toggle="tooltip" title="共' . $post_n . '篇文章" href="' . zib_get_user_home_url($user_id) . '">' . zib_get_svg('post') . $post_n . '</a>';
    $html .= '<a class="but c-green tag-comment" data-toggle="tooltip" title="共' . $com_n . '条评论" href="' . zib_get_user_home_url($user_id, array('tab' => 'comment')) . '">' . zib_get_svg('comment') . $com_n . '</a>';

    if ($followed_n) {
        $html .= '<a class="but c-yellow tag-follow" data-toggle="tooltip" title="共' . $followed_n . '个粉丝" href="' . zib_get_user_home_url($user_id, array('tab' => 'follow')) . '"><i class="fa fa-heart em09"></i>' . $followed_n . '</a>';
    } else {
        if ($like_n) {
            $html .= '<span class="badg c-yellow tag-like" data-toggle="tooltip" title="获得' . $like_n . '个点赞">' . zib_get_svg('like') . $like_n . '</span>';
        }
    }
    $html .= '<span class="badg c-red tag-view" data-toggle="tooltip" title="人气值 ' . $view_n . '">' . zib_get_svg('hot') . $view_n . '</span>';

    return apply_filters('user_count_badges', $html, $user_id);
}

function zib_yiyan($class = 'zib-yiyan', $before = '', $after = '')
{
    $yiyan = '<div class="' . $class . '">' . $before . '<div data-toggle="tooltip" data-original-title="点击切换一言" class="yiyan"></div>' . $after . '</div>';
    echo $yiyan;
}

function zib_posts_prevnext()
{
    $current_category = get_the_category();
    $prev_post        = get_previous_post($current_category, '');
    $next_post        = get_next_post($current_category, '');
    if (!empty($prev_post)):
        $prev_title = $prev_post->post_title;
        $prev_link  = 'href="' . get_permalink($prev_post->ID) . '"';
    else:
        $prev_title = '无更多文章';
        $prev_link  = 'href="javascript:;"';
    endif;
    if (!empty($next_post)):
        $next_title = $next_post->post_title;
        $next_link  = 'href="' . get_permalink($next_post->ID) . '"';
    else:
        $next_title = '无更多文章';
        $next_link  = 'href="javascript:;"';
    endif;
    ?>
    <div class="theme-box" style="height:99px">
        <nav class="article-nav">
            <div class="main-bg box-body radius8 main-shadow">
                <a <?php echo $prev_link; ?>>
                    <p class="muted-2-color"><i class="fa fa-angle-left em12"></i><i class="fa fa-angle-left em12 mr6"></i>上一篇</p>
                    <div class="text-ellipsis-2">
                        <?php echo $prev_title; ?>
                    </div>
                </a>
            </div>
            <div class="main-bg box-body radius8 main-shadow">
                <a <?php echo $next_link; ?>>
                    <p class="muted-2-color">下一篇<i class="fa fa-angle-right em12 ml6"></i><i class="fa fa-angle-right em12"></i></p>
                    <div class="text-ellipsis-2">
                        <?php echo $next_title; ?>
                    </div>
                </a>
            </div>
        </nav>
    </div>
<?php
}

function zib_posts_related($related_title = '相关阅读', $limit = 6, $orderby = 'views')
{
    global $post;
    $thumb_s   = _pz('post_related_type') == 'img';
    $categorys = get_the_terms($post, 'category');
    $topics    = get_the_terms($post, 'topics');
    $tags      = get_the_terms($post, 'post_tag');

    $posts_args = array(
        'showposts'           => $limit,
        'ignore_sticky_posts' => 1,
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'order'               => 'DESC',
        'tax_query'           => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => array_column((array) $categorys, 'term_id'),
            ),
            array(
                'taxonomy' => 'topics',
                'field'    => 'term_id',
                'terms'    => array_column((array) $topics, 'term_id'),
            ),
            array(
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => array_column((array) $tags, 'term_id'),
            ),
        ),
    );

    $posts_args = zib_query_orderby_filter($orderby, $posts_args);

    $posts_lits = '';
    $new_query  = new WP_Query($posts_args);
    while ($new_query->have_posts()) {
        $new_query->the_post();
        if (_pz('post_related_type') == 'list') {
            $posts_lits .= zib_posts_mini_while(array('echo' => false, 'show_number' => false));
        } else {
            if ($thumb_s) {
                $title    = get_the_title() . get_the_subtitle(false);
                $time_ago = zib_get_time_ago(get_the_time('U'));
                $info     = '<item>' . $time_ago . '</item><item class="pull-right">' . zib_get_svg('view') . ' ' . get_post_view_count($before = '', $after = '') . '</item>';
                $img      = zib_post_thumbnail('', 'fit-cover', true);
                $img      = $img ? $img : zib_get_spare_thumb();
                $card     = array(
                    'type'         => 'style-3',
                    'class'        => 'mb10',
                    'img'          => $img,
                    'alt'          => $title,
                    'link'         => array(
                        'url'    => get_permalink(),
                        'target' => '',
                    ),
                    'text1'        => $title,
                    'text2'        => zib_str_cut($title, 0, 45, '...'),
                    'text3'        => $info,
                    'lazy'         => true,
                    'height_scale' => 70,
                );
                $posts_lits .= '<div class="swiper-slide mr10">';
                $posts_lits .= zib_graphic_card($card);
                $posts_lits .= '</div>';
            } else {
                $posts_lits .= '<li><a class="icon-circle" href="' . get_permalink() . '">' . get_the_title() . get_the_subtitle() . '</a></li>';
            }
        }
    }
    wp_reset_query();
    wp_reset_postdata();

    echo '<div class="theme-box relates' . ($thumb_s ? ' relates-thumb' : '') . '">
            <div class="box-body notop">
                <div class="title-theme">' . $related_title . '</div>
            </div>';

    echo '<div class="zib-widget">';
    echo $thumb_s ? '<div class="swiper-container swiper-scroll"><div class="swiper-wrapper">' : '<ul class="no-thumb">';
    if (!$posts_lits) {
        echo '<li>暂无相关文章</li>';
    } else {
        echo $posts_lits;
    }
    echo $thumb_s ? '</div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>' : '</ul>';
    echo '</div></div>';
}

// 获取文章标签
function zib_get_posts_tags($class = 'but', $before = '', $after = '', $count = 0)
{
    global $post;
    $tags = get_the_tags($post->ID);
    return zib_get_tags($tags, $class, $before, $after, $count);
}

//数组按一个值从新排序
function arraySort($arrays, $sort_key, $sort_order = SORT_DESC, $sort_type = SORT_NUMERIC)
{
    if (is_array($arrays)) {
        foreach ($arrays as $array) {
            $key_arrays[] = $array->$sort_key;
        }
    } else {
        return false;
    }
    array_multisort($key_arrays, $sort_order, $sort_type, $arrays);
    return $arrays;
}

// 获取标签
function zib_get_tags($tags, $class = 'but', $before = '', $after = '', $count = 0, $ajax_replace = false)
{
    $html = '';
    if (!empty($tags[0])) {
        $ii     = 0;
        $tags_s = arraySort($tags, 'count');
        if (!empty($tags_s[0])) {
            foreach ($tags_s as $tag_id) {
                $ii++;
                $url = get_tag_link($tag_id);
                $tag = get_tag($tag_id);
                $html .= '<a href="' . $url . '"' . ($ajax_replace ? ' ajax-replace="true"' : '') . ' title="查看此标签更多文章" class="' . $class . '">' . $before . $tag->name . $after . '</a>';
                if ($count && $count == $ii) {
                    break;
                }
            }
        }
    }
    return $html;
}

// 获取专题标签
function zib_get_topics_tags($pid = '', $class = 'but', $before = '', $after = '', $count = 0)
{
    if (!$pid) {
        global $post;
        $pid = $post->ID;
    }
    $category = get_the_terms($pid, 'topics');
    $cat      = '';
    if (!empty($category[0])) {
        $ii = 0;
        foreach ($category as $category1) {
            $ii++;
            $cls = array('c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red');
            $cat .= '<a class="' . $class . ' ' . $cls[$ii - 1] . '" title="查看此专题更多文章" href="' . get_category_link($category1->term_id) . '">' . $before . $category1->name . $after . '</a>';
            if ($count && $ii == $count) {
                break;
            }

        }
    }
    return $cat;
}

// 获取分类标签
function zib_get_cat_tags($class = 'but', $before = '', $after = '', $count = 0)
{
    $category = get_the_category();
    $cat      = '';
    if (!empty($category[0])) {
        $ii = 0;
        foreach ($category as $category1) {
            $ii++;
            $cls = array('c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red', 'c-blue', 'c-yellow', 'c-green', 'c-purple', 'c-red');
            $cat .= '<a class="' . $class . ' ' . $cls[$ii - 1] . '" title="查看更多分类文章" href="' . get_category_link($category1->term_id) . '">' . $before . $category1->cat_name . $after . '</a>';
            if ($count && $ii == $count) {
                break;
            }

        }
    }
    return $cat;
}

// 获取文章meta标签
function zib_get_posts_meta($post = null)
{

    if (!is_object($post)) {
        $post = get_post($post);
    }
    $post_id = $post->ID;

    $meta         = '';
    $comment_href = '';
    $is_single    = is_single($post);
    if (comments_open($post) && !_pz('close_comments')) {
        if ($is_single) {
            $comment_href = 'javascript:(scrollTo(\'#comments\',-50));';
        } else {
            $comment_href = get_comments_link($post_id);
        }
        $meta .= '<item class="meta-comm"><a data-toggle="tooltip" title="去评论" href="' . $comment_href . '">' . zib_get_svg('comment') . get_comments_number($post_id) . '</a></item>';
    }
    $meta .= '<item class="meta-view">' . zib_get_svg('view') . get_post_view_count('', '', $post_id) . '</item>';
    if (_pz('post_like_s', true)) {
        $meta .= '<item class="meta-like">' . zib_get_svg('like') . (zib_get_post_like('', $post_id, '', true) ?: '0') . '</item>';
    }

    return $meta;
}

// 链接列表盒子
function zib_links_box($links = array(), $type = 'card', $echo = true, $go_link = false)
{
    if (!$links) {
        return false;
    }

    $html   = '';
    $card   = '';
    $image  = '';
    $simple = '';
    $i      = 0;
    foreach ($links as $link) {
        $link = (array) $link;

        if (empty($link['href']) && !empty($link['link_url'])) {
            $link['href'] = $link['link_url'];
        }
        if (empty($link['title']) && !empty($link['link_name'])) {
            $link['title'] = $link['link_name'];
        }
        if (empty($link['src']) && !empty($link['link_image'])) {
            $link['src'] = $link['link_image'];
        }
        if (empty($link['desc']) && !empty($link['link_description'])) {
            $link['desc'] = $link['link_description'];
        }
        if (empty($link['blank']) && !empty($link['link_target'])) {
            $link['blank'] = $link['link_target'];
        }

        if (!empty($link['href']) && !empty($link['title'])) {
            $href = empty($link['href']) ? '' : esc_url($link['href']);

            if (!empty($link['go_link']) || $go_link) {
                $href = go_link($href, true);
            }

            $title = empty($link['title']) ? '' : esc_attr($link['title']);
            $src   = empty($link['src']) ? '' : esc_attr($link['src']);

            $blank    = empty($link['blank']) ? '' : ' target="_blank"';
            $dec      = empty($link['desc']) ? '' : esc_attr($link['desc']);
            $img      = '<img class="lazyload avatar" src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail-sm.svg" data-src="' . $src . '">';
            $data_dec = $dec ? ' title="' . $title . '" data-content="' . $dec . '" ' : ' data-content="' . $title . '"';
            $card .= '<div class="author-minicard links-card radius8">
                <ul class="list-inline">
                    <li><a ' . $blank . ' class="avatar-img link-img" href="' . $href . '">' . $img . '</a>
                    </li>
                    <li>
                        <dl>
                            <dt><a' . $blank . ' href="' . $href . '" title="' . $dec . '">' . $title . '</a></dt>
                            <dd class="avatar-dest em09 muted-3-color text-ellipsis">' . $dec . '</dd>
                        </dl>
                    </li>
                </ul>
            </div>';
            $image .= '<a ' . $blank . ' class="avatar-img link-only-img" data-trigger="hover" data-toggle="popover" data-placement="top"' . $data_dec . ' href="' . $href . '">' . $img . '</a>';
            $sc = 0 == $i ? '' : 'icon-spot';
            $simple .= '<a ' . $blank . ' class="' . $sc . '" data-trigger="hover" data-toggle="popover" data-placement="top"' . $data_dec . ' href="' . $href . '">' . $title . '</a>';
            $i++;
        }
    }
    if ('card' == $type) {
        $html = $card;
    }
    if ('image' == $type) {
        $html = $image;
    }
    if ('simple' == $type) {
        $html = $simple;
    }

    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * @description: 获取帖子或者版块状态post_status的徽章
 * @param {*} $class
 * @param {*} $posts_id
 * @return {*}
 */
function zib_get_post_status_badge($class = '', $post = null)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }

    if (!isset($post->ID)) {
        return;
    }

    $post_status     = $post->post_status;
    $status_img_name = array(
        'pending' => 'pending-badge',
        'draft'   => 'draft-badge',
        'trash'   => 'trash-badge',
    );
    if (!isset($status_img_name[$post_status])) {
        return;
    }

    $class     = $class ? ' ' . $class : '';
    $lazy_attr = zib_is_lazy('lazy_other', true) ? 'class="lazyload fit-cover" src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail-null.svg" data-' : '';
    $html      = '<span class="img-badge top badge-status' . $class . '"><img ' . $lazy_attr . 'src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/' . $status_img_name[$post_status] . '.svg" alt="状态：' . $post_status . '"></span>';
    return $html;
}

// 公告栏
function zib_notice($args = array(), $echo = true)
{
    $defaults = array(
        'class'    => 'c-blue',
        'interval' => 5000,
        'notice'   => array(),
    );

    $args = wp_parse_args((array) $args, $defaults);

    $interval = ' data-interval="' . $args['interval'] . '"';
    $i        = 0;
    $slides   = '';
    foreach ($args['notice'] as $notice) {
        if (!empty($notice['title'])) {
            $href    = empty($notice['href']) ? '' : $notice['href'];
            $title   = empty($notice['title']) ? '' : $notice['title'];
            $icon    = empty($notice['icon']) ? '' : '<div class="relative bulletin-icon mr6"><i class="abs-center fa ' . $notice['icon'] . '"></i></div>';
            $blank   = empty($notice['blank']) ? '' : ' target="_blank"';
            $s_class = ' notice-slide';
            $slides .= '<div class="swiper-slide' . ' ' . $s_class . '">
            <a class="text-ellipsis"' . $blank . ($href ? ' href="' . $href . '"' : '') . '>'
                . $icon . $title . '</a>
            </div>';
            $i++;
        }
    }

    $html = '<div class="new-swiper" ' . $interval . ' data-direction="vertical" data-loop="true" data-autoplay="1">
            <div class="swiper-wrapper">' . $slides . '</div>
            </div>';

    if ($echo) {
        echo '<div class="swiper-bulletin ' . $args['class'] . '">' . $html . '</div>';
    } else {
        return $html;
    }
}

// 弹出通知
function zib_system_notice()
{
    if (isset($_COOKIE["showed_system_notice"]) || !_pz('system_notice_s', true)) {
        return;
    }

    //显示策略
    $policy = _pz('system_notice_policy');
    if ($policy) {
        switch ($policy) {
            case 'signin':
                if (get_current_user_id()) {
                    return;
                }
                break;

            case 'vip':
            case 'vip_2':
                $user_id = get_current_user_id();
                if ($user_id) {
                    $vip = zib_get_user_vip_level($user_id);
                    if (($vip && $policy === 'vip') || ($vip == 2 && $policy === 'vip_2')) {
                        return;
                    }
                }

                break;

            case 'auth':
                $user_id = get_current_user_id();
                if ($user_id) {
                    $auth = zib_is_user_auth($user_id);
                    if ($auth) {
                        return;
                    }
                }
                break;
        }
    }

    $args = array(
        'id'            => 'modal-system-notice',
        'class'         => _pz('system_notice_size', 'modal-sm'),
        'style'         => '',
        'title'         => _pz('system_notice_title'),
        'content'       => _pz('system_notice_content'),
        'buttons'       => _pz('system_notice_button'),
        'buttons_class' => 'but' . (_pz('system_notice_radius') ? ' radius' : ''),
    );

    if (_pz('system_notice_title_style', 'default') == 'colorful') {
        $args['colorful_header'] = true;
        $args['header_icon']     = zib_get_cfs_icon(_pz('system_notice_title_icon', 'fa fa-heart'));
        $args['header_class']    = _pz('system_notice_title_class', 'jb-blue');
    }

    zib_modal($args);
    $expires = round(_pz('system_notice_expires', 24) / 24, 3);
    $script  = '<script type="text/javascript">';
    $script .= 'window.onload = function(){
        setTimeout(function () {$(\'#modal-system-notice\').modal(\'show\');
        ' . ($expires > 0 ? '$.cookie("showed_system_notice","showed", {path: "/",expires: ' . $expires . '});' : '') . '
    }, 500)};';
    $script .= '</script>';
    echo $script;
}
add_action('wp_footer', 'zib_system_notice', 10);

//模态框构建
function zib_modal($args = array())
{
    $defaults = array(
        'id'              => '',
        'class'           => '',
        'style'           => '',
        'colorful_header' => false,
        'header_class'    => 'jb-blue',
        'header_icon'     => '<i class="fa fa-heart"></i>',
        'title'           => '',
        'content'         => '',
        'buttons_align'   => 'right', //left/centent/right/average
        'buttons'         => array(),
        'buttons_class'   => 'but',
    );

    $args = wp_parse_args((array) $args, $defaults);
    if (!$args['title'] && !$args['content']) {
        return;
    }

    $close_btn = '<button class="close" data-dismiss="modal">' . zib_get_svg('close', null, 'ic-close') . '</button>';
    $title     = '<h4>' . $args['title'] . '</h4>';
    $content   = '<div>' . $args['content'] . '</div>';

    if ($args['colorful_header']) {
        $content   = '<div style="padding: 1px;">' . zib_get_modal_colorful_header($args['header_class'], $args['header_icon'], $args['title']) . $content . '</div>';
        $close_btn = '';
        $title     = '';
    }
    $args['buttons'] = (array) $args['buttons'];
    $buttons         = array();
    if (!empty($args['buttons'][0])) {
        foreach ($args['buttons'] as $but_args) {
            if (!empty($but_args['link']['text'])) {
                $href          = !empty($but_args['link']['url']) ? esc_url($but_args['link']['url']) : 'javascript:;';
                $buttons_class = !empty($but_args['class']) ? ' ' . $but_args['class'] : '';
                $target        = !empty($but_args['link']['target']) ? ' target=' . $but_args['link']['target'] : '';
                $attr          = !empty($but_args['attr']) ? ' ' . $but_args['attr'] : '';
                $buttons[]     = '<a type="button"' . $target . $attr . ' class="' . $args['buttons_class'] . $buttons_class . '" href="' . $href . '">' . $but_args['link']['text'] . '</a>';
            }
        }
    }
    $button_box_class = 'modal-buts box-body notop text-' . $args['buttons_align'];
    if ('average' == $args['buttons_align']) {
        $button_box_class = 'modal-buts but-average';
    }
    //按钮平均分布

    ?>
    <div class="modal fade" id="<?php echo $args['id']; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog <?php echo $args['class']; ?>" <?php echo 'style="' . $args['style'] . '"'; ?> role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php echo $close_btn . $title . $content; ?>
                </div>
                <?php if ($buttons) {
        echo '<div class="' . $button_box_class . '">' . implode($buttons) . '</div>';
    }?>
            </div>
        </div>
    </div>
<?php
}

function zib_get_admin_ajax_url($action = false, $query_arg = array())
{
    $url = admin_url('admin-ajax.php');
    if ($action) {
        $query_arg['action'] = $action;
    }
    if ($query_arg) {
        return add_query_arg($query_arg, $url);
    }
    return $url;
}

//获取每次都会刷新的模态框按钮
function zib_get_refresh_modal_link($args = array())
{
    $defaults = array(
        'new'           => false,
        'tag'           => 'botton',
        'class'         => '',
        'data_class'    => '',
        'remote'        => '',
        'text'          => '按钮',
        'attr'          => '',
        'mobile_bottom' => false,
        'query_arg'     => false,
        'height'        => false,
    );
    $args = wp_parse_args($args, $defaults);
    if (!$args['remote'] && !$args['query_arg']) {
        return;
    }

    if (!$args['remote'] && $args['query_arg']) {
        $args['remote'] = zib_get_admin_ajax_url(null, $args['query_arg']);
    }
    $data_attr = $args['attr'] ? ' ' . $args['attr'] : '';
    $data_attr .= $args['new'] ? ' new="new"' : '';
    $data_attr .= $args['data_class'] ? ' data-class="' . $args['data_class'] . '"' : '';
    $data_attr .= $args['mobile_bottom'] ? ' mobile-bottom="true"' : '';
    $data_attr .= (int) $args['height'] ? ' data-height="' . (int) $args['height'] . '"' : '';

    $link = '<' . $args['tag'] . $data_attr . ' data-remote="' . esc_url($args['remote']) . '" class="' . esc_attr($args['class']) . '" href="javascript:;" data-toggle="RefreshModal">' . $args['text'] . '</' . $args['tag'] . '>';
    return $link;
}

//获取空白的模态框链接
function zib_get_blank_modal_link($args = array())
{
    $defaults = array(
        'id'         => 'blank_modal_' . mt_rand(100, 999),
        'link_class' => '',
        'remote'     => '',
        'text'       => '',
    );
    $args = wp_parse_args((array) $args, $defaults);

    $link = '<a class="' . esc_attr($args['link_class']) . '" href="javascript:;" data-toggle="modal" data-target="#' . esc_attr($args['id']) . '" data-remote="' . esc_url($args['remote']) . '">' . $args['text'] . '</a>';
    return $link . zib_get_blank_modal($args);
}

/**
 * @description: 空白模态框构建，适用于带AJAX的模态框
 * @param {*}
 * @return {*}
 */
function zib_get_blank_modal($args = array())
{
    $defaults = array(
        'id'              => '',
        'class'           => '',
        'flex'            => 'jc',
        'mobile_bottom'   => false,
        'style'           => '',
        'colorful_header' => false,
        'content'         => '<div class="modal-body"><div class="box-body"><p class="placeholder t1"></p> <h4 style="height:120px;" class="placeholder k1"></h4><p class="placeholder k2"></p><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></div></div>',
    );
    $args = wp_parse_args((array) $args, $defaults);

    if ($args['colorful_header']) {
        $args['content'] = '<div style="padding: 1px;">' . zib_get_modal_colorful_header('jb-blue', '<i class="loading"></i>') . $args['content'] . '</div>';
    }

    $modal_class = 'modal fade';

    $html = '';
    $html .= '<div class="' . $modal_class . '" id="' . $args['id'] . '" tabindex="-1" role="dialog">';
    $html .= '<div class="modal-dialog ' . $args['class'] . '" style="' . $args['style'] . '" role="document">';
    $html .= '<div class="modal-content">';
    $html .= $args['content'];
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

/**
 * @description: 获取模态框的炫彩头部
 * @param {*} $class
 * @param {*} $icon
 * @param {*} $cetent
 * @param {*} $close_btn
 * @return {*}
 */
function zib_get_modal_colorful_header($class = 'jb-blue', $icon = '', $cetent = '', $close_btn = true)
{
    $html = '<div class="modal-colorful-header colorful-bg ' . $class . '">';
    $html .= $close_btn ? '<button class="close" data-dismiss="modal">' . zib_get_svg('close', null, 'ic-close') . '</button>' : '';
    $html .= '<div class="colorful-make"></div>';
    $html .= '<div class="text-center">';
    $html .= $icon ? '<div class="em2x">' . $icon . '</div>' : '';
    $html .= $cetent ? '<div class="mt10 em12 padding-w10">' . $cetent . '</div>' : '';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}

/**
 * @description: 万能-构建AJAX的tab内容
 * @param {*}
 * @return {*}
 */
function zib_get_ajax_tab($type = 'nav', $tabs = array(), $args = array())
{
    $example   = array();
    $example[] = array(
        'name'     => '例子',
        'id'       => 'posts-example',
        'ajax_url' => '',
        'action'   => 'posts_example',
        'class'    => 'example',
        'loader'   => '',
    );
    $defaults = array(
        'ajax_url'  => admin_url('admin-ajax.php'),
        'nav_class' => '',
        'loader'    => zib_placeholder(),
        'no_scroll' => true,
    );
    $defaults['loader'] .= $defaults['loader'];
    $defaults['loader'] .= $defaults['loader'];
    $args = wp_parse_args($args, $defaults);

    $html = '';
    foreach ($tabs as $tab) {
        $action   = !empty($tab['action']) ? $tab['action'] : '';
        $id       = !empty($tab['id']) ? $tab['id'] : 'tab_' . $action;
        $name     = !empty($tab['name']) ? $tab['name'] : '';
        $class    = !empty($tab['class']) ? ' ' . $tab['class'] : '';
        $ajax_url = !empty($tab['ajax_url']) ? $tab['ajax_url'] : $args['ajax_url'];
        $loader   = !empty($tab['loader']) ? $tab['loader'] : $args['loader'];

        if (!$action) {
            continue;
        }

        if ('nav' == $type) {
            $html .= '<li><a class="' . $args['nav_class'] . '" data-toggle="tab" data-ajax="" href="#' . $id . '">' . $name . '</a></li>';
        } else {
            $a_attr = $args['no_scroll'] ? ' no-scroll="true"' : '';
            $html .= '<div class="tab-pane fade ajaxpager' . $class . '" id="' . $id . '">';
            $html .= '<span class="post_ajax_trigger hide"><a' . $a_attr . ' ajax-href="' . esc_url(add_query_arg('action', $action, $ajax_url)) . '" class="ajax_load ajax-next ajax-open"></a></span>';
            $html .= '<div class="post_ajax_loader">' . $loader . '</div>';
            $html .= '</div>';
        }
    }
    return $html;
}

/**
 * @description: 上传文件的模态框构建|未使用
 * @param array $args
 * @param bool $echo
 * @return $html
 */
function zib_upload_modal($args = array(), $echo = true)
{
    $defaults = array(
        'id'            => '', //必须
        'action'        => 'img-upload', //必须
        'class'         => '',
        'style'         => '',
        'before'        => '<h4><i class="fa fa-cloud-upload em12 mr10 focus-color"></i></h4><h4>上传图片</h4><div class="muted-2-color">请选择上传图片，支持jpg/png/gif，大小不能超过' . _pz("up_max_size", '4') . 'M</div>',
        'after'         => '',
        'action_url'    => '',
        'button1_title' => '<i class="fa fa-cloud-upload mr10"></i>选择图片',
        'button1_class' => 'but padding-lg c-yellow',
        'button2_title' => '<i class="fa fa-check mr10"></i>确认上传',
        'button2_class' => 'but jb-blue padding-lg',
        'success'       => '',
    );
    $args = wp_parse_args((array) $args, $defaults);

    $action  = $args['action_url'] ? ' action="' . esc_url($args['action_url']) . '"' : '';
    $success = $args['success'] ? ' zibupload-success="' . esc_attr($args['success']) . '"' : '';
    $from    = '<form' . $action . '>
        <div class="box-body">
            <div class="preview text-center"><img style="width: 100%;" src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail-sm.svg' . '"></div>
        </div>
        <div class="text-right">
        <label>
            <a class="' . $args['button1_class'] . '">' . $args['button1_title'] . '</a>
            <input class="hide" type="file" zibupload="image_upload" accept="image/gif,image/jpeg,image/jpg,image/png" name="image_upload" action="image_upload">
        </label>
            <button type="button" zibupload="submit"' . $success . ' class="' . $args['button2_class'] . '" name="submit">' . $args['button2_title'] . '</button>
            <input type="hidden" name="action" value="' . $args['action'] . '">
            ' . wp_nonce_field($args['action'], $args['action'] . '_nonce', false, false) . '
        </div>
</form>';

    $html = '<div class="modal modal-upload fade" id="' . $args['id'] . '" tabindex="-1" role="dialog">';
    $html .= '<div class="modal-dialog ' . $args['class'] . '" style="' . $args['style'] . '" role="document">';
    $html .= '<div class="modal-content"><div class="modal-body">';
    $html .= '<button class="close" data-dismiss="modal">' . zib_get_svg('close', null, 'ic-close') . '</button>';
    $html .= $args['before'];
    $html .= $from;
    $html .= $args['after'];
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';

    if ($echo) {
        echo $html;
    }

    return $html;
}

/**
 * @description: 判断自己是不是文章的作者
 * @param {*} $post
 * @param {*} $user_id
 * @return {*}
 */
function zib_is_the_author($post = null, $user_id = null)
{
    if (!is_object($post)) {
        $post = get_post($post);
    }
    if (empty($post->post_author)) {
        return false;
    }
    return $post->post_author == $user_id;
}

//获取社交登录的链接
//自动判断是否开启此社交登录方式，可直接当做判断函数使用
function zib_get_oauth_login_url($type, $rurl = '')
{
    if (!$rurl) {
        $rurl = !empty($_GET['redirect_to']) ? $_GET['redirect_to'] : zib_get_current_url();
    }

    static $login_args = array();

    if (isset($login_args[$type])) {
        $login_url = $login_args[$type];
    } else {
        $login_url         = apply_filters('zib_oauth_login_url', '', $type);
        $login_args[$type] = $login_url;
    }

    if ($login_url) {
        return add_query_arg('rurl', urlencode($rurl), $login_url);
    }

    return false;
}

/**
 * @description: 获取自带的社交账号登录链接
 * @param {*} $type
 * @return {*}
 */
function zib_get_self_oauth_login_url($url, $type)
{
    if (!$url && _pz('oauth_' . $type . '_s')) {
        $url = home_url('oauth/' . $type);
    }

    return $url;
}

//判断扫码登录功能是否开启
function zib_is_oauth_qrcode_s()
{
    $qrcode_type = array('weixingzh'); //使用扫码登录的类型
    foreach ($qrcode_type as $type) {
        if (zib_get_oauth_login_url($type)) {
            return true;
        }
    }
    return false;
}

//社交登录按钮构建
function zib_social_login($echo = true)
{
    if (zib_is_close_sign()) {
        return;
    }

    $buttons = '';
    if (_pz('social') && function_exists('xh_social_loginbar')) {
        $buttons = xh_social_loginbar('', false);
    } else {
        $b_c  = _pz('oauth_button_lg') ? ' button-lg' : '';
        $args = zib_get_social_type_data();
        foreach ($args as $arg) {
            $type = $arg['type'];
            $name = $arg['name'];
            $icon = zib_get_cfs_icon($arg['icon']);
            if ('alipay' == $type) {
                if (wp_is_mobile() && !strpos($_SERVER['HTTP_USER_AGENT'], 'Alipay')) {
                    continue;
                }
                //移动端并且不是支付宝APP不显示支付宝
            }

            $href = zib_get_oauth_login_url($type);
            if ($href) {
                $_class = $type . ($b_c ? $b_c : ' toggle-radius');
                if (!empty($arg['qrcode'])) {
                    $_class .= ' qrcode-signin';
                }

                $buttons .= '<a title="' . $name . '登录" href="' . esc_url($href) . '" class="social-login-item ' . $_class . '">' . $icon . ($b_c ? $name . '登录' : '') . '</a>';
            }
        }
    }
    if ($echo && $buttons) {
        echo '<p class="social-separator separator muted-3-color em09">社交帐号登录</p>';
        echo '<div class="social_loginbar">';
        echo $buttons;
        echo '</div>';
    } else {
        return $buttons;
    }
}

/**
 * @description: 获取社交登录的类型名字
 * @param {*} $type
 * @return {*}
 */
function zib_get_social_type_name($type)
{
    $type_name = zib_get_social_type_data();
    return isset($type_name[$type]['name']) ? $type_name[$type]['name'] : '第三方';
}

/**
 * @description: 判断微信公众号是否是扫码模式
 * @param {*}
 * @return {*}
 */
function zib_weixingzh_is_qrcode()
{
    //不在微信内
    if (!zib_is_wechat_app()) {
        return true;
    }

    if (_pz('oauth_agent', 'close') === 'client') {
        if (_pz('oauth_agent_client_option', '', 'gzh_type') === 'not') {
            return true;
        }

    } elseif (_pz('oauth_weixingzh_option', '', 'gzh_type') === 'not') {
        return true;
    }

    return false;
}

/**
 * @description: 获取全部社交登录的资料
 * @param {*}
 * @return {*}
 */
function zib_get_social_type_data()
{
    $args       = array();
    $args['qq'] = array(
        'name'     => 'QQ',
        'type'     => 'qq',
        'class'    => 'c-blue',
        'name_key' => 'nickname',
        'icon'     => 'fa fa-qq',
    );
    $args['weixin'] = array(
        'name'     => '微信',
        'type'     => 'weixin',
        'class'    => 'c-green',
        'name_key' => 'nickname',
        'icon'     => 'fa fa-weixin',
    );
    $args['weixingzh'] = array(
        'name'   => '微信',
        'type'   => 'weixingzh',
        'class'  => 'c-green',
        'icon'   => 'fa fa-weixin',
        'qrcode' => zib_weixingzh_is_qrcode(),
    );
    $args['weibo'] = array(
        'name'     => '微博',
        'type'     => 'weibo',
        'class'    => 'c-red',
        'name_key' => 'screen_name',
        'icon'     => 'fa fa-weibo',
    );
    $args['gitee'] = array(
        'name'     => '码云',
        'type'     => 'gitee',
        'name_key' => 'name',
        'class'    => '',
        'icon'     => 'zibsvg-gitee',
    );
    $args['baidu'] = array(
        'name'  => '百度',
        'type'  => 'baidu',
        'class' => '',
        'icon'  => 'zibsvg-baidu',
    );
    $args['alipay'] = array(
        'name'  => '支付宝',
        'type'  => 'alipay',
        'class' => 'c-blue',
        'icon'  => 'zibsvg-alipay',
    );
    $args['dingtalk'] = array(
        'name'  => '钉钉',
        'type'  => 'dingtalk',
        'class' => 'c-blue',
        'icon'  => 'zibsvg-dingtalk',
    );
    $args['huawei'] = array(
        'name'  => '华为',
        'type'  => 'huawei',
        'class' => 'c-blue',
        'icon'  => 'zibsvg-huawei',
    );
    $args['github'] = array(
        'name'     => 'GitHub',
        'type'     => 'github',
        'class'    => '',
        'name_key' => 'name',
        'icon'     => 'fa fa-github',
    );
    $args['google'] = array(
        'name'  => 'Google',
        'type'  => 'google',
        'class' => 'c-blue',
        'icon'  => 'fa fa-google',
    );
    $args['microsoft'] = array(
        'name'  => 'Microsoft',
        'type'  => 'microsoft',
        'class' => 'c-blue',
        'icon'  => 'fa fa-windows',
    );
    $args['facebook'] = array(
        'name'  => 'Facebook',
        'type'  => 'facebook',
        'class' => 'c-blue',
        'icon'  => 'fa fa-facebook',
    );
    $args['twitter'] = array(
        'name'  => 'Twitter',
        'type'  => 'twitter',
        'class' => 'c-blue',
        'icon'  => 'fa fa-twitter',
    );

    return $args;
}

//微信app内自动登录
//限制时间外&&开启辞工&&在微信APP内&&未登录状态&&开启了微信公众号登录

function zib_weixingzh_sign_script()
{

    //判断微信公众号功能是否开启
    //判断是否已经登录
    if (!zib_get_oauth_login_url('weixingzh') || get_current_user_id()) {
        return;
    }

    if (zib_weixingzh_is_qrcode()) {
        //PC端扫码登录
        //在PC端点击登录优先显示微信扫码登录
        if (_pz('weixingzh_priority')) {
            $script = '<script type="text/javascript">';
            $script .= '_win.signin_wx_priority = true;';
            $script .= is_page_template('pages/user-sign.php') && isset($_GET['tab']) && $_GET['tab'] === 'signin' ? 'window.onload = function(){
                            $($(\'.social-login-item.weixingzh\')[0]).click();
                        };' : '';
            $script .= '</script>';
            echo $script;
        }
    } else {
        //不是扫码登录，也就是微信APP内登录
        //在微信APP内自动弹出微信登录
        if (!isset($_COOKIE["showed_weixingzh_auto"]) && _pz('weixingzh_auto')) {
            $expires = round(_pz('weixingzh_auto_expires', 24) / 24, 3);
            $script  = '<script type="text/javascript">';
            $script .= 'window.onload = function(){setTimeout(function () {
                            var _w = $($(\'.social-login-item.weixingzh\')[0]);
                            if(_w.length){
                                window.location.href=_w.attr(\'href\');
                                ' . ($expires > 0 ? '$.cookie("showed_weixingzh_auto","showed", {path: "/",expires: ' . $expires . '});' : '') . '
                            }
                        }, 200)};';
            $script .= '</script>';
            echo $script;
        }
    }
}
add_action('wp_footer', 'zib_weixingzh_sign_script', 99);

// 链接提交的卡片
function zib_submit_links_card($args = array())
{
    $defaults = array(
        'class'      => '',
        'title'      => '',
        'subtitle'   => '',
        'dec'        => '',
        'show_title' => true,
    );

    $args = wp_parse_args((array) $args, $defaults);

    $subtitle = $args['subtitle'];
    if ($subtitle) {
        $subtitle = '<small class="ml10">' . esc_attr($subtitle) . '</small>';
    }
    $title = $args['title'];
    if ($title) {
        $title = '<div class="box-body notop"><div class="title-theme">' . $title . $subtitle . '</div></div>';
    }

    $card = '<div class="zib-widget">';
    $card .= '<form class="form-horizontal mt10">';
    if ($args['dec']) {
        $card .= '<div class="form-group">
                <label class="col-sm-2 control-label c-red">提交说明</label>
                <div class="col-sm-9 mb10">
                    ' . $args['dec'] . '
                </div>
            </div>';
    }
    $card .= '<div class="form-group">
                <label for="link_name" class="col-sm-2 control-label">链接名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="link_name" name="link_name" placeholder="链接名称（必填）">
                </div>
            </div>';
    $card .= '<div class="form-group">
                <label for="link_url" class="col-sm-2 control-label">链接地址</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="link_url" name="link_url" placeholder="链接地址（必填）">
                </div>
            </div>';
    $card .= '<div class="form-group">
                <label for="link_description" class="col-sm-2 control-label">链接简介</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="link_description" name="link_description" placeholder="链接简介">
                </div>
            </div>';
    $card .= '<div class="form-group">
                <label for="link_image" class="col-sm-2 control-label">LOGO地址</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="link_image" name="link_image" placeholder="LOGO图像地址">
                </div>
            </div>';

    //人机验证
    if (_pz('verification_links_s')) {
        $verification_input = zib_get_machine_verification_input('frontend_links_submit');
        if ($verification_input) {
            $card .= '<div class="form-group">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-9" style="max-width: 300px;">' . $verification_input . '</div>
                </div>';
        }
    }

    $card .= '<div class="form-group">
                <label class="col-sm-2"></label>
                <div class="col-sm-9">
                    <button class="but c-blue padding-lg wp-ajax-submit"><i class="fa fa-check" aria-hidden="true"></i>提交链接</button>
                </div>
            </div>';

    $card .= wp_nonce_field('frontend_links_submit', '_wpnonce', false, false); //安全效验
    $card .= '<input type="hidden" name="action" value="frontend_links_submit">';
    $card .= '</form>';

    $card .= '</div>';

    $html = $title . $card;
    return $html;
}

//获取新建按钮
function zib_get_new_add_btns($types = array('post'), $class = '', $con = '')
{

    if (zib_is_close_sign()) {
        return;
    }

    $btns = array();
    $html = '';
    foreach ($types as $type) {
        $_btn = apply_filters('new_add_btns_' . $type, '');
        if ($_btn) {
            $btns[] = $_btn;
        }
    }

    if (!$btns) {
        return;
    }

    if (!isset($btns[1])) {
        //只有一个按钮
        $pattern     = "/<a(.*?) class=('|\")(.*?)('|\")(.*?)>(.*?)<\/a>/i";
        $replacement = '<a$1 class="newadd-btns ' . $class . ' $3"$5>' . $con . '</a>';
        return preg_replace($pattern, $replacement, $btns[0]);
    } else {
        //有多个按钮
        $html = '<span class="newadd-btns hover-show ' . $class . '">
                    ' . $con . '
                    <div class="hover-show-con dropdown-menu drop-newadd">' . implode('', $btns) . '</div>
                </span>';
    }
    return $html;
}

//新建按钮的选项
function zib_new_add_btns_options()
{
    return apply_filters('new_add_btns_options', array('post' => '文章投稿'));
}

//新建按钮-前台投稿
function zib_new_add_btns_post_filter()
{
    if (_pz('post_article_s', true) && !is_page_template('pages/newposts.php')) {
        $href = zib_get_template_page_url('pages/newposts.php');
        $icon = '<icon class="jb-green"><i class="fa fa-pencil-square"></i></icon>';
        return '<a class="btn-newadd" href="' . $href . '">' . $icon . '<text>' . _pz('post_article_btn_txte', '发布文章') . '</text></a>';
    }
    return;
}
add_filter('new_add_btns_post', 'zib_new_add_btns_post_filter');

//文章多重筛选代码
//通过pre_get_posts钩子筛选
add_action('pre_get_posts', 'zib_sift_posts_per_page', 999);
function zib_sift_posts_per_page($query)
{
    //is_category()即为分类页面有效，自行更换。
    //$query->is_main_query()使得仅对默认的页面主查询有效
    //!is_admin()避免影响后台文章列表

    if ((is_category() || is_tag() || is_home() || is_tax('topics')) && $query->is_main_query() && !is_admin()) {
        // 分类
        if (isset($_GET['cat'])) {
            $cat = $_GET['cat'];
            $query->set('cat', $cat);
        }
        //  标签
        if (isset($_GET['tag'])) {
            $tag = $_GET['tag'];
            $query->set('tag', $tag);
        }
        // 自定义分类法：taxonomy  topics
        if (isset($_GET['topics'])) {
            $array_temp = array(array('taxonomy' => 'topics', 'terms' => preg_split("/,|，|\s|\n/", $_GET['topics'])));
            $query->set('tax_query', $array_temp);
        }

        // 自定义字段：mate type
        if (isset($_GET['type'])) {
            $array_temp = array('key' => 'type', 'value' => $_GET['type'], 'compare' => '=');
        }
    }
}

//文章排序
//通过pre_get_posts钩子筛选
add_action('pre_get_posts', 'zib_sift_posts_per_orde', 9999);
function zib_sift_posts_per_orde($query)
{
    //正反顺序
    if (isset($_GET['order']) && $query->is_main_query() && !is_admin()) {
        $order = 'DESC' == $_GET['order'] ? 'DESC' : 'ASC';
        $query->set('order', $order);
    }
    //按照什么排序
    if (isset($_GET['orderby']) && $query->is_main_query() && !is_admin()) {
        $orderby           = $_GET['orderby'];
        $mate_orderbys     = array('last_reply', 'last_post');
        $mate_orderbys_num = array('views', 'favorite', 'like', 'score', 'plate_id', 'posts_count', 'reply_count', 'today_reply_count', 'follow_count', 'views');
        if (in_array($orderby, $mate_orderbys_num)) {
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', $orderby);
        } elseif (in_array($orderby, $mate_orderbys)) {
            $query->set('orderby', 'meta_value');
            $query->set('meta_key', $orderby);
        } else {
            $query->set('orderby', $orderby);
        }
    }

    //帖子状态
    global $wp_query;
    $curauth = $wp_query->get_queried_object();

    if (isset($_GET['post_status']) && $query->is_main_query() && (is_super_admin() || (!empty($curauth->ID) && get_current_user_id() === $curauth->ID))) {
        $query->set('post_status', $_GET['post_status']);
    }
}

/**
 * @description: 过滤new WP_Query orderby的args
 * @param {*} $orderby
 * @param {*} $args
 * @return {*}
 */
function zib_query_orderby_filter($orderby, $args = array())
{

    $mate_orderbys     = array('last_reply', 'last_post');
    $mate_orderbys_num = array('score', 'plate_id', 'posts_count', 'reply_count', 'today_reply_count', 'follow_count', 'follow', 'views', 'like');

    if (in_array($orderby, $mate_orderbys_num)) {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = $orderby;
    } elseif (in_array($orderby, $mate_orderbys)) {
        $args['orderby']  = 'meta_value';
        $args['meta_key'] = $orderby;
    } else {
        $args['orderby'] = $orderby;
    }

    if (!isset($args['order'])) {
        $args['order'] = 'DESC';
    }

    return $args;
}

/**
 * @description: 编辑器按钮扩展
 * @param {*}
 * @return {*}
 */
function zib_get_input_expand_but($type = 'smilie', $upload = true, $upload_id = '')
{
    $but      = '';
    $dropdown = '';
    if (!is_user_logged_in()) {
        $upload = false;
    }
    //表情
    if ('smilie' == $type) {
        $but              = '<a class="but btn-input-expand input-smilie mr6" href="javascript:;"><i class="fa fa-fw fa-smile-o"></i><span class="hide-sm">表情</span></a>';
        $smilie_icon_args = array('aoman', 'baiyan', 'bishi', 'bizui', 'cahan', 'ciya', 'dabing', 'daku', 'deyi', 'doge', 'fadai', 'fanu', 'fendou', 'ganga', 'guzhang', 'haixiu', 'hanxiao', 'zuohengheng', 'zhuakuang', 'zhouma', 'zhemo', 'zhayanjian', 'zaijian', 'yun', 'youhengheng', 'yiwen', 'yinxian', 'xu', 'xieyanxiao', 'xiaoku', 'xiaojiujie', 'xia', 'wunai', 'wozuimei', 'weixiao', 'weiqu', 'tuosai', 'tu', 'touxiao', 'tiaopi', 'shui', 'se', 'saorao', 'qiudale', 'se', 'qinqin', 'qiaoda', 'piezui', 'penxue', 'nanguo', 'liulei', 'liuhan', 'lenghan', 'leiben', 'kun', 'kuaikule', 'ku', 'koubi', 'kelian', 'keai', 'jingya', 'jingxi', 'jingkong', 'jie', 'huaixiao', 'haqian', 'aini', 'OK', 'qiang', 'quantou', 'shengli', 'woshou', 'gouyin', 'baoquan', 'aixin', 'bangbangtang', 'xiaoyanger', 'xigua', 'hexie', 'pijiu', 'lanqiu', 'juhua', 'hecai', 'haobang', 'caidao', 'baojin', 'chi', 'dan', 'kulou', 'shuai', 'shouqiang', 'yangtuo', 'youling');
        $smilie_icon      = '';
        $img_url          = ZIB_TEMPLATE_DIRECTORY_URI . '/img/smilies/';
        $lazy_attr        = zib_is_lazy('lazy_other', true) ? 'class="lazyload" data-' : '';
        foreach ($smilie_icon_args as $smilie_i) {
            $smilie_icon .= '<a class="smilie-icon" href="javascript:;" data-smilie="' . $smilie_i . '"><img ' . $lazy_attr . 'src="' . $img_url . $smilie_i . '.gif" alt="[' . $smilie_i . ']" /></a>';
        }
        $dropdown = '<div class="dropdown-smilie scroll-y mini-scrollbar">' . $smilie_icon . '</div>';
    }
    if ('code' == $type) {
        $but = '<a class="but btn-input-expand input-code mr6" href="javascript:;"><i class="fa fa-fw fa-code"></i><span class="hide-sm">代码</span></a>';

        $dropdown = '<div class="dropdown-code">';
        $dropdown .= '<p>请输入代码：</p>';
        $dropdown .= '<p><textarea rows="6" tabindex="1" class="form-control input-textarea" placeholder="在此处粘贴或输入代码"></textarea></p>';
        $dropdown .= '<div class="text-right"><a type="submit" class="but c-blue pw-1em" href="javascript:;">确认</a></div>';
        $dropdown .= '</div>';
    }
    if ('image' == $type) {
        $but = '<a class="but btn-input-expand input-image mr6" href="javascript:;"><i class="fa fa-fw fa-image"></i><span class="hide-sm">图片</span></a>';

        $dropdown = '<div class="tab-content">';

        //第一个tab|输入图片地址
        $dropdown .= '<div class="tab-pane fade in active dropdown-image" id="image-tab-' . $upload_id . '-1">';
        $dropdown .= '<p>请填写图片地址：</p>';
        $dropdown .= '<p><textarea rows="2" tabindex="1" class="form-control input-textarea" style="height:95px;" placeholder="http://..."></textarea></p>';
        $dropdown .= '<div class="text-right">';
        if ($upload) {
            $dropdown .= '<a class="but c-yellow mr10 pw-1em" data-toggle="tab" href="#image-tab-' . $upload_id . '-2" data-onclick="#input_' . $upload_id . '_image_upload">上传图片</a>';
        }
        $dropdown .= '<a type="submit" class="but c-blue pw-1em" href="javascript:;">确认</a>';
        $dropdown .= '</div>';
        $dropdown .= '</div>';

        if ($upload) {
            //第二个tab|上传图片
            $dropdown .= '<div class="tab-pane fade dropdown-image" id="image-tab-' . $upload_id . '-2">';
            $dropdown .= '<p><a class="muted-color" data-toggle="tab" href="#image-tab-' . $upload_id . '-1"><i class="fa fa-angle-left mr6"></i>填写图片地址</a></p>';

            $from = '<div class="form-upload">
                        <label style="width:100%;" class="pointer">
                            <div class="preview text-center mb6"><img style="width:100%;height:96px;object-fit:cover;" src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/upload-add.svg' . '"></div>
                            <input class="hide" type="file" id="input_' . $upload_id . '_image_upload" zibupload="image_upload" accept="image/gif,image/jpeg,image/jpg,image/png" name="image_upload" action="image_upload">
                        </label>
                        <div class="text-right">
                            <button type="button" zibupload="submit" auto-submit="true" class="but jb-blue pw-1em input-expand-upload" name="submit">确认上传</button>
                            <input type="hidden" data-name="action" data-value="user_upload_image">
                            ' . wp_nonce_field('upload_image', 'upload_image_nonce', false, false) . '
                        </div>
                </div>';

            $dropdown .= $from;

            $dropdown .= '</div>';
        }

        $dropdown .= '</div>';
    }

    $con = $but . '<div class="dropdown-menu">' . $dropdown . '</div>';

    return '<span class="dropup relative ' . $type . '">' . $con . '</span>';
}

/**
 * @description: 图文卡片
 * @param {*}
 * @return {*}
 */
function zib_graphic_card($args = array(), $echo = false)
{
    $defaults = array(
        'type'         => '',
        'class'        => 'mb20',
        'img'          => '',
        'alt'          => '图片',
        'link'         => array(
            'url'    => '',
            'target' => '',
        ),
        'text'         => '',
        'text1'        => '',
        'text2'        => '',
        'text3'        => '',
        'more'         => '',
        'lazy'         => true,
        'height_scale' => 0,
        'mask_opacity' => 0,
    );

    $args = wp_parse_args((array) $args, $defaults);
    if (!$args['img']) {
        return;
    }

    $args['class'] .= ' ' . $args['type'];

    $lazy     = $args['lazy'];
    $lazy_src = ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail.svg';
    $img      = '<img class="fit-cover' . ($lazy ? ' lazyload' : '') . '" ' . ($lazy ? ' alt="' . ($args['alt'] ?: '图片') . zib_get_delimiter_blog_name() . '" data-src="' . $args['img'] . '" src="' . $lazy_src . '"': ' src="' . $args['img'] . '"') . '>';
    $mask     = $args['mask_opacity'] ? '<div class="absolute graphic-mask" style="opacity: ' . ((int) $args['mask_opacity'] / 100) . ';"></div>' : '';
    $text     = '';
    if ('style-2' == $args['type']) {
        $text = '<div class="abs-center conter-conter graphic-text">';
        $text .= $args['text1'] ? '<div class="title-h-center"><b>' . $args['text1'] . '</b></div>' : '';
        $text .= $args['text2'] ? '<div class="em09 opacity8">' . $args['text2'] . '</div>' : '';
        $text .= '</div>';
        if ($args['text3']) {
            $text .= '<div class="abs-center right-top">';
            $text .= '<badge class="b-black opacity8">' . $args['text3'] . '</badge>';
            $text .= '</div>';
        }
    } elseif ('style-3' == $args['type']) {
        $text = '<div class="abs-center left-bottom graphic-text text-ellipsis">';
        $text .= $args['text1'];
        $text .= '</div>';
        $text .= '<div class="abs-center left-bottom graphic-text">';
        $text .= '<div class="em09 opacity8">' . $args['text2'] . '</div>';
        $text .= $args['text3'] ? '<div class="px12 opacity8 mt6">' . $args['text3'] . '</div>' : '';
        $text .= '</div>';
    } elseif ('style-4' == $args['type']) {
        $text = '';
        $text .= '<div class="abs-center right-top">';
        $text .= '<badge class="b-black opacity8 mr6 mt6">' . $args['text3'] . '</badge>';
        $text .= '</div>';
    } else {
        $text = $args['text1'] ? '<div class="title-h-left"><b>' . $args['text1'] . '</b></div>' : '';
        $text .= $args['text3'] ? '<div class="em09 opacity8">' . $args['text3'] . '</div>' : '';
        $text .= $args['text2'] ? '<div class="em09">' . $args['text2'] . '</div>' : '';
        $text = $text ? '<div class="abs-center left-bottom graphic-text">' . $text . '</div>' : '';
    }
    $text .= $args['more'] ? $args['more'] : '';

    $height_scale = $args['height_scale'] ? ' style="padding-bottom: ' . (int) $args['height_scale'] . '%!important;"' : '';
    $html         = $args['link']['url'] ? '<a' . ($args['link']['target'] ? ' target="' . $args['link']['target'] . '"' : '') . ' href="' . $args['link']['url'] . '">' : '';
    $html .= 'style-4' == $args['type'] ? '<div class="main-shadow radius8 main-bg mb10">' : '';
    $html .= '<div class="graphic hover-zoom-img ' . $args['class'] . '"' . $height_scale . '>';
    $html .= $img;
    $html .= $mask;
    $html .= $text;
    $html .= 'style-4' == $args['type'] ? '</div>' : '';
    if ('style-4' == $args['type']) {
        $html .= '<div class="padding-10">';
        $html .= '<div class="text-ellipsis"> ' . $args['text1'] . '</div>';
        $html .= $args['text2'] ? '<div class="muted-2-color em09 text-ellipsis mt6"> ' . $args['text2'] . '</div>' : '';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= $args['link']['url'] ? '</a>' : '';

    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * @description: 图文卡片
 * @param {*} $args
 * @param {*} $echo
 * @return {*}
 */
function zib_icon_card($args = array(), $echo = false)
{
    $defaults = array(
        'type'              => '',
        'class'             => 'box-body nopw-sm',
        'icon'              => '',
        'icon_size'         => '',
        'customize_icon'    => '',
        'link'              => array(
            'url'    => '',
            'target' => '',
        ),
        'icon_radius'       => '',
        'icon_color'        => '',
        'icon_custom_color' => '',
        'icon_class'        => '',
        'title'             => '',
        'desc'              => '',
    );
    $args = wp_parse_args((array) $args, $defaults);
    if (!$args['customize_icon'] && !$args['icon']) {
        return;
    }

    $icon       = $args['customize_icon'] ? $args['customize_icon'] : zib_get_cfs_icon($args['icon']);
    $icon_class = $args['icon_radius'] ? 'card-icon toggle-radius fa-3x ' . ($args['icon_class'] ? $args['icon_class'] : $args['icon_color']) : 'card-icon fa-4x ' . $args['icon_color'];

    $icon          = '<span class="' . $icon_class . '"' . (!$args['icon_radius'] && $args['icon_custom_color'] ? ' style="color:' . ($args['icon_custom_color']) . ';"' : '') . '>' . $icon . '</span>';
    $icon          = $args['link']['url'] ? '<a' . ($args['link']['target'] ? ' target="' . $args['link']['target'] . '"' : '') . ' href="' . $args['link']['url'] . '">' . $icon . '</a>' : $icon;
    $icon          = $args['icon_size'] ? '<span style="font-size:' . $args['icon_size'] . 'px;">' . $icon . '</span>' : $icon;
    $args['title'] = $args['link']['url'] ? '<a class="main-color" ' . ($args['link']['target'] ? ' target="' . $args['link']['target'] . '"' : '') . ' href="' . $args['link']['url'] . '">' . $args['title'] . '</a>' : $args['title'];

    $class = $args['class'] . ' ' . $args['type'];

    $title = $args['title'] ? '<div class="mt10 em12 text-ellipsis"> ' . $args['title'] . '</div>' : '';
    $title .= $args['desc'] ? '<div class="muted-color mt6"> ' . $args['desc'] . '</div>' : '';

    $html = '';
    $html .= '<div class="icon-card ' . $class . '">';
    $html .= $icon;
    $html .= $title ? '<div class="px12-m-s"> ' . $title . '</div>' : '';
    $html .= '</div>';
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * @description: 获取AJAX分页按钮的函数|仅显示下一页
 * @param {*} $count_all 列表总数量
 * @param {*} $page 当前页码
 * @param {*} $ice_perpage 每页加载数量
 * @param {*} $ajax_url
 * @param {*} $pag_class
 * @param {*} $next_class
 * @param {*} $nex 按钮内容
 * @param {*} $query_key = paged
 * @param {*} $scroll 是否自动滑动
 * @return {*}
 */
function zib_get_ajax_next_paginate($count_all, $page = 1, $ice_perpage = 10, $ajax_url = '', $pag_class = 'text-center theme-pagination ajax-pag', $next_class = 'next-page ajax-next', $nex = '', $query_key = 'paged', $scroll = null)
{
    $total_pages = ceil($count_all / $ice_perpage);
    $con         = '';
    if ($total_pages > $page) {
        $nex = $nex ? $nex : _pz("ajax_trigger", '加载更多');
        if (!$ajax_url) {
            $ajax_url = home_url(remove_query_arg($query_key));
        }
        $attr = '';
        if ($scroll === 'no') {
            $attr = ' no-scroll="true"';
        } else {
            $attr = $scroll ? ' scroll-selector="' . $scroll . '"' : '';
        }

        $href = esc_url(add_query_arg(array($query_key => $page + 1), $ajax_url));
        $con .= '<div class="' . $pag_class . '"><div' . $attr . ' class="' . $next_class . '">';
        $con .= '<a href="' . $href . '" paginate-all="' . $count_all . '" paginate-perpage="' . $ice_perpage . '">' . $nex . '</a>';
        $con .= '</div></div>';
    }
    return $con;
}

/**
 * @description: 获取AJAX数字分页按钮的函数|显示数字分页
 * @param {*} $count_all  列表总数量
 * @param {*} $paged  当前页码
 * @param {*} $ice_perpage  每页加载数量
 * @param {*} $ajax_url  链接
 * @param {*} $pag_class
 * @param {*} $next_class
 * @param {*} $query_key
 * @return {*}
 */
function zib_get_ajax_number_paginate($count_all, $paged = 1, $ice_perpage = 10, $ajax_url = '', $pag_class = 'ajax-pag', $next_class = 'next-page ajax-next', $query_key = 'paged')
{
    $args = array(
        'url_base'     => add_query_arg(array($query_key => '%#%'), $ajax_url), // http://example.com/all_posts.php%#% : %#% 替换为页码。
        'link_sprintf' => '<a class="' . $next_class . ' %s" ajax-replace="true" href="%s">%s</a>', // 1.class 2.link 3.内容
        'total'        => $count_all, //总计条数
        'current'      => $paged, //当前页码
        'page_size'    => $ice_perpage, //每页几条
        'class'        => 'pagenav ' . $pag_class,
    );

    return zib_get_paginate_links($args);
}

//标准数字分页按钮构建
function zib_get_paginate_links($args)
{

    $defaults = array(
        'url_base'     => '', // http://example.com/all_posts.php%#% : %#% 替换为页码。
        'link_sprintf' => '<a class="%s" href="%s">%s</a>', // 1.class 2.link 3.内容
        'total'        => 0, //总计条数
        'current'      => 1, //当前页码
        'page_size'    => 12, //每页几条
        'prev_text'    => '<i class="fa fa-angle-left em12"></i><span class="hide-sm ml6">上一页</span>', //上一页按钮文字
        'next_text'    => '<span class="hide-sm mr6">下一页</span><i class="fa fa-angle-right em12"></i>', //下一页按钮文字
        'array'        => false,
        'class'        => 'pagenav ajax-pag',
    );

    $args = wp_parse_args($args, $defaults);

    $current      = (int) $args['current'];
    $total        = (int) $args['total'];
    $total_pages  = ceil($total / $args['page_size']); //总计页面格式
    $link_base    = $args['url_base'];
    $link_sprintf = $args['link_sprintf'];

    $end_size = 1;
    $mid_size = 2;

    if ($total_pages < 2) {
        return;
    }

    $page_links = array();
    $dots       = false;

    //上一页
    if ($args['prev_text'] && $current && 1 < $current) {
        $link         = $link_base ? str_replace('%#%', $current - 1, $link_base) : 'javascript:void(0);';
        $page_links[] = sprintf($link_sprintf, 'prev page-numbers', esc_url($link), $args['prev_text']);
    }

    //循环数字
    for ($n = 1; $n <= $total_pages; $n++):
        if ($n == $current):
            $page_links[] = sprintf('<span class="page-numbers current">%s</span>', $n);
            $dots         = true;
        else:
            if ($n <= $end_size || ($current && $n >= $current - $mid_size && $n <= $current + $mid_size) || $n > $total_pages - $end_size):
                $link         = $link_base ? str_replace('%#%', $n, $link_base) : 'javascript:void(0);';
                $page_links[] = sprintf($link_sprintf, 'page-numbers', esc_url($link), $n);

                $dots = true;
            elseif ($dots):
                $page_links[] = '<span class="page-numbers dots">' . __('&hellip;') . '</span>';

                $dots = false;
            endif;
        endif;
    endfor;

    //下一页
    if ($args['next_text'] && $current && $current < $total_pages) {
        $link         = $link_base ? str_replace('%#%', $current + 1, $link_base) : 'javascript:void(0);';
        $page_links[] = sprintf($link_sprintf, 'next page-numbers', esc_url($link), $args['next_text']);
    }

    if ($args['array']) {
        return $page_links;
    }

    $html = '<div class="' . $args['class'] . '">';
    $html .= implode("", $page_links);
    $html .= '</div>';
    return $html;
}

function zib_get_remote_box($args)
{
    $defaults = array(
        'type'   => 'ias',
        'class'  => '',
        'loader' => '<i class="loading-spot"><i></i></i>', // 加载动画
        'url'    => admin_url('/admin-ajax.php'), // url
        'query'  => false, // add_query_arg
    );
    $args        = wp_parse_args($args, $defaults);
    $args['url'] = add_query_arg($args['query'], $args['url']);
    $attr        = ' remote-box="' . $args['url'] . '"';

    if ('ias' == $args['type']) {
        $attr .= ' lazyload-action="ias"';
        $args['class'] .= ' lazyload';
    }
    if ('load' == $args['type']) {
        $attr .= ' load-click';
    }

    $class = $args['class'] ? ' class="' . $args['class'] . '"' : '';
    return '<div' . $class . $attr . '>' . $args['loader'] . '</div>';
}

/**
 * @description: ajax方式输出ajaxpager内容
 * @param {*} $html
 * @param {*} $is_one  是否是一个独立内容，不需要分页
 * @return {*}
 */
function zib_ajax_send_ajaxpager($html, $is_one = false)
{

    if ($is_one) {
        $html = zib_get_ajax_ajaxpager_one_centent($html);
    }

    echo '<body style="display:none;"><main><div class="ajaxpager">' . $html . '</div></main></body>';
    exit;
}

/**
 * @description: ajax模态框通知
 * @param {*} $type success|info|warning|danger
 * @param {*} $msg
 * @return {*}
 */
function zib_ajax_notice_modal($type = 'warning', $msg = '')
{
    $type_class = array(
        'success' => 'blue',
        'info'    => 'green',
        'warning' => 'yellow',
        'danger'  => 'red',
    );
    $icon_class = array(
        'success' => '<i class="fa fa-check fa-2x" aria-hidden="true"></i>',
        'info'    => '<i class="fa fa-bell-o fa-2x" aria-hidden="true"></i>',
        'warning' => '<i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true"></i>',
        'danger'  => '<i class="fa fa-times-circle-o fa-2x" aria-hidden="true"></i>',
    );

    $class = isset($type_class[$type]) ? $type_class[$type] : 'yellow';
    $icon  = isset($icon_class[$type]) ? $icon_class[$type] : '<i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true"></i>';

    $header = zib_get_modal_colorful_header('jb-' . $class, $icon);

    $html = '';
    $html .= $header;
    $html .= '<div class="em12 text-center c-' . $class . '" style="padding: 30px 0;">' . $msg . '</div>';
    echo $html;
    exit;
}

function zib_get_ajax_ajaxpager_one_centent($html)
{
    $html = '<div class="ajax-item">' . $html . '</div>';
    $html .= '<div class="ajax-pag hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
    return $html;
}

/**
 * @description: AJAX空白内容
 * @param {*}
 * @return {*}
 */
function zib_get_ajax_null($text = '暂无内容', $margin = '60', $img = 'null.svg')
{
    $html = zib_get_null($text, $margin, $img, 'ajax-item ');
    $html .= '<div class="ajax-pag hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
    return $html;
}

/**
 * @description: 空白内容
 * @param {*}
 * @return {*}
 */
function zib_get_null($text = '暂无内容', $margin = '60', $img = 'null.svg', $class = '', $width = 280, $height = 0)
{
    $text = $text ? '<p style="margin-top:' . $margin . 'px;" class="em09 muted-3-color separator">' . $text . '</p>' : '';
    if ($height) {
        $style = $width ? 'max-width:' . $width . 'px;' : '';
        $style .= $height ? 'height:' . $height . 'px;' : '';
    } else {
        $style = $width ? 'width:' . $width . 'px;' : '';
    }
    $style .= 'opacity: .7;';
    $html = '<div class="text-center ' . $class . '" style="padding:' . $margin . 'px 0;"><img style="' . $style . '" src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/' . $img . '">' . $text . '</div>';
    return $html;
}

/**
 * @description: 前台构建一个自动AJAX加载的ajaxpager
 * @param {*} $args
 * @return {*}
 */
function zib_get_ias_ajaxpager($args)
{
    $defaults = array(
        'type'   => 'ias',
        'id'     => '',
        'class'  => '',
        'loader' => '<i class="loading-spot"><i></i></i>', // 加载动画
        'url'    => admin_url('/admin-ajax.php'), // url
        'query'  => false, // add_query_arg
    );

    $args        = wp_parse_args($args, $defaults);
    $args['url'] = add_query_arg($args['query'], $args['url']);

    $id    = $args['id'] ? ' id="' . $args['id'] . '"' : '';
    $class = $args['class'] ? ' ' . $args['class'] : '';
    $attr  = 'ias' == $args['type'] ? ' lazyload-action="ias"' : '';

    $html = '';
    $html .= '<div class="ajaxpager lazyload' . $class . '"' . $id . $attr . '>';
    $html .= '<span class="post_ajax_trigger hide"><a' . ('load' == $args['type'] ? ' load-click' : '') . ' ajax-href="' . esc_url($args['url']) . '" class="ajax_load ajax-next ajax-open"></a></span>';
    $html .= '<div class="post_ajax_loader">' . $args['loader'] . '</div>';
    $html .= '</div>';

    return $html;
}

/**
 * @description: 获取当前页码的统一接口
 * @param {*}
 * @return {*}
 */
function zib_get_the_paged()
{
    $paged = isset($_REQUEST['paged']) ? (int) $_REQUEST['paged'] : 0;
    if ($paged) {
        return $paged;
    }
    $paged = (int) get_query_var('paged');
    if ($paged) {
        return $paged;
    }
    $paged = (int) get_query_var('page');
    if ($paged) {
        return $paged;
    }
    return 1;
}

/**
 * @description: 链接删除分页页码
 * @param {*} $url
 * @return {*}
 */
function zib_url_del_paged($url)
{
    $url = remove_query_arg(array('paged'), $url);
    global $wp_rewrite;
    $url = preg_replace("/\/$wp_rewrite->pagination_base\/\d*/", "", $url);

    return $url;
}

/**
 * @description: 获取当前页面的链接函数
 * @param {*}
 * @return {*}
 */
function zib_get_current_url()
{
    $home_url = home_url();
    $home_url = preg_replace('/^(http|https)(:\/\/)(?)([^\/]+).*$/im', '$1$2$3', $home_url);

    return $home_url . add_query_arg(null, false);
}

/**
 * @description: 后台页面的页面显示
 * @param {*} $total_count
 * @param {*} $number_per_page
 * @return {*}
 */
function zibpay_admin_pagenavi($total_count, $number_per_page = 15)
{
    $current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;

    if (isset($_GET['paged'])) {
        unset($_GET['paged']);
    }

    $total_pages = ceil($total_count / $number_per_page);

    $first_page_url = add_query_arg('paged', 1);
    $last_page_url  = add_query_arg('paged', $total_pages);

    if ($current_page > 1 && $current_page < $total_pages) {
        $prev_page     = $current_page - 1;
        $prev_page_url = add_query_arg('paged', $prev_page);

        $next_page     = $current_page + 1;
        $next_page_url = add_query_arg('paged', $next_page);
    } elseif (1 == $current_page) {
        $prev_page_url  = '#';
        $first_page_url = '#';
        if ($total_pages > 1) {
            $next_page     = $current_page + 1;
            $next_page_url = add_query_arg('paged', $next_page);
        } else {
            $next_page_url = '#';
            $last_page_url = '#';
        }
    } elseif ($current_page == $total_pages) {
        $prev_page     = $current_page - 1;
        $prev_page_url = add_query_arg('paged', $prev_page);
        $next_page_url = '#';
        $last_page_url = '#';
    }
    ?>
    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <span class="displaying-num">每页 <?php echo $number_per_page; ?> 共 <?php echo $total_count; ?></span>
            <span class="pagination-links">
                <a class="first-page button <?php if (1 == $current_page) {
        echo 'disabled';
    }
    ?>" title="前往第一页" href="<?php echo $first_page_url; ?>">«</a>
                <a class="prev-page button <?php if (1 == $current_page) {
        echo 'disabled';
    }
    ?>" title="前往上一页" href="<?php echo $prev_page_url; ?>">‹</a>
                <span class="paging-input">第 <?php echo $current_page; ?> 页，共 <span class="total-pages"><?php echo $total_pages; ?></span> 页</span>
                <a class="next-page button <?php if ($current_page == $total_pages) {
        echo 'disabled';
    }
    ?>" title="前往下一页" href="<?php echo $next_page_url; ?>">›</a>
                <a class="last-page button <?php if ($current_page == $total_pages) {
        echo 'disabled';
    }
    ?>" title="前往最后一页" href="<?php echo $last_page_url; ?>">»</a>
            </span>
        </div>
        <br class="clear">
    </div>
<?php
}

/**
 * @description: 页面主要TAB统一接口|一个页面只能有一个这样的调用
 * @param {*} $type
 * @param {*} $tabs_options
 * 结构示例：$opt_shili = array(
'key' => array(
'title' => '栏目1',
'loader' => '',
),
'key_2' => array(
'title' => '栏目2',
'loader' => '',
),
);˝
 * @param {*} $id_prefix
 * @param {*} $is_swiper
 * @return {*}
 */
function zib_get_main_tab_nav($type = 'nav', $tabs_options, $id_prefix = 'home', $is_mobile_swiper = true, $active_key_str = 'tab', $route = false)
{
    //开始
    if (!$tabs_options || !is_array($tabs_options)) {
        return '';
    }

    //$active_key = !empty($_GET[$active_key_str]) ? $_GET[$active_key_str] : '';
    $active_key = get_query_var($active_key_str);
    $active_key = $active_key ? $active_key : (!empty($_GET[$active_key_str]) ? $_GET[$active_key_str] : '');

    if (!isset($tabs_options[$active_key])) {
        $active_key = '';
    }

    $is_mobile = wp_is_mobile();
    $is_swiper = $is_mobile_swiper ? wp_is_mobile() : false;

    $placeholder_default = '<div class="mb20"><div class="text-center muted-2-color mt20"><i class="loading mr10"></i>加载中...</div></div>';

    $ajax_url = zib_url_del_paged(zib_get_current_url());

    $html         = '';
    $i            = 1;
    $active_index = 1;
    foreach ($tabs_options as $key => $opt) {
        if (1 === $i && !$active_key) {
            $active_key = $key;
        }

        $id = $id_prefix . '-tab-' . $key;

        $query_arg[$active_key_str] = $key;
        $ajax_href                  = esc_url(add_query_arg($query_arg, $ajax_url));
        //开始构建
        if ('nav' == $type) {
            $is_active = $key == $active_key ? ' class="active"' : '';

            if ($is_mobile && $is_active && 1 !== $i) {
                $is_active = ' class="active lazyload" lazyload-action="ias"';
            }

            //nav按钮
            $name = $opt['title'] ? $opt['title'] : '栏目';
            $attr = !empty($opt['nav_attr']) ? ' ' . $opt['nav_attr'] : '';
            $attr .= ($route || !empty($opt['route'])) ? ' data-route="' . $ajax_href . '"' : '';

            $html .= $is_swiper ? '<li class="swiper-slide"><a' . $attr . ' href="javascript:;" tab-id="' . $id . '">' . $name . '</a></li>' : '<li' . $is_active . '><a' . $attr . ' data-toggle="tab" data-ajax href="#' . $id . '">' . $name . '</a></li>';
        } else {
            $is_active = $key == $active_key ? ' in active' : '';

            $loader  = isset($opt['loader']) ? $opt['loader'] : $placeholder_default;
            $c_class = $is_swiper ? 'swiper-slide' : 'tab-pane fade' . $is_active;
            $c_class .= !empty($opt['content_class']) ? ' ' . $opt['content_class'] : '';

            $html .= '<div class="ajaxpager ' . $c_class . '" id="' . $id . '">';
            $_key = is_string($key) ? $key : 'other';

            if (!$is_active) {
                //只要不是选中页面，则都显示为AJAX
                $html .= '<span class="post_ajax_trigger hide"><a href="' . $ajax_href . '" class="ajax_load ajax-next ajax-open"></a></span>';
                $html .= '<div class="post_ajax_loader">' . $loader . '</div>';
            } else {
                //第一页则直接显示内容
                $active_index = $i;
                $opt['index'] = $i;
                $html .= apply_filters('main_' . $id_prefix . '_tab_content_' . $_key, '', $opt);
            }
            $html .= '</div>';
        }
        $i++;
    }

    if ('nav' == $type) {
        $html = apply_filters('main_' . $id_prefix . '_tab_nav_content', $html);
        $html = $is_swiper ? '<div class="swiper-tab-nav swiper-scroll tab-nav-theme" swiper-tab-nav="tab-' . $id_prefix . '" scroll-nogroup="true"><div class="swiper-wrapper">' . $html . '</div></div>' : '<ul class="list-inline scroll-x mini-scrollbar tab-nav-theme">' . $html . '</ul>';
    } else {
        $html = apply_filters('main_' . $id_prefix . '_tab_content_content', $html);
        $html = $is_swiper ? '<div class="swiper-tab" swiper-tab="tab-' . $id_prefix . '" active-index="' . ($active_index - 1) . '" active-key="' . $active_key . '"><div class="swiper-wrapper">' . $html . '</div></div>' : '<div class="tab-content main-tab-content">' . $html . '</div>';
    }

    return $html;
}

function zib_get_cfs_icon($val, $class = '')
{
    if (!$val) {
        return;
    }

    $class = $class ? ' ' . $class : '';
    if (stristr($val, 'zibsvg-')) {
        return zib_get_svg(str_replace('zibsvg-', '', $val), null, 'icon' . $class);
    }

    return '<i class="' . $val . $class . '" aria-hidden="true"></i>';
}

//获取ID
function zib_get_id_by_post_or_term($obj, $type = '')
{
    if (isset($obj->term_id)) {
        return $obj->term_id;
    }
    if (isset($obj->ID)) {
        return $obj->ID;
    }

    if ('post' === $type) {
        return zib_get_id_by_post_or_term(get_post($obj));
    }
    if ('term' === $type) {
        return zib_get_id_by_post_or_term(get_term($obj));
    }
    return false;
}

//加载一个错误页面
function zib_die_page($message = '', $args = array())
{
    header("Content-Type: text/html; charset=charset=UTF-8");

    $img = '<img src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/404.svg">';
    if (isset($args['img'])) {
        $img = $args['img'] ? '<img src="' . $args['img'] . '">' : '';
    }

    if (isset($args['title'])) {
        $GLOBALS['new_title'] = $args['title'];
    }

    get_header();
    ?>
        <main class="container flex ac">
            <div class="f404 flex1">
                <?php echo $img . '<div class="f404-msg mt20">' . $message . '</div>'; ?>
            </div>
        </main>
    <?php
get_footer();
    exit;
}