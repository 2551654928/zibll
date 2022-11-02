<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-10-17 19:56:54
 * @LastEditTime: 2022-10-08 23:58:46
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|搜索功能相关函数
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//搜索页面的主要内容函数
function zib_search_content($s, $type = 'post', $cat = false, $user = false)
{
    $type = $type ? $type : false;
    $cat  = $cat ? $cat : false;
    $user = $user ? $user : false;

    $con = '';
    if ($s) {
        $type_name_array = zib_get_search_types();
        $type            = isset($type_name_array[$type]) ? $type : 'post';
        $nav_lists       = '';

        if ($user) {
            unset($type_name_array['user']);
        }

        foreach ($type_name_array as $k => $v) {
            $class = $k == $type ? ' class="active"' : '';
            $href  = zib_url_del_paged(add_query_arg(array('type' => $k, 'cat' => $cat, 'user' => $user), zib_get_current_url()));

            $nav_lists .= '<li' . $class . '><a ajax-replace="true" class="ajax-next" href="' . $href . '">' . $v . '</a></li>';
        }
        $nav = '<div class="author-tab"><ul win-ajax-replace="filter" class="em12 list-inline scroll-x mini-scrollbar tab-nav-theme" style="margin:0 -8px 10px;">' . $nav_lists . '</ul></div>';

        $tab_content = apply_filters('search_content_' . $type, '', $s, $cat, $user);

        if ($tab_content) {
            /**有搜索结果再保存搜索关键词 */
            if ('user' != $type) {
                zib_update_search_keywords($s . ($type ? '&type=' . $type : ''));
            }
        } else {
            $tab_content = zib_get_ajax_null('未找到相关结果', '60', 'null-search.svg', '', '300');
        }

        $con .= $nav;
        if ('user' != $type) {
            $con .= zib_bbs_get_search_desc($s, $type, $cat, $user);
        }
        $con .= $tab_content;
    } else {
        $con .= zib_get_ajax_null('请输入搜索关键词', '60', 'null-search.svg', '', '300');
    }

    echo '<div class="zib-widget ajaxpager search-content type-' . $type . '">' . $con . '</div>';
}

//搜索用户的结果
function zib_get_search_content_user($html = '', $s, $cat, $user)
{
    if (!$s) {
        return '';
    }

    $user_paged  = !empty($_REQUEST['user_paged']) ? (int) $_REQUEST['user_paged'] : 1;
    $ice_perpage = 12;

    $users_args = array(
        'search'         => '*' . $s . '*',
        'search_columns' => array('user_email', 'user_nicename', 'display_name'),
        'count_total'    => true,
        'number'         => $ice_perpage,
        'paged'          => $user_paged,
    );

    $users_args['count_total'] = true;
    $user_search               = new WP_User_Query($users_args);
    $users                     = $user_search->get_results();

    $text = '';
    $text = '搜索[<a href="' . home_url('/?s=' . $s) . '"><b class="search-key focus-color">' . $s . '</b></a>]，共找到<b class="focus-color">' . $user_search->total_users . '</b>个用户';
    $text = '<div win-ajax-replace="search-key"><div class="badg">' . $text . '</div></div>';

    $lists = '';
    if ($users) {
        foreach ($users as $user) {
            $lists .= zib_author_card($user->ID, 'ajax-item');
        }
    }

    if ($lists) {
        /**有搜索结果再保存搜索关键词 */
        zib_update_search_keywords($s . '&type=user');

        $paginate = zib_get_ajax_next_paginate($user_search->total_users, $user_paged, $ice_perpage, home_url(remove_query_arg(array('trem', 'user'))), 'text-center theme-pagination ajax-pag', 'next-page ajax-next', '', 'user_paged');

        if ($paginate) {
            $lists .= $paginate;
            $lists .= '<div class="post_ajax_loader" style="display: none;"><div class="author-minicard radius8 flex ac" style="display: inline-flex;"><div class="avatar-img mr10"><div class="avatar placeholder"></div></div><div class="flex1"><div class="placeholder k1 mb6"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>
            <div class="author-minicard radius8 flex ac" style="display: inline-flex;"><div class="avatar-img mr10"><div class="avatar placeholder"></div></div><div class="flex1"><div class="placeholder k1 mb6"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>
            <div class="author-minicard radius8 flex ac" style="display: inline-flex;"><div class="avatar-img mr10"><div class="avatar placeholder"></div></div><div class="flex1"><div class="placeholder k1 mb6"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>
            <div class="author-minicard radius8 flex ac" style="display: inline-flex;"><div class="avatar-img mr10"><div class="avatar placeholder"></div></div><div class="flex1"><div class="placeholder k1 mb6"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>
            <div class="author-minicard radius8 flex ac" style="display: inline-flex;"><div class="avatar-img mr10"><div class="avatar placeholder"></div></div><div class="flex1"><div class="placeholder k1 mb6"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div><div class="author-minicard radius8 flex ac" style="display: inline-flex;"><div class="avatar-img mr10"><div class="avatar placeholder"></div></div><div class="flex1"><div class="placeholder k1 mb6"></div><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div></div>';
        }
    } else {
        $lists = zib_get_ajax_null('没有找到相关用户', '75', 'null-user.svg');
    }

    return $text . $lists;
}
add_filter('search_content_user', 'zib_get_search_content_user', 10, 4);

