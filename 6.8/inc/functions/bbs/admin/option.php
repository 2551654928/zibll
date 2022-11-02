<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-05 20:25:29
 * @LastEditTime: 2022-10-08 22:50:54
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|后台功能配置
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

$prefix    = 'zibll_options';
$new_badge = zib_get_csf_option_new_badge();
CSF::createSection($prefix, array(
    'parent'      => 'forum',
    'title'       => '全局设置',
    'icon'        => 'fa fa-fw fa-forumbee',
    'description' => '',
    'fields'      => array(
        array(
            'content' => '<h4>欢迎使用子比社区论坛功能</h4>
            <li>论坛首页页面地址：<code>' . zib_bbs_get_home_url() . '</code></li>
            <li>如需将论坛首页设置为网站首页，可以在<a href="' . admin_url('options-reading.php') . '">WP设置-阅读</a>中将<code>主页显示：首页</code>设为论坛首页即可</li>
            <li class="c-yellow">如果您未将论坛首页设置为网站首页，请进入<a href="' . admin_url('edit.php?post_type=page') . '">页面->选择[论坛首页]->编辑</a>，添加论坛首页的SEO内容</li>
            <li>论坛系统的核心用户功能请在<a href="' . zib_get_admin_csf_url('功能&权限/论坛权限') . '">功能&权限/论坛权限</a>中进行设置</li>
            <li>论坛系统依赖于用户登录注册功能，如果关闭了<a href="' . zib_get_admin_csf_url('用户互动/注册登录') . '">注册登录功能</a>，则请同时关闭此功能</li>
            <li style="color:#ff5521;">论坛内容的添加、修改、管理、删除的大部分功能都可以在前台操作，强烈建议：如非必要，尽量在前台管理论坛内容！以避免逻辑错误！</li>
            <li><a target="_blank" href="https://www.zibll.com/3103.html">查看官方教程</a></li>
            ',
            'style'   => 'warning',
            'type'    => 'submessage',
        ),
        array(
            'title'   => '社区&论坛',
            'label'   => '启用社区论坛功能',
            'id'      => 'bbs_s',
            'default' => true,
            'type'    => 'switcher',
        ),
        array(
            'title'       => '论坛管理员',
            'id'          => 'bbs_admin_users',
            'options'     => 'user',
            'default'     => array(),
            'placeholder' => '输入用户名、昵称等关键词以搜索用户',
            'desc'        => '输入用户名、昵称等关键词以搜索用户<div style="color:#ff5521;"><i class="fa fa-fw fa-info-circle fa-fw"></i>论坛管理员拥有所有论坛能力权限，不含后台权限，非论坛权限则和常规用户一致，具体能力请参考<a href="' . zib_get_admin_csf_url('功能&权限/论坛权限') . '">论坛权限</a>设置</div>',
            'chosen'      => true,
            'multiple'    => true,
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'type'        => 'select',
        ),
        array(
            'title'    => '图像异步懒加载',
            'id'       => 'lazy_bbs_list_thumb',
            'default'  => true,
            'subtitle' => __('列表图懒加载', 'zib_language'),
            'help'     => '开启图片懒加载，当页面滚动到图像位置时候才加载图片，可极大的提高页面访问速度。',
            'type'     => 'switcher',
        ),
        array(
            'title'   => '版块新窗口打开',
            'id'      => 'plate_target_blank',
            'default' => false,
            'type'    => 'switcher',
        ),
        array(
            'title'   => '帖子新窗口打开',
            'id'      => 'posts_target_blank',
            'default' => false,
            'type'    => 'switcher',
        ),

        array(
            'title'  => '热门版块判断',
            'id'     => 'is_hot_plate',
            'type'   => 'fieldset',
            'fields' => array(
                array(
                    'title'   => '帖子数量大于',
                    'id'      => 'posts_count',
                    'default' => 20,
                    'type'    => 'spinner',
                    'step'    => 10,
                    'unit'    => '篇',
                ),
                array(
                    'title'   => '阅读量大于',
                    'id'      => 'views',
                    'class'   => 'compact',
                    'default' => 1000,
                    'type'    => 'spinner',
                    'step'    => 20,
                    'unit'    => '次',
                ),
                array(
                    'title'   => '回帖量大于',
                    'id'      => 'comment',
                    'class'   => 'compact',
                    'default' => 20,
                    'type'    => 'spinner',
                    'step'    => 5,
                    'unit'    => '条',
                ),
                array(
                    'title'   => '阅读量高于平均值',
                    'id'      => 'average',
                    'class'   => 'compact',
                    'default' => 0.6,
                    'type'    => 'spinner',
                    'step'    => 0.1,
                    'unit'    => '倍',
                    'desc'    => '判断热门版块的标准，同时满足以上要求时则为热门版块<br/>例如：当阅读量大约1000次，且回帖量大于20，且阅读量超过所在分类版块平均阅读量的0.5倍则为热门版块',
                ),
            ),
        ),
        array(
            'title'  => '热门帖子判断',
            'id'     => 'is_hot_posts',
            'type'   => 'fieldset',
            'fields' => array(
                array(
                    'title'   => '阅读量大于',
                    'id'      => 'views',
                    'default' => 100,
                    'type'    => 'spinner',
                    'step'    => 20,
                    'unit'    => '次',
                ),
                array(
                    'title'   => '评分大于',
                    'id'      => 'score',
                    'class'   => 'compact',
                    'default' => 10,
                    'type'    => 'spinner',
                    'step'    => 1,
                    'unit'    => '分',
                ),
                array(
                    'title'   => '回帖量大于',
                    'id'      => 'comment',
                    'class'   => 'compact',
                    'default' => 5,
                    'type'    => 'spinner',
                    'step'    => 5,
                    'unit'    => '条',
                ),
                array(
                    'title'   => '阅读量高于平均值',
                    'id'      => 'average',
                    'class'   => 'compact',
                    'default' => 0.6,
                    'type'    => 'spinner',
                    'step'    => 0.1,
                    'unit'    => '倍',
                    'desc'    => '判断热门帖子的标准，同时满足以上要求时则为热门帖子<br/>例如：当阅读量大约100次，且回帖量大于5，且阅读量超过所在版块帖子平均阅读量的0.5倍则为热门帖子',
                ),
            ),
        ),
        array(
            'title'  => '热门评论判断',
            'id'     => 'is_hot_comment',
            'type'   => 'fieldset',
            'fields' => array(
                array(
                    'title'   => '点赞数大于',
                    'id'      => 'like',
                    'default' => 10,
                    'type'    => 'spinner',
                    'step'    => 5,
                    'unit'    => '次',
                    'desc'    => '判断热门评论的标准，点赞数大于设定值且最多点赞的为热门评论<br/>每一篇帖子只会有一个热门评论',
                ),
            ),
        ),
        array(
            'title'   => '帖子单页数量',
            'id'      => 'bbs_posts_per_page',
            'default' => 20,
            'min'     => 6,
            'step'    => 1,
            'unit'    => '篇',
            'desc'    => '每页显示的帖子数量',
            'type'    => 'spinner',
        ),
        array(
            'id'      => 'bbs_posts_paginate_type',
            'title'   => '帖子列表翻页模式',
            'default' => 'default',
            'type'    => "radio",
            'inline'  => true,
            'options' => array(
                'ajax_lists' => __('AJAX追加列表翻页', 'zib_language'),
                'default'    => __('数字翻页按钮', 'zib_language'),
            ),
        ),
        array(
            'dependency' => array('bbs_posts_paginate_type', '==', 'ajax_lists'),
            'title'      => ' ',
            'subtitle'   => 'AJAX翻页自动加载',
            'class'      => 'compact',
            'id'         => 'bbs_posts_paginate_ias_s',
            'type'       => 'switcher',
            'label'      => '页面滚动到列表尽头时，自动加载下一页',
            'default'    => true,
        ),
        array(
            'dependency' => array('bbs_posts_paginate_type|bbs_posts_paginate_ias_s', '==|!=', 'ajax_lists|'),
            'title'      => ' ',
            'subtitle'   => '自动加载页数',
            'desc'       => 'AJAX翻页自动加载最多加载几页（为0则不限制，直到加载全部评论）',
            'id'         => 'bbs_posts_paginate_ias_max',
            'class'      => 'compact',
            'default'    => 3,
            'max'        => 10,
            'min'        => 0,
            'step'       => 1,
            'unit'       => '页',
            'type'       => 'spinner',
        ),
        array(
            'id'      => 'bbs_thumb_size',
            'title'   => '列表缩略图大小',
            'default' => 'medium',
            'desc'    => '此处的三个尺寸均可在<a href="' . admin_url('options-media.php') . '">WP后台-媒体设置</a>中修改，建议此处选择中尺寸，并将中尺寸的尺寸设置为700x490效果最佳
            <div class="c-yellow">当此处设置不为“文章原图”时，强烈建议使用Redis或Memcached缓存插件，能极大的提高执行效率 | <a target="_blank" href="https://www.zibll.com/1997.html">查看官网教程</a></div>',
            'type'    => "radio",
            'inline'  => true,
            'options' => array(
                'thumbnail' => __('小尺寸', 'zib_language'),
                'medium'    => __('中尺寸', 'zib_language'),
                'large'     => __('大尺寸', 'zib_language'),
                ''          => __('文章原图', 'zib_language'),
            ),
        ),
    ),
));

