<?php

add_action('widgets_init', 'widget_register_posts');
function widget_register_posts()
{
    register_widget('widget_ui_mian_posts');
    register_widget('widget_ui_oneline_posts');
    register_widget('widget_ui_mini_posts');
    register_widget('widget_ui_mini_tab_posts');
    register_widget('widget_ui_main_tab_posts');
}
class widget_ui_main_tab_posts extends WP_Widget
{
    public function __construct()
    {
        $widget = array(
            'w_id'        => 'widget_ui_main_tab_posts',
            'w_name'      => _name('多栏目文章'),
            'classname'   => '',
            'description' => '多栏目文章显示，可同时显示多个栏目的文章',
        );
        parent::__construct($widget['w_id'], $widget['w_name'], $widget);
    }
    public function widget($args, $instance)
    {
        extract($args);

        $defaults = array(
            'show_thumb'  => '',
            'show_meta'   => '',
            'show_number' => '',
            'type'        => 'auto',
            'limit_day'   => '',
            'limit'       => 6,
            'tabs'        => array(),
        );
        $defaults['tabs'][] = array(
            'title'   => '热门文章',
            'cat'     => '',
            'topics'  => '',
            'orderby' => 'views',
        );

        $instance = wp_parse_args((array) $instance, $defaults);

        echo '<div class="theme-box">';
        echo '<div class="index-tab">';
        echo '<ul class="list-inline scroll-x mini-scrollbar">';
        $_i  = 0;
        $nav = '';
        $con = '';
        foreach ($instance['tabs'] as $tabs) {
            if ($tabs['title']) {
                $nav_class = $_i == 0 ? 'active' : '';
                $id        = $this->get_field_id('tab_') . $_i;
                echo '<li class="' . $nav_class . '" ><a data-toggle="tab" href="#' . $id . '">' . $tabs['title'] . '</a></li>';
                $_i++;
            }
        }
        echo '</ul>';
        echo '</div>';
        $list_args = array(
            'type' => $instance['type'],
        );
        $_i2 = 0;

        echo '<div class="tab-content">';
        foreach ($instance['tabs'] as $tabs) {
            if ($tabs['title']) {
                $args = array(
                    'cat'                 => $tabs['cat'],
                    'order'               => 'DESC',
                    'showposts'           => $instance['limit'],
                    'ignore_sticky_posts' => 1,
                );
                $orderby = $tabs['orderby'];
                if ($orderby !== 'views' && $orderby !== 'favorite' && $orderby !== 'like') {
                    $args['orderby'] = $orderby;
                } else {
                    $args['orderby']    = 'meta_value_num';
                    $args['meta_query'] = array(
                        array(
                            'key'   => $orderby,
                            'order' => 'DESC',
                        ),
                    );
                }
                if ($tabs['topics']) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'topics',
                            'terms'    => preg_split("/,|，|\s|\n/", $tabs['topics']),
                        ),
                    );
                }
                if ($instance['limit_day'] > 0) {
                    $args['date_query'] = array(
                        array(
                            'after'     => date('Y-m-d H:i:s', strtotime("-" . $instance['limit_day'] . " day")),
                            'before'    => date('Y-m-d H:i:s'),
                            'inclusive' => true,
                        ),
                    );
                }
                $con_class = $_i2 == 0 ? ' active in' : '';
                $id        = $this->get_field_id('tab_') . $_i2;
                echo '<div class="tab-pane fade' . $con_class . '" id="' . $id . '">';

                $the_query = new WP_Query($args);
                if ($instance['type'] == 'oneline_card') {
                    $list_args['type'] = 'card';
                    echo '<div class="swiper-container swiper-scroll" data-slideClass="posts-item">';
                    echo '<div class="posts-row swiper-wrapper">';
                    zib_posts_list($list_args, $the_query);
                    echo '</div>';
                    echo '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
                    echo '</div>';
                } else {
                    echo '<div>';
                    zib_posts_list($list_args, $the_query);
                    echo '</div>';
                }
                echo '</div>';
                $_i2++;
            }
        }
        echo '</div>';
        echo '</div>';
    }
    public function form($instance)
    {
        $defaults = array(
            'type'      => 'auto',
            'limit'     => 6,
            'limit_day' => '',
            'tabs'      => array(),
        );
        $defaults['tabs'][] = array(
            'title'   => '热门文章',
            'cat'     => '',
            'topics'  => '',
            'orderby' => 'views',
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        $img_html = '';
        $img_i    = 0;
        foreach ($instance['tabs'] as $category) {
            $_html_a = '<label>栏目' . ($img_i + 1) . '-标题（必填）：<input style="width:100%;" type="text" id="' . $this->get_field_id('tabs') . '[' . $img_i . '].title" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][title]" value="' . $instance['tabs'][$img_i]['title'] . '" /></label>';

            $_html_b = '<label>栏目' . ($img_i + 1) . '-分类限制：<input style="width:100%;" type="text" id="' . $this->get_field_id('tabs') . '[' . $img_i . '].cat" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][cat]" value="' . $instance['tabs'][$img_i]['cat'] . '" /></label>';
            $_html_b .= '<label>栏目' . ($img_i + 1) . '-专题：<input style="width:100%;" type="text" id="' . $this->get_field_id('tabs') . '[' . $img_i . '].topics" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][topics]" value="' . $instance['tabs'][$img_i]['topics'] . '" /></label>';

            $_html_c = '<label>栏目' . ($img_i + 1) . '-排序方式：
			<select style="width:100%;" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][orderby]">
			<option value="comment_count" ' . selected('comment_count', $instance['tabs'][$img_i]['orderby'], false) . '>评论数</option>
			<option value="views" ' . selected('views', $instance['tabs'][$img_i]['orderby'], false) . '>浏览量</option>
			<option value="like" ' . selected('like', $instance['tabs'][$img_i]['orderby'], false) . '>点赞数</option>
			<option value="favorite" ' . selected('favorite', $instance['tabs'][$img_i]['orderby'], false) . '>收藏数</option>
			<option value="date" ' . selected('date', $instance['tabs'][$img_i]['orderby'], false) . '>发布时间</option>
			<option value="modified" ' . selected('modified', $instance['tabs'][$img_i]['orderby'], false) . '>更新时间</option>
			<option value="rand" ' . selected('rand', $instance['tabs'][$img_i]['orderby'], false) . '>随机排序</option>
			</select></label>';

            $_tt  = '<div class="panel"><h4 class="panel-title">栏目' . ($img_i + 1) . '：' . $instance['tabs'][$img_i]['title'] . '</h4><div class="panel-hide panel-conter">';
            $_tt2 = '</div></div>';

            $img_html .= '<div class="widget_ui_slider_g">' . $_tt . $_html_a . $_html_b . $_html_c . $_tt2 . '</div>';
            $img_i++;
        }

        $add_b = '<button type="button" data-name="' . $this->get_field_name('tabs') . '" data-count="' . $img_i . '" class="button add_button add_lists_button">添加栏目</button>';
        $add_b .= '<button type="button" data-name="' . $this->get_field_name('tabs') . '" data-count="' . $img_i . '" class="button rem_lists_button">删除栏目</button>';
        $img_html .= $add_b;
        ?> <p>
			<i style="width:100%;font-size: 12px;">在一个模块中实现多栏目的文章显示。通过对栏目分类的显示和排序方式可组合成多种需求的文章显示，主题！如果要显示在全宽度页面，请确保显示模式统一，不要选择自动模式</i><br>
			<?php zib_cat_help()?>
			<?php zib_topics_help()?>
		</p>
		<p>
			<label>
				显示数目：
				<input style="width:100%;" id="<?php echo $this->get_field_id('limit');
        ?>" name="<?php echo $this->get_field_name('limit');
        ?>" type="number" value="<?php echo $instance['limit'];
        ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				限制时间（最近X天）：
				<input style="width:100%;" name="<?php echo $this->get_field_name('limit_day') ?>" type="number" value="<?php echo $instance['limit_day'] ?>" size="24" />
			</label>
		</p>

		<p>
			<label>
				列表显示模式：
				<select style="width:100%;" id="<?php echo $this->get_field_id('type');
        ?>" name="<?php echo $this->get_field_name('type');
        ?>">
					<option value="auto" <?php selected('auto', $instance['type']);
        ?>>默认（自动跟随主题设置)</option>
					<option value="card" <?php selected('card', $instance['type']);
        ?>>卡片模式</option>
					<option value="oneline_card" <?php selected('oneline_card', $instance['type']);
        ?>>单行滚动卡片模式</option>
					<option value="no_thumb" <?php selected('no_thumb', $instance['type']);
        ?>>无缩略图列表</option>
					<option value="mult_thumb" <?php selected('mult_thumb', $instance['type']);
        ?>>多图模式</option>
				</select>
			</label>
		</p>
		<?php echo $img_html; ?>
	<?php
}
}

