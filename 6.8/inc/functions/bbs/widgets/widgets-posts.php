<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-11-09 13:38:06
 * @LastEditTime: 2022-09-30 19:21:54
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|小工具模块函数
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//主要的帖子输出列表
Zib_CFSwidget::create('zib_bbs_widget_ui_posts_lists', array(
    'title'       => '[' . $zib_bbs->forum_name . ']' . $zib_bbs->posts_name . '列表',
    'zib_title'   => true,
    'zib_affix'   => true,
    'zib_show'    => true,
    'description' => '显示' . $zib_bbs->posts_name . '列表的主要模块，根据多种筛选及排序后显示' . $zib_bbs->posts_name . '列表，不同筛选、排序方式可实现多种效果',
    'fields'      => array(
        array(
            'label'   => '仅显示当前' . $zib_bbs->plate_name . '的' . $zib_bbs->posts_name,
            'id'      => 'current_plate',
            'desc'    => '当此模块放置在' . $zib_bbs->plate_name . '页面的时候，开启此功能后，则按照当前' . $zib_bbs->plate_name . '进行筛选。可实现本版热门、本版精华等效果<div style="color: #ff6c6c;">开启此功能后，该模块只会在' . $zib_bbs->plate_name . '和' . $zib_bbs->posts_name . '页面显示</div>',
            'type'    => 'switcher',
            'default' => false,
        ),
        array(
            'dependency'  => array('current_plate', '==', '', '', 'visible'),
            'id'          => 'include_plate',
            'title'       => __('包含' . $zib_bbs->plate_name, 'zib_language'),
            'desc'        => '仅显示所选' . $zib_bbs->plate_name . '的帖子，支持单选、多选。输入版块关键词搜索选择',
            'default'     => '',
            'options'     => 'post',
            'query_args'  => array(
                'post_type' => 'plate',
            ),
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'placeholder' => '输入关键词以搜索' . $zib_bbs->plate_name,
            'chosen'      => true,
            'multiple'    => true,
            'type'        => 'select',
        ),
        array(
            'dependency'  => array('include_plate|current_plate', '==|==', '|', '', 'visible'),
            'id'          => 'exclude_plate',
            'title'       => __('排除版块', 'zib_language'),
            'desc'        => '排除所选版块的帖子，支持单选、多选。输入版块关键词搜索选择',
            'default'     => '',
            'options'     => 'post',
            'query_args'  => array(
                'post_type' => 'plate',
            ),
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'placeholder' => '输入关键词以搜索版块分类',
            'chosen'      => true,
            'multiple'    => true,
            'type'        => 'select',
        ),
        array(
            'id'          => 'include_topic',
            'title'       => __('包含' . $zib_bbs->topic_name, 'zib_language'),
            'desc'        => '仅显示所选' . $zib_bbs->topic_name . '的' . $zib_bbs->posts_name . '，支持单选、多选。输入关键词搜索选择',
            'default'     => '',
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy' => 'forum_topic',
            ),
            'placeholder' => '输入关键词以搜索' . $zib_bbs->topic_name,
            'chosen'      => true,
            'multiple'    => true,
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'type'        => 'select',
        ),
        array(
            'id'          => 'include_tag',
            'title'       => __('包含' . $zib_bbs->tag_name, 'zib_language'),
            'desc'        => '仅显示所选' . $zib_bbs->tag_name . '的' . $zib_bbs->posts_name . '，支持单选、多选。输入关键词搜索选择',
            'default'     => '',
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy' => 'forum_tag',
            ),
            'placeholder' => '输入关键词以搜索' . $zib_bbs->tag_name,
            'chosen'      => true,
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'multiple'    => true,
            'type'        => 'select',
        ),
        array(
            'title'       => '类型筛选',
            'id'          => 'bbs_type',
            'default'     => '',
            'type'        => "select",
            'placeholder' => '限制帖子类型，支持单选、多选',
            'chosen'      => true,
            'multiple'    => true,
            'options'     => zib_bbs_get_posts_type_options(),
        ),
        array(
            'title'       => '其它筛选',
            'id'          => 'filter',
            'default'     => '',
            'type'        => "select",
            'placeholder' => '不做其它筛选',
            'options'     => array(
                'topping'         => '置顶帖子',
                'vote'            => '投票帖子',
                'essence'         => '精华帖子',
                'question_status' => '提问已解决',
                'is_hot'          => '热门帖子',
            ),
        ),
        array(
            'title'   => '排序方式',
            'id'      => 'orderby',
            'default' => 'date',
            'type'    => "select",
            'options' => zib_bbs_get_posts_order_options(),
        ),
        array(
            'title'   => '列表样式',
            'id'      => 'style',
            'default' => 'detail',
            'type'    => "radio",
            'inline'  => true,
            'options' => array(
                'detail'     => '详细内容',
                'mini'       => '简约风格',
                'minimalism' => '极简风格',
            ),
        ),
        array(
            'title'   => '列表独立',
            'id'      => 'alone',
            'desc'    => '每一个列表都独立显示为模块',
            'type'    => 'switcher',
            'default' => false,
        ),
        array(
            'title'   => '显示数量',
            'id'      => 'paged_size',
            'class'   => '',
            'default' => 10,
            'max'     => 20,
            'min'     => 4,
            'step'    => 1,
            'unit'    => '篇',
            'type'    => 'spinner',
        ),
        array(
            'id'      => 'paginate',
            'title'   => '翻页按钮',
            'default' => 'none',
            'type'    => "radio",
            'inline'  => true,
            'options' => array(
                'none'       => __('不允许翻页', 'zib_language'),
                'ajax_lists' => __('AJAX追加列表翻页', 'zib_language'),
                'default'    => __('数字翻页按钮', 'zib_language'),
            ),
        ),
    ),
));