CSF::createSection($prefix, array(
    'parent'      => 'forum',
    'title'       => '名称定义',
    'icon'        => 'fa fa-fw fa-retweet',
    'description' => '',
    'fields'      => array(
        array(
            'content' => '<h4>功能属性名称定义注意事项</h4>
            <li>在此处您可以自定义功能的名称，实现不同的功能效果</li>
            <li>不同逻辑的功能除了设置不同的名称，还需要同时设置合理的用户权限</li>
            <li>在自定义之前，请先熟悉各项属性的逻辑以及对应的内容</li>
            <li>默认逻辑如下（请参考）：</li>
            <li>全局功能名叫[论坛]->[论坛]有很多个[版块]->版块可以创建[帖子]内容->[帖子]可以单独设置[专题]和[标签]</li>
            <li>[论坛]拥有角色：论坛管理员->分区版主->超级版主->版主->用户</li>',
            'style'   => 'warning',
            'type'    => 'submessage',
        ),
        array(
            'title'   => '论坛名称',
            'id'      => 'bbs_forum_display_name',
            'default' => '论坛',
            'desc'    => '全局总名称，例如：论坛、社区、圈子',
            'class'   => 'mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '版块名称',
            'id'      => 'bbs_plate_display_name',
            'default' => '版块',
            'desc'    => '总模块名称，例如：版块、吧、圈子',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '帖子名称',
            'id'      => 'bbs_posts_display_name',
            'desc'    => '文章内容名称，例如：帖子、主题、文章',
            'default' => '帖子',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '话题名称',
            'id'      => 'bbs_topic_display_name',
            'desc'    => '内容分类方式1，不建议修改',
            'default' => '话题',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '标签名称',
            'id'      => 'bbs_tag_display_name',
            'desc'    => '内容分类方式2，次要分类方式，不建议修改',
            'default' => '标签',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '帖子评论名称',
            'id'      => 'bbs_comment_display_name',
            'desc'    => '帖子评论名称，建议为：评论、回复',
            'default' => '回复',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '提问徽章名称',
            'id'      => 'bbs_question_badge_name',
            'desc'    => '提问类型的帖子的提问徽章名称，建议为：问答、提问',
            'default' => '提问',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '提问已解决徽章名称',
            'id'      => 'bbs_question_ok_badge_name',
            'desc'    => '提问解决后的徽章名称，建议为：已解决',
            'default' => '已解决',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '版块分类管理员名称',
            'id'      => 'bbs_cat_moderator_name',
            'desc'    => '版块管理员的名称，例如：分区版主，实习分区版主，实习分区管理',
            'default' => '分区版主',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '版块创建者名称',
            'id'      => 'bbs_plate_author_name',
            'desc'    => '版块创建者的名称，例如：超级版主、吧主、圈主',
            'default' => '超级版主',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
        array(
            'title'   => '版块管理员名称',
            'id'      => 'bbs_plate_moderator_name',
            'desc'    => '版块管理员的名称，例如：版主、实习版主、理事人',
            'default' => '版主',
            'class'   => 'compact mini-input',
            'type'    => 'text',
        ),
    ),
));

CSF::createSection($prefix, array(
    'parent'      => 'forum',
    'title'       => '首页设置',
    'icon'        => 'fa fa-fw fa-home',
    'description' => '',
    'fields'      => array(
        array(
            'content' => '<h4>在此设置首页的主内容TAB</h4>
            <li>论坛首页地址：<code>' . zib_bbs_get_home_url() . '</code></li>
            <li>在此添加的每一个tab栏目均有一个独立的地址，可以直接打开对应的Tab内容，只需要在首页地址结尾添加<code>?index=tab序号</code>即可</li>
            <li>例如：<code>' . zib_bbs_get_home_url() . '?index=2</code></li>
            <li>例如：<code>' . zib_bbs_get_home_url() . '?index=3</code></li>
            <li>请确保添加的栏目都有一定的内容，以避免UI显示错误</li>
            ',
            'style'   => 'warning',
            'type'    => 'submessage',
        ),
        array(
            'id'       => 'bbs_home_tab',
            'type'     => 'sortable',
            'title'    => '首页栏目',
            'subtitle' => '选择并排序首页需要显示的栏目',
            'default'  => array(
                'follow'    => array(
                    'title' => '关注',
                    'show'  => array('pc_s', 'm_s'),
                ),
                'synthesis' => array(
                    'title' => '综合',
                    'show'  => array('pc_s', 'm_s'),
                ),
                'plate'     => array(
                    'title' => '版块',
                    'show'  => array('pc_s', 'm_s'),
                ),
                'tabs'      => array(
                    array(
                        'show'    => array('pc_s', 'm_s'),
                        'title'   => '热门',
                        'style'   => 'detail',
                        'orderby' => 'views',
                    ),
                    array(
                        'show'    => array('pc_s', 'm_s'),
                        'style'   => 'detail',
                        'title'   => '精华',
                        'filter'  => 'essence',
                        'orderby' => 'modified',
                    ),
                    array(
                        'show'     => array('pc_s', 'm_s'),
                        'style'    => 'detail',
                        'title'    => '问答',
                        'bbs_type' => array('question'),
                        'orderby'  => 'modified',
                    ),
                    array(
                        'show'    => array('pc_s', 'm_s'),
                        'style'   => 'detail',
                        'title'   => '投票',
                        'filter'  => 'vote',
                        'orderby' => 'modified',
                    ),
                    array(
                        'show'    => array('pc_s', 'm_s'),
                        'style'   => 'detail',
                        'title'   => '最新回复',
                        'orderby' => 'last_reply',
                    ),
                    array(
                        'show'    => array('pc_s', 'm_s'),
                        'style'   => 'detail',
                        'title'   => '最高评分',
                        'orderby' => 'score',
                    ),
                ),
            ),
            'fields'   => array(
                array(
                    'title'      => '关注',
                    'subtitle'   => '显示用户关注的版块的帖子',
                    'id'         => 'follow',
                    'type'       => 'accordion',
                    'accordions' => array(
                        array(
                            'title'  => '栏目设置',
                            'fields' => array(
                                array(
                                    'title'   => '显示此栏目',
                                    'inline'  => true,
                                    'id'      => 'show',
                                    'type'    => "checkbox",
                                    'options' => array(
                                        'pc_s' => 'PC端开启',
                                        'm_s'  => '移动端开启',
                                    ),
                                ),
                                array(
                                    'title'      => '栏目标题',
                                    'class'      => 'compact',
                                    'id'         => 'title',
                                    'attributes' => array(
                                        'rows' => 1,
                                    ),
                                    'default'    => '关注',
                                    'sanitize'   => false,
                                    'type'       => 'textarea',
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
                                        'detail' => '详细内容',
                                        'mini'   => '简约风格',
                                    ),
                                ),
                                array(
                                    'title'    => '推荐版块',
                                    'subtitle' => '显示用户还未关注的版块',
                                    'id'       => 'plate',
                                    'type'     => 'fieldset',
                                    'fields'   => array(
                                        array(
                                            'title'    => ' ',
                                            'subtitle' => '显示版块推荐',
                                            'id'       => 's',
                                            'type'     => 'switcher',
                                            'default'  => true,
                                        ),
                                        array(
                                            'dependency' => array('s', '!=', ''),
                                            'title'      => '栏目标题',
                                            'id'         => 'title',
                                            'attributes' => array(
                                                'rows' => 1,
                                            ),
                                            'default'    => '热门推荐',
                                            'sanitize'   => false,
                                            'type'       => 'textarea',
                                        ),
                                        array(
                                            'dependency' => array('s', '!=', ''),
                                            'id'         => 'orderby',
                                            'class'      => 'compact',
                                            'title'      => ' ',
                                            'subtitle'   => __('版块排序方式', 'zib_language'),
                                            'default'    => 'views',
                                            'options'    => zib_bbs_get_plate_order_options(),
                                            'type'       => 'select',
                                        ),
                                        array(
                                            'dependency' => array(
                                                array('s', '!=', ''),
                                            ),
                                            'title'      => ' ',
                                            'subtitle'   => '最多显示数量',
                                            'id'         => 'count',
                                            'default'    => 8,
                                            'type'       => 'spinner',
                                            'step'       => 2,
                                            'unit'       => '个',
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'title'      => '综合',
                    'subtitle'   => '显示所有的帖子',
                    'id'         => 'synthesis',
                    'type'       => 'accordion',
                    'accordions' => array(
                        array(
                            'title'  => '栏目设置',
                            'fields' => array(
                                array(
                                    'title'   => '显示此栏目',
                                    'inline'  => true,
                                    'id'      => 'show',
                                    'type'    => "checkbox",
                                    'options' => array(
                                        'pc_s' => 'PC端开启',
                                        'm_s'  => '移动端开启',
                                    ),
                                ),
                                array(
                                    'title'      => '栏目标题',
                                    'class'      => 'compact',
                                    'id'         => 'title',
                                    'attributes' => array(
                                        'rows' => 1,
                                    ),
                                    'default'    => '综合',
                                    'sanitize'   => false,
                                    'type'       => 'textarea',
                                ),
                                array(
                                    'id'          => 'exclude_plate',
                                    'title'       => __('排除版块', 'zib_language'),
                                    'desc'        => '排除所选版块的帖子，支持单选、多选。输入版块关键词搜索选择',
                                    'default'     => array(),
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
                                    'title'   => '全局置顶',
                                    'label'   => '置顶显示全局置顶帖子',
                                    'id'      => 'topping_s',
                                    'type'    => 'switcher',
                                    'default' => false,
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
                                        'detail' => '详细内容',
                                        'mini'   => '简约风格',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'title'      => '版块列表',
                    'subtitle'   => '显示全部论坛版块列表',
                    'id'         => 'plate',
                    'type'       => 'accordion',
                    'accordions' => array(
                        array(
                            'title'  => '栏目设置',
                            'fields' => array(
                                array(
                                    'title'   => '显示此栏目',
                                    'inline'  => true,
                                    'id'      => 'show',
                                    'type'    => "checkbox",
                                    'options' => array(
                                        'pc_s' => 'PC端开启',
                                        'm_s'  => '移动端开启',
                                    ),
                                ),
                                array(
                                    'title'      => '栏目标题',
                                    'id'         => 'title',
                                    'attributes' => array(
                                        'rows' => 1,
                                    ),
                                    'sanitize'   => false,
                                    'default'    => '版块',
                                    'type'       => 'textarea',
                                ),
                                array(
                                    'title'  => '用户关注版块',
                                    'id'     => 'user_follow',
                                    'type'   => 'fieldset',
                                    'fields' => array(
                                        array(
                                            'title'    => ' ',
                                            'subtitle' => '显示此版块',
                                            'id'       => 's',
                                            'type'     => 'switcher',
                                            'default'  => true,
                                        ),
                                        array(
                                            'dependency' => array('s', '!=', ''),
                                            'title'      => '栏目标题',
                                            'id'         => 'title',
                                            'attributes' => array(
                                                'rows' => 1,
                                            ),
                                            'default'    => '已关注',
                                            'sanitize'   => false,
                                            'type'       => 'textarea',
                                        ),
                                        array(
                                            'dependency' => array('s', '!=', ''),
                                            'id'         => 'orderby',
                                            'class'      => 'compact',
                                            'title'      => ' ',
                                            'subtitle'   => __('版块排序方式', 'zib_language'),
                                            'default'    => 'count',
                                            'options'    => zib_bbs_get_plate_order_options(),
                                            'type'       => 'select',
                                        ),
                                    ),
                                ),
                                array(
                                    'title'  => '系统推荐版块',
                                    'id'     => 'hot_plate',
                                    'type'   => 'fieldset',
                                    'fields' => array(
                                        array(
                                            'title'    => ' ',
                                            'subtitle' => '显示此版块',
                                            'id'       => 's',
                                            'type'     => 'switcher',
                                            'default'  => true,
                                        ),
                                        array(
                                            'dependency' => array('s', '!=', ''),
                                            'title'      => '栏目标题',
                                            'id'         => 'title',
                                            'attributes' => array(
                                                'rows' => 1,
                                            ),
                                            'default'    => '推荐',
                                            'sanitize'   => false,
                                            'type'       => 'textarea',
                                        ),
                                        array(
                                            'dependency' => array('s', '!=', ''),
                                            'id'         => 'orderby',
                                            'class'      => 'compact',
                                            'title'      => ' ',
                                            'subtitle'   => __('版块排序方式', 'zib_language'),
                                            'default'    => 'views',
                                            'options'    => array_merge(zib_bbs_get_plate_order_options(), array(
                                                'include' => '手动选择并排序',
                                            )),
                                            'type'       => 'select',
                                        ),
                                        array(
                                            'dependency' => array(
                                                array('s', '!=', ''),
                                                array('orderby', '==', 'include', '', 'visible'),
                                            ),
                                            'title'      => ' ',
                                            'id'         => 'orderby_include',
                                            'class'      => 'compact',
                                            'subtitle'   => __('显示版块', 'zib_language'),
                                            'desc'       => '请选择并排序需要显示的版块，未选择的分类则不会显示',
                                            'default'    => '',
                                            'options'    => 'post',
                                            'query_args' => array(
                                                'post_type' => 'plate',
                                            ),
                                            'ajax'       => true,
                                            'settings'   => array(
                                                'min_length' => 2,
                                            ),
                                            'chosen'     => true,
                                            'multiple'   => true,
                                            'sortable'   => true,
                                            'type'       => 'select',
                                        ),
                                        array(
                                            'dependency' => array(
                                                array('s', '!=', ''),
                                                array('orderby', '!=', 'include', '', 'visible'),
                                            ),
                                            'title'      => ' ',
                                            'subtitle'   => '最多显示数量',
                                            'id'         => 'count',
                                            'default'    => 8,
                                            'type'       => 'spinner',
                                            'step'       => 2,
                                            'unit'       => '个',
                                        ),
                                    ),
                                ),
                                array(
                                    'id'      => 'cat_orderby',
                                    'title'   => __('版块分类排序方式', 'zib_language'),
                                    'default' => 'count',
                                    'options' => array(
                                        'count'      => '版块数量',
                                        'views'      => '热度排序',
                                        'last_reply' => '最后回帖',
                                        'last_post'  => '最后发帖',
                                        'name'       => '名称排序',
                                        'include'    => '手动排序',
                                    ),
                                    'type'    => 'select',
                                ),
                                array(
                                    'dependency' => array('cat_orderby', '==', 'include', '', 'visible'),
                                    'title'      => ' ',
                                    'id'         => 'orderby_include',
                                    'class'      => 'compact',
                                    'subtitle'   => __('手动排序', 'zib_language'),
                                    'desc'       => '请选择并排序需要显示的版块类别，未选择的分类则不会显示',
                                    'placeholder' => '选择板块分类并排序',
                                    'default'    => '',
                                    'options'    => 'categories',
                                    'chosen'     => true,
                                    'multiple'   => true,
                                    'sortable'   => true,
                                    'query_args' => array(
                                        'taxonomy' => 'plate_cat', // for get all pages (also it's same for posts).
                                    ),
                                    'type'       => 'select',
                                ),
                                array(
                                    'id'      => 'orderby',
                                    'title'   => __('版块排序方式', 'zib_language'),
                                    'default' => 'posts_count',
                                    'options' => zib_bbs_get_plate_order_options(),
                                    'type'    => 'select',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'title'        => '帖子列表',
                    'subtitle'     => '根据不同规则筛选后显示的帖子列表',
                    'id'           => 'tabs',
                    'type'         => 'group',
                    'button_title' => '添加栏目',
                    'fields'       => array(
                        array(
                            'title'      => '栏目标题(必填)',
                            'id'         => 'title',
                            'desc'       => '根据下方的不同方式筛选可实现（最新帖子，随机帖子，热门帖子等）以及固定版块帖子的功能',
                            'attributes' => array(
                                'rows' => 1,
                            ),
                            'default'    => '',
                            'sanitize'   => false,
                            'type'       => 'textarea',
                        ),
                        array(
                            'title'   => '显示此栏目',
                            'inline'  => true,
                            'class'   => 'compact',
                            'id'      => 'show',
                            'type'    => "checkbox",
                            'options' => array(
                                'pc_s' => 'PC端开启',
                                'm_s'  => '移动端开启',
                            ),
                            'default' => array('pc_s', 'm_s'),
                        ),
                        array(
                            'id'          => 'include_plate',
                            'title'       => __('包含版块', 'zib_language'),
                            'desc'        => '仅显示所选版块的帖子，支持单选、多选。输入版块关键词搜索选择',
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
                            'dependency'  => array('include_plate', '==', '', '', 'visible'),
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
                            'title'       => __('包含话题', 'zib_language'),
                            'desc'        => '仅显示所选话题的帖子，支持单选、多选。输入关键词搜索选择',
                            'default'     => '',
                            'options'     => 'categories',
                            'query_args'  => array(
                                'taxonomy' => 'forum_topic',
                            ),
                            'placeholder' => '输入关键词以搜索版块话题',
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
                            'title'       => __('包含标签', 'zib_language'),
                            'desc'        => '仅显示所选标签的帖子，支持单选、多选。输入关键词搜索选择',
                            'default'     => '',
                            'options'     => 'categories',
                            'query_args'  => array(
                                'taxonomy' => 'forum_tag',
                            ),
                            'placeholder' => '输入关键词以搜索标签',
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
                                'detail' => '详细内容',
                                'mini'   => '简约风格',
                            ),
                        ),
                    ),
                ),
                array(
                    'title'        => '版块帖子',
                    'subtitle'     => '显示某一个版块的帖子',
                    'id'           => 'tabs_2',
                    'type'         => 'group',
                    'button_title' => '添加栏目',
                    'fields'       => array(
                        array(
                            'title'      => '栏目标题(必填)',
                            'id'         => 'title',
                            'desc'       => '根据下方的不同方式筛选可实现（最新帖子，随机帖子，热门帖子等）以及固定版块帖子的功能',
                            'attributes' => array(
                                'rows' => 1,
                            ),
                            'sanitize'   => false,
                            'default'    => '',
                            'type'       => 'textarea',
                        ),
                        array(
                            'title'   => '显示此栏目',
                            'inline'  => true,
                            'class'   => 'compact',
                            'id'      => 'show',
                            'type'    => "checkbox",
                            'options' => array(
                                'pc_s' => 'PC端开启',
                                'm_s'  => '移动端开启',
                            ),
                            'default' => array('pc_s', 'm_s'),
                        ),
                        array(
                            'title'   => '显示版块信息',
                            'class'   => 'hide',
                            'id'      => 'plate_info',
                            'default' => true,
                            'type'    => 'switcher',
                        ),
                        array(
                            'id'         => 'include_plate',
                            'title'      => __('选择版块', 'zib_language'),
                            'desc'       => '请选择需要显示的版块',
                            'default'    => '',
                            'options'    => 'post',
                            'query_args' => array(
                                'post_type'      => 'plate',
                                'posts_per_page' => -1,
                            ),
                            'ajax'       => true,
                            'settings'   => array(
                                'min_length' => 2,
                            ),
                            'type'       => 'select',
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
                                'detail' => '详细内容',
                                'mini'   => '简约风格',
                            ),
                        ),
                    ),
                ),
                array(
                    'title'        => '话题/标签的帖子',
                    'subtitle'     => '显示某一个话题或标签的帖子',
                    'id'           => 'tabs_3',
                    'type'         => 'group',
                    'sanitize'     => false,
                    'button_title' => '添加栏目',
                    'fields'       => array(
                        array(
                            'title'      => '栏目标题(必填)',
                            'id'         => 'title',
                            'desc'       => '根据下方的不同方式筛选可实现某一个话题或者标签的（最新帖子，随机帖子，热门帖子等）',
                            'attributes' => array(
                                'rows' => 1,
                            ),
                            'sanitize'   => false,
                            'default'    => '',
                            'type'       => 'textarea',
                        ),
                        array(
                            'title'   => '显示此栏目',
                            'inline'  => true,
                            'class'   => 'compact',
                            'id'      => 'show',
                            'type'    => "checkbox",
                            'options' => array(
                                'pc_s' => 'PC端开启',
                                'm_s'  => '移动端开启',
                            ),
                            'default' => array('pc_s', 'm_s'),
                        ),
                        array(
                            'title'   => '显示头部信息',
                            'class'   => 'hide',
                            'id'      => 'term_info',
                            'default' => true,
                            'type'    => 'switcher',
                        ),
                        array(
                            'id'          => 'include_topic',
                            'title'       => __('选择话题', 'zib_language'),
                            'desc'        => '仅显示所选话题的帖子',
                            'default'     => '',
                            'options'     => 'categories',
                            'query_args'  => array(
                                'taxonomy' => 'forum_topic',
                            ),
                            'placeholder' => '输入关键词以搜索版块话题',
                            'chosen'      => true,
                            'multiple'    => false,
                            'ajax'        => true,
                            'settings'    => array(
                                'min_length' => 2,
                            ),
                            'type'        => 'select',
                        ),
                        array(
                            'id'          => 'include_tag',
                            'title'       => __('选择标签', 'zib_language'),
                            'desc'        => '仅显示所选标签的帖子',
                            'default'     => '',
                            'options'     => 'categories',
                            'query_args'  => array(
                                'taxonomy' => 'forum_tag',
                            ),
                            'placeholder' => '输入关键词以搜索标签',
                            'chosen'      => true,
                            'ajax'        => true,
                            'settings'    => array(
                                'min_length' => 2,
                            ),
                            'multiple'    => false,
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
                                'detail' => '详细内容',
                                'mini'   => '简约风格',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        array(
            'title'    => '首页栏目',
            'subtitle' => '默认显示第几个栏目TAB',
            'id'       => 'bbs_home_tab_active_index',
            'default'  => 2,
            'type'     => 'spinner',
            'step'     => 1,
        ),
        array(
            'title'    => ' ',
            'subtitle' => '开启移动端滑动切换功能',
            'label'    => '移动端可以左右滑动切换栏目(对浏览器性能有一定要求，性能太差的手机会出现卡顿现象)',
            'id'       => 'bbs_home_tab_swiper',
            'class'    => 'compact',
            'default'  => true,
            'type'     => 'switcher',
        ),
    ),
));

CSF::createSection($prefix, array(
    'parent'      => 'forum',
    'title'       => '版块页面',
    'icon'        => 'fa fa-fw fa-windows',
    'description' => '',
    'fields'      => array(
        array(
            'title'   => '顶部版块信息卡片',
            'inline'  => true,
            'id'      => 'bbs_plate_top_info_s',
            'type'    => "checkbox",
            'desc'    => "在页面顶部显示本板信息的卡片，如果关闭PC端显示，可以在侧边栏添加[版块信息]模块",
            'options' => array(
                'pc_s' => 'PC端显示',
                'm_s'  => '移动端显示',
            ),
            'default' => array('pc_s', 'm_s'),
        ),
        array(
            'title'        => '版块帖子栏目',
            'subtitle'     => '版块页面主要内容',
            'desc'         => '在版块页面显示的栏目内容，请至少保证有两个栏目<br>会自动在第一个栏目内显示置顶文章(置顶文章只会显示为简约模式)<br>每一个tab栏目均独立的地址，地址结尾添加?index=tab序号即可<br>此处栏目为默认配置，也可以为版块单独配置Tab栏目',
            'button_title' => '添加栏目',
            'min'          => 2,
            'id'           => 'bbs_plate_tab',
            'type'         => 'group',
            'default'      => array(
                array(
                    'show'    => array('pc_s', 'm_s'),
                    'title'   => '全部',
                    'style'   => 'mini',
                    'orderby' => 'modified',
                ),
                array(
                    'show'    => array('pc_s', 'm_s'),
                    'style'   => 'detail',
                    'title'   => '最新发布',
                    'orderby' => 'date',
                ),
                array(
                    'show'    => array('pc_s', 'm_s'),
                    'style'   => 'detail',
                    'title'   => '最新回复',
                    'orderby' => 'last_reply',
                ),
                array(
                    'show'    => array('pc_s', 'm_s'),
                    'title'   => '热门',
                    'style'   => 'detail',
                    'orderby' => 'views',
                ),
                array(
                    'show'    => array('pc_s', 'm_s'),
                    'style'   => 'detail',
                    'title'   => '精华',
                    'filter'  => 'essence',
                    'orderby' => 'modified',
                ),
            ),
            'fields'       => BBS_CFS_Module::plate_tab(),
        ),
        array(
            'title'    => '版块栏目设置',
            'subtitle' => '默认显示第几个栏目TAB',
            'id'       => 'bbs_plate_tab_active_index',
            'default'  => 1,
            'type'     => 'spinner',
            'step'     => 1,
        ),
        array(
            'title'    => ' ',
            'subtitle' => '开启移动端滑动切换功能',
            'label'    => '移动端可以左右滑动切换栏目(对浏览器性能有一定要求，性能太差的手机会出现卡顿现象)',
            'id'       => 'bbs_plate_tab_swiper',
            'class'    => 'compact',
            'default'  => true,
            'type'     => 'switcher',
        ),
    ),
));

CSF::createSection(
    $prefix,
    array(
        'parent'      => 'forum',
        'title'       => '帖子页面',
        'icon'        => 'fa fa-fw fa-ioxhost',
        'description' => '',
        'fields'      => array(
            array(
                'title'   => __('面包屑导航', 'zib_language'),
                'id'      => 'bbs_breadcrumbs_s',
                'type'    => 'switcher',
                'default' => true,
            ),
            array(
                'dependency' => array('bbs_breadcrumbs_s', '!=', ''),
                'title'      => ' ',
                'subtitle'   => __('显示网站首页', 'zib_language'),
                'id'         => 'bbs_breadcrumbs_home',
                'class'      => 'compact',
                'type'       => 'switcher',
                'default'    => true,
            ),
            array(
                'dependency' => array('bbs_breadcrumbs_s', '!=', ''),
                'title'      => ' ',
                'label'      => '如果您将论坛首页设置为网站首页，那么请关闭此处',
                'subtitle'   => __('显示论坛首页', 'zib_language'),
                'id'         => 'bbs_breadcrumbs_bbs_home',
                'class'      => 'compact',
                'type'       => 'switcher',
                'default'    => true,
            ),
            array(
                'dependency' => array('bbs_breadcrumbs_s|bbs_breadcrumbs_bbs_home', '!=|!=', '|'),
                'id'         => 'bbs_breadcrumbs_bbs_home_name',
                'class'      => 'compact mini-input',
                'title'      => ' ',
                'subtitle'   => '论坛首页显示名称',
                'default'    => '社区',
                'type'       => 'text',
            ),
            array(
                'dependency' => array('bbs_breadcrumbs_s', '!=', ''),
                'title'      => ' ',
                'subtitle'   => __('显示版块分类', 'zib_language'),
                'id'         => 'bbs_breadcrumbs_plate_cat',
                'class'      => 'compact',
                'type'       => 'switcher',
                'default'    => true,
            ),
            array(
                'title'    => '帖子加分',
                'subtitle' => '每个用户最多加几分',
                'id'       => 'bbs_score_extra_max',
                'default'  => 5,
                'type'     => 'spinner',
                'step'     => 1,
                'mini'     => 1,
            ),
            array(
                'title'    => '帖子扣分',
                'subtitle' => '每个用户最扣几分',
                'id'       => 'bbs_score_deduct_max',
                'default'  => 3,
                'type'     => 'spinner',
                'step'     => 1,
                'mini'     => 1,
            ),
        ),
    )
);

CSF::createSection(
    $prefix,
    array(
        'parent'      => 'forum',
        'title'       => '回复评论',
        'icon'        => 'fa fa-fw fa-commenting-o',
        'description' => '',
        'fields'      => array(
            array(
                'id'      => 'bbs_reply_paginate_type',
                'title'   => '列表翻页模式',
                'default' => 'ajax_lists',
                'type'    => "radio",
                'inline'  => true,
                'options' => array(
                    'ajax_lists' => __('AJAX追加列表翻页', 'zib_language'),
                    'default'    => __('数字翻页按钮', 'zib_language'),
                ),
            ),
            array(
                'dependency' => array('bbs_reply_paginate_type', '==', 'ajax_lists'),
                'title'      => ' ',
                'subtitle'   => 'AJAX翻页自动加载',
                'class'      => 'compact',
                'id'         => 'bbs_reply_paginate_ias_s',
                'type'       => 'switcher',
                'label'      => '页面滚动到列表尽头时，自动加载下一页',
                'default'    => true,
            ),
            array(
                'dependency' => array('bbs_reply_paginate_type|bbs_reply_paginate_ias_s', '==|!=', 'ajax_lists|'),
                'title'      => ' ',
                'subtitle'   => '自动加载页数',
                'desc'       => 'AJAX翻页自动加载最多加载几页（为0则不限制，直到加载全部评论）',
                'id'         => 'bbs_reply_paginate_ias_max',
                'class'      => 'compact',
                'default'    => 3,
                'max'        => 10,
                'min'        => 0,
                'step'       => 1,
                'unit'       => '页',
                'type'       => 'spinner',
            ),
            array(
                'id'      => 'bbs_comment_smilie',
                'type'    => 'switcher',
                'default' => true,
                'title'   => __('允许插入表情', 'zib_language'),
            ),
            array(
                'id'      => 'bbs_comment_code',
                'class'   => 'compact',
                'type'    => 'switcher',
                'default' => true,
                'title'   => __('允许插入代码', 'zib_language'),
            ),
            array(
                'id'      => 'bbs_comment_img',
                'class'   => 'compact',
                'type'    => 'switcher',
                'default' => true,
                'title'   => __('允许插入图片', 'zib_language'),
            ),
            array(
                'id'      => 'bbs_comment_upload_img',
                'class'   => 'compact',
                'type'    => 'switcher',
                'default' => false,
                'title'   => __('允许上传图片', 'zib_language'),
            ),
            array(
                'id'       => 'bbs_comment_placeholder',
                'class'    => 'compact',
                'title'    => ' ',
                'subtitle' => __('自定义评论框占位符文案', 'zib_language'),
                'default'  => __('欢迎您留下宝贵的见解！', 'zib_language'),
                'type'     => 'text',
            ),
        ),
    )
);

CSF::createSection(
    $prefix,
    array(
        'parent'      => 'forum',
        'title'       => '权限配置',
        'icon'        => 'fa fa-fw fa-user-secret',
        'description' => '',
        'fields'      => array(
            array(
                'content' => '<div>在此处添加一些[限制发帖]的选项，添加之后可以在版块设置中进行选择，即可实现不同版块不同的发帖限制功能</div>
                <div>先设置一个需要的选项数量，刷新页面后再设置每个选项的权限规则以及名称定义</div>
                <div class="c-yellow"><i class="fa fa-fw fa-info-circle fa-fw"></i>修改数量后，请先刷新页面后再做其它配置</div>
                ',
                'title'   => '限制[发帖]',
                'style'   => 'warning',
                'type'    => 'content',
            ),
            array(
                'title'    => ' ',
                'subtitle' => '限制[发帖]选项数量',
                'id'       => 'bbs_posts_add_limit_opt_max',
                'class'    => 'compact',
                'default'  => 4,
                'max'      => 12,
                'min'      => 0,
                'step'     => 1,
                'unit'     => '个',
                'type'     => 'spinner',
            ),
            array(
                'dependency' => array('bbs_posts_add_limit_opt_max', '>', '0'),
                'id'         => 'user_cap',
                'type'       => 'accordion',
                'class'      => 'accordion-mini compact',
                'title'      => ' ',
                'subtitle'   => '选项权限配置',
                'accordions' => BBS_CFS_Module::add_limit('posts'),
            ),
            array(
                'content' => '<div>在此处添加一些[限制创建版块]的选项，添加之后可以在版块分类设置中进行选择，即可实现不同版块分类不同的创建版块限制功能</div>
                <div>先设置一个需要的选项数量，刷新页面后再设置每个选项的权限规则以及名称定义</div>
                <div class="c-yellow"><i class="fa fa-fw fa-info-circle fa-fw"></i>修改数量后，请先刷新页面后再做其它配置</div>
                ',
                'title'   => '限制[创建版块]',
                'style'   => 'warning',
                'type'    => 'content',
            ),
            array(
                'title'    => ' ',
                'class'    => 'compact',
                'subtitle' => '限制[创建版块]选项数量',
                'id'       => 'bbs_plate_add_limit_opt_max',
                'default'  => 4,
                'max'      => 12,
                'min'      => 0,
                'step'     => 1,
                'unit'     => '个',
                'type'     => 'spinner',
            ),
            array(
                'dependency' => array('bbs_plate_add_limit_opt_max', '>', '0'),
                'id'         => 'user_cap',
                'type'       => 'accordion',
                'class'      => 'accordion-mini compact',
                'title'      => ' ',
                'subtitle'   => '选项权限配置',
                'accordions' => BBS_CFS_Module::add_limit('plate'),
            ),
            array(
                'title'   => '一直显示创建版块按钮',
                'id'      => 'bbs_show_new_plate',
                'type'    => 'switcher',
                'default' => true,
                'label'   => '对没有创建版块权限的用户也显示创建版块按钮',
                'help'    => '关闭后只会对有权限的用户显示，开启后如果用户没有权限，点击按钮会提示权限不足',
            ),
            array(
                'title'   => '一直显示创建话题按钮',
                'id'      => 'bbs_show_new_topic',
                'type'    => 'switcher',
                'default' => true,
                'label'   => '对没有创建话题权限的用户也显示创建话题按钮',
                'help'    => '关闭后只会对有权限的用户显示，开启后如果用户没有权限，点击按钮会提示权限不足',
            ),
            array(
                'title'   => '一直显示申请版主按钮',
                'id'      => 'bbs_show_apply_moderator',
                'type'    => 'switcher',
                'default' => true,
                'label'   => '对没有申请版主权限的用户也显示申请版主按钮',
                'help'    => '关闭后只会对有权限的用户显示，开启后如果用户没有权限，点击按钮会提示权限不足',
            ),
        ),
    )
);

CSF::createSection(
    $prefix,
    array(
        'parent'      => 'forum',
        'title'       => '其它设置' . $new_badge['6.5'],
        'icon'        => 'fa fa-fw fa-life-ring',
        'description' => '',
        'fields'      => array(
            array(
                'title'   => '投票数据显示为',
                'id'      => 'bbs_vote_number_type',
                'default' => "percentage",
                'type'    => "radio",
                'inline'  => true,
                'options' => array(
                    'percentage' => __('百分比', 'zib_language'),
                    'number'     => __('获得票数', 'zib_language'),
                    ''           => __('不显示', 'zib_language'),
                ),
            ),
            array(
                'title'      => '版主申请说明',
                'id'         => 'bbs_apply_moderator_desc',
                'desc'       => '用户申请版主时，显示的说明',
                'default'    => '<p>成为版主，您可以管理版块相关实务</p>
<p>申请版主前，需要先满足一定要求</p>
<p>申请提交后，管理员会在1-2个工作日内进行审核</p>
<p>审核结果将会已站内信以及邮件的方式通知您，请注意查收</p>',
                'attributes' => array(
                    'rows' => 3,
                ),
                'sanitize'   => false,
                'type'       => 'textarea',
            ),
            array(
                'title'       => '标签默认缩略图',
                'id'          => 'bbs_term_thumb',
                'type'        => 'gallery',
                'add_title'   => '新增图片',
                'edit_title'  => '编辑图片',
                'clear_title' => '清空图片',
                'default'     => false,
                'desc'        => '标签、版块分类未设置图像时候，显示的默认图像（支持添加多张图像随机显示）',
            ),
            array(
                'title'   => '发帖标题字数限制' . $new_badge['6.4'],
                'desc'    => '限制标题字数可有效的防止灌水等无意义内容（后台发布不限制，英文字符按0.5个字计算）',
                'id'      => 'bbs_post_title_strlen_limit',
                'type'    => 'between_number',
                'desc'    => '',
                'unit'    => '字',
                'default' => array(
                    'min' => 5,
                    'max' => 30,
                ),
            ),
            array(
                'title'   => __('发帖付费内容允许设置隐藏模式', 'zib_language') . $new_badge['6.6'],
                'id'      => 'bbs_post_pay_hide_type_s',
                'default' => true,
                'type'    => 'switcher',
                'desc'    => '开启此项，用户可以选择隐藏全文或者隐藏部分内容<br/>关闭此项，则默认为隐藏全文',
            ),
            array(
                'title'   => __('发帖付费内容允许设置会员价', 'zib_language'),
                'id'      => 'bbs_post_pay_vip_price_s',
                'default' => true,
                'type'    => 'switcher',
                'desc'    => '发帖时对拥有设置付费内容权限的用户，是否开启设置会员价格<br/>开启此项，会直接在前台显示设置会员价的选项<br/>关闭此项，则用户只能设置普通价格，会员价则按照下方设置的折扣自动计算',
            ),
            array(
                'dependency' => array('bbs_post_pay_vip_price_s', '==', ''),
                'id'         => 'bbs_post_pay_vip_1_discount', //折扣
                'title'      => ' ',
                'subtitle'   => _pz('pay_user_vip_1_name') . '折扣',
                'default'    => 100,
                'type'       => 'number',
                'unit'       => '%',
                'class'      => 'compact',
            ),
            array(
                'dependency' => array('bbs_post_pay_vip_price_s', '==', ''),
                'id'         => 'bbs_post_pay_vip_2_discount', //折扣
                'class'      => 'compact',
                'title'      => ' ',
                'subtitle'   => _pz('pay_user_vip_2_name') . '折扣',
                'desc'       => '执行价的百分之多少，0为免费，100为没有折扣，不能高于100',
                'default'    => 100,
                'type'       => 'number',
                'unit'       => '%',
                'class'      => 'compact',
            ),
            array(
                'title'   => '投票选项数量',
                'id'      => 'bbs_vote_max',
                'default' => 8,
                'max'     => 18,
                'min'     => 4,
                'step'    => 1,
                'unit'    => '个',
                'type'    => 'spinner',
                'desc'    => '投票最多可以添加几个选项，不能低于2',
            ),
            array(
                'id'      => 'bbs_rewrite_suffix_html_s',
                'class'   => '',
                'title'   => '链接URL后缀.html' . $new_badge['6.5'],
                'desc'    => '论坛版块、帖子页面的网址将以.html结尾，有利于SEO',
                'default' => true,
                'type'    => 'switcher',
            ),
            array(
                'title'      => '链接URL别名',
                'subtitle'   => '版块链接URL别名',
                'id'         => 'bbs_plate_rewrite_slug',
                'default'    => 'forum',
                'class'      => 'mini-input',
                'attributes' => array(
                    'data-readonly-id' => 'bbs_slug',
                    'readonly'         => 'readonly',
                ),
                'type'       => 'text',
            ),
            array(
                'title'      => ' ',
                'subtitle'   => '发布修改帖子URL别名',
                'id'         => 'bbs_posts_edit_rewrite_slug',
                'default'    => 'posts-edit',
                'class'      => 'mini-input compact',
                'attributes' => array(
                    'data-readonly-id' => 'bbs_slug',
                    'readonly'         => 'readonly',
                ),
                'type'       => 'text',
            ),
            array(
                'title'      => ' ',
                'subtitle'   => '帖子链接URL别名',
                'id'         => 'bbs_posts_rewrite_slug',
                'default'    => 'forum-post',
                'class'      => 'mini-input compact',
                'type'       => 'text',
                'attributes' => array(
                    'data-readonly-id' => 'bbs_slug',
                    'readonly'         => 'readonly',
                ),
                'desc'       => 'URL别名为开启固定链接之后对应网址的地址后缀。<br>如需要修改首页URL别名，请进入<a href="' . admin_url('edit.php?post_type=page') . '">页面->选择[论坛首页]</a>进行URL别名修改<div style="color:#ff4021;"><i class="fa fa-fw fa-info-circle fa-fw"></i>如非必要，请勿修改，修改后请保存一次固定链接</div>
                <br><a href="javascript:;" class="but jb-yellow remove-readonly" readonly-id="bbs_slug">我要修改</a>',
            ),
        ),
    )
);

CSF::createSection($prefix, array(
    'parent'      => 'cap',
    'title'       => '论坛权限' . $new_badge['6.3'],
    'icon'        => 'fa fa-fw fa-grav',
    'description' => '',
    'fields'      => CFS_Module::user_can_fields(BBS_CFS_Module::user_caps(), '<p>论坛功能的用户权限管理</p>'),
));

class BBS_CFS_Module
{

    public static function plate_tab()
    {
        return array(
            array(
                'title'      => '栏目标题(必填)',
                'id'         => 'title',
                'desc'       => '根据下方的不同方式筛选可实现：最新帖子，热门帖子，最新回复等栏目',
                'attributes' => array(
                    'rows' => 1,
                ),
                'default'    => '',
                'sanitize'   => false,
                'type'       => 'textarea',
            ),
            array(
                'title'   => '显示此栏目',
                'inline'  => true,
                'class'   => 'compact',
                'id'      => 'show',
                'type'    => "checkbox",
                'options' => array(
                    'pc_s' => 'PC端开启',
                    'm_s'  => '移动端开启',
                ),
                'default' => array('pc_s', 'm_s'),
            ),
            array(
                'id'          => 'include_topic',
                'title'       => __('话题筛选', 'zib_language'),
                'desc'        => '仅显示所选话题的帖子，支持单选、多选。输入关键词搜索选择',
                'default'     => '',
                'options'     => 'categories',
                'query_args'  => array(
                    'taxonomy' => 'forum_topic',
                ),
                'placeholder' => '输入关键词以搜索版块话题',
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
                'title'       => __('标签筛选', 'zib_language'),
                'desc'        => '仅显示所选标签的帖子，支持单选、多选。输入关键词搜索选择',
                'default'     => '',
                'options'     => 'categories',
                'query_args'  => array(
                    'taxonomy' => 'forum_tag',
                ),
                'placeholder' => '输入关键词以搜索标签',
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
                'default' => 'mini',
                'type'    => "radio",
                'inline'  => true,
                'options' => array(
                    'detail' => '详细内容',
                    'mini'   => '简约风格',
                ),
            ),
        );
    }

    public static function add_limit($type = 'plate')
    {
        $max         = _pz('bbs_' . $type . '_add_limit_opt_max', 4);
        $name        = 'plate' === $type ? '版块创建权限' : '发布帖子权限';
        $caps        = array();
        $user_fields = CFS_Module::user_can_user_fields();
        unset($user_fields['logged']);
        unset($user_fields['all']);

        for ($i = 1; $i <= $max; $i++) {
            $_fields = array_merge(array(array(
                'title'   => '选项名称定义',
                'id'      => 'name',
                'class'   => 'mini-input',
                'default' => '限制' . $i,
                'type'    => 'text',
            )), $user_fields);

            $_id   = 'bbs_' . $type . '_add_limit_' . $i;
            $_name = _pz('user_cap', array(), $_id);
            $_name = !empty($_name['name']) ? $_name['name'] : '限制' . $i;

            $caps[] = array(
                'title'  => '选项-' . $i . '：' . $_name,
                'fields' => array(array(
                    'id'      => $_id,
                    'default' => array(),
                    'desc'    => '',
                    'help'    => '',
                    'type'    => 'fieldset',
                    'fields'  => $_fields,
                )),
            );
        }

        return $caps;
    }

    public static function user_caps()
    {
        $new_badge = zib_get_csf_option_new_badge();

        $roles_all     = array('all', 'logged', 'level', 'vip', 'auth', 'cat_moderator', 'plate_author', 'moderator');
        $user_all_caps = array();

        $user_all_caps['论坛[帖子操作]'] = array(
            array(
                'id'            => 'bbs_' . 'posts_add',
                'name'          => '发布新的帖子',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_save_audit_no',
                'name'          => '发布帖子无需审核直接发布',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'posts_save_audit_no_manual',
                'name'          => '发布帖子无需[人工审核]直接发布',
                'desc'          => '需启用<a href="' . zib_get_admin_csf_url('扩展增强/api内容审核') . '">api内容审核</a>功能，API审核通过后直接发布',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'posts_upload_img',
                'name'          => '发帖允许在编辑器上传图片',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'posts_upload_video',
                'name'          => '发帖允许在编辑器上传视频',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'posts_iframe_video',
                'name'          => '发帖允许在编辑器插入iframe嵌入视频' . $new_badge['6.3'],
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_hide',
                'name'          => '发帖允许在编辑器发布隐藏内容',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_type_question',
                'name'          => '允许发布提问帖子',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            /**
             * 待处理，暂未启用
            array(
            'id'            => 'bbs_' . 'posts_type_atlas',
            'name'          => '允许发布图集帖子',
            'exclude_roles' => array('all'),
            ),
            array(
            'id'            => 'bbs_' . 'posts_type_video',
            'name'          => '允许发布视频帖子',
            'exclude_roles' => array('all'),
            ),
             */
            array(
                'id'            => 'bbs_' . 'posts_edit',
                'name'          => '编辑自己发布的帖子',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_allow_view_add',
                'name'          => '发布帖子时允许设置阅读权限',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_allow_view_edit',
                'name'          => '修改自己已发布帖子的阅读权限',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_allow_view_points',
                'name'          => '设置阅读权限时候允许设置为[积分支付可见]' . $new_badge['6.3'],
                'desc'          => '依赖于设置阅读限制的权限',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_allow_view_pay',
                'name'          => '设置阅读权限时候允许设置为[付费可见]' . $new_badge['6.3'],
                'desc'          => '依赖于设置阅读限制的权限',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_vote_add',
                'name'          => '发布帖子时允许发起投票',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_vote_edit',
                'name'          => '修改自己发布的投票选项',
                'desc'          => '自己无法修改已经开始的投票选项',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'posts_plate_move',
                'name'          => '移动自己发布的帖子到其它版块',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'posts_delete',
                'name'          => '删除自己发布的帖子',
                'exclude_roles' => array('all'),
            ),
        );

        $user_all_caps['论坛[帖子管理]'] = array(
            array(
                'id'      => 'bbs_' . 'posts_edit_other',
                'name'    => '编辑自己管理下的帖子',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_plate_move_other',
                'name'    => '移动自己管理下的帖子到其它版块',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_delete_other',
                'name'    => '删除自己管理下的帖子',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_essence_set',
                'name'    => '为自己管理下的帖子设置精华',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_topping_set',
                'name'    => '为自己管理下的帖子设置置顶',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_audit',
                'name'    => '审核自己管理下的帖子',
                'help'    => '拥有此权限同时会拥有查看未审核帖子的权限',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_vote_edit_other',
                'name'    => '为自己管理下的帖子修改投票功能及选项',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'    => 'bbs_' . 'posts_vote_ing_edit',
                'name'  => '为自己管理下的帖子修改已经进行中的投票选项',
                'roles' => array('moderator', 'plate_author', 'cat_moderator'),
            ),
            array(
                'id'      => 'bbs_' . 'posts_allow_view_edit_other',
                'name'    => '为自己管理下的帖子设置、修改阅读权限',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_allow_view_points_other',
                'name'    => '为自己管理下的帖子设置、修改阅读权限时候允许设置为[积分支付可见]',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'desc'    => '依赖于设置阅读限制的权限',
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'posts_allow_view_pay_other',
                'name'    => '为自己管理下的帖子设置、修改阅读权限时候允许设置为[付费可见]',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'desc'    => '依赖于设置阅读限制的权限',
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'question_answer_adopt_other',
                'name'    => '为自己管理下的帖子采纳回答',
                'help'    => '自己发布的帖子自己可以采纳回答',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),

        );
        $user_all_caps['论坛[版块操作]'] = array(
            array(
                'id'            => 'bbs_' . 'plate_add',
                'name'          => '创建新的版块',
                'desc'          => '请注意：自己创建的版块，自己就是该版块的超级版主！',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'plate_set_add_limit',
                'name'          => '为自己的创建的版块设置发帖权限',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'plate_set_allow_view',
                'name'          => '为自己的创建的版块设置查看权限',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'bbs_' . 'plate_plate_cat_edit',
                'name'          => '为自己创建的版块切换版块分类',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'plate_edit',
                'name'          => '编辑自己创建的版块',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'plate_delete',
                'name'          => '删除自己创建的版块',
                'exclude_roles' => array('all'),
            ),
        );

        $user_all_caps['论坛[版块管理]'] = array(
            array(
                'id'      => 'bbs_' . 'plate_set_add_limit_other',
                'name'    => '为自己管理的版块设置[发帖限制]',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'plate_set_allow_view_other',
                'name'    => '为自己管理的版块设置[查看权限]',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'    => 'bbs_' . 'plate_plate_cat_edit_other',
                'name'  => '为自己管理下的版块切换版块分类',
                'roles' => array('cat_moderator'),
            ),
            array(
                'id'    => 'bbs_' . 'plate_edit_other',
                'name'  => '编辑自己管理下的版块',
                'roles' => array('moderator', 'plate_author', 'cat_moderator'),
            ),
            array(
                'id'    => 'bbs_' . 'plate_delete_other',
                'name'  => '删除自己管理下的版块',
                'roles' => array('moderator', 'plate_author', 'cat_moderator'),
            ),
        );

        $user_all_caps['论坛[版块分类]'] = array(
            array(
                'id'            => 'bbs_' . 'plate_cat_add',
                'name'          => '创建新的版块分类',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'plate_cat_set_add_limit',
                'name'          => '为自己创建/管理的版块分类设置[版块创建限制]',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                )),
            array(
                'id'            => 'bbs_' . 'plate_cat_edit',
                'name'          => '编辑自己创建/管理的版块分类',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'bbs_' . 'plate_cat_delete',
                'name'          => '删除自己创建/管理的版块分类',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'    => 'bbs_' . 'plate_cat_edit_other',
                'name'  => '编辑其他人创建的版块分类(危险操作)',
                'roles' => array('cat_moderator'),
            ),
            array(
                'id'    => 'bbs_' . 'plate_cat_delete_other',
                'name'  => '删除其他人创建的版块分类(危险操作)',
                'roles' => array('cat_moderator'),
            ),
        );

        foreach (
            array(
                'forum_topic' => '帖子话题',
                'forum_tag'   => '帖子标签',
            ) as $k => $v
        ) {
            $user_all_caps['论坛[' . $v . ']'] = array(
                array(
                    'id'            => 'bbs_' . $k . '_add',
                    'name'          => '创建新的' . $v,
                    'exclude_roles' => array('all'),
                    'default'       => array(
                        'vip'   => 1,
                        'level' => 3,
                    ),
                ),
                array(
                    'id'            => 'bbs_' . $k . '_edit',
                    'name'          => '编辑自己创建的' . $v,
                    'exclude_roles' => array('all'),
                ),
                array(
                    'id'            => 'bbs_' . $k . '_delete',
                    'name'          => '删除自己创建的' . $v,
                    'exclude_roles' => array('all'),
                ),
                array(
                    'id'    => 'bbs_' . $k . '_edit_other',
                    'name'  => '编辑其他人创建的' . $v . '(危险操作)',
                    'roles' => array('moderator', 'plate_author', 'cat_moderator'),
                ),
                array(
                    'id'    => 'bbs_' . $k . '_delete_other',
                    'name'  => '删除其他人创建的' . $v . '(危险操作)',
                    'roles' => array('moderator', 'plate_author', 'cat_moderator'),
                ),
            );
        }
        $user_all_caps['论坛[用户权限]'] = array(
            array(
                'id'            => 'bbs_' . 'apply_moderator',
                'name'          => '申请成为版主',
                'exclude_roles' => array('all', 'moderator', 'plate_author', 'cat_moderator'),
                'default'       => array(
                    'vip'   => 1,
                    'level' => 3,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'moderator_apply_process',
                'name'    => '处理、审核版主申请',
                'roles'   => array('plate_author', 'cat_moderator'),
                'default' => array(
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'moderator_add',
                'name'    => '为管理的版块添加版主',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'moderator_edit',
                'name'    => '为管理的版块删除、修改版主',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
        );

        $user_all_caps['论坛[评论权限]'] = array(
            array(
                'id'            => 'bbs_' . 'comment_add',
                'name'          => '发布评论',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'      => 'bbs_' . 'comment_set_hot',
                'name'    => '将自己管理下的帖子的评论手动设置为神评',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'logged' => true,
                ),
            ),
        );

        $user_all_caps['论坛[其它权限]'] = array(
            array(
                'id'            => 'bbs_' . 'add_url_slug',
                'name'          => '创建[版块分类、话题、标签]的时候允许设置URL别名',
                'exclude_roles' => array('all'),
                'help'          => '依赖于对应的新建权限',
            ),
            array(
                'id'            => 'bbs_' . 'edit_url_slug',
                'name'          => '修改[版块分类、话题、标签]的时候允许修改URL别名',
                'help'          => '依赖于对应的编辑权限',
                'exclude_roles' => array('all'),
            ),
        );

        return $user_all_caps;

    }
}