//////---多栏目文章mini---////////
class widget_ui_mini_tab_posts extends WP_Widget
{
    public function __construct()
    {
        $widget = array(
            'w_id'        => 'widget_ui_mini_tab_posts',
            'w_name'      => _name('多栏目文章mini'),
            'classname'   => '',
            'description' => '多栏目文章显示mini版，可同时显示多个栏目的文章',
        );
        parent::__construct($widget['w_id'], $widget['w_name'], $widget);
    }
    public function widget($args, $instance)
    {
        extract($args);

        $defaults = array(
            'title'        => '',
            'in_affix'     => '',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'show_thumb'   => '',
            'show_meta'    => '',
            'show_number'  => '',
            'limit'        => 6, 'limit_day' => '',
            'tabs'         => array(),
        );
        $defaults['tabs'][] = array(
            'title'   => '热门文章',
            'cat'     => '',
            'topics'  => '',
            'orderby' => 'views',
        );

        $instance = wp_parse_args((array) $instance, $defaults);

        $mini_title = $instance['mini_title'];
        if ($mini_title) {
            $mini_title = '<small class="ml10">' . $mini_title . '</small>';
        }
        $title    = $instance['title'];
        $class    = '';
        $more_but = '';
        if ($instance['more_but'] && $instance['more_but_url']) {
            $more_but = '<div class="pull-right em09 mt3"><a href="' . $instance['more_but_url'] . '" class="muted-2-color">' . $instance['more_but'] . '</a></div>';
        }
        $mini_title .= $more_but;

        if ($title) {
            $title = '<div class="box-body notop' . $class . '"><div class="title-theme">' . $title . $mini_title . '</div></div>';
        }

        $in_affix = $instance['in_affix'] ? ' data-affix="true"' : '';
        echo '<div' . $in_affix . ' class="theme-box">';
        echo $title;
        echo '<div class="box-body posts-mini-lists zib-widget">';
        echo '<ul class="list-inline scroll-x mini-scrollbar tab-nav-theme">';
        $_i      = 0;
        $id_base = 'post_mini_';
        foreach ($instance['tabs'] as $tabs) {
            if ($tabs['title']) {
                $nav_class = $_i == 0 ? 'active' : '';
                $id        = $id_base . $_i;
                echo '<li class="' . $nav_class . '" ><a class="post-tab-toggle" data-toggle="tab" href="javascript:;" tab-id="' . $id . '">' . $tabs['title'] . '</a></li>';
                $_i++;
            }
        }
        echo '</ul>';
        $list_args = array(
            'show_thumb'  => $instance['show_thumb'] ? true : false,
            'show_meta'   => $instance['show_meta'] ? true : false,
            'show_number' => $instance['show_number'] ? true : false,
        );
        $_i2 = 0;

        echo '<div class="tab-content">';
        foreach ($instance['tabs'] as $tabs) {
            if ($tabs['title']) {
                $args = array(
                    'cat'                 => $tabs['cat'],
                    'order'               => 'DESC',
                    'showposts'           => $instance['limit'],
                    'ignore_sticky_posts' => 1,
                );
                $orderby = $tabs['orderby'];
                if ($orderby !== 'views' && $orderby !== 'favorite' && $orderby !== 'like') {
                    $args['orderby'] = $orderby;
                } else {
                    $args['orderby']    = 'meta_value_num';
                    $args['meta_query'] = array(
                        array(
                            'key'   => $orderby,
                            'order' => 'DESC',
                        ),
                    );
                }
                if ($tabs['topics']) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'topics',
                            'terms'    => preg_split("/,|，|\s|\n/", $tabs['topics']),
                        ),
                    );
                }
                if ($instance['limit_day'] > 0) {
                    $args['date_query'] = array(
                        array(
                            'after'     => date('Y-m-d H:i:s', strtotime("-" . $instance['limit_day'] . " day")),
                            'before'    => date('Y-m-d H:i:s'),
                            'inclusive' => true,
                        ),
                    );
                }
                $con_class = $_i2 == 0 ? ' active in' : '';
                $id        = $id_base . $_i2;
                echo '<div class="tab-pane fade' . $con_class . '" tab-id="' . $id . '">';
                $the_query = new WP_Query($args);
                zib_posts_mini_list($list_args, $the_query);
                echo '</div>';
                $_i2++;
            }
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    public function form($instance)
    {
        $defaults = array(
            'title'        => '',
            'mini_title'   => '',
            'in_affix'     => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'show_thumb'   => '',
            'show_meta'    => '',
            'show_number'  => '',
            'limit'        => 6,
            'limit_day'    => '',
            'tabs'         => array(),
        );
        $defaults['tabs'][] = array(
            'title'   => '热门文章',
            'cat'     => '',
            'topics'  => '',
            'orderby' => 'views',
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        $img_html = '';
        $img_i    = 0;
        foreach ($instance['tabs'] as $category) {
            $_html_a = '<label>栏目' . ($img_i + 1) . '-标题（必填）：<input style="width:100%;" type="text" id="' . $this->get_field_id('tabs') . '[' . $img_i . '].title" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][title]" value="' . $instance['tabs'][$img_i]['title'] . '" /></label>';

            $_html_b = '<label>栏目' . ($img_i + 1) . '-分类限制：<input style="width:100%;" type="text" id="' . $this->get_field_id('tabs') . '[' . $img_i . '].cat" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][cat]" value="' . $instance['tabs'][$img_i]['cat'] . '" /></label>';
            $_html_b .= '<label>栏目' . ($img_i + 1) . '-专题：<input style="width:100%;" type="text" id="' . $this->get_field_id('tabs') . '[' . $img_i . '].topics" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][topics]" value="' . $instance['tabs'][$img_i]['topics'] . '" /></label>';

            $_html_c = '<label>栏目' . ($img_i + 1) . '-排序方式：
			<select style="width:100%;" name="' . $this->get_field_name('tabs') . '[' . $img_i . '][orderby]">
			<option value="comment_count" ' . selected('comment_count', $instance['tabs'][$img_i]['orderby'], false) . '>评论数</option>
			<option value="views" ' . selected('views', $instance['tabs'][$img_i]['orderby'], false) . '>浏览量</option>
			<option value="like" ' . selected('like', $instance['tabs'][$img_i]['orderby'], false) . '>点赞数</option>
			<option value="favorite" ' . selected('favorite', $instance['tabs'][$img_i]['orderby'], false) . '>收藏数</option>
			<option value="date" ' . selected('date', $instance['tabs'][$img_i]['orderby'], false) . '>发布时间</option>
			<option value="modified" ' . selected('modified', $instance['tabs'][$img_i]['orderby'], false) . '>更新时间</option>
			<option value="rand" ' . selected('rand', $instance['tabs'][$img_i]['orderby'], false) . '>随机排序</option>
		</select></label>';
            $_tt  = '<div class="panel"><h4 class="panel-title">栏目' . ($img_i + 1) . '：' . $instance['tabs'][$img_i]['title'] . '</h4><div class="panel-hide panel-conter">';
            $_tt2 = '</div></div>';

            $img_html .= '<div class="widget_ui_slider_g">' . $_tt . $_html_a . $_html_b . $_html_c . $_tt2 . '</div>';

            $img_i++;
        }

        $add_b = '<button type="button" data-name="' . $this->get_field_name('tabs') . '" data-count="' . $img_i . '" class="button add_button add_lists_button">添加栏目</button>';
        $add_b .= '<button type="button" data-name="' . $this->get_field_name('tabs') . '" data-count="' . $img_i . '" class="button rem_lists_button">删除栏目</button>';
        $img_html .= $add_b;
        $page_input[] = array(
            'name'  => __('标题：', 'zib_language'),
            'id'    => $this->get_field_name('title'),
            'std'   => $instance['title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('副标题：', 'zib_language'),
            'id'    => $this->get_field_name('mini_title'),
            'std'   => $instance['mini_title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->文案：', 'zib_language'),
            'id'    => $this->get_field_name('more_but'),
            'std'   => $instance['more_but'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->链接：', 'zib_language'),
            'id'    => $this->get_field_name('more_but_url'),
            'std'   => $instance['more_but_url'],
            'desc'  => '设置为任意链接',
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );

        echo zib_edit_input_construct($page_input);

        ?> <p>
			<i style="width:100%;">在一个模块中实现多栏目的文章显示。通过对栏目分类的显示和排序方式可组合成多种需求的文章显示</i><br>
			<?php zib_cat_help()?>
			<?php zib_topics_help()?>
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['in_affix'], 'on');?> id="<?php echo $this->get_field_id('in_affix'); ?>" name="<?php echo $this->get_field_name('in_affix'); ?>"> 侧栏随动（仅在侧边栏有效）
			</label>
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['show_thumb'], 'on');?> id="<?php echo $this->get_field_id('show_thumb'); ?>" name="<?php echo $this->get_field_name('show_thumb'); ?>">显示缩略图
			</label>
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['show_number'], 'on');?> id="<?php echo $this->get_field_id('show_number'); ?>" name="<?php echo $this->get_field_name('show_number'); ?>">显示编号
			</label>
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['show_meta'], 'on');?> id="<?php echo $this->get_field_id('show_meta'); ?>" name="<?php echo $this->get_field_name('show_meta'); ?>">显示作者、时间、点赞等信息
			</label>
		</p>

		<p>
			<label>
				显示数目：
				<input style="width:100%;" id="<?php echo $this->get_field_id('limit');
        ?>" name="<?php echo $this->get_field_name('limit');
        ?>" type="number" value="<?php echo $instance['limit'];
        ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				限制时间（最近X天）：
				<input style="width:100%;" name="<?php echo $this->get_field_name('limit_day') ?>" type="number" value="<?php echo $instance['limit_day'] ?>" size="24" />
			</label>
		</p>

		<?php echo $img_html; ?>
	<?php
}
}