//搜索文章的结果
function zib_get_search_content_post($html = '', $s, $cat, $user)
{
    global $wp_query;
    if (!have_posts() || !$s) {
        return '';
    }

    $args = array(
        'no_margin' => true,
        'is_card'   => false,
    );

    $html = '';
    $html .= zib_posts_list($args, false, false);
    $html .= zib_paging(false, false);

    return $html;
}
add_filter('search_content_post', 'zib_get_search_content_post', 10, 4);

//搜索内容的文字提示
function zib_bbs_get_search_desc($s, $type = 'post', $cat = 0, $user = 0)
{
    global $wp_query;

    $text      = '';
    $user_text = '';
    if ($user) {
        $user = get_userdata($user);
        if (!empty($user->display_name)) {
            $user_text = '用户[<a class="focus-color" href="' . zib_get_user_home_url($user->ID) . '"><b>' . esc_attr($user->display_name) . '</b></a>]';
            $text .= $user_text;
        }
    }

    if ($cat) {
        $in_cat_name = '';
        $in_cat_type = '';
        $in_cat_link = '';
        if (stristr($cat, 'plate_')) {
            $get_post = get_post(str_replace('plate_', '', $cat));
            if (!empty($get_post->post_title)) {
                $in_cat_name = $get_post->post_title;
                $in_cat_type = '版块';
                $in_cat_link = get_permalink($get_post->ID);
            }
        } else {
            $get_term = get_term($cat);
            if (!empty($get_term->name)) {
                $in_cat_name = $get_term->name;
                $in_cat_type = zib_bbs_get_taxonomy_name($get_term->taxonomy);
                $in_cat_link = get_term_link($get_term->term_id, $get_term->taxonomy);
            }
        }
        if ($in_cat_name) {
            $text .= $user_text ? '、' : '';
            $text .= $in_cat_type . '[<a class="focus-color" href="' . $in_cat_link . '"><b>' . $in_cat_name . '</b></a>]';
        }
    }
    $text = $text ? '在' . $text . '中' : '';

    $text .= '搜索[<a href="' . home_url('/?s=' . $s) . '"><b class="search-key focus-color">' . $s . '</b></a>]，共找到<b class="focus-color">' . $wp_query->found_posts . '</b>个' . zib_get_search_types()[$type];
    $text = '<div win-ajax-replace="search-key"><div class="badg">' . $text . '</div></div>';
    return $text;
}

