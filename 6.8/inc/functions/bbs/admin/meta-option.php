<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-05 20:25:29
 * @LastEditTime: 2022-09-14 16:05:48
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|后台功能文章meta配置
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

$zib_bbs = zib_bbs();
//forum_post
CSF::createMetabox('forum_extend', array(
    'title'     => $zib_bbs->posts_name . '选项',
    'post_type' => array('forum_post'),
    'context'   => 'side',
    'priority'  => 'high',
    'data_type' => 'unserialize',
));
CSF::createSection('forum_extend', array(
    'fields' => array(
        zib_bbs_admin_warning_csf(),
        array(
            'title'      => $zib_bbs->plate_name,
            'id'         => 'plate_id',
            'desc'       => '选择发布的' . $zib_bbs->plate_name,
            'default'    => '',
            'options'    => 'post',
            'query_args' => array(
                'post_type'      => 'plate',
                'posts_per_page' => -1,
            ),
            'settings'   => array(
                'min_length' => 2,
            ),
            'type'       => 'select',
        ),
        array(
            'content' => '<a href="' . esc_url(admin_url('post-new.php?post_type=plate')) . '" class="but jb-blue"><i class="fa fa-plus"></i>创建新' . $zib_bbs->plate_name . '</a>',
            'style'   => 'warning',
            'type'    => 'content',
            'class'   => 'compact',
        ),
        array(
            'title'   => '内容置顶',
            'id'      => 'topping',
            'default' => 0,
            'options' => zib_bbs_get_posts_topping_options(),
            'type'    => 'select',
        ),
        array(
            'title'   => '精华',
            'label'   => '将此帖子标记为精华内容',
            'id'      => 'essence',
            'default' => false,
            'type'    => 'switcher',
        ),
        array(
            'title'   => $zib_bbs->posts_name . '类型',
            'id'      => 'bbs_type',
            'default' => "",
            'desc'    => '为此' . $zib_bbs->posts_name . '设置类型，不同类型将会在列表显示不同风格的样式',
            'type'    => "radio",
            'options' => 'zib_bbs_get_posts_type_options',
        ),
        array(
            'title'   => '阅读量',
            'id'      => 'views',
            'default' => 0,
            'type'    => "number",
            'options' => 'zib_bbs_get_posts_type_options',
        ),
    ),
));

function zib_bbs_admin_warning_csf()
{
    $post_id = !empty($_REQUEST['post']) ? $_REQUEST['post'] : 0;
    global $zib_bbs;
    return array(
        'content' => $zib_bbs->posts_name . '的发布、修改都推荐在' . zib_bbs_get_posts_add_page_link(array('id' => $post_id), 'c-blue', '前台编辑器') . '进行！以避免逻辑错误',
        'style'   => 'warning',
        'type'    => 'submessage',
    );
}

CSF::createMetabox('forum_allow_view', array(
    'title'     => '阅读权限',
    'post_type' => array('forum_post'),
    'context'   => 'side',
    'priority'  => 'high',
    'data_type' => 'unserialize',
));