class widget_ui_mini_posts extends WP_Widget
{
    public function __construct()
    {
        $widget = array(
            'w_id'        => 'widget_ui_mini_posts',
            'w_name'      => _name('文章mini'),
            'classname'   => '',
            'description' => '尺寸更小的文章列表，更适合放置在侧边栏',
        );
        parent::__construct($widget['w_id'], $widget['w_name'], $widget);
    }
    public function widget($args, $instance)
    {
        extract($args);

        $defaults = array(
            'title'        => '',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'in_affix'     => '',
            'limit'        => 6,
            'limit_day'    => '',
            'cat'          => '',
            'topics'       => '',
            'orderby'      => 'views',
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        $orderby  = $instance['orderby'];

        $mini_title = $instance['mini_title'];
        if ($mini_title) {
            $mini_title = '<small class="ml10">' . $mini_title . '</small>';
        }
        $title    = $instance['title'];
        $class    = '';
        $more_but = '';
        if ($instance['more_but'] && $instance['more_but_url']) {
            $more_but = '<div class="pull-right em09 mt3"><a href="' . $instance['more_but_url'] . '" class="muted-2-color">' . $instance['more_but'] . '</a></div>';
        }
        $mini_title .= $more_but;

        if ($title) {
            $title = '<div class="box-body notop' . $class . '"><div class="title-theme">' . $title . $mini_title . '</div></div>';
        }

        $in_affix = $instance['in_affix'] ? ' data-affix="true"' : '';
        echo '<div' . $in_affix . ' class="theme-box">';
        echo $title;
        //    echo '<pre>'.json_encode($instance).'</pre>';

        $args = array(
            'cat'                 => str_replace('，', ',', $instance['cat']),
            'order'               => 'DESC',
            'showposts'           => $instance['limit'],
            'ignore_sticky_posts' => 1,
        );

        if ($orderby !== 'views' && $orderby !== 'favorite' && $orderby !== 'like') {
            $args['orderby'] = $orderby;
        } else {
            $args['orderby']    = 'meta_value_num';
            $args['meta_query'] = array(
                array(
                    'key'   => $orderby,
                    'order' => 'DESC',
                ),
            );
        }
        if ($instance['topics']) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'topics',
                    'terms'    => preg_split("/,|，|\s|\n/", $instance['topics']),
                ),
            );
        }
        if ($instance['limit_day'] > 0) {
            $args['date_query'] = array(
                array(
                    'after'     => date('Y-m-d H:i:s', strtotime("-" . $instance['limit_day'] . " day")),
                    'before'    => date('Y-m-d H:i:s'),
                    'inclusive' => true,
                ),
            );
        }
        $list_args = array(
            'show_thumb'  => isset($instance['show_thumb']) ? true : false,
            'show_meta'   => isset($instance['show_meta']) ? true : false,
            'show_number' => isset($instance['show_number']) ? true : false,
        );
        echo '<div class="box-body posts-mini-lists zib-widget">';
        $the_query = new WP_Query($args);
        zib_posts_mini_list($list_args, $the_query);
        echo '</div>';
        echo '</div>';
    }
    public function form($instance)
    {
        $defaults = array(
            'title'        => '',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'in_affix'     => '',
            'show_thumb'   => '',
            'show_meta'    => '',
            'show_number'  => '',
            'limit'        => 6, 'limit_day' => '',
            'topics'       => '',
            'cat'          => '',
            'orderby'      => 'views',
        );
        $instance = wp_parse_args((array) $instance, $defaults);

        $page_input[] = array(
            'name'  => __('标题：', 'zib_language'),
            'id'    => $this->get_field_name('title'),
            'std'   => $instance['title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('副标题：', 'zib_language'),
            'id'    => $this->get_field_name('mini_title'),
            'std'   => $instance['mini_title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->文案：', 'zib_language'),
            'id'    => $this->get_field_name('more_but'),
            'std'   => $instance['more_but'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->链接：', 'zib_language'),
            'id'    => $this->get_field_name('more_but_url'),
            'std'   => $instance['more_but_url'],
            'desc'  => '设置为任意链接',
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );

        echo zib_edit_input_construct($page_input);
        ?>
		<p>
			尺寸更小的文章列表，推荐设置在侧边栏，如果要设置在非侧边栏位置，请打开显示缩略图
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['in_affix'], 'on');?> id="<?php echo $this->get_field_id('in_affix'); ?>" name="<?php echo $this->get_field_name('in_affix'); ?>"> 侧栏随动（仅在侧边栏有效）
			</label>
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['show_thumb'], 'on');?> id="<?php echo $this->get_field_id('show_thumb'); ?>" name="<?php echo $this->get_field_name('show_thumb'); ?>">显示缩略图
			</label>
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['show_number'], 'on');?> id="<?php echo $this->get_field_id('show_number'); ?>" name="<?php echo $this->get_field_name('show_number'); ?>">显示编号
			</label>
		</p>
		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['show_meta'], 'on');?> id="<?php echo $this->get_field_id('show_meta'); ?>" name="<?php echo $this->get_field_name('show_meta'); ?>">显示作者、时间、点赞等信息
			</label>
		</p>
		<p>
			<?php zib_cat_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('cat');
        ?>" name="<?php echo $this->get_field_name('cat');
        ?>" type="text" value="<?php echo str_replace('，', ',', $instance['cat']);
        ?>" size="24" />
		</p>
		<p>
			<?php zib_topics_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('topics');
        ?>" name="<?php echo $this->get_field_name('topics');
        ?>" type="text" value="<?php echo $instance['topics'];
        ?>" size="24" />
		</p>
		<p>
			<label>
				显示数目：
				<input style="width:100%;" id="<?php echo $this->get_field_id('limit');
        ?>" name="<?php echo $this->get_field_name('limit');
        ?>" type="number" value="<?php echo $instance['limit'];
        ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				限制时间（最近X天）：
				<input style="width:100%;" name="<?php echo $this->get_field_name('limit_day') ?>" type="number" value="<?php echo $instance['limit_day'] ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				排序：
				<select style="width:100%;" id="<?php echo $this->get_field_id('orderby');
        ?>" name="<?php echo $this->get_field_name('orderby');
        ?>">
					<option value="comment_count" <?php selected('comment_count', $instance['orderby']);
        ?>>评论数</option>
					<option value="views" <?php selected('views', $instance['orderby']);
        ?>>浏览量</option>
					<option value="like" <?php selected('like', $instance['orderby']);
        ?>>点赞数</option>
					<option value="favorite" <?php selected('favorite', $instance['orderby']);
        ?>>收藏数</option>
					<option value="date" <?php selected('date', $instance['orderby']);
        ?>>发布时间</option>
					<option value="modified" <?php selected('modified', $instance['orderby']);
        ?>>更新时间</option>
					<option value="rand" <?php selected('rand', $instance['orderby']);
        ?>>随机排序</option>
				</select>
			</label>
		</p>
	<?php
}
}

