<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-11-11 11:41:45
 * @LastEditTime: 2022-04-16 18:05:38
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//文档导航模板
CSF::createMetabox('documentnav_options', array(
    'title'          => '文档导航页面设置',
    'post_type'      => 'page',
    'context'        => 'side',
    'data_type'      => 'serialize',
    'page_templates' => 'pages/documentnav.php',
));
CSF::createSection('documentnav_options', array(
    'fields' => array(
        array(
            'type'    => 'submessage',
            'style'   => 'info',
            'content' => '选择一个一级分类，用于此页面的内容显示，系统会自动获取该分类下的二级分类以及文章，适合用作产品文档、帮助文档等页面',
        ),
        array(
            'id'          => 'cat',
            'title'       => '选择分类',
            'class'       => 'compact',
            'default'     => '',
            'options'     => 'categories',
            'placeholder' => '选择分类',
            'subtitle'    => '请选择一个一级分类',
            'type'        => 'select',
        ),
        array(
            'id'      => 'initial_content',
            'title'   => '初始内容',
            'default' => 'updated_posts',
            'type'    => 'select',
            'options' => array(
                'page_content'  => __('显示页面内容'),
                'date_posts'    => __('最近发布文章'),
                'updated_posts' => __('最近更新文章'),
                'views_posts'   => __('查看最多文章'),
            ),
        ),
    ),
));

//链接列表模板
function zib_cfs_link_category()
{
    $options_linkcats     = array();
    $options_linkcats[0]  = '全部选择';
    $options_linkcats_obj = get_terms('link_category');
    foreach ($options_linkcats_obj as $tag) {
        $options_linkcats[$tag->term_id] = $tag->name;
    }
    return $options_linkcats;
}

CSF::createMetabox('links_templates', array(
    'title'          => '链接列表页面设置',
    'post_type'      => 'page',
    'context'        => 'side',
    'data_type'      => 'unserialize',
    'page_templates' => 'pages/links.php', // Spesific page template
));

CSF::createSection('links_templates', array(
    'fields' => array(
        array(
            'type'    => 'submessage',
            'style'   => 'info',
            'content' => '用于显示链接的页面，支持链接提交模块，可用于创建‘友情链接’、‘链接导航’等页面 ',
        ),
        array(
            'title'   => __('显示页面内容', 'zib_language'),
            'id'      => 'page_links_content_s',
            'type'    => "switcher",
            'default' => false,
        ),
        array(
            'dependency' => array('page_links_content_s', '!=', ''),
            'id'         => 'page_links_content_position',
            'title'      => ' ',
            'subtitle'   => '显示位置',
            'default'    => 'top',
            'class'      => 'compact',
            'inline'     => true,
            'type'       => 'radio',
            'options'    => array(
                'top'    => __('链接列表上面'),
                'bottom' => __('链接列表下面'),
            ),
        ),
        array(
            'id'      => 'page_links_orderby',
            'title'   => '链接排序方式',
            'default' => 'name',
            'type'    => 'select',
            'options' => array(
                'name'    => __('名称排序'),
                'updated' => __('更新时间'),
                'rating'  => __('链接评分'),
                'rand'    => __('随机排序'),
            ),
        ),
        array(
            'id'       => 'page_links_order',
            'title'    => ' ',
            'subtitle' => ' ',
            'default'  => 'ASC',
            'class'    => 'compact',
            'inline'   => true,
            'type'     => 'radio',
            'options'  => array(
                'ASC'  => __('升序'),
                'DESC' => __('降序'),
            ),
        ),
        array(
            'title'   => '限制数量',
            'class'   => 'compact',
            'id'      => 'page_links_limit',
            'default' => '-1',
            'type'    => 'spinner',
            'min'     => -1,
            'step'    => 5,
            'unit'    => '个',
            'desc'    => '最多显示多少个链接，“-1”则为不限制',
        ),
        array(
            'id'      => 'page_links_category',
            'title'   => '显示分类',
            'default' => '0',
            'type'    => 'select',
            'options' => 'zib_cfs_link_category',
        ),
        array(
            'title'   => __('提交链接模块', 'zib_language'),
            'id'      => 'page_links_submit_s',
            'type'    => "switcher",
            'default' => false,
        ),
        array(
            'dependency' => array('page_links_submit_s', '!=', ''),
            'title'      => ' ',
            'subtitle'   => '提交链接模块：标题',
            'id'         => 'page_links_submit_title',
            'class'      => 'compact',
            'default'    => '提交链接',
            'type'       => 'text',
        ),
        array(
            'dependency' => array('page_links_submit_s', '!=', ''),
            'title'      => ' ',
            'subtitle'   => '提交链接模块：副标题',
            'id'         => 'page_links_submit_subtitle',
            'class'      => 'compact',
            'default'    => '提交链接',
            'type'       => 'text',
        ),
        array(
            'dependency' => array('page_links_submit_s', '!=', ''),
            'id'         => 'page_links_submit_dec',
            'title'      => ' ',
            'subtitle'   => '提交链接模块：提交说明',
            'class'      => 'compact',
            'default'    => '<p>
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
            'attributes' => array(
                'rows' => 6,
            ),
            'sanitize'   => false,
            'type'       => 'textarea',
        ),
    ),
));

