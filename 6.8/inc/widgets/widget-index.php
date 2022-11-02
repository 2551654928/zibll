<?php
add_action('widgets_init', 'unregister_d_widget');
function unregister_d_widget()
{
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Recent_Comments');
}
add_action('load-widgets.php', 'zib_register_widget_jsloader');

function zib_register_widget_jsloader()
{
    _jsloader(array('widget-set.min'));
    _cssloader(array('widget-set' => 'widget-set.min'));
}

$widgets = array(
    'more',
    'posts',
    'user',
    'slider',
);

foreach ($widgets as $widget) {
    $path = 'inc/widgets/widget-' . $widget . '.php';
    require_once get_theme_file_path($path);
}

// 注册小工具
if (function_exists('register_sidebar')) {
    $sidebars = array();
    $pags     = array(
        'home'   => '首页',
        'single' => '文章页',
        'cat'    => '分类页',
        'tag'    => '标签页',
        'search' => '搜索页',
        'page'   => '带侧边栏页面',
    );

    $poss = array(
        'top_fluid'      => '顶部全宽度',
        'top_content'    => '主内容上面',
        'bottom_content' => '主内容下面',
        'bottom_fluid'   => '底部全宽度',
        'sidebar'        => '侧边栏',
    );

    $sidebars[] = array(
        'name'        => '所有页面-顶部全宽度',
        'id'          => 'all_top_fluid',
        'description' => '显示在所有页面的顶部全宽度位置，由于位置较多，建议使用实时预览管理！',
    );

    $sidebars[] = array(
        'name'        => '所有页面-底部全宽度',
        'id'          => 'all_bottom_fluid',
        'description' => '显示在所有页面的底部全宽度位置，由于位置较多，建议使用实时预览管理！',
    );

    $sidebars[] = array(
        'name'        => '所有页面-侧边栏-顶部位置',
        'id'          => 'all_sidebar_top',
        'description' => '显示在所有侧边栏的最上面位置，由于位置较多，建议使用实时预览管理！',
    );

    $sidebars[] = array(
        'name'        => '所有页面-侧边栏-底部位置',
        'id'          => 'all_sidebar_bottom',
        'description' => '显示在所有侧边栏的最下面，由于位置较多，建议使用实时预览管理！',
    );

    $sidebars[] = array(
        'name'        => '所有页面-页脚区内部',
        'id'          => 'all_footer',
        'description' => '显示最底部页脚区域内部，由于位置较多，建议使用实时预览管理！',
    );

    foreach ($pags as $key => $value) {
        foreach ($poss as $poss_key => $poss_value) {
            $sidebars[] = array(
                'name'        => $value . '-' . $poss_value,
                'id'          => $key . '_' . $poss_key,
                'description' => '显示在 ' . $value . ' 的 ' . $poss_value . ' 位置，由于位置较多，建议使用实时预览管理！',
            );
        }
    }

    $pags_full = array(
        'author' => '作者页',
        'user'   => '用户中心',
        'msg'    => '消息中心',
    );

    foreach ($pags_full as $key => $value) {
        $sidebars[] = array(
            'name'        => $value . '-顶部全宽度',
            'id'          => $key . '_top_fluid',
            'description' => '显示在 ' . $value . ' 的 内容顶部 位置，由于位置较多，建议使用实时预览管理！',
        );
        $sidebars[] = array(
            'name'        => $value . '-底部全宽度',
            'id'          => $key . '_bottom_fluid',
            'description' => '显示在 ' . $value . ' 的 内容底部 位置，由于位置较多，建议使用实时预览管理！',
        );
        if ($key === 'user') {
            $sidebars[] = array(
                'name'        => $value . '-侧边栏顶部',
                'id'          => $key . '_sidebar_top',
                'description' => '显示在 ' . $value . ' 的 侧边栏顶部 位置，由于位置较多，建议使用实时预览管理！',
            );
            $sidebars[] = array(
                'name'        => $value . '-侧边栏底部',
                'id'          => $key . '_sidebar_bottom',
                'description' => '显示在 ' . $value . ' 的 侧边栏底部 位置，由于位置较多，建议使用实时预览管理！',
            );
        }
    }

    $sidebars[] = array(
        'name'        => '前台投稿—侧边栏顶部',
        'id'          => 'newposts_sidebar_top',
        'description' => '显示在前台投稿页面的侧边栏顶部，由于宽度较小，请勿添加大尺寸模块，同时会在移动端显示',
    );
    $sidebars[] = array(
        'name'        => '前台投稿—侧边栏底部',
        'id'          => 'newposts_sidebar_bottom',
        'description' => '显示在前台投稿页面的侧边栏底部，由于宽度较小，请勿添加大尺寸模块，同时会在移动端显示',
    );
    $sidebars[] = array(
        'name'        => '移动端—弹出菜单底部',
        'id'          => 'mobile_nav_fluid',
        'description' => '显示在移动端弹出的菜单内部的下方，由于宽度较小，请勿添加大尺寸模块',
    );

    foreach ($sidebars as $value) {
        register_sidebar(array(
            'name'          => $value['name'],
            'id'            => $value['id'],
            'description'   => $value['description'],
            'before_widget' => '<div class="zib-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
        ));
    }
    ;
}