class widget_ui_mian_posts extends WP_Widget
{
    public function __construct()
    {
        $widget = array(
            'w_id'        => 'widget_ui_mian_posts',
            'w_name'      => _name('文章列表'),
            'classname'   => '',
            'description' => '核心的文章列表功能',
        );
        parent::__construct($widget['w_id'], $widget['w_name'], $widget);
    }
    public function widget($args, $instance)
    {
        extract($args);

        $defaults = array(
            'title'        => '',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'type'         => 'auto',
            'limit'        => 6, 'limit_day' => '',
            'cat'          => '',
            'topics'       => '',
            'orderby'      => 'views',
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        $orderby  = $instance['orderby'];

        $mini_title = $instance['mini_title'];
        if ($mini_title) {
            $mini_title = '<small class="ml10">' . $mini_title . '</small>';
        }
        $title = $instance['title'];
        $class = ' nobottom';
        if ($instance['type'] == 'card') {
            $class = '';
        }
        $more_but = '';
        if ($instance['more_but'] && $instance['more_but_url']) {
            $more_but = '<div class="pull-right em09 mt3"><a href="' . $instance['more_but_url'] . '" class="muted-2-color">' . $instance['more_but'] . '</a></div>';
        }
        $mini_title .= $more_but;

        if ($title) {
            $title = '<div class="box-body notop' . $class . '"><div class="title-theme">' . $title . $mini_title . '</div></div>';
        }

        echo '<div class="theme-box">';
        echo $title;
        //    echo '<pre>'.json_encode($instance).'</pre>';

        $args = array(
            'cat'                 => str_replace('，', ',', $instance['cat']),
            'order'               => 'DESC',
            'showposts'           => $instance['limit'],
            'ignore_sticky_posts' => 1,
        );

        if ($orderby !== 'views' && $orderby !== 'favorite' && $orderby !== 'like') {
            $args['orderby'] = $orderby;
        } else {
            $args['orderby']    = 'meta_value_num';
            $args['meta_query'] = array(
                array(
                    'key'   => $orderby,
                    'order' => 'DESC',
                ),
            );
        }
        if ($instance['topics']) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'topics',
                    'terms'    => preg_split("/,|，|\s|\n/", $instance['topics']),
                ),
            );
        }
        if ($instance['limit_day'] > 0) {
            $args['date_query'] = array(
                array(
                    'after'     => date('Y-m-d H:i:s', strtotime("-" . $instance['limit_day'] . " day")),
                    'before'    => date('Y-m-d H:i:s'),
                    'inclusive' => true,
                ),
            );
        }

        $list_args = array(
            'type' => $instance['type'],
        );

        $the_query = new WP_Query($args);
        echo '<div class="posts-row">';
        zib_posts_list($list_args, $the_query);
        echo '</div>';
        echo '</div>';
    }
    public function form($instance)
    {
        $defaults = array(
            'title'        => '',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'limit'        => 6, 'limit_day' => '',
            'type'         => 'auto',
            'topics'       => '',
            'cat'          => '',
            'orderby'      => 'views',
        );
        $instance     = wp_parse_args((array) $instance, $defaults);
        $page_input[] = array(
            'name'  => __('标题：', 'zib_language'),
            'id'    => $this->get_field_name('title'),
            'std'   => $instance['title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('副标题：', 'zib_language'),
            'id'    => $this->get_field_name('mini_title'),
            'std'   => $instance['mini_title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->文案：', 'zib_language'),
            'id'    => $this->get_field_name('more_but'),
            'std'   => $instance['more_but'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->链接：', 'zib_language'),
            'id'    => $this->get_field_name('more_but_url'),
            'std'   => $instance['more_but_url'],
            'desc'  => '设置为任意链接',
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );

        echo zib_edit_input_construct($page_input);
        ?>
		<p>
			<label>
				<i style="width:100%;font-size: 12px;">核心的文章列表功能，不建议设置在侧边栏。如果要设置在全宽度位置，请确保显示模式一致，不要选择自动模式</i>
			</label>
		</p>

		<p>

			<?php zib_cat_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('cat');
        ?>" name="<?php echo $this->get_field_name('cat');
        ?>" type="text" value="<?php echo str_replace('，', ',', $instance['cat']);
        ?>" size="24" />
		</p>
		<p>
			<?php zib_topics_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('topics');
        ?>" name="<?php echo $this->get_field_name('topics');
        ?>" type="text" value="<?php echo $instance['topics'];
        ?>" size="24" />
		</p>
		<p>
			<label>
				显示数目：
				<input style="width:100%;" name="<?php echo $this->get_field_name('limit') ?>" type="number" value="<?php echo $instance['limit'] ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				限制时间（最近X天）：
				<input style="width:100%;" name="<?php echo $this->get_field_name('limit_day') ?>" type="number" value="<?php echo $instance['limit_day'] ?>" size="24" />
			</label>
		</p>

		<p>
			<label>
				列表显示模式：
				<select style="width:100%;" id="<?php echo $this->get_field_id('type');
        ?>" name="<?php echo $this->get_field_name('type');
        ?>">
					<option value="auto" <?php selected('auto', $instance['type']);
        ?>>默认（自动跟随主题设置)</option>
					<option value="card" <?php selected('card', $instance['type']);
        ?>>卡片模式</option>
					<option value="no_thumb" <?php selected('no_thumb', $instance['type']);
        ?>>无缩略图列表</option>
					<option value="mult_thumb" <?php selected('mult_thumb', $instance['type']);
        ?>>多图模式</option>
				</select>
			</label>
		</p>
		<p>
			<label>
				排序：
				<select style="width:100%;" id="<?php echo $this->get_field_id('orderby');
        ?>" name="<?php echo $this->get_field_name('orderby');
        ?>">
					<option value="comment_count" <?php selected('comment_count', $instance['orderby']);
        ?>>评论数</option>
					<option value="views" <?php selected('views', $instance['orderby']);
        ?>>浏览量</option>
					<option value="like" <?php selected('like', $instance['orderby']);
        ?>>点赞数</option>
					<option value="favorite" <?php selected('favorite', $instance['orderby']);
        ?>>收藏数</option>
					<option value="date" <?php selected('date', $instance['orderby']);
        ?>>发布时间</option>
					<option value="modified" <?php selected('modified', $instance['orderby']);
        ?>>更新时间</option>
					<option value="rand" <?php selected('rand', $instance['orderby']);
        ?>>随机排序</option>
				</select>
			</label>
		</p>
	<?php
}
}