//外链特色图像
CSF::createMetabox('bbs_thumbnail', array(
    'title'     => '外链特色图像',
    'post_type' => array('post', 'page'),
    'context'   => 'side',
    'data_type' => 'unserialize',
));
CSF::createSection('bbs_thumbnail', array(
    'fields' => array(
        array(
            'id'      => 'thumbnail_url',
            'library' => 'image',
            'type'    => 'upload',
            'default' => false,
            'desc'    => '支持直接输入链接用作特色图像<br/><span style="color:#ff4646;">此处设置仅在未设置wp特色图片时有效</span>',
        ),
    ),
));

//文章扩展
CSF::createMetabox('posts_main', array(
    'title'     => '文章扩展',
    'post_type' => array('post'),
    'context'   => 'side',
    'data_type' => 'unserialize',
));
if (_pz('article_image_cover')) {
    CSF::createSection('posts_main', array(
        'fields' => array(
            array(
                'title'   => '封面图',
                'id'      => 'cover_image',
                'library' => 'image',
                'type'    => 'upload',
                'default' => false,
                'desc'    => '在文章页顶部显示封面图',
            ),
        ),
    ));
}
if (_pz('list_thumb_slides_s') || _pz('article_slide_cover')) {
    CSF::createSection('posts_main', array(
        'fields' => array(
            array(
                'title'       => '特色幻灯片',
                'id'          => 'featured_slide',
                'type'        => 'gallery',
                'add_title'   => '添加图像',
                'edit_title'  => '编辑图像',
                'clear_title' => '清空图像',
                'default'     => false,
                'desc'        => '为该文章显示幻灯片封面或幻灯片略图（优先级>特色图像及封面图）',
            ),
        ),
    ));
}
if (_pz('list_thumb_video_s') || _pz('article_video_cover')) {
    CSF::createSection('posts_main', array(
        'fields' => array(
            array(
                'title'   => '特色视频',
                'id'      => 'featured_video',
                'type'    => 'upload',
                'preview' => false,
                'library' => 'video',
                'default' => false,
                'desc'    => '为该文章显示视频封面（优先级>幻灯片封面）',
            ),
            array(
                'dependency'  => array('featured_video', '!=', ''),
                'id'          => 'featured_video_title',
                'title'       => ' ',
                'subtitle'    => '本集标题',
                'desc'        => '如需添加剧集则需填写此处',
                'default'     => '',
                'placeholder' => '第1集',
                'class'       => 'compact',
                'type'        => 'text',
            ),
            array(
                'dependency'   => array('featured_video', '!=', '', '', 'visible'),
                'id'           => 'featured_video_episode',
                'type'         => 'group',
                'button_title' => '添加剧集',
                'class'        => 'compact',
                'title'        => '视频剧集',
                'subtitle'     => '为视频封面添加更多剧集',
                'default'      => array(),
                'fields'       => array(
                    array(
                        'id'       => 'title',
                        'title'    => ' ',
                        'subtitle' => '剧集标题',
                        'default'  => '',
                        'type'     => 'text',
                    ),
                    array(
                        'title'       => ' ',
                        'subtitle'    => '视频地址',
                        'id'          => 'url',
                        'class'       => 'compact',
                        'type'        => 'upload',
                        'preview'     => false,
                        'library'     => 'video',
                        'placeholder' => '选择视频或填写视频地址',
                        'default'     => false,
                    ),

                ),
            ),
        ),
    ));
}