CSF::createSection('forum_allow_view', array(
    'fields' => array(zib_bbs_admin_allow_view_csf(false),
        zib_bbs_admin_allow_view_csf(true),
        array(
            'dependency' => array('allow_view', 'any', 'pay,points'),
            'title'      => '支付参数',
            'id'         => 'posts_zibpay',
            'type'       => 'fieldset',
            'class'      => 'compact',
            'fields'     => array(
                array(
                    'dependency' => array('allow_view', '==', 'points', 'all'),
                    'id'         => 'points_price',
                    'title'      => '积分售价',
                    'class'      => '',
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '积分',
                ),
                array(
                    'dependency' => array('allow_view', '==', 'points', 'all'),
                    'title'      => _pz('pay_user_vip_1_name') . '积分售价',
                    'id'         => 'vip_1_points',
                    'class'      => 'compact',
                    'subtitle'   => '填0则为' . _pz('pay_user_vip_1_name') . '免费',
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '积分',
                ),
                array(
                    'dependency' => array('allow_view', '==', 'points', 'all'),
                    'title'      => _pz('pay_user_vip_2_name') . '积分售价',
                    'id'         => 'vip_2_points',
                    'class'      => 'compact',
                    'subtitle'   => '填0则为' . _pz('pay_user_vip_1_name') . '免费',
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '积分',
                    'desc'       => '会员价格不能高于售价',
                ),
                array(
                    'dependency' => array('allow_view', '!=', 'points', 'all'),
                    'id'         => 'pay_price',
                    'title'      => '执行价',
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '元',
                ),
                array(
                    'dependency' => array('allow_view', '!=', 'points', 'all'),
                    'id'         => 'pay_original_price',
                    'title'      => '原价',
                    'class'      => 'compact',
                    'subtitle'   => '显示在执行价格前面，并划掉',
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '元',
                ),
                array(
                    'dependency' => array('allow_view|pay_original_price', '!=|!=', 'points|', 'all'),
                    'title'      => ' ',
                    'subtitle'   => '促销标签',
                    'class'      => 'compact',
                    'id'         => 'promotion_tag',
                    'type'       => 'textarea',
                    'default'    => '',
                    'attributes' => array(
                        'rows' => 1,
                    ),
                ),
                array(
                    'dependency' => array('allow_view', '!=', 'points', 'all'),
                    'title'      => _pz('pay_user_vip_1_name') . '价格',
                    'id'         => 'vip_1_price',
                    'class'      => 'compact',
                    'subtitle'   => '填0则为' . _pz('pay_user_vip_1_name') . '免费',
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '元',
                ),
                array(
                    'dependency' => array('allow_view', '!=', 'points', 'all'),
                    'title'      => _pz('pay_user_vip_2_name') . '价格',
                    'id'         => 'vip_2_price',
                    'class'      => 'compact',
                    'subtitle'   => '填0则为' . _pz('pay_user_vip_1_name') . '免费',
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '元',
                    'desc'       => '会员价格不能高于执行价',
                ),
                array(
                    'dependency' => array('allow_view', '!=', 'points', 'all'),
                    'title'      => '推广折扣',
                    'id'         => 'pay_rebate_discount',
                    'class'      => 'compact',
                    'subtitle'   => __('通过推广链接购买，额外优惠的金额', 'zib_language'),
                    'desc'       => __('1.需开启推广返佣功能  2.注意此金不能超过实际购买价，避免出现负数', 'zib_language'),
                    'default'    => '',
                    'type'       => 'number',
                    'unit'       => '元',
                ),
                array(
                    'title'    => '销量浮动',
                    'id'       => 'pay_cuont',
                    'subtitle' => __('为真实销量增加或减少的数量', 'zib_language'),
                    'default'  => '',
                    'type'     => 'number',
                ),
                array(
                    'title'      => ' ',
                    'subtitle'   => __('内容摘要', 'zib_language'),
                    'id'         => 'pay_doc',
                    'desc'       => __('填写内容摘要有助于用户了解该付费的大致内容', 'zib_language'),
                    'class'      => 'compact',
                    'type'       => 'textarea',
                    'attributes' => array(
                        'rows' => 1,
                    ),
                ),
            ),
        ),
        array(
            'dependency' => array('allow_view', 'any', 'pay,points'),
            'label'      => '只隐藏部分内容',
            'desc'       => '默认会隐藏全部内容，开启后则只会隐藏部分内容，请在内容中添加【隐藏内容-付费可见】内容',
            'id'         => 'pay_hide_part',
            'default'    => false,
            'type'       => 'switcher',
        ),

    ),
));

function zib_bbs_admin_allow_view_csf($is_roles = true)
{
    $vip = array();
    if (_pz('pay_user_vip_1_s', true)) {
        if (_pz('pay_user_vip_2_s', true)) {
            $vip = array(
                1 => _pz('pay_user_vip_1_name') . '及以上会员可查看',
                2 => _pz('pay_user_vip_2_name') . '可查看',
            );
        } else {
            $vip = array(
                1 => _pz('pay_user_vip_1_name') . '可查看',
            );
        }
    }
    $vip = $vip ? array(
        '' => '不限制会员角色',
    ) + $vip : false;

    $level_max = _pz('user_level_max', 10);
    $level     = array();
    for ($i = 1; $i <= $level_max; $i++) {
        $level[$i] = _pz('user_level_opt', 'LV' . $i, 'name_' . $i);
    }
    $level = $level ? array(
        '' => '不限制等级角色',
    ) + $level : false;

    $allow_view_roles = array();
    if ($vip) {
        $allow_view_roles[] = array(
            'title'   => '会员阅读权限',
            'id'      => 'vip',
            'default' => "",
            'type'    => "radio",
            'options' => $vip,
        );
    }
    if ($level) {
        $allow_view_roles[] = array(
            'title'   => '会员阅读权限',
            'id'      => 'level',
            'default' => "",
            'type'    => "radio",
            'options' => $level,
        );
    }
    if (_pz('user_auth_s', true)) {
        $allow_view_roles[] = array(
            'label'   => '允许认证用户查看',
            'id'      => 'auth',
            'default' => false,
            'type'    => 'switcher',
        );
    }

    if ($is_roles && $allow_view_roles) {
        return array(
            'dependency' => array('allow_view', '==', 'roles'),
            'title'      => '可查看用户组设置',
            'id'         => 'allow_view_roles',
            'type'       => 'fieldset',
            'class'      => 'compact',
            'fields'     => $allow_view_roles,
        );
    }
    $options = array(
        ''        => __('公开', 'zib_language'),
        'signin'  => __('登录后可查看', 'zib_language'),
        'comment' => __('评论后可查看', 'zib_language'),
    );
    if ($allow_view_roles) {
        $options['roles'] = __('部分用户可查看', 'zib_language');
    }
    $options['pay']    = '付费查看';
    $options['points'] = '支付积分后查看';
    return array(
        'title'   => '阅读权限',
        'id'      => 'allow_view',
        'default' => "",
        'type'    => "radio",
        'options' => $options,
    );

}

