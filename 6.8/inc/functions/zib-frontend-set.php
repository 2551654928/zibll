<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:38
 * @LastEditTime: 2022-09-26 00:14:08
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

/**开启条件 */
function zib_admin_frontend_set_s()
{
    $is_on = true;
    if (!_pz('admin_frontend_set', true) || !is_super_admin() || (!is_page() && !is_single())) {
        $is_on = false;
    }

    $pid = get_queried_object_id();
    if (!$pid) {
        $is_on = false;
    }

    if (is_page_template('pages/postsnavs.php')) {
        $is_on = false;
    }

    return apply_filters('frontend_set_switch', $is_on);
}

/**前台编辑按钮 */
function zib_admin_frontend_set_botton($float)
{
    if (zib_admin_frontend_set_s()) {
        $float .= '<a href="javascript:;" data-toggle="modal" data-target="#modal_admin_set" title="设置页面参数" class="admin-set-page float-btn"><i class="fa fa-cog fa-spin" aria-hidden="true"></i><div class="abs-right badg c-red px12 admin-set-info" style="width:187px;">在此编辑此页面参数</div></a>';
    }
    return $float;
}
add_filter('zib_float_right', 'zib_admin_frontend_set_botton', 10, 2);

// AJAX-前台编辑
function zib_frontend_set_modal()
{

    $post_id = get_queried_object_id();
    if (!zib_admin_frontend_set_s()) {
        return;
    }

    $header = '<div class="modal-header"><strong class="modal-title"><i class="fa fa-sliders mr10" aria-hidden="true"></i>页面设置</strong>
    <button class="close" data-dismiss="modal">' . zib_get_svg('close', null, 'ic-close') . '</button>
                </div>';
    $footer = '<div class="modal-footer">
                    <a target="_blank" href="' . get_edit_post_link($post_id) . '" class="but c-yellow padding-lg"><i class="fa fa-wordpress" aria-hidden="true"></i>后台编辑</a>
                    <button class="but jb-blue padding-lg wp-ajax-submit"><i class="fa fa-check" aria-hidden="true"></i>确认修改</button>
                </div>';

    $body = '<div class="modal-body">';
    $body .= zib_get_frontend_set_input($post_id);
    $body .= '<input type="hidden" name="action" value="frontend_set_save">';
    $body .= '<input type="hidden" name="post_id" value="' . $post_id . '">';
    $body .= '</div>';
    $body  = apply_filters('zib_frontend_set_modal_body', $body, $post_id);
    $modal = '<div class="modal fade" id="modal_admin_set" tabindex="-1" role="dialog" aria-hidden="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content page-set-modal">
                        <form>' . $header . $body . $footer . '</form>
                    </div>
                </div>
              </div>';
    echo $modal;
}
add_action('wp_footer', 'zib_frontend_set_modal', 10);