//获取搜索按钮的统一函数
function zib_get_search_link($args)
{
    $defaults = array(
        'class'       => '',
        'con'         => zib_get_svg('search'),
        'trem'        => '',
        'trem_name'   => '',
        'type'        => '',
        'user'        => '',
        'placeholder' => '',
    );
    $args = wp_parse_args($args, $defaults);

    $args['class'] = $args['class'] ? ' ' . $args['class'] : '';

    $attr = '';
    $attr .= ($args['user']) ? ' search-user="' . $args['user'] . '"' : '';
    $attr .= ($args['type']) ? ' search-type="' . $args['type'] . '"' : '';
    $attr .= ($args['trem']) ? ' search-trem="' . $args['trem'] . '"' : '';
    $attr .= ($args['trem_name']) ? ' trem-name="' . $args['trem_name'] . '"' : '';
    $attr .= ($args['placeholder']) ? ' search-placeholder="' . $args['placeholder'] . '"' : '';

    return '<a' . $attr . ' class="main-search-btn' . $args['class'] . '" href="javascript:;">' . $args['con'] . '</a>';
}

//获取文档模式的分类搜索框
function zib_single_cat_search($cat_id)
{
    $cat_obj = get_category($cat_id);
    ?>
    <div class="theme-box zib-widget dosc-search">
        <div class="title-h-left"><b>搜索<?php echo $cat_obj->cat_name; ?></b></div>

        <?php
$more_cats = array();
    $more_cats = get_term_children($cat_id, 'category');
    array_push($more_cats, $cat_id);
    $args = array(
        'class'          => '',
        'show_keywords'  => false,
        'show_input_cat' => true,
        'show_more_cat'  => true,
        'placeholder'    => '搜索' . $cat_obj->cat_name,
        'in_cat'         => $cat_id,
        'more_cats'      => $more_cats,
    );
    zib_get_search_box($args, true);
    ?>
    </div>
<?php
}

function zib_get_search_keywords_text($s)
{
    $s     = str_replace('&amp;type', '&type', $s);
    $index = strpos($s, '&type=');
    $t     = substr($s, 0, $index);
    return $index ? substr($s, 0, $index) : $s;
}

//保存热门关键词
function zib_update_search_keywords($s)
{
    $s      = strip_tags($s);
    $s_text = zib_get_search_keywords_text($s);

    if (_pz('search_popular_key', true) && zib_new_strlen($s_text) >= 2 && zib_new_strlen($s_text) <= 8) {
        $keywords = get_option('search_keywords');
        if (!is_array($keywords)) {
            $keywords = array();
        }

        $max_num      = (int) _pz('search_popular_key_num', 20) + 10;
        $keywords     = array_slice($keywords, 0, $max_num, true);
        $keywords[$s] = !empty($keywords[$s]) ? (int) $keywords[$s] + 1 : 1;
        arsort($keywords);
        update_option('search_keywords', $keywords);
    }
}

//保存搜索历史
function zib_save_history_search($s)
{
    $s      = strip_tags($s);
    $s_text = zib_get_search_keywords_text($s);

    if (zib_new_strlen($s_text) >= 0 && zib_new_strlen($s_text) < 90) {
        $old_k = !empty($_COOKIE["history_search"]) ? json_decode(stripslashes($_COOKIE["history_search"])) : array();
        if (!is_array($old_k)) {
            $old_k = array();
        }

        foreach ($old_k as $k => $v) {
            if (zib_get_search_keywords_text($v) == zib_get_search_keywords_text($s)) {
                unset($old_k[$k]);
            }
        }

        array_unshift($old_k, $s);
        setcookie('history_search', json_encode($old_k), time() + 3600 * 24 * 30, '/', '', false);
    }
}