//版块选项
CSF::createMetabox('plate_extend', array(
    'title'     => $zib_bbs->plate_name . '选项',
    'post_type' => array('plate'),
    'context'   => 'side',
    'priority'  => 'high',
    'data_type' => 'unserialize',
));
CSF::createSection('plate_extend', array(
    'fields' => array(
        array(
            'content' => $zib_bbs->plate_name . '的创建、修改都推荐在前台进行！以避免逻辑错误',
            'style'   => 'warning',
            'type'    => 'submessage',
        ),
        array(
            'title'   => '图像',
            'id'      => 'thumbnail_url',
            'library' => 'image',
            'type'    => 'upload',
            'default' => false,
            'desc'    => '选择一张图片作为版块特色图像',
        ),
        /**
        array(
        'title'   => $zib_bbs->plate_name . '类型',
        'id'      => 'plate_type',
        'default' => "",
        'desc'    => '为此' . $zib_bbs->plate_name . '设置类型，不同类型将会在列表显示不同风格的样式',
        'type'    => "radio",
        'options' => 'zib_bbs_get_plate_type_options',
        ),
         */
        array(
            'title'       => $zib_bbs->plate_moderator_name,
            'id'          => 'moderator',
            'class'       => 'compact',
            'options'     => 'user',
            'default'     => array(),
            'placeholder' => '输入用户名、昵称等关键词以搜索用户',
            'desc'        => '输入用户名、昵称等关键词以搜索用户<br/>您可以在主题设置中管理' . $zib_bbs->plate_moderator_name . '权限<br/>请勿将管理员、分区版本、版块创建者设置为版主！',
            'chosen'      => true,
            'multiple'    => true,
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'type'        => 'select',
        ),
        array(
            'id'      => 'add_limit',
            'title'   => '发帖限制',
            'desc'    => '设置一个限制选项，设置后会根据对应选项的限制规则判断是否允许创建版块',
            'default' => 0,
            'options' => zib_bbs_get_add_limit_options('posts'),
            'type'    => 'radio',
        ),
    ),
));

//版块TAB栏目
CSF::createMetabox('plate_tab', array(
    'title'     => '页面配置',
    'post_type' => array('plate'),
    'context'   => 'advanced',
    'priority'  => 'high',
    'data_type' => 'unserialize',
));
CSF::createSection('plate_tab', array(
    'fields' => array(
        array(
            'title'   => '单独配置Tab栏目',
            'label'   => '如需单独配置Tab栏目，请开启此项目',
            'id'      => 'plate_tab_alone_s',
            'default' => false,
            'type'    => 'switcher',
        ),
        array(
            'dependency'   => array('plate_tab_alone_s', '!=', ''),
            'title'        => '版块帖子栏目',
            'subtitle'     => '版块页面主要内容',
            'desc'         => '在版块页面显示的栏目内容，请至少保证有两个栏目<br>会自动在第一个栏目内显示置顶文章(置顶文章只会显示为简约模式)<br>每一个tab栏目均独立的地址，地址结尾添加?index=tab序号即可',
            'button_title' => '添加栏目',
            'min'          => 2,
            'id'           => 'plate_tab',
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
            'dependency' => array('plate_tab_alone_s', '!=', ''),
            'title'      => '栏目默认显示',
            'subtitle'   => '默认显示第几个栏目TAB',
            'id'         => 'tab_active_index',
            'default'    => 1,
            'type'       => 'spinner',
            'step'       => 1,
        ),
    ),
));

//为版块分类添加参数
CSF::createTaxonomyOptions('plate_cat_extend', array(
    'title'     => $zib_bbs->plate_name . '分类选项',
    'taxonomy'  => 'plate_cat',
    'data_type' => 'unserialize',
));
CSF::createSection('plate_cat_extend', array(
    'fields' => array(
        array(
            'title'       => $zib_bbs->cat_moderator_name,
            'id'          => 'moderator',
            'options'     => 'user',
            'default'     => array(),
            'placeholder' => '输入用户名、昵称等关键词以搜索用户',
            'desc'        => '输入用户名、昵称等关键词以搜索用户<br/>您可以在主题设置中管理[' . $zib_bbs->cat_moderator_name . ']的权限',
            'chosen'      => true,
            'multiple'    => true,
            'ajax'        => true,
            'settings'    => array(
                'min_length' => 2,
            ),
            'type'        => 'select',
        ),
        array(
            'id'      => 'add_limit',
            'title'   => '版块创建限制',
            'desc'    => '设置一个限制选项，设置后会根据对应选项的限制规则判断是否允许创建版块',
            'default' => 0,
            'class'   => 'button-mini',
            'options' => zib_bbs_get_add_limit_options('plate'),
            'type'    => 'radio',
        ),
    ),
));