function zib_bbs_widget_ui_posts_lists($args, $instance)
{

    $widget_id   = $args['widget_id'];
    $id_base     = 'zib_bbs_widget_ui_posts_lists';
    $index       = str_replace($id_base . '-', '', $widget_id);
    $alone       = !empty($instance['alone']);
    $style       = !empty($instance['style']) ? $instance['style'] : 'mini';
    $placeholder = 'posts_' . $style;
    $placeholder .= $alone ? '_alone' : '';
    $current_plate = 0;
    if (!empty($instance['current_plate'])) {
        $current_plate = zib_bbs_get_the_plate_id();
    }

    $ias_args = array(
        'type'   => 'ias',
        'id'     => '',
        'class'  => '',
        'loader' => zib_bbs_get_placeholder($placeholder), // 加载动画
        'query'  => array(
            'action'        => 'ajax_widget_ui',
            'id'            => $id_base,
            'index'         => $index,
            'current_plate' => $current_plate,
        ),
    );

    $show_class = Zib_CFSwidget::show_class($instance);
    if (!$show_class) {
        return;
    }

    Zib_CFSwidget::echo_before($instance);
    echo $alone ? '' : '<div class="zib-widget padding-h6">';
    echo zib_get_ias_ajaxpager($ias_args);
    echo $alone ? '' : '</div>';
    Zib_CFSwidget::echo_after($instance);
}

function zib_bbs_widget_ui_posts_lists_ajax($instance)
{

    $paged       = zib_get_the_paged();
    $style       = $instance['style'] ? $instance['style'] : 'mini';
    $alone       = !empty($instance['alone']);
    $lists_class = $alone ? 'alone ajax-item' : 'ajax-item';
    $ajax_url    = zib_get_current_url();
    $paginate    = $instance['paginate'];
    $paged_size  = $instance['paged_size'];

    $posts_args = array(
        'plate'         => $instance['include_plate'],
        'plate_exclude' => $instance['exclude_plate'],
        'topic'         => $instance['include_topic'],
        'tag'           => $instance['include_tag'],
        'orderby'       => $instance['orderby'],
        'bbs_type'      => $instance['bbs_type'],
        'filter'        => $instance['filter'],
        'paged'         => $paged,
        'paged_size'    => $paged_size,
    );
    $show_topping = 'topping' === $posts_args['filter'];

    if (!empty($_REQUEST['current_plate'])) {
        $posts_args['plate'] = $_REQUEST['current_plate'];
    }
    $posts = zib_bbs_get_posts_query($posts_args);

    $lists = '';
    if ($posts->have_posts()) {
        while ($posts->have_posts()): $posts->the_post();
            if ('detail' === $style) {
                $lists .= zib_bbs_get_posts_list('class=' . $lists_class . '&show_topping=' . $show_topping);
            } elseif ('minimalism' === $style) {
            $lists .= '<posts class="forum-posts minimalism ' . $lists_class . '">';
            $lists .= zib_bbs_get_posts_lists_title('forum-title', 'em09', $show_topping, true, false);
            $lists .= '</posts>';
        } else {
            $lists .= zib_bbs_get_posts_mini_list($lists_class, $show_topping);
        }
        endwhile;
        wp_reset_query();
    }
    if (1 == $paged && !$lists) {
        $lists = zib_get_ajax_null('暂无内容', 10);
    }
    //帖子分页paginate
    if ('none' !== $paginate) {
        $paginate = zib_bbs_get_paginate($posts->found_posts, $paged, $paged_size, $ajax_url, $paginate);
        if (!$paginate && 1 == $paged) {
            $lists .= '<div class="ajax-pag hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
        } else {
            $lists .= $paginate;
        }
    } else {
        $lists .= '<div class="ajax-pag hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
    }
    zib_ajax_send_ajaxpager($lists);
}