//获取热门关键词
function zib_get_search_keywords()
{
    //置顶关键词
    $sticky   = _pz('search_popular_sticky');
    $sticky   = preg_split("/,|，|\s|\n/", $sticky);
    $keywords = get_option('search_keywords');
    if (!$keywords || !is_array($keywords)) {
        $keywords = array();
    }

    $sticky_a = array();
    foreach ($sticky as $key) {
        if (zib_new_strlen($key) < 2) {
            continue;
        }

        if (isset($keywords[$key])) {
            unset($keywords[$key]);
        }
        $sticky_a[$key] = 999999;
    }

    $keywords = array_merge($sticky_a, $keywords);
    return $keywords;
}

//获取搜索历史关键词
function zib_get_search_history_keywords()
{
    $old_k = !empty($_COOKIE["history_search"]) ? json_decode(stripslashes($_COOKIE["history_search"])) : '';
    if (!is_array($old_k)) {
        return false;
    }

    return $old_k;
}

//获取搜索历史关键词
function zib_get_search_keywords_but($keywords = array(), $type = 'popular')
{
    $k_i          = 1;
    $keyword_link = '';
    //echo var_dump($keywords);
    if (!is_array($keywords)) {
        return;
    }

    $k_text_array = array();
    foreach ($keywords as $key => $keyword) {
        $key = 'history' === $type ? $keyword : $key;
        if (zib_new_strlen($key) < 2) {
            continue;
        }
        if ('popular' === $type && count($k_text_array) >= (int) _pz('search_popular_key_num', 20)) {
            continue;
        }

        $s_text = zib_get_search_keywords_text($key);
        if (in_array($s_text, $k_text_array)) {
            continue;
        }
        $k_text_array[] = $s_text;

        $keyword_link .= '<a class="search_keywords muted-2-color but em09 mr6 mb6" href="' . esc_url(home_url('/')) . '?s=' . esc_attr($key) . '">' . esc_attr($s_text) . '</a>';
    }
    return $keyword_link;
}

function zib_get_search_types()
{
    $types = array(
        'post' => '文章',
        'user' => '用户',
    );
    return apply_filters('search_types', $types);
}

//挂钩显示一个AJAX加载的核心搜索框
add_action('wp_footer', function () {
    if (is_search() || (is_404() && _pz('404_search_s', true))) {
        return;
    }

    $args = array(
        'type'   => 'load',
        'class'  => '',
        'loader' => '<div class="search-input"><p><i class="placeholder s1 mr6"></i><i class="placeholder s1 mr6"></i><i class="placeholder s1 mr6"></i></p><p class="placeholder k2"></p>
        <p class="placeholder t1"></p><p><i class="placeholder s1 mr6"></i><i class="placeholder s1 mr6"></i><i class="placeholder s1 mr6"></i><i class="placeholder s1 mr6"></i></p><p class="placeholder k1"></p><p class="placeholder t1"></p><p></p>
        <p class="placeholder k1" style="height: 80px;"></p>
        </div>', // 加载动画
        'query'  => array(
            'action' => 'search_box',
        ),
    );

    echo '<div mini-touch="nav_search" touch-direction="top" class="main-search fixed-body main-bg box-body navbar-search nopw-sm">';
    echo '<div class="container">';
    echo '<div class="mb20"><button class="close" data-toggle-class data-target=".navbar-search" >' . zib_get_svg('close', null, 'ic-close') . '</button></div>';
    echo zib_get_remote_box($args);
    echo '</div>';
    echo '</div>';
});

function zib_get_main_search($args = array(), $echo = false)
{
    $search_cat  = _pz('search_cat', true);
    $search_type = _pz('search_type', true);

    $defaults = array(
        'class'          => '',
        'show_keywords'  => _pz('search_popular_key', true),
        'show_history'   => _pz('search_history', true),
        'show_posts'     => _pz('search_posts', true),
        'show_input_cat' => $search_cat,
        'more_cats'      => $search_cat ? _pz('search_more_cat_obj') : false,
        'in_cat'         => $search_cat ? _pz('search_cat_in') : false,
        'show_type'      => $search_type,
        'in_type'        => $search_type ? _pz('search_type_in') : '',
        'in_user'        => '',
    );
    $args = wp_parse_args($args, $defaults);

    return zib_get_search_box($args, $echo);
}