// 前台编辑的input
function zib_get_frontend_set_input($post_id)
{
    $page_input = array();

    $page_input[] = array(
        'name'    => __('显示布局', 'zib_language'),
        'id'      => 'show_layout',
        'std'     => get_post_meta($post_id, 'show_layout', true),
        'type'    => "radio",
        'options' => array(
            ''              => __('跟随主题', 'zib_language'),
            'no_sidebar'    => __('无侧边栏', 'zib_language'),
            'sidebar_left'  => __('侧边栏靠左', 'zib_language'),
            'sidebar_right' => __('侧边栏靠右', 'zib_language'),
        ),
    );

    $page_input[] = array(
        'name' => __('标题', 'zib_language'),
        'id'   => 'post_title',
        'std'  => get_the_title($post_id),
        'type' => 'text',
    );
    if (is_single()) {
        $page_input[] = array(
            'name' => __('副标题', 'zib_language'),
            'id'   => 'subtitle',
            'std'  => get_post_meta($post_id, 'subtitle', true),
            'type' => 'text',
        );
        $page_input[] = array(
            'name'    => __('文章格式', 'zib_language'),
            'id'      => 'post_format',
            'std'     => get_post_format($post_id),
            'type'    => "select",
            'options' => array(
                'standard' => __('标准', 'zib_language'),
                'image'    => __('图像', 'zib_language'),
                'gallery'  => __('画廊', 'zib_language'),
                'video'    => __('视频', 'zib_language'),
            ),
        );
        $page_input[] = array(
            'name' => __('点赞数', 'zib_language'),
            'id'   => 'like',
            'std'  => get_post_meta($post_id, 'like', true),
            'type' => 'number',
        );
        $page_input[] = array(
            'name' => __('阅读数', 'zib_language'),
            'id'   => 'views',
            'std'  => get_post_meta($post_id, 'views', true),
            'type' => 'number',
        );
        $page_input[] = array(
            'name' => __('目录树', 'zib_language'),
            'id'   => 'no_article-navs',
            'std'  => get_post_meta($post_id, 'no_article-navs', true),
            'type' => 'checkbox',
            'desc' => __('不显示', 'zib_language'),
        );
        $page_input[] = array(
            'name' => __('文章高度', 'zib_language'),
            'id'   => 'article_maxheight_xz',
            'std'  => get_post_meta($post_id, 'article_maxheight_xz', true),
            'type' => 'checkbox',
            'desc' => __('限制文章最大高度', 'zib_language'),
        );
    }
    $page_input[] = array(
        'name' => __('评论', 'zib_language'),
        'id'   => 'comments_open',
        'std'  => comments_open($post_id),
        'type' => 'checkbox',
        'desc' => __('允许评论', 'zib_language'),
    );
    if (is_page()) {
        $page_input[] = array(
            'name'    => __('标题样式', 'zib_language'),
            'id'      => 'page_header_style',
            'std'     => get_post_meta($post_id, 'page_header_style', true),
            'type'    => "select",
            'options' => array(
                '' => __('跟随主题', 'zib_language'),
                1  => __('简单样式', 'zib_language'),
                2  => __('卡片样式', 'zib_language'),
                3  => __('图文样式', 'zib_language'),
            ),
        );
    }

    /**添加挂钩 */
    $page_input = apply_filters('zib_frontend_set_input_array', $page_input, $post_id);
    $input      = zib_edit_input_construct($page_input);
    $input      = apply_filters('zib_frontend_set_input_html', $input, $post_id);

    return $input;
}

// AJAX-前台编辑保存
function zib_frontend_set_save_ajax()
{

    if (!is_super_admin() || !_pz('admin_frontend_set', true)) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '权限不足')));
        exit();
    }
    if (empty($_POST['post_id'])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '页面数据出错')));
        exit();
    }
    /**添加执行挂钩 */
    do_action('zib_frontend_set_save', $_POST['post_id']);
    echo (json_encode(array('msg' => '保存成功，正在刷新页面', 'reload' => true)));
    exit();
}
add_action('wp_ajax_frontend_set_save', 'zib_frontend_set_save_ajax');

function zib_frontend_set_save($post_id)
{
    /**update_post_meta的保存 */
    $update_post_meta = array('like', 'subtitle', 'views', 'show_layout', 'page_header_style');
    foreach ($update_post_meta as $meta) {
        if (isset($_POST[$meta])) {
            update_post_meta($post_id, $meta, $_POST[$meta]);
        }

    }
    /**checkbox的保存 */
    $update_post_meta_checkbox = array('article_maxheight_xz', 'no_article-navs', 'page_show_header');
    foreach ($update_post_meta_checkbox as $meta) {
        $v = empty($_POST[$meta]) ? '' : '1';
        update_post_meta($post_id, $meta, $v);
    }

    /**post_info的保存 */
    if (isset($_POST['post_format'])) {
        set_post_format($post_id, $_POST['post_format']);
    }

    $postarr = array(
        'ID'             => $post_id,
        'post_title'     => $_POST['post_title'],
        'comment_status' => empty($_POST['comments_open']) ? '' : 'open',
    );
    $in_id = wp_update_post($postarr, 1);
    if (is_wp_error($in_id)) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => $in_id->get_error_message())));
        exit();
    }
    return;
}
add_action('zib_frontend_set_save', 'zib_frontend_set_save');