//获取一个随机数
function zib_get_mt_rand_number($var = array())
{
    $defaults = array(
        'max' => 0,
        'min' => 0,
    );
    $var = wp_parse_args((array) $var, $defaults);

    return @mt_rand((int) $var['min'], (int) $var['max']);
}

CSF::createSection('posts_main', array(
    'fields' => array(
        array(
            'id'    => 'subtitle',
            'type'  => 'text',
            'title' => '副标题',
        ),
        array(
            'id'       => 'views',
            'type'     => 'number',
            'title'    => '阅读量',
            'default'  => zib_get_mt_rand_number(_pz('post_default_mate', '', 'views')),
            'validate' => 'csf_validate_numeric',
        ),
        array(
            'id'       => 'like',
            'type'     => 'number',
            'title'    => '点赞数',
            'default'  => zib_get_mt_rand_number(_pz('post_default_mate', '', 'like')),
            'validate' => 'csf_validate_numeric',
        ),
        array(
            'id'      => 'show_layout',
            'type'    => 'radio',
            'title'   => '显示布局',
            'default' => 'false',
            'options' => array(
                'false'         => '跟随主题',
                'no_sidebar'    => '无侧边栏',
                'sidebar_left'  => '侧边栏靠左',
                'sidebar_right' => '侧边栏靠右',
            ),
        ),
        array(
            'id'    => 'no_article-navs',
            'type'  => 'checkbox',
            'label' => '不显示目录树',
        ),
        array(
            'id'    => 'article_maxheight_xz',
            'type'  => 'checkbox',
            'label' => '限制内容最大高度',
        ),
    ),
));

//页面扩展
CSF::createMetabox('page_main', array(
    'title'     => '页面扩展',
    'post_type' => array('page'),
    'context'   => 'side',
    'data_type' => 'unserialize',
));
CSF::createSection('page_main', array(
    'fields' => array(
        array(
            'id'      => 'show_layout',
            'type'    => 'radio',
            'title'   => '显示布局',
            'default' => '',
            'options' => array(
                ''              => '跟随主题',
                'no_sidebar'    => '无侧边栏',
                'sidebar_left'  => '侧边栏靠左',
                'sidebar_right' => '侧边栏靠右',
            ),
        ),
        array(
            'id'      => 'page_header_style',
            'type'    => 'radio',
            'title'   => '标题样式',
            'default' => '',
            'options' => array(
                '' => __('跟随主题', 'zib_language'),
                1  => __('简单样式', 'zib_language'),
                2  => __('卡片样式', 'zib_language'),
                3  => __('图文样式', 'zib_language'),
            ),
        ),
    ),
));

if ((_pz('xzh_post_on') || _pz('xzh_post_daily_push')) && _pz('xzh_post_token')) {
    CSF::createMetabox('baidu_resource_submission', array(
        'title'     => '百度资源提交',
        'post_type' => array('post', 'page', 'plate', 'forum_post'),
        'context'   => 'advanced',
        'data_type' => 'unserialize',
    ));
    CSF::createSection('baidu_resource_submission', array(
        'fields' => array(
            array(
                'title'   => __('百度资源提交', 'zib_language'),
                'type'    => 'content',
                'content' => zib_get_baidu_resource_submission_metabox(),
            ),
        ),
    ));

    //为term添加百度资源提交
    CSF::createTaxonomyOptions('term_baidu_resource_submission', array(
        'title'     => '百度资源提交',
        'taxonomy'  => ['category','post_tag','topics','plate_cat','forum_topic','forum_tag'],
        'data_type' => 'unserialize',
    ));
    CSF::createSection('term_baidu_resource_submission', array(
        'fields' => array(
            array(
                'title'   => __('百度资源提交', 'zib_language'),
                'type'    => 'content',
                'content' => zib_get_baidu_resource_submission_metabox(false),
            ),
        ),
    ));

}