/**
 * @description: 获取搜索卡片的主要函数
 * @param {*} $args
 * @return {*}
 */
function zib_get_search_box($args = array(), $echo = false)
{
    $defaults = array(
        's'              => '',
        'class'          => '',
        'show_form'      => true,
        'show_keywords'  => true,
        'show_history'   => true,
        'keywords_title' => _pz('search_popular_title', '热门搜索'),
        'placeholder'    => _pz('search_placeholder', '开启精彩搜索'),
        'show_input_cat' => true,
        'show_posts'     => false,
        'show_type'      => false,
        'in_cat'         => '',
        'in_type'        => 'post',
        'in_user'        => '',
        'more_cats'      => array(),
    );
    $args                = wp_parse_args($args, $defaults);
    $args['placeholder'] = esc_attr($args['placeholder']);
    $form_html           = '';
    if ($args['show_form']) {
        //分类搜索
        $cat_html = '';
        if ($args['show_input_cat']) {
            $all_cat     = zib_get_search_cat($args['more_cats'], 'text-ellipsis');
            $input_cat   = '';
            $in_cat_name = '';
            if ($args['in_cat']) {
                if (stristr($args['in_cat'], 'plate_')) {
                    $get_post = get_post(str_replace('plate_', '', $args['in_cat']));
                    if (!empty($get_post->post_title)) {
                        $in_cat_name = $get_post->post_title;
                    }
                } else {
                    $get_term = get_term($args['in_cat']);
                    if (!empty($get_term->name)) {
                        $in_cat_name = $get_term->name;
                    }
                }
            }
            if ($in_cat_name || $all_cat) {
                $input_cat_name = $in_cat_name ? zib_str_cut($in_cat_name, 0, 5) : '请选择';
                $input_cat_name = '<span name="trem">' . $input_cat_name . '</span>';
                $input_cat_name .= $all_cat ? '<i class="fa ml6 fa-sort opacity5" aria-hidden="true"></i>' : '';
                $input_cat .= $all_cat ? '<a href="javascript:;" class="padding-h10" data-toggle="dropdown">' . $input_cat_name . '</a>' : $input_cat_name;
            }
            if ($all_cat) {
                $input_cat .= $all_cat;
                $input_cat = '<div class="dropdown">' . $input_cat . '</div>';
            }
            if ($input_cat) {
                $cat_html = '<div class="option-dropdown splitters-this-r search-drop">';
                $cat_html .= $input_cat;
                $cat_html .= '</div>';
            }
        }

        //类型
        $type_html = '';
        if ($args['show_type']) {
            $type_name_array = zib_get_search_types();
            $args['in_type'] = $args['in_type'] ? $args['in_type'] : 'post';
            $in_type_name    = $type_name_array[$args['in_type']];
            if ($cat_html) {
                $type_items = '';
                foreach ($type_name_array as $k => $v) {
                    $_class = $args['in_type'] == $k ? ' active' : '';
                    $type_items .= '<a class="but' . $_class . '" data-for="type" data-value="' . $k . '" href="javascript:;">' . $v . '</a>';
                    //  $type_items .= '<a href="javascript:;" class="text-ellipsis" data-for="type" data-value="' . $k . '">' . $v . '</a>';
                }

                $type_html = '<div class="flex jc mb10">';
                $type_html .= '<div class="but-average radius">';
                $type_html .= $type_items;
                $type_html .= '</div>';
                $type_html .= '</div>';
            } else {
                $type_items = '';
                foreach ($type_name_array as $k => $v) {
                    $type_items .= '<li><a href="javascript:;" class="text-ellipsis" data-for="type" data-value="' . $k . '">' . $v . '</a></li>';
                }
                $type_html = '<div class="option-dropdown splitters-this-r search-drop"><div class="dropdown">';
                $type_html .= '<a href="javascript:;" class="padding-h10" data-toggle="dropdown"><span name="type">' . $in_type_name . '</span><i class="fa ml6 fa-sort opacity5" aria-hidden="true"></i></a>';
                $type_html .= '<ul class="dropdown-menu">' . $type_items . '</ul>';
                $type_html .= '</div></div>';
            }
        }
        $s          = esc_attr(strip_tags($args['s']));
        $input_html = '<div class="search-input-text">
                <input type="text" name="s" class="line-form-input" tabindex="1" value="' . $s . '"><i class="line-form-line"></i>
                <div class="scale-placeholder' . ($s ? ' is-focus' : '') . '" default="' . $args['placeholder'] . '">' . $args['placeholder'] . '</div>
                <div class="abs-right muted-color"><button type="submit" tabindex="2" class="null">' . zib_get_svg('search') . '</button>
                </div>
            </div>';

        if ($args['in_type'] || $type_html) {
            $input_html .= '<input type="hidden" name="type" value="' . $args['in_type'] . '">';
        }

        if ($args['in_cat'] || $cat_html) {
            $input_html .= '<input type="hidden" name="trem" value="' . $args['in_cat'] . '">';
        }

        if ($args['in_user']) {
            $input_html .= '<input type="hidden" name="user" value="' . $args['in_user'] . '">';
        }
        if (!$cat_html) {
            $cat_html  = $type_html;
            $type_html = '';
        }
        $form_html = '<form method="get" class="padding-10 search-form" action="' . esc_url(home_url('/')) . '">' . $type_html . '<div class="line-form">' . $cat_html . $input_html . '</div></form>';
    }

    //关键词
    $keywords_html = '';
    if ($args['show_keywords']) {
        $keywords     = zib_get_search_keywords();
        $keyword_link = zib_get_search_keywords_but($keywords);
        if ($keyword_link) {
            //如果没有关键词，则不显示
            $keywords_html = '<div class="search-keywords">
                                <p class="muted-color">' . $args['keywords_title'] . '</p>
                                <div>' . $keyword_link . '</div>
                            </div>';
        }
    }

    //历史关键词
    $history_html = '';
    if ($args['show_history']) {
        $keywords     = zib_get_search_history_keywords();
        $keyword_link = zib_get_search_keywords_but($keywords, 'history');
        if ($keyword_link) {
            //如果没有关键词，则不显示
            $history_html = '<div class="search-keywords history-search">
                                <p class="muted-color"><span>历史搜索</span><a class="pull-right trash-history-search muted-3-color" href="javascript:;"><i class="fa fa-trash-o em12" aria-hidden="true"></i></a></p>
                                <div>' . $keyword_link . '</div>
                            </div>';
        }
    }

    //热门文章
    $posts_html = '';
    if ($args['show_posts']) {
        $posts_html = '<div class="padding-10 relates relates-thumb">
        <p class="muted-color">热门文章</p>
        <div class="swiper-container swiper-scroll">
            <div class="swiper-wrapper">
                ' . zib_get_search_posts() . '
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>';
    }
    $class = $args['class'] ? ' ' . $args['class'] : '';
    if ($echo) {
        echo '<div class="search-input' . $class . '">' . $form_html . $keywords_html . $history_html . $posts_html . '</div>';
    } else {
        return '<div class="search-input">' . $form_html . $keywords_html . $history_html . $posts_html . '</div>';
    }
}