/**链接列表页面模板设置 */
function zib_frontend_set_input_array_links($page_input)
{
    $post_id = get_queried_object_id();
    if (is_page_template('pages/links.php')) {
        $page_input[] = array(
            'name'  => __('页面内容', 'zib_language'),
            'class' => 'op-multicheck',
            'id'    => 'page_links_content_s',
            'std'   => get_post_meta($post_id, 'page_links_content_s', true),
            'desc'  => __('显示页面内容', 'zib_language'),
            'type'  => 'checkbox',
        );
        $page_input[] = array(
            'name'    => __('内容显示位置', 'zib_language'),
            'id'      => 'page_links_content_position',
            'std'     => get_post_meta($post_id, 'page_links_content_position', true),
            'type'    => "radio",
            'options' => array(
                'top'    => __('链接列表上面'),
                'bottom' => __('链接列表下面'),
            ),
        );

        $args_orderby  = get_post_meta($post_id, 'page_links_orderby', true);
        $args_order    = get_post_meta($post_id, 'page_links_order', true);
        $args_limit    = get_post_meta($post_id, 'page_links_limit', true);
        $args_category = get_post_meta($post_id, 'page_links_category', true);
        $page_input[]  = array(
            'name'    => __('排序方式', 'zib_language'),
            'id'      => 'page_links_orderby',
            'std'     => $args_orderby ? $args_orderby : 'name',
            'type'    => "select",
            'options' => array(
                'name'    => __('名称排序'),
                'updated' => __('更新时间'),
                'rating'  => __('链接评分'),
                'rand'    => __('随机排序'),
            ),
        );
        $page_input[] = array(
            'id'      => 'page_links_order',
            'std'     => $args_order ? $args_order : 'ASC',
            'type'    => "radio",
            'options' => array(
                'ASC'  => __('升序'),
                'DESC' => __('降序'),
            ),
        );
        $page_input[] = array(
            'name' => __('最多显示', 'zib_language'),
            'id'   => 'page_links_limit',
            'std'  => $args_limit ? $args_limit : -1,
            'type' => 'text',
            'desc' => __('填“-1”则不限制数量', 'zib_language'),
        );
        // 将所有链接拉入数组
        $options_linkcats     = array();
        $options_linkcats[0]  = '全部选择';
        $options_linkcats_obj = get_terms('link_category');
        foreach ($options_linkcats_obj as $tag) {
            $options_linkcats[$tag->term_id] = $tag->name;
        }
        $page_input[] = array(
            'name'    => __('链接分类', 'zib_language'),
            'id'      => 'page_links_category',
            'std'     => $args_category,
            'type'    => "select",
            'options' => $options_linkcats,
        );
        $page_input[] = array(
            'name'  => __('提交链接', 'zib_language'),
            'class' => 'op-multicheck',
            'id'    => 'page_links_submit_s',
            'std'   => get_post_meta($post_id, 'page_links_submit_s', true),
            'desc'  => __('显示提交链接模块', 'zib_language'),
            'type'  => 'checkbox',
        );
        $submit_defaults = array(
            'class'      => '',
            'title'      => '提交链接',
            'subtitle'   => '欢迎与我交换友情链接',
            'dec'        => '<p>
 <li>您的网站已稳定运行，且有一定的文章量 </li>
 <li>原创、技术、设计类网站优先考虑</li>
 <li>不收录有反动、色情、赌博等不良内容或提供不良内容链接的网站</li>
 <li>您需要将本站链接放置在您的网站中</li>
</p>
<p><b>本站信息示例：</b></p>
<ul>
 <li>名称：Zibll子比主题</li>
 <li>简介：更优雅的Wordpress网站主题</li>
 <li>链接：<a href="https://www.zibll.com/wp-admin/post.php?post=50&amp;action=edit">https://www.zibll.com</a></li>
 <li>图标：https://oss.zibll.com/zibll.com/2020/03/favicon-1.png</li>
</ul>',
            'show_title' => true,
        );

        $page_input[] = array(
            'id'   => 'page_links_submit_title',
            'std'  => get_post_meta($post_id, 'page_links_submit_title', true) ? get_post_meta($post_id, 'page_links_submit_title', true) : $submit_defaults['title'],
            'type' => 'text',
            'desc' => __('提交链接模块-标题', 'zib_language'),
        );
        $page_input[] = array(
            'id'   => 'page_links_submit_subtitle',
            'std'  => get_post_meta($post_id, 'page_links_submit_subtitle', true),
            'type' => 'text',
            'desc' => __('提交链接模块-副标题', 'zib_language'),
        );
        $page_input[] = array(
            'id'   => 'page_links_submit_dec',
            'std'  => get_post_meta($post_id, 'page_links_submit_dec', true) ? get_post_meta($post_id, 'page_links_submit_dec', true) : $submit_defaults['dec'],
            'type' => 'textarea',
            'desc' => __('提交链接模块-提交说明(支持HTML代码)', 'zib_language'),
        );
    }
    return $page_input;
}