function zib_get_baidu_resource_submission_metabox($is_post = true)
{
    if ($is_post) {
        if (isset($_GET['post'])) {
            $post_id = (int) $_GET['post'];
        } elseif (isset($_POST['post_ID'])) {
            $post_id = (int) $_POST['post_ID'];
        } else {
            $post_id = 0;
        }
        $tui = get_post_meta($post_id, 'xzh_tui_back', true);
    } else {
        if (isset($_GET['tag_ID'])) {
            $term_id = (int) $_GET['tag_ID'];
        } else {
            $term_id = 0;
        }
        $tui = get_term_meta($term_id, 'xzh_tui_back', true);
    }

    $Resubmit  = '';
    $show_text = '';
    if (!empty($tui['normal_push'])) {
        $show_text .= '<strong>普通收录：成功</strong> ' . json_encode($tui['normal_result']) . '<br>';
    } elseif (isset($tui['normal_push']) && false == $tui['normal_push']) {
        $show_text .= '<strong>普通收录：失败</strong> ' . json_encode($tui['normal_result']) . '<br>';
    }
    if (!empty($tui['daily_push'])) {
        $show_text .= '<strong>快速收录：成功</strong> ' . json_encode($tui['daily_result']) . '<br>';
    } elseif (isset($tui['daily_push']) && false == $tui['daily_push']) {
        $show_text .= '<strong>快速收录：失败</strong> ' . json_encode($tui['daily_result']) . '<br>';
    }
    if (!empty($tui['update_time'])) {
        $show_text .= '<strong>更新时间：</strong>' . $tui['update_time'] . '<br>';
        $Resubmit = '<span style="margin:0 20px 15px 0; display:inline-block;"><label><input type="checkbox" name="xzh_post_resubmit"> 重新提交</label></span>';
    }
    if (strstr(json_encode($tui), '成功') || strstr(json_encode($tui), '失败')) {
        $show_text .= json_encode($tui) . '<br>';
    }
    if ($show_text) {
        $show_text = '<div>提交结果:</div>' . $show_text;
    } else {
        $show_text = '发布、更新后刷新页面后可查看提交结果';
    }

    return $Resubmit . $show_text;
}