//搜索框热门文章
function zib_get_search_posts($count = 6, $orderby = 'views', $show_img = true)
{
    $args = array(
        'showposts'           => $count,
        'ignore_sticky_posts' => 1,
    );

    if ('views' !== $orderby) {
        $args['orderby'] = $orderby;
    } else {
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'views';
    }

    $new_query = new WP_Query($args);
    $lists     = '';
    while ($new_query->have_posts()) {
        $new_query->the_post();
        $title = get_the_title() . get_the_subtitle(false);
        if ($show_img) {
            //$author = get_the_author();
            $time_ago = zib_get_time_ago(get_the_time('U'));
            $info     = '<item>' . $time_ago . '</item><item class="pull-right">' . zib_get_svg('view') . ' ' . get_post_view_count($before = '', $after = '') . '</item>';
            $img      = zib_post_thumbnail('', 'fit-cover', true);
            $img      = $img ? $img : zib_get_spare_thumb();
            $lists .= '<div class="swiper-slide em09 mr10" style="width:160px;">';
            $card = array(
                'type'         => 'style-3',
                'class'        => '',
                'img'          => $img,
                'alt'          => $title,
                'link'         => array(
                    'url'    => get_permalink(),
                    'target' => '',
                ),
                'text1'        => $title,
                'text2'        => zib_str_cut($title, 0, 29),
                'text3'        => $info,
                'lazy'         => true,
                'height_scale' => 70,
            );
            $lists .= zib_graphic_card($card, false);
            $lists .= '</div>';
        } else {
            $lists .= '<li><a class="icon-circle text-ellipsis" href="' . get_permalink() . '">' . get_the_title() . get_the_subtitle() . '</a></li>';
        }
    }
    ;

    return $lists;
    wp_reset_query();
    wp_reset_postdata();
}