///////单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------
///////单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------
///////单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------
///////单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------
///////单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------//单行滚动文章版块------

class widget_ui_oneline_posts extends WP_Widget
{
    public function __construct()
    {
        $widget = array(
            'w_id'        => 'widget_ui_oneline_posts',
            'w_name'      => _name('单行文章列表'),
            'classname'   => '',
            'description' => '显示文章列表，只显示一行，自动横向滚动',
        );
        parent::__construct($widget['w_id'], $widget['w_name'], $widget);
    }
    public function widget($args, $instance)
    {
        extract($args);
        $defaults = array(
            'title'        => '',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'in_affix'     => '',
            'type'         => 'auto',
            'limit'        => 6, 'limit_day' => '',
            'topics'       => '',
            'cat'          => '',
            'orderby'      => 'views',
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        $orderby  = $instance['orderby'];

        $mini_title = $instance['mini_title'];
        if ($mini_title) {
            $mini_title = '<small class="ml10">' . $mini_title . '</small>';
        }
        $title    = $instance['title'];
        $more_but = '';
        if ($instance['more_but'] && $instance['more_but_url']) {
            $more_but = '<div class="pull-right em09 mt3"><a href="' . $instance['more_but_url'] . '" class="muted-2-color">' . $instance['more_but'] . '</a></div>';
        }
        $mini_title .= $more_but;

        if ($title) {
            $title = '<div class="box-body notop"><div class="title-theme">' . $title . $mini_title . '</div></div>';
        }

        $in_affix = $instance['in_affix'] ? ' data-affix="true"' : '';
        echo '<div' . $in_affix . ' class="theme-box">';
        echo $title;
        //    echo '<pre>'.json_encode($instance).'</pre>';

        $args = array(
            'cat'                 => str_replace('，', ',', $instance['cat']),
            'order'               => 'DESC',
            'showposts'           => $instance['limit'],
            'ignore_sticky_posts' => 1,
        );

        if ($orderby !== 'views' && $orderby !== 'favorite' && $orderby !== 'like') {
            $args['orderby'] = $orderby;
        } else {
            $args['orderby']    = 'meta_value_num';
            $args['meta_query'] = array(
                array(
                    'key'   => $orderby,
                    'order' => 'DESC',
                ),
            );
        }
        if ($instance['topics']) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'topics',
                    'terms'    => preg_split("/,|，|\s|\n/", $instance['topics']),
                ),
            );
        }
        if ($instance['limit_day'] > 0) {
            $args['date_query'] = array(
                array(
                    'after'     => date('Y-m-d H:i:s', strtotime("-" . $instance['limit_day'] . " day")),
                    'before'    => date('Y-m-d H:i:s'),
                    'inclusive' => true,
                ),
            );
        }

        $list_args = array(
            'type' => 'card',
        );
        $the_query = new WP_Query($args);

        echo '<div class="swiper-container swiper-scroll" data-slideClass="posts-item">';
        echo '<div class="swiper-wrapper">';
        zib_posts_list($list_args, $the_query);
        echo '</div>';
        echo '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
        echo '</div>';
        echo '</div>';
    }

    public function form($instance)
    {
        $defaults = array(
            'title'        => '热门文章',
            'mini_title'   => '',
            'more_but'     => '<i class="fa fa-angle-right fa-fw"></i>更多',
            'more_but_url' => '',
            'in_affix'     => '',
            'limit'        => 6, 'limit_day' => '',
            'type'         => 'auto',
            'topics'       => '',
            'cat'          => '',
            'orderby'      => 'views',
        );
        $instance     = wp_parse_args((array) $instance, $defaults);
        $page_input[] = array(
            'name'  => __('标题：', 'zib_language'),
            'id'    => $this->get_field_name('title'),
            'std'   => $instance['title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('副标题：', 'zib_language'),
            'id'    => $this->get_field_name('mini_title'),
            'std'   => $instance['mini_title'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->文案：', 'zib_language'),
            'id'    => $this->get_field_name('more_but'),
            'std'   => $instance['more_but'],
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );
        $page_input[] = array(
            'name'  => __('标题右侧按钮->链接：', 'zib_language'),
            'id'    => $this->get_field_name('more_but_url'),
            'std'   => $instance['more_but_url'],
            'desc'  => '设置为任意链接',
            'style' => 'margin: 10px auto;',
            'type'  => 'text',
        );

        echo zib_edit_input_construct($page_input);
        ?>

		<p>
			<label>
				<input style="vertical-align:-3px;margin-right:4px;" class="checkbox" type="checkbox" <?php checked($instance['in_affix'], 'on');?> id="<?php echo $this->get_field_id('in_affix'); ?>" name="<?php echo $this->get_field_name('in_affix'); ?>"> 侧栏随动（仅在侧边栏有效）
			</label>
		</p>
		<p>
			<?php zib_cat_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('cat');
        ?>" name="<?php echo $this->get_field_name('cat');
        ?>" type="text" value="<?php echo str_replace('，', ',', $instance['cat']);
        ?>" size="24" />
		</p>
		<p>
			<?php zib_topics_help()?>
			<input style="width:100%;" id="<?php echo $this->get_field_id('topics');
        ?>" name="<?php echo $this->get_field_name('topics');
        ?>" type="text" value="<?php echo $instance['topics'];
        ?>" size="24" />
		</p>
		<p>
			<label>
				显示数目：
				<input style="width:100%;" id="<?php echo $this->get_field_id('limit');
        ?>" name="<?php echo $this->get_field_name('limit');
        ?>" type="number" value="<?php echo $instance['limit'];
        ?>" size="24" />
			</label>
		</p>
		<p>
			<label>
				限制时间（最近X天）：
				<input style="width:100%;" name="<?php echo $this->get_field_name('limit_day') ?>" type="number" value="<?php echo $instance['limit_day'] ?>" size="24" />
			</label>
		</p>

		<p>
			<label>
				排序方式：
				<select style="width:100%;" id="<?php echo $this->get_field_id('orderby');
        ?>" name="<?php echo $this->get_field_name('orderby');
        ?>">
					<option value="comment_count" <?php selected('comment_count', $instance['orderby']);
        ?>>评论数</option>
					<option value="views" <?php selected('views', $instance['orderby']);
        ?>>浏览量</option>
					<option value="like" <?php selected('like', $instance['orderby']);
        ?>>点赞数</option>
					<option value="favorite" <?php selected('favorite', $instance['orderby']);
        ?>>收藏数</option>
					<option value="date" <?php selected('date', $instance['orderby']);
        ?>>发布时间</option>
					<option value="modified" <?php selected('modified', $instance['orderby']);
        ?>>更新时间</option>
					<option value="rand" <?php selected('rand', $instance['orderby']);
        ?>>随机排序</option>
				</select>
			</label>
		</p>
<?php
}
}

//分类、专题图文模块
Zib_CFSwidget::create('zib_widget_ui_term_card', array(
    'title'       => '分类图文卡片',
    'zib_title'   => true,
    'zib_affix'   => true,
    'zib_show'    => true,
    'description' => '将分类、专题显示为图文卡片',
    'fields'      => array(
        array(
            'title'   => __("卡片样式", 'zib_language'),
            'id'      => 'type',
            'type'    => 'radio',
            'default' => 'style-1',
            'options' => array(
                'style-1' => '简单样式',
                'style-2' => '样式二',
                'style-3' => '样式三',
                'style-4' => '样式四',
            ),
        ),
        array(
            'id'      => 'height_scale',
            'class'   => 'compact',
            'title'   => '卡片长宽比例',
            'default' => 60,
            'max'     => 300,
            'min'     => 20,
            'step'    => 5,
            'unit'    => '%',
            'type'    => 'spinner',
        ),
        array(
            'id'       => 'pc_row',
            'title'    => '排列布局',
            'subtitle' => 'PC端单行排列数量',
            'default'  => 4,
            'class'    => 'compact button-mini',
            'default'  => 2,
            'options'  => array(
                1  => '1个',
                2  => '2个',
                3  => '3个',
                4  => '4个',
                6  => '6个',
                12 => '12个',
            ),
            'type'     => 'button_set',
        ),
        array(
            'id'       => 'm_row',
            'title'    => ' ',
            'subtitle' => '移动端单行排列数量',
            'decs'     => '请根据此模块放置位置的宽度合理调整单行数量，避免显示不佳',
            'default'  => 2,
            'class'    => 'compact button-mini',
            'default'  => 2,
            'options'  => array(
                1  => '1个',
                2  => '2个',
                3  => '3个',
                4  => '4个',
                6  => '6个',
                12 => '12个',
            ),
            'type'     => 'button_set',
        ),
        array(
            'id'      => 'mask_opacity',
            'title'   => '遮罩透明度',
            'help'    => '图片上显示的黑色遮罩层的透明度',
            'default' => 10,
            'class'   => 'compact',
            'max'     => 90,
            'min'     => 0,
            'step'    => 1,
            'unit'    => '%',
            'type'    => 'slider',
        ),
        array(
            'id'          => 'term_id',
            'title'       => '添加分类、专题',
            'desc'        => '选择并排序需要的分类、专题，如选择的分类(专题)下没有文章则不会显示',
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy' => array('topics', 'category'),
                'orderby'  => 'taxonomy',
            ),
            'placeholder' => '输入关键词以搜索分类或专题',
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'type'        => 'select',
        ),
        array(
            'title'   => '新窗口打开',
            'id'      => 'target_blank',
            'type'    => 'switcher',
            'default' => false,
        ),
    ),
));