//文章和页面seo
if (_pz('post_keywords_description_s')) {
    CSF::createMetabox('posts_seo', array(
        'title'     => '独立SEO',
        'post_type' => array('post', 'page', 'plate', 'forum_post'),
        'context'   => 'advanced',
        'data_type' => 'unserialize',
    ));
    CSF::createSection('posts_seo', array(
        'fields' => array(
            array(
                'title'   => __('SEO预览', 'zib_language'),
                'type'    => 'content',
                'content' => zib_get_seo_preview_box(),
            ),
            array(
                'title' => __('标题', 'zib_language'),
                'id'    => 'title',
                'desc'  => 'Title 一般建议15到30个字符',
                'std'   => '',
                'type'  => 'text',
            ),
            array(
                'title' => __('关键词', 'zib_language'),
                'id'    => 'keywords',
                'desc'  => 'Keywords 每个关键词用逗号隔开',
                'std'   => '',
                'type'  => 'text',
            ),
            array(
                'title' => __('描述', 'zib_language'),
                'id'    => 'description',
                'desc'  => 'Description 一般建议50到150个字符',
                'std'   => '',
                'type'  => 'textarea',
            ),
            array(
                'type'       => 'accordion',
                'id'         => 'accordion',
                'accordions' => array(
                    array(
                        'title'  => 'SEO优化建议',
                        'icon'   => 'fas fa-star',
                        'fields' => array(
                            array(
                                'title'   => ' ',
                                'type'    => 'content',
                                'content' => '<div style="color:#048cf0;margin-bottom:5px;">SEO标题优化建议：</div>
                                <li>主题默认会自动获取标题、副标题、网站名称作为SEO标题</li>
                                <li>标题内容应该紧扣页面的主要内容有吸引力</li>
                                <li>网站标题不要有过多的重复</li>
                                <li>第一个词放最重要的关键词</li>
                                <li>关键词只能重复2次，不要堆砌关键词</li>
                                <li>最后一个词放品牌词，不重要的词语</li>
                                <div style="color:#048cf0;margin-bottom:5px;margin-top:15px;">SEO关键词优化建议：</div>
                                <li>主题默认会自动获取分类及标签作为关键词，页面请单独自定义</li>
                                <li>关键词一般建议4到8个</li>
                                <li>尽量与网站定位一致</li>
                                <li>添加网站专属关键词</li>
                                <div style="color:#048cf0;margin-bottom:5px;margin-top:15px;">SEO描述优化建议：</div>
                                <li>主题默认会自动获取摘要、内容为SEO描述</li>
                                <li>description是对网页内容的精练概括</li>
                                <li>写成一段通顺有意义的话，要有吸引力</li>
                                <li>建议加入多个关键词，但不宜重复太多</li>
                                <div style="color:#f7497e;margin-bottom:5px;margin-top:15px;">优化建议来自互联网，仅供参考</div>',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ));
}

function zib_get_seo_preview_box($type = 'post')
{
    $title       = '';
    $keywords    = '';
    $description = '';
    $html        = '';
    $permalink   = '';

    $after = (_pz('connector') ? _pz('connector') : '-') . get_bloginfo('name');
    if ($type == 'post') {
        if (isset($_GET['post'])) {
            $post_id = (int) $_GET['post'];
        } elseif (isset($_POST['post_ID'])) {
            $post_id = (int) $_POST['post_ID'];
        } else {
            $post_id = 0;
        }
        if ($post_id) {
            $post      = get_post($post_id);
            $permalink = get_permalink($post);

            $title = get_post_meta($post->ID, 'title', true);
            $title = $title ? $title : $post->post_title . get_post_meta($post->ID, 'subtitle', true) . $after;

            $keywords = get_post_meta($post->ID, 'keywords', true);

            if (!$keywords) {
                if (get_the_tags($post->ID)) {
                    foreach (get_the_tags($post->ID) as $tag) {
                        $keywords .= $tag->name . ', ';
                    }
                }
                foreach (get_the_category($post->ID) as $category) {
                    $keywords .= $category->cat_name . ', ';
                }
                $keywords = substr_replace($keywords, '', -2);
            }
            $description = get_post_meta($post->ID, 'description', true);
            if (!$description) {
                if (!empty($post->post_excerpt)) {
                    $description = $post->post_excerpt;
                } else {
                    $description = $post->post_content;
                }
                $description = trim(str_replace(array("\r\n", "\r", "\n", "　", " "), " ", str_replace("\"", "'", strip_tags($description))));

                /**删除短代码内容 */
                $description = preg_replace('/\[payshow.*payshow\]||\[hidecontent.*hidecontent\]||\[reply.*reply\]||\[postsbox.*\]/', '', $description);

                $description = mb_substr($description, 0, 200, 'utf-8');
                if (!$description) {
                    $description = get_bloginfo('name') . "-" . trim(wp_title('', false));
                }
            }
        }
    }
    $html .= '<style>
    .zib-widget.seo-preview {
        padding: 15px 20px;
        border-radius: 10px;
        max-width: 600px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    }
    .seo-title a{
        font-size: 18px;
        line-height: 22px;
        color: #2440b3;
        text-decoration: none;
    }
    .seo-description {
        margin:10px 0 5px 0;
    }
    .seo-keywords {
        opacity: .6;
        margin-top: 5px;
    }
    </style>';

    if (!$permalink) {
        return $html . '<div style=" text-align: center; padding: 30px 15px; color: #fc61a5; font-size: 14px; " class="zib-widget seo-preview"><div class="seo-title"><span class="dashicons dashicons-warning"></span> 请保存内容后 刷新页面查看SEO预览</div></div>';
    }
    $title       = $title ? $title : '<span style=" color: #fa4784; "><span class="dashicons dashicons-warning"></span> SEO标题或者文章标题为空</span>';
    $keywords    = $keywords ? $keywords : '<span style=" color: #fa4784; "><span class="dashicons dashicons-warning"></span> SEO关键词为空</span>';
    $description = $description ? $description : '<span style=" color: #fa4784; "><span class="dashicons dashicons-warning"></span> SEO描述或文章内容为空</span>';

    $html .= '<div class="zib-widget seo-preview">';
    $html .= '<div class="seo-header"></div>';
    $html .= '<div class="seo-title">';
    $html .= '<a class="" href="javascript:;">' . $title . '</a>';
    $html .= '</div>';

    $html .= '<div class="seo-description">' . $description . '</div>';
    $html .= '<a class="" href="javascript:;">' . $permalink . '</a>';
    $html .= '<div class="seo-keywords">';
    $html .= '<div class="">' . $keywords . '</div>';
    $html .= '</div>';

    $html .= '</div>';

    return $html;
}