/**
 * 搜索卡片
 */
function zib_get_search_cat($cat_ids = array(), $link_class = '')
{
    if (!$cat_ids) {
        return false;
    }

    $cats = get_terms(array(
        'include' => $cat_ids,
        'orderby' => 'include',
    ));

    if (empty($cats[0])) {
        return false;
    }

    $links = '';
    $links .= '<li data-for="trem" data-value="null"><a href="javascript:;" class="' . $link_class . '">请选择</a></li>';
    foreach ($cats as $cat) {
        $links .= '<li data-for="trem" data-value="' . $cat->term_id . '"><a href="javascript:;" class="' . $link_class . '">' . zib_str_cut($cat->name, 0, 8) . '</a></li>';
    }
    return $links ? '<ul class="dropdown-menu cat-drop">' . $links . '</ul>' : false;
}

add_filter('pre_get_posts', 'zib_main_search_query', 99);
function zib_main_search_query($query)
{
    if ($query->is_search() && $query->is_main_query()) {
        $cat  = !empty($_REQUEST['trem']) ? (int) $_REQUEST['trem'] : '';
        $type = !empty($_REQUEST['type']) ? trim($_REQUEST['type']) : 'post';
        $user = !empty($_REQUEST['user']) ? trim($_REQUEST['user']) : '';

        if ('post' == $type) {
            if (_pz('search_no_page')) {
                $query->set('post_type', 'post');
            }
        }
        if ($cat) {
            $get_term = get_term($cat);
            if (!empty($get_term->name)) {
                $tax_query = array(array(
                    'taxonomy' => $get_term->taxonomy,
                    'field'    => 'id',
                    'terms'    => $get_term->term_id,
                ));
                $query->set('tax_query', $tax_query);
            }
        }
        if ($user) {
            $query->set('author', $user);
        }
    }
}

/**
 * @description: 搜索仅匹配标题
 * @param {*} $search
 * @param {*} $wp_query
 * @return {*}
 */
function zib_search_only_title($search, $wp_query)
{
    if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
        global $wpdb;
        $q      = $wp_query->query_vars;
        $n      = !empty($q['exact']) ? '' : '%';
        $search = array();
        foreach ((array) $q['search_terms'] as $term) {
            $search[] = $wpdb->prepare("$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like($term) . $n);
        }

        if (!is_user_logged_in()) {
            $search[] = "$wpdb->posts.post_password = ''";
        }

        $search = ' AND ' . implode(' AND ', $search);
    }
    return $search;
}
if (_pz('search_only_title', false)) {
    add_filter('posts_search', 'zib_search_only_title', 90, 2);
}