function zib_widget_ui_term_card($args, $instance)
{
    $show_class = Zib_CFSwidget::show_class($instance);
    if (empty($instance['term_id'][0]) || !$show_class) {
        return;
    }

    //准备栏目
    $pc_row = (int) $instance['pc_row'];
    $m_row  = (int) $instance['m_row'];

    $row_class = 'col-sm-' . (int) (12 / $pc_row);
    $row_class .= $m_row > 1 ? ' col-xs-' . (int) (12 / $m_row) : '';

    $terms = get_terms(array(
        'include' => $instance['term_id'],
        'orderby' => 'include',
    ));
    $is_row       = count($terms) > 1;
    $target_blank = !empty($instance['target_blank']) ? '_blank' : '';
    $html         = '';
    if ($terms) {
        foreach ($terms as $term) {
            $default_img = '';
            if ($term->taxonomy == 'category') {
                $default_img = _pz('cat_default_cover');
                $icon        = '<i class="fa fa-folder-open-o mr6" aria-hidden="true"></i>';
            } elseif ($term->taxonomy == 'topics') {
                $default_img = _pz('topics_default_cover');
                $icon        = '<i class="fa fa-cube mr6" aria-hidden="true"></i>';
            }
            $img         = zib_get_taxonomy_img_url($term->term_id, null, $default_img);
            $name        = zib_str_cut($term->name, 0, 8, '...');
            $count       = (int) $term->count ? (int) $term->count : 0;
            $description = zib_str_cut($term->description, 0, 24, '...');

            $href = get_term_link($term);
            $card = array(
                'type'         => $instance['type'],
                'class'        => 'mb10',
                'img'          => $img,
                'alt'          => $name . '-' . $description,
                'link'         => array(
                    'url'    => $href,
                    'target' => $target_blank,
                ),
                'text1'        => $name,
                'text2'        => $description,
                'text3'        => $icon . $count . '篇文章',
                'lazy'         => true,
                'height_scale' => $instance['height_scale'],
                'mask_opacity' => $instance['mask_opacity'],
            );

            if ($instance['type'] == 'style-2') {
                $card['text1'] = $name;
                $card['text2'] = $description;
                $card['text3'] = '<item data-toggle="tooltip" title="共' . $count . '篇文章">' . $icon . $count . '</item>';
            } elseif ($instance['type'] == 'style-3') {
                $card['text1'] = $icon . $name;
                $card['text2'] = $description;
                $card['text3'] = '<i class="fa mr6 fa-file-text-o"></i>' . $count . '篇文章';
            } elseif ($instance['type'] == 'style-4') {
                $card['text1'] = $icon . $name;
                $card['text2'] = $description;
                $card['text3'] = '<item data-toggle="tooltip" title="共' . $count . '篇文章">' . $icon . $count . '</item>';
            }
            $html .= $is_row ? '<div class="' . $row_class . '">' : '';
            $html .= zib_graphic_card($card);
            $html .= $is_row ? '</div>' : '';
        }
    }

    Zib_CFSwidget::echo_before($instance, ($is_row ? 'mb10' : 'mb20'));
    echo $is_row ? '<div class="row gutters-5">' : '';
    echo $html;
    echo $is_row ? '</div>' : '';
    Zib_CFSwidget::echo_after($instance);
}