function zib_frontend_set_save_links($post_id)
{
    /**checkbox的保存 */
    $update_post_meta_checkbox = array('page_links_content_s', 'page_links_submit_s', 'page_show_content');
    foreach ($update_post_meta_checkbox as $meta) {
        $v = empty($_POST[$meta]) ? '' : '1';
        update_post_meta($post_id, $meta, $v);
    }
    /**update_post_meta的保存 */
    $update_post_meta = array('page_links_content_position', 'page_links_orderby', 'page_links_order', 'page_links_limit', 'page_links_category', 'page_links_submit_title', 'page_links_submit_subtitle', 'page_links_submit_dec');
    foreach ($update_post_meta as $meta) {
        if (isset($_POST[$meta])) {
            update_post_meta($post_id, $meta, $_POST[$meta]);
        }

    }
}
add_filter('zib_frontend_set_input_array', 'zib_frontend_set_input_array_links');
add_action('zib_frontend_set_save', 'zib_frontend_set_save_links');

/**链接列表页面模板设置 */
function zib_frontend_set_input_array_download($page_input)
{
    $post_id = get_queried_object_id();
    if (is_page_template('pages/download.php')) {
        $page_input[] = array(
            'name'  => __('页面内容', 'zib_language'),
            'class' => 'op-multicheck',
            'id'    => 'page_show_content',
            'std'   => get_post_meta($post_id, 'page_show_content', true),
            'desc'  => __('显示页面内容', 'zib_language'),
            'type'  => 'checkbox',
        );
    }
    return $page_input;
}
add_filter('zib_frontend_set_input_array', 'zib_frontend_set_input_array_download');