//挂钩一个通用ajax获取小工具内容
function zib_ajax_widget_ui()
{
    $id          = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
    $index       = isset($_REQUEST['index']) ? $_REQUEST['index'] : 0;
    $all_options = get_option('widget_' . $id);
    call_user_func($id . '_ajax', $all_options[$index]);
    exit;
}
add_action('wp_ajax_ajax_widget_ui', 'zib_ajax_widget_ui');
add_action('wp_ajax_nopriv_ajax_widget_ui', 'zib_ajax_widget_ui');

function zib_cat_help()
{
    ?>
	<div>
		分类限制：<a class="cat-help-button" style="font-weight:bold;color: #ff0039;text-decoration:none;background: #ffe8e8;width: 1.5em;line-height: 1.5em;text-align: center;display: inline-block;border-radius: 50%;" href="javascript:;">?</a>
		<div class="cat-help-con" style="display:none;padding: 5px;border: 1px solid #ddd;margin: 5px 0;background: #f7f8f9;border-radius: 8px;font-size: 12px;">
			<p>分类限制通过分类的id进行分类筛选，可以选择某些分类或者排除某些分类。示例及id列表如下</p>
			<b>分类限制示例：</b>
			<p>
			<div>仅仅显示分类ID为"10"的文章</div>
			<div style="padding: 6px;background: #ececec">10</div>
			</p>
			<p>
			<div>显示包含分类ID为"10、11、12、13"的文章</div>
			<div style="padding: 6px;background: #ececec">10,11,12,13</div>
			</p>
			<p>
			<div>排除分类ID为"10、11、12、13"的文章</div>
			<div style="padding: 6px;background: #ececec">-10,-11,-12,-13</div>
			</p>
			<p>
			<div>排除分类ID为"10"的文章</div>
			<div style="padding: 6px;background: #ececec">-10</div>
			</p>
		</div>
	</div>
<?php
}
/**
 * 专题帮助
 *
 * @param string $name
 * @return void
 */

function zib_topics_help()
{
    ?>
	<div>
		专题选择：<a class="cat-help-button" style="font-weight:bold;color: #ff0039;text-decoration:none;background: #ffe8e8;width: 1.5em;line-height: 1.5em;text-align: center;display: inline-block;border-radius: 50%;" href="javascript:;">?</a>
		<div class="cat-help-con" style="display:none;padding: 5px;border: 1px solid #ddd;margin: 5px 0;background: #f7f8f9;border-radius: 8px;font-size: 12px;">
			<p>输入专题ID，输入多个ID请用英文逗号分割</p>
		</div>
	</div>
<?php
}
function zib_user_help($name = '')
{
    ?>
	<div>
		<?php echo $name; ?><a class="cat-help-button" style="font-weight:bold;color: #ff0039;text-decoration:none;background: #ffe8e8;width: 1.5em;line-height: 1.5em;text-align: center;display: inline-block;border-radius: 50%;" href="javascript:;">?</a>
		<div class="cat-help-con" style="display:none;padding: 5px;border: 1px solid #ddd;margin: 5px 0;background: #f7f8f9;border-radius: 8px;font-size: 12px;">
			<p>输入用户ID，输入多个ID请用英文逗号分割</p>
		</div>
	</div>
<?php
}

function zib_widget_option($type = 'cat', $selected = '')
{
    $html = '<option value="" ' . selected('', $selected, false) . '>未选择</option>';
    if ('cat' == $type) {
        $args = array(
            'orderby'    => 'count',
            'order'      => 'DESC',
            'hide_empty' => false,
        );
        $options_cat = get_categories($args);
        foreach ($options_cat as $category) {
            $title = rtrim(get_category_parents($category->cat_ID, false, '>'), '>') . '[ID:' . $category->cat_ID . '][共' . $category->count . '篇]';
            $_id   = $category->cat_ID;
            $html .= '<option value="' . $_id . '" ' . selected($_id, $selected, false) . '>' . $title . '</option>';
        }
    }
    return $html;
}
add_filter('zib_widget_title', 'zib_widget_filter_title', 11);
function zib_widget_filter_title($instance = array())
{
    $html     = '';
    $defaults = array(
        'title'        => '',
        'mini_title'   => '',
        'more_but'     => '',
        'more_but_url' => '',
    );
    $instance   = wp_parse_args((array) $instance, $defaults);
    $mini_title = $instance['mini_title'] ? '<small class="ml10 muted-color">' . $instance['mini_title'] . '</small>' : '';
    $title      = $instance['title'] ? $instance['title'] : '';
    $more_but   = '';
    if ($instance['more_but'] && $instance['more_but_url']) {
        $more_but = '<div class="pull-right em09 mt3"><a href="' . esc_url($instance['more_but_url']) . '" class="muted-2-color">' . $instance['more_but'] . '</a></div>';
    }
    $mini_title .= $more_but;
    if ($title) {
        $title = '<div class="box-body notop"><div class="title-theme">' . $title . $mini_title . '</div></div>';
    }
    return $title;
}