//分类、专题聚合模块
Zib_CFSwidget::create('zib_widget_ui_term_lists_card', array(
    'title'       => '专题&分类聚合卡片',
    'zib_title'   => true,
    'zib_affix'   => true,
    'zib_show'    => true,
    'description' => '将分类、专题以及文字内容显示为卡片',
    'fields'      => array(
        array(
            'id'      => 'pc_row',
            'title'   => '单行布局',
            'desc'    => '请根据此模块放置位置的宽度合理调整单行数量',
            'class'   => 'compact',
            'default' => 2,
            'max'     => 2,
            'min'     => 1,
            'step'    => 1,
            'unit'    => '个',
            'type'    => 'slider',
        ),
        array(
            'id'          => 'term_id',
            'title'       => '添加分类、专题',
            'desc'        => '选择并排序需要的分类、专题，如选择的分类(专题)下没有文章则不会显示',
            'options'     => 'categories',
            'query_args'  => array(
                'taxonomy'   => array('topics', 'category'),
                'orderby'    => 'taxonomy',
                'hide_empty' => false,
            ),
            'placeholder' => '输入关键词以搜索分类或专题',
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'chosen'      => true,
            'multiple'    => true,
            'sortable'    => true,
            'type'        => 'select',
        ),
        array(
            'dependency' => array('term_id', '!=', ''),
            'id'         => 'orderby',
            'default'    => 'modified',
            'title'      => '排序方式',
            'type'       => "select",
            'options'    => CFS_Module::posts_orderby(),
        ),
        array(
            'dependency' => array('term_id', '!=', ''),
            'id'         => 'count',
            'title'      => '最大文章数量',
            'desc'       => '请确保所选的分类或专题内的文章数量均超过此数量',
            'default'    => 4,
            'max'        => 20,
            'min'        => 1,
            'step'       => 1,
            'unit'       => '篇',
            'type'       => 'spinner',
        ),
        array(
            'dependency' => array('term_id', '!=', ''),
            'title'      => '新窗口打开',
            'id'         => 'target_blank',
            'type'       => 'switcher',
            'default'    => false,
        ),
    ),
));