/**input框架构建函数 */
function zib_edit_input_construct($input)
{
    /**完整示例 */
    $Examples[] = array(
        'name'        => '显示名称',
        'id'          => 'Examples_id',
        'class'       => "class",
        'question'    => "question",
        'type'        => "checkbox",
        'html'        => "<div>html</div>",
        'value'       => false,
        'std'         => false,
        'desc'        => 'desc',
        'placeholder' => 'placeholder',
        'options'     => array(
            'enlighter'  => __('默认浅色主题'),
            'bootstrap4' => __('浅色：Bootstrap'),
        ),
        'settings'    => array(
            'rows' => 3,
        ),
    );
    $output = '';
    foreach ($input as $meta) {
        $value_id    = isset($meta['id']) ? $meta['id'] : '';
        $std         = isset($meta['std']) ? $meta['std'] : '';
        $class       = isset($meta['class']) ? $meta['class'] : '';
        $question    = isset($meta['question']) ? $meta['question'] : '';
        $type        = isset($meta['type']) ? $meta['type'] : '';
        $placeholder = isset($meta['placeholder']) ? $meta['placeholder'] : '';
        $value       = '';
        $value       = isset($meta['value']) ? $meta['value'] : $std;
        $style       = isset($meta['style']) ? ' style="' . $meta['style'] . '"' : '';
        $class       = '';
        if (isset($meta['type'])) {
            $class .= ' option-' . $meta['type'];
        }
        if (isset($meta['class'])) {
            $class .= ' ' . $meta['class'];
        }
        $output .= '<div class="mb10 row ' . $class . '"' . $style . '>' . "\n";

        $output .= '<div class="heading col-xs-3 text-right">' . (isset($meta['name']) ? esc_html($meta['name']) : '') . '</div>' . "\n";

        $output .= '<div class="option col-xs-8">' . "\n";
        //echo json_encode($meta);
        switch ($type) {

            // Basic text input
            case 'text':
                $output .= '<input class="form-control" name="' . $value_id . '" type="text" value="' . esc_attr($value) . '"/>';
                break;

            // Password input
            case 'password':
                $output .= '<input class="form-control" name="' . $value_id . '" type="password" value="' . esc_attr($value) . '"/>';
                break;

            case 'html':
                $output .= $meta['html'];
                break;

            case 'number':
                $output .= '<input class="form-control" name="' . $value_id . '" type="number" value="' . esc_attr($value) . '"/>';
                break;

            case 'checkbox':
                $output .= '<span class="form-checkbox"><input $value="' . $value . '" name="' . $value_id . '" id="' . $value_id . '" type="checkbox" ' . zib_checked($value, 1, false) . '/><label for="' . $value_id . '" class="em09 muted-color ml6" style=" font-weight: normal; ">' . esc_html($meta['desc']) . '</label></span>';
                break;

            // Textarea
            case 'textarea':
                $rows = '4';

                if (isset($meta['settings']['rows'])) {
                    $custom_rows = $meta['settings']['rows'];
                    if (is_numeric($custom_rows)) {
                        $rows = $custom_rows;
                    }
                }

                $value = stripslashes($value);
                $output .= '<textarea class="form-control" name="' . $value_id . '" rows="' . $rows . '"' . $placeholder . '>' . esc_textarea($value) . '</textarea>';
                break;

            // Select Box
            case 'select':
                $output .= '<div class="form-select"><select class="form-control" name="' . $value_id . '">';

                foreach ($meta['options'] as $key => $option) {
                    $output .= '<option' . selected($value, $key, false) . ' value="' . esc_attr($key) . '">' . esc_html($option) . '</option>';
                }
                $output .= '</select></div>';
                break;

            // Radio Box
            case "radio":
                foreach ($meta['options'] as $key => $option) {
                    $output .= '<label class="mr10"><input type="radio" name="' . $value_id . '" value="' . esc_attr($key) . '" ' . checked($value, $key, false) . ' /><span class="ml6 em09 muted-color" style=" font-weight: normal; ">' . esc_html($option) . '</span></label>';
                }
                break;
        }

        if (!empty($meta['desc']) && $type != 'checkbox') {
            $desc = esc_html($meta['desc']);

            $output .= '<span class="mt6 em09 muted-2-color">' . $desc . '</span>' . "\n";
        }

        if ($question) {
            $output .= '<span class="ml10" data-toggle="tooltip" title="' . esc_attr($question) . '"><i class="fa fa-question-circle c-red" aria-hidden="true"></i></span>' . "\n";
        }

        $output .= '</div>' . "\n";
        $output .= '</div>' . "\n";
    }

    return $output;
    // echo  json_encode( $get_mate);
    //  echo json_encode( $this->args);
}

function zib_checked($value = '', $key = '1', $echo = 1)
{
    $checked = array('on', '1', $key);
    $html    = '';
    if (in_array($value, $checked)) {
        $html = ' checked="checked"';
    }

    if ($echo) {
        echo $html;
    }

    return $html;
}