//
function zib_widget_ui_term_lists_card($args, $instance)
{
    $show_class = Zib_CFSwidget::show_class($instance);
    if (empty($instance['term_id'][0]) || !$show_class) {
        return;
    }

    //准备栏目
    $pc_row = (int) $instance['pc_row'];

    $row_class = 'col-sm-' . (int) (12 / $pc_row);
    $row_class .= ' col-xs-12';

    $is_row       = count($instance['term_id']) > 1;
    $target_blank = !empty($instance['target_blank']) ? '_blank' : '';
    $html         = '';
    if ($instance['term_id']) {
        foreach ($instance['term_id'] as $term_id) {
            $term_args = array(
                'term_id'      => $term_id,
                'class'        => '',
                'target_blank' => $target_blank,
                'orderby'      => $instance['orderby'],
                'count'        => $instance['count'],
            );
            $html .= $is_row ? '<div class="' . $row_class . '">' : '';
            $html .= zib_term_aggregation($term_args);
            $html .= $is_row ? '</div>' : '';
        }
    }

    Zib_CFSwidget::echo_before($instance, '');
    echo $is_row ? '<div class="row gutters-5">' : '';
    echo $html;
    echo $is_row ? '</div>' : '';
    Zib_CFSwidget::echo_after($instance);
}

//热榜文章
Zib_CFSwidget::create('zib_widget_ui_hot_posts', array(
    'title'       => '热榜文章',
    'zib_title'   => true,
    'zib_affix'   => true,
    'zib_show'    => true,
    'size'        => 'mini',
    'description' => '显示文章榜单排名，此模块适合放置在侧边栏或移动菜单内',
    'fields'      => array(
        array(
            'id'      => 'orderby',
            'default' => 'views',
            'title'   => '榜单类型',
            'type'    => "select",
            'options' => array(
                'views'         => '热门榜单(按阅读量排序)',
                'like'          => '超赞榜单(按点赞量排序)',
                'comment_count' => '话题榜单(按评论量排序)',
                'favorite'      => '收藏榜单(按收藏量排序)',
            ),
        ),
        array(
            'id'      => 'limit_day',
            'title'   => '限制时间(最近X天)',
            'desc'    => '设置多少天内发布的文章有效，为0则不限制时间',
            'default' => 0,
            'max'     => 999999,
            'min'     => 0,
            'step'    => 1,
            'unit'    => '天',
            'type'    => 'spinner',
        ),
        array(
            'id'      => 'count',
            'title'   => '最大显示数量',
            'default' => 6,
            'max'     => 20,
            'min'     => 1,
            'step'    => 1,
            'unit'    => '篇',
            'type'    => 'spinner',
        ),
        array(
            'title'   => '新窗口打开',
            'id'      => 'target_blank',
            'type'    => 'switcher',
            'default' => false,
        ),
    ),
));

//
function zib_widget_ui_hot_posts($args, $instance)
{
    $show_class = Zib_CFSwidget::show_class($instance);
    if (!$show_class) {
        return;
    }

    $html         = zib_hot_posts($instance);
    $args['size'] = 'mini';
    Zib_CFSwidget::echo_before($instance, '', $args);
    echo $html;
    Zib_CFSwidget::echo_after($instance, $args);
}

//付费商品
Zib_CFSwidget::create('zib_widget_ui_posts_pay', array(
    'title'       => '付费购买',
    'zib_title'   => false,
    'zib_affix'   => true,
    'zib_show'    => false,
    'size'        => 'mini',
    'description' => '显示当前文章的付费购买模块，推荐放置在侧边栏',
    'fields'      => array(
        array(
            'id'      => 'theme',
            'title'   => '色彩主题',
            'class'   => 'skin-color',
            'default' => "jb-red",
            'type'    => "palette",
            'options' => array(
                'jb-red'    => array('linear-gradient(135deg, #ffbeb4 10%, #f61a1a 100%)'),
                'jb-yellow' => array('linear-gradient(135deg, #ffd6b2 10%, #ff651c 100%)'),
                'jb-blue'   => array('linear-gradient(135deg, #b6e6ff 10%, #198aff 100%)'),
                'jb-green'  => array('linear-gradient(135deg, #ccffcd 10%, #52bb51 100%)'),
                'jb-purple' => array('linear-gradient(135deg, #fec2ff 10%, #d000de 100%)'),
                'jb-vip1'   => array('linear-gradient(25deg, #eab869 10%, #fbecd4 60%, #ffe0ae 100%)'),
                'jb-vip2'   => array('linear-gradient(317deg, #4d4c4c 30%, #878787 70%, #5f5c5c 100%)'),
            ),
        ),
    ),
));

//
function zib_widget_ui_posts_pay($args, $instance)
{
    $args['size'] = 'mini';
    $html         = zibpay_get_widget_box($instance);
    Zib_CFSwidget::echo_before($instance, '', $args);
    echo $html;
    Zib_CFSwidget::echo_after($instance, $args);
}
