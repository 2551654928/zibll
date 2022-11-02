<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-08-23 23:16:33
 * @LastEditTime: 2022-09-26 00:49:37
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|编辑功能相关
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

class zib_bbs_edit
{

    public static function posts_in($post = null)
    {
        $args = array(
            'post_id'        => 0,
            'title'          => '',
            'post_date'      => 0,
            'post_status'    => 0,
            'comment_status' => 0,
            'post_author'    => 0,
            'post_date'      => 0,
            'content'        => '',
            'plate'          => 0,
            'topic'          => 0,
            'tag'            => 0,
            'bbs_type'       => 0,
        );
        if ($post) {
            $post = get_post($post);
            if (isset($post->ID)) {
                $args['post_id']        = $post->ID;
                $args['post_author']    = $post->post_author;
                $args['post_date']      = $post->post_date;
                $args['post_date']      = $post->post_date;
                $args['post_status']    = $post->post_status;
                $args['comment_status'] = $post->comment_status;
                $args['title']          = $post->post_title;
                $args['content']        = $post->post_content;
                $args['plate']          = zib_bbs_get_plate_id($post->ID);
                $get_topic              = get_the_terms($post, 'forum_topic');
                if (!empty($get_topic[0]->term_id)) {
                    $args['topic'] = $get_topic[0]->term_id;
                }
                $tags = get_the_terms($post, 'forum_tag');
                if ($tags) {
                    $args['tag'] = array_column((array) $tags, 'term_id');
                }
                $args['bbs_type'] = get_post_meta($post->ID, 'bbs_type', true);
            }
        } else {
            $args['plate'] = !empty($_REQUEST['plate_id']) ? (int) $_REQUEST['plate_id'] : 0;
            $args['topic'] = !empty($_REQUEST['topic_id']) ? (int) $_REQUEST['topic_id'] : 0;
            $args['tag']   = !empty($_REQUEST['tag_id']) ? $_REQUEST['tag_id'] : 0;
        }
        return $args;
    }

    public static function desc_text($inargs = array())
    {
        $submit_text = '';
        if ($inargs['post_status'] && 'draft' == $inargs['post_status']) {
            $submit_text = '<div class="muted-2-color">正在编辑草稿</div>';
        }

        if ($inargs['post_id']) {
            if ($inargs['post_author'] && get_current_user_id() != $inargs['post_author']) {
                //如果不是编辑自己的文章，则给予额提示
                $post_author = $inargs['post_author'];
                $submit_text = '<div class="muted-2-color mt6 mr10">正在修改[' . zib_get_user_name("id=$post_author&auth=0&level=0&medal=0&class=&name_class=focus-color") . ']提交的内容</div>';
            }
            $submit_text .= '<div class="muted-3-color px12 mt6"><span class="badg badg-sm mr6">编辑内容</span>最后更新：' . get_the_modified_time('Y-m-d H:i:s', $inargs['post_id']) . '</div>';
        }
        $html = '<div class="submit-text em09 flex ac hh jsb">' . $submit_text . '</div>';
        return $html;
    }

    public static function posts_submit($inargs = array())
    {
        extract($inargs);
        if (!get_current_user_id()) {
            return '<div class="but-average mb20"><botton type="button" action="bbs_posts_draft" name="submit" class="but jb-green signin-loader padding-lg"><i class="fa fa-fw fa-dot-circle-o"></i>保存草稿</botton><botton type="button" action="bbs_posts_save" class="but jb-blue padding-lg signin-loader "><i class="fa fa-fw fa-check-square-o"></i>提交发布</botton></div>';
        }

        $hidden = '';
        $hidden .= '<input type="hidden" name="post_id" value="' . $post_id . '">';
        $hidden .= '<input type="hidden" name="post_author" value="' . $post_author . '">';
        $hidden .= '<input type="hidden" name="post_date" value="' . $post_date . '">';
        $hidden .= wp_nonce_field('bbs_edit_posts', '_wpnonce', false, false); //安全效验

        $_draft = '';

        if ('publish' !== $post_status && 'pending' !== $post_status && (!$post_id || !zib_current_user_can('posts_audit', $post_id))) {
            //已经是已发布状态和待审核状态则不能保存草稿
            $_draft = '<botton type="button" action="bbs_posts_draft" name="submit" class="but jb-green bbs-posts-submit padding-10"><i class="fa fa-fw fa-dot-circle-o"></i>保存草稿</botton>';
        }

        //人机验证
        $verification_input = '';
        if (_pz('verification_bbspost_s')) {
            $verification_input = zib_get_machine_verification_input('bbs_post_submit');
            if (_pz('user_verification_type') === 'image') {
                $verification_input = '<div class="zib-widget" style="margin-bottom: -18px;">' . $verification_input . '</div>';
            }
        }

        $submit_text = $post_id ? '保存' : '发布';
        $btns        = $verification_input;
        $btns .= '<div class="but-average mb20">';
        $btns .= $_draft;
        $btns .= '<botton type="button" action="bbs_posts_save" name="submit" class="but jb-blue bbs-posts-submit padding-10"><i class="fa fa-paper-plane-o fa-fw"></i>提交' . $submit_text . '</botton>';
        $btns .= $hidden;
        $btns .= '</div>';

        return $btns;
    }

    //帖子标题
    public static function posts_title($title = '', $placeholder = '请输入标题', $class = "mb10", $name = 'post_title')
    {

        $class = $class ? ' ' . $class : "";

        $html = '<div class="relative edit-posts-title' . $class . '">';
        $html .= '<textarea type="text" class="line-form-input input-lg new-title" name="' . $name . '" tabindex="1" placeholder="' . $placeholder . '" rows="1" autoHeight="true" maxHeight="83">' . $title . '</textarea>';
        $html .= '<i class="line-form-line"></i>';
        $html .= '</div>';
        return $html;
    }

    public static function posts_editor($in_args = array(), $id = "post_content", $settings = array())
    {
        $content = $in_args['content'];
        if ((!$in_args['post_id'] && zib_bbs_current_user_can('posts_upload_img')) || ($in_args['post_id'] && zib_bbs_current_user_can('posts_upload_img', $in_args['post_id']))) {
            add_filter('tinymce_upload_img', '__return_true'); //上传图片
        }
        if ((!$in_args['post_id'] && zib_bbs_current_user_can('posts_hide')) || ($in_args['post_id'] && zib_bbs_current_user_can('posts_hide', $in_args['post_id']))) {
            add_filter('tinymce_hide', '__return_true'); //隐藏内容
        }
        if ((!$in_args['post_id'] && zib_bbs_current_user_can('posts_upload_video')) || ($in_args['post_id'] && zib_bbs_current_user_can('posts_upload_video', $in_args['post_id']))) {
            add_filter('tinymce_upload_video', '__return_true'); //上传视频
        }

        if ((!$in_args['post_id'] && zib_bbs_current_user_can('posts_iframe_video')) || ($in_args['post_id'] && zib_bbs_current_user_can('posts_iframe_video', $in_args['post_id']))) {
            add_filter('tinymce_iframe_video', '__return_true'); //嵌入视频
        }

        if (_pz('bbs_post_pay_hide_type_s', true)) {
            add_filter('tinymce_hide_pay', '__return_true');
        }

        $defaults = array(
            'textarea_rows'  => 20,
            'editor_height'  => (wp_is_mobile() ? 460 : 486),
            'media_buttons'  => false,
            'default_editor' => 'tinymce',
            'quicktags'      => false,
            'editor_css'     => '<link rel="stylesheet" href="' . ZIB_TEMPLATE_DIRECTORY_URI . '/css/new-posts.min.css?ver=' . THEME_VERSION . '" type="text/css">',
            'teeny'          => false,
        );
        $settings = wp_parse_args((array) $settings, $defaults);

        wp_editor($content, $id, $settings);
    }

    //查看权限，阅读限制设置，允许查看
    public static function allow_view_set($id = 0, $class = 'zib-widget mb10-sm')
    {
        if (!get_current_user_id()) {
            //未登录
            return '<div class="allow-view-drop signin-loader"><div class="flex ac jsb drop-btn ' . $class . '">
                        <div class="flex ac">' . zib_get_svg('view', null, 'mr6') . '阅读权限</div>
                        <div class="flex ac"><span class="em09 muted-2-color">公开</span><i class="ml6 fa fa-angle-right em12"></i></div>
                    </div></div>';
        }

        if ((!$id && !zib_bbs_current_user_can('posts_allow_view_add')) || ($id && !zib_bbs_current_user_can('posts_allow_view_edit', $id))) {
            return;
        }

        $wp_is_mobile = wp_is_mobile();

        $in_type      = '';
        $in_trpe_text = '公开';
        if ($id) {
            $in_type          = get_post_meta($id, 'allow_view', true);
            $allow_view_types = zib_bbs_get_post_allow_view_options();
            $in_trpe_text     = isset($allow_view_types[$in_type]) ? $allow_view_types[$in_type] : $in_trpe_text;
        }
        $con = self::allow_view_set_content($id);

        //内容
        $ok_attr = $wp_is_mobile ? 'data-drawer="hide" drawer-selector=".allow-view-drawer"' : 'data-toggle-class="open" data-target=".allow-view-drop"';
        $aciton  = '<div class="muted-color pointer text-right mb10" ' . $ok_attr . '>完成</div>';
        //显示按钮
        $btn_attr = $wp_is_mobile ? 'data-drawer="show" drawer-title="阅读权限设置" drawer-selector=".allow-view-drawer"' : 'data-toggle-class="open" data-target=".allow-view-drop"';
        $btn_html = '<div ' . $btn_attr . ' class="flex ac jsb drop-btn ' . $class . '">
                                <div class="flex ac">' . zib_get_svg('view', null, 'mr6') . '阅读权限</div>
                                <div class="flex ac"><span name="allow_view" class="em09 muted-2-color">' . $in_trpe_text . '</span><i class="ml6 fa fa-angle-right em12"></i></div>
                            </div>';

        if ($wp_is_mobile) {
            $dropdown = '<div class="drawer-sm right allow-view-drawer">' . $aciton . '<div class="zib-widget">' . $con . '</div></div>';
            $html     = '<div class="drop-select dependency-box">' . $btn_html . $dropdown . '</div>';
        } else {
            $dropdown = '<div class="dropdown-menu fluid" style="margin-bottom: -4px;"><div class="padding-10 flex jsb">设置阅读权限' . $aciton . '</div><div class="padding-w10 select-drop-box">' . $con . '</div></div>';
            $html     = '<div class="allow-view-drop drop-select dropdown dependency-box">' . $btn_html . $dropdown . '</div>';
        }
        return $html;
    }

    public static function plate_allow_view_set_content($id = 0, $class = 'mt20')
    {
        $in_type = '';

        if ($id) {
            $in_type = get_post_meta($id, 'allow_view', true);
        }

        $allow_view_types = array(
            ''       => '公开',
            'signin' => '登录后可查看',
            'roles'  => '部分用户可查看',
        );
        //选择类型
        $type_html       = '';
        $type_lists_html = '';
        $in_trpe_text    = '公开';
        $roles_lists     = self::allow_view_roles_lists($id);
        if (!$roles_lists) {
            unset($allow_view_types['roles']);
        }
        foreach ($allow_view_types as $k => $v) {
            $_class = '';
            if ($k == $in_type) {
                $_class       = ' active';
                $in_trpe_text = $v;
            }
            $type_lists_html .= '<span data-for="allow_view" data-value="' . $k . '" class="mb6 mr6 badg p2-10 pointer' . $_class . '">' . $v . '</span>';
        }
        $type_html = '<div class="">' . $type_lists_html . '<input type="hidden" name="allow_view" value="' . $in_type . '"></div>';
        //汇总
        $con = $type_html;
        $con .= $roles_lists ? '<div class="' . $class . '" data-controller="allow_view" data-condition="==" data-value="roles" ' . ('roles' != $in_type ? ' style="display: none;"' : '') . '>' . $roles_lists . '</div>' : '';

        return $con;

    }

    //文章的限制阅读
    public static function allow_view_set_content($id = 0)
    {
        $lists             = '';
        $controller        = '';
        $allow_view_types  = zib_bbs_get_post_allow_view_options();
        $in_type           = $id ? get_post_meta($id, 'allow_view', true) : '';
        $pay_public_option = false;
        foreach ($allow_view_types as $k => $v) {
            $_class = $k == $in_type ? ' active' : '';
            switch ($k) {
                case 'roles':
                    $roles_lists = self::allow_view_roles_lists($id);
                    if ($roles_lists) {
                        $pay_public_option = true;
                        $controller .= '<div class="mt20" data-controller="allow_view" data-condition="==" data-value="' . $k . '" ' . ($k != $in_type ? ' style="display: none;"' : '') . '>' . $roles_lists . '</div>';
                        $lists .= '<span data-for="allow_view" data-value="' . $k . '" class="mb6 mr6 badg p2-10 pointer mi-col-2' . $_class . '">' . $v . '</span>';
                    }
                    break;

                case 'points':
                    if (_pz('points_s') && zib_bbs_current_user_can('posts_allow_view_points', $id)) {
                        $pay_public_option = true;
                        $controller .= '<div class="mt20" data-controller="allow_view" data-condition="==" data-value="' . $k . '" ' . ($k != $in_type ? ' style="display: none;"' : '') . '>' . self::points_option($id) . '</div>';
                        $lists .= '<span data-for="allow_view" data-value="' . $k . '" class="mb6 mr6 badg p2-10 pointer mi-col-2' . $_class . '">' . $v . '</span>';
                    }
                    break;

                case 'pay':
                    if (zib_bbs_current_user_can('posts_allow_view_pay', $id)) {
                        $controller .= '<div class="mt20" data-controller="allow_view" data-condition="==" data-value="' . $k . '" ' . ($k != $in_type ? ' style="display: none;"' : '') . '>' . self::pay_option($id) . '</div>';
                        $lists .= '<span data-for="allow_view" data-value="' . $k . '" class="mb6 mr6 badg p2-10 pointer mi-col-2' . $_class . '">' . $v . '</span>';
                    }
                    break;

                default:
                    $lists .= '<span data-for="allow_view" data-value="' . $k . '" class="mb6 mr6 badg p2-10 pointer mi-col-2' . $_class . '">' . $v . '</span>';

            }

        }
        $controller .= $pay_public_option ? '<div class="mt20" data-controller="allow_view" data-condition="any" data-value="points,pay" ' . (!in_array($in_type, ['points', 'pay']) ? ' style="display: none;"' : '') . '>' . self::pay_public_option($id) . '</div>' : '';

        return '<div class="">' . $lists . '</div><div class="">' . $controller . '</div><input type="hidden" name="allow_view" value="' . $in_type . '">';
    }

    //付费可见
    public static function pay_option($post_id = 0)
    {
        $in      = (array) get_post_meta($post_id, 'posts_zibpay', true);
        $default = array(
            'points_price' => '',
            'vip_1_points' => '',
            'vip_2_points' => '',
            'pay_price'    => '',
            'vip_1_price'  => '',
            'vip_2_price'  => '',
        );
        $in = array_merge($default, $in);

        //设置会员价
        $vip_input_s = _pz('bbs_post_pay_vip_price_s');
        $vip_1_s     = _pz('pay_user_vip_1_s');
        $vip_2_s     = _pz('pay_user_vip_2_s');

        $vip_pay_price_input = '';
        if ($vip_input_s && $vip_1_s) {
            $vip_pay_price_input .= '<div class="relative mt6">
            <div class="flex ab">
                <div class="muted-color mb6 flex0">' . zibpay_get_vip_icon(1, 'em12 mr6', false) . _pz('pay_user_vip_1_name') . '价格</div><input type="number" name="posts_zibpay[vip_1_price]" value="' . $in['vip_1_price'] . '" style="padding: 0;" class="line-form-input em2x key-color text-right">
                <i class="line-form-line"></i>
            </div>
        </div>';
        }
        if ($vip_input_s && $vip_2_s) {
            $vip_pay_price_input .= '<div class="relative mt6">
            <div class="flex ab">
                <div class="muted-color mb6 flex0">' . zibpay_get_vip_icon(2, 'em12 mr6', false) . _pz('pay_user_vip_2_name') . '价格</div><input type="number" name="posts_zibpay[vip_2_price]" value="' . $in['vip_2_price'] . '" style="padding: 0;" class="line-form-input em2x key-color text-right">
                <i class="line-form-line"></i>
            </div>
        </div>';
        }

        if ($vip_pay_price_input) {
            $vip_pay_price_input .= '<div class="px12 mt6 muted-color">会员价不能高于普通价，为0则为会员免费</div>';
        }

        $html = '<div class="mb20"><div class="relative">
                    <div class="flex ab">
                    <div class="muted-color mb6 flex0">' . zib_get_svg('money-color-2', null, 'mr6 em12') . '设置价格</div><input type="number" name="posts_zibpay[pay_price]" value="' . $in['pay_price'] . '" style="padding: 0;" class="line-form-input em2x key-color text-right">
                    <i class="line-form-line"></i>
                </div>
                </div>' . $vip_pay_price_input . '</div>';

        return $html;
    }

    //积分可见
    public static function points_option($post_id = 0)
    {
        $in      = (array) get_post_meta($post_id, 'posts_zibpay', true);
        $default = array(
            'points_price' => '',
            'vip_1_points' => '',
            'vip_2_points' => '',
            'pay_price'    => '',
            'vip_1_price'  => '',
            'vip_2_price'  => '',
        );
        $in = array_merge($default, $in);

        $vip_input_s = _pz('bbs_post_pay_vip_price_s');
        $vip_1_s     = _pz('pay_user_vip_1_s');
        $vip_2_s     = _pz('pay_user_vip_2_s');

        $vip_pay_price_input = '';
        if ($vip_input_s && $vip_1_s) {
            $vip_pay_price_input .= '<div class="relative mt6">
        <div class="flex ab">
            <div class="muted-color mb6 flex0">' . zibpay_get_vip_icon(1, 'em12 mr6', false) . _pz('pay_user_vip_1_name') . '积分</div><input type="number" name="posts_zibpay[vip_1_points]" value="' . $in['vip_1_points'] . '" style="padding: 0;" class="line-form-input em2x key-color text-right">
            <i class="line-form-line"></i>
        </div>
    </div>';
        }
        if ($vip_input_s && $vip_2_s) {
            $vip_pay_price_input .= '<div class="relative mt6">
        <div class="flex ab">
            <div class="muted-color mb6 flex0">' . zibpay_get_vip_icon(2, 'em12 mr6', false) . _pz('pay_user_vip_2_name') . '积分</div><input type="number" name="posts_zibpay[vip_2_points]" value="' . $in['vip_2_points'] . '" style="padding: 0;" class="line-form-input em2x key-color text-right">
            <i class="line-form-line"></i>
        </div>
    </div>';
        }
        if ($vip_pay_price_input) {
            $vip_pay_price_input .= '<div class="px12 mt6 muted-color">会员价不能高于普通价，为0则为会员免费</div>';
        }

        $html = '<div class="mb20">
                <div class="relative">
                    <div class="flex ab">
                        <div class="muted-color mb6 flex0">' . zib_get_svg('points-color', null, 'mr6 em12') . '设置积分</div><input type="number" name="posts_zibpay[points_price]" value="' . $in['points_price'] . '" style="padding: 0;" class="line-form-input em2x key-color text-right">
                        <i class="line-form-line"></i>
                    </div>
                </div>' . $vip_pay_price_input . '</div>';

        return $html;
    }

    //支付的公共配置
    public static function pay_public_option($post_id = 0)
    {
        global $zib_bbs;
        $in_html = '';
        $input   = '';
        $name    = $zib_bbs->posts_name;

        $in            = (array) get_post_meta($post_id, 'posts_zibpay', true);
        $pay_hide_part = get_post_meta($post_id, 'pay_hide_part', true);
        $pay_doc       = !empty($in['pay_doc']) ? esc_attr($in['pay_doc']) : '';
        $user_id       = get_current_user_id();
        $desc          = '填写内容摘要有助于用户了解该付费的大致内容';

        if (_pz('pay_income_s')) {
            $income_ratio = zibpay_get_user_income_ratio($user_id);
            if ($income_ratio) {
                $desc .= '<div class="c-blue px12 mt6">您已参与创作分成，本文获得的收益将与您分成，您可以进入<a target="_blank" class="c-blue-2" href="' . zib_get_user_center_url('income') . '">用户中心-创作分成</a>查看您的分成比例及分成详情</div>';
            }
        }

        //设置隐藏模式
        $hide_type_s     = _pz('bbs_post_pay_hide_type_s', true);
        $hide_type_input = '';
        if ($hide_type_s) {
            $hide_type_input = '<div class="mb10 muted-box padding-10">
            <label class="muted-color flex ac jsb" style="font-weight: normal; ">
                <input class="hide"' . ($pay_hide_part ? ' checked="checked"' : '') . ' type="checkbox" name="pay_hide_part">只隐藏部分内容<div class="form-switch flex0"></div>
            </label>
            <div class="muted-3-color px12 mt3">默认会隐藏' . $name . '全部内容，开启后则只会隐藏部分内容，请在内容中添加【隐藏内容-付费可见】内容</div>
        </div>';
        }

        return $hide_type_input . '<div class="relative">
                    <textarea class="form-control" name="posts_zibpay[pay_doc]" placeholder="请输入内容摘要" rows="3">' . $pay_doc . '</textarea>
                    <div class="muted-3-color px12 mb6 mt3">' . $desc . '</div>
                </div>';
    }

    //获取全部角色
    public static function allow_view_roles_lists($post_id = 0)
    {
        $allow_view_roles_null = array(
            'vip'   => '',
            'level' => '',
            'auth'  => '',
        );
        $in = array();
        if ($post_id) {
            $in = (array) get_post_meta($post_id, 'allow_view_roles', true);
        }
        $in = wp_parse_args($in, $allow_view_roles_null);

        $vip_roles_lists = '';
        $vip_count       = array();
        //会员角色
        if (_pz('pay_user_vip_2_s', true)) {
            $vip_count[] = 2;
        }
        if (_pz('pay_user_vip_1_s', true)) {
            $vip_count[] = 1;
        }
        if ($vip_count) {
            if (count($vip_count) < 2) {
                $vip_roles_lists = '<div><label class="badg p2-10 mr10 pointer"><input' . ($in['vip'] ? ' checked="checked"' : '') . ' type="radio" name="allow_view_roles[vip]" value="1">' . zibpay_get_vip_icon(1, 'mr6 ml6 em12') . _pz('pay_user_vip_1_name') . '可查看</label></div>';
            } else {
                $vip_roles_lists = '<div><label class="badg p2-10 mr10 pointer"><input type="radio"' . (checked($in['vip'], 1, false)) . ' name="allow_view_roles[vip]" value="1">' . zibpay_get_vip_icon(1, 'mr6 ml6 em12') . _pz('pay_user_vip_1_name') . '及以上会员可查看</label></div>';
                $vip_roles_lists .= '<div><label class="badg p2-10 mr10 pointer"><input type="radio"' . (checked($in['vip'], 2, false)) . ' name="allow_view_roles[vip]" value="2">' . zibpay_get_vip_icon(2, 'mr6 ml6 em12') . _pz('pay_user_vip_2_name') . '可查看</label></div>';
            }
            $vip_roles_lists = '<div><label class="badg p2-10 mr10 pointer"><input type="radio"' . (!$in['vip'] ? ' checked="checked"' : '') . ' name="allow_view_roles[vip]" value=""><span class="ml6">不限制会员</span></label></div>' . $vip_roles_lists;
            $vip_roles_lists = '<div class="mb20"><div class="muted-color mb6">会员限制</div><div class="">' . $vip_roles_lists . '</div><div class="muted-3-color em09">允许查看的会员组</div></div>';
        }

        //等级角色
        $level_max         = _pz('user_level_max', 10);
        $level_roles_lists = '';
        for ($i = 1; $i <= $level_max; $i++) {
            $level_roles_lists .= '<label class="badg p2-10 mr10 pointer"><input type="radio"' . (checked($in['level'], $i, false)) . ' name="allow_view_roles[level]" value="' . $i . '">' . zib_get_level_badge($i, 'ml6 em12') . '</label>';
        }
        $level_roles_lists = $level_roles_lists ? '<div><label class="badg p2-10 mr10 pointer"><input type="radio"' . (!$in['level'] ? ' checked="checked"' : '') . ' name="allow_view_roles[level]" value=""><span class="ml6">不限制等级</span></label></div>' . $level_roles_lists : '';
        $level_roles_lists = $level_roles_lists ? '<div class="mb20"><div class="muted-color mb6">等级限制</div><div class="">' . $level_roles_lists . '</div><div class="muted-3-color em09">用户的等级达到所选等级时允许查看</div></div>' : '';

        $auth_roles_lists = '';
        if (_pz('user_auth_s', true)) {
            $auth_roles_lists = '<label class="muted-color flex ac jsb" style="font-weight: normal; "><input class="hide"' . ($in['auth'] ? ' checked="checked"' : '') . ' type="checkbox" name="allow_view_roles[auth]">允许认证用户查看<div class="form-switch flex0"></div></label>';
        }

        $roles_lists = $vip_roles_lists . $level_roles_lists . $auth_roles_lists;

        return $roles_lists;
    }

    //发起投票
    public static function vote_set($id = 0, $class = 'zib-widget mb10-sm')
    {
        if (!get_current_user_id()) {
            //未登录
            return '<div class="vote-set signin-loader">
                        <div class="flex ac jsb drop-btn ' . $class . '">
                            <div class="flex ac"><i class="fa fa-bar-chart mr6"></i>投票</div>
                            <i class="ml6 fa fa-angle-right em12"></i>
                        </div>
                    </div>';
        }

        //权限判断
        if (($id && !zib_bbs_current_user_can('posts_vote_edit', $id)) || (!$id && !zib_bbs_current_user_can('posts_vote_add'))) {
            return '';
        }

        $vote_opt_null = array(
            'title'      => '',
            'type'       => 'single', //创建时间
            'time'       => '', //创建时间
            'time_limit' => 0, //有效时间限制
            'options'    => array(
                '', '',
            ),
        );
        $vote_opt = array();
        $is_vote  = false;
        $can_edit = true;
        $vote_ing = false;

        if ($id) {
            //已经开始投票的，则不允许编辑，除了管理员
            $vote_ing = get_post_meta($id, 'vote_data', true); //已经投票内容

            if ($vote_ing) {
                $can_edit = zib_bbs_current_user_can('posts_vote_ing_edit', $id);
            }

            $vote_opt = get_post_meta($id, 'vote_option', true);
            $is_vote  = get_post_meta($id, 'vote', true);
        }
        $opt = wp_parse_args($vote_opt, $vote_opt_null);

        if (!$can_edit) {
            return '<div class="vote-set">
                        <div class="flex ac jsb ' . $class . '">
                            <div class="flex ac"><i class="fa fa-bar-chart mr6"></i>投票</div>
                            <div><span class="c-red badg badg-sm">投票已开始，已无法修改</span></div>
                        </div>
                    </div>';
        }

        //投票标题
        $title_html = $vote_ing ? '<div class="c-red badg mt10 em09" style="width: 100%;"><i class="fa fa-fw fa-info-circle mr6"></i>投票已开始，请谨慎修改投票选项</div>' : '';
        $title_html .= '<div class="relative mb10"><input type="text" class="line-form-input" name="vote[title]" placeholder="请输入投票标题" value="' . esc_attr($opt['title']) . '"><i class="line-form-line"></i></div>';

        //投票选项
        $option_html = '';
        foreach ($opt['options'] as $k) {
            $option_html .= '<div class="cloneable-item vote-opt-item form-right-icon mb6"><input type="input" class="form-control" name="vote[options][]" value="' . esc_attr($k) . '" placeholder="请输入选项内容"><a href="javascript:;" class="abs-right muted-color cloneable-remove">' . zib_get_svg('close') . '</a></div>';
        }
        $option_html = '<div class="cloneable vote-options" data-max="' . (_pz('bbs_vote_max') ?: 8) . '" data-min="2">' . $option_html . '</div>';

        $option_html .= '<botton type="button" class="cloneable-add but block c-blue">' . zib_get_svg('add') . '添加选项</botton>';

        $option_html = '<div class="mb10"><div class="em09 muted-2-color mb6">投票选项</div>' . $option_html . '</div>';

        //投票类型
        $type_html  = '';
        $types_args = array(
            'single'   => '单选',
            'multiple' => '多选',
            'pk'       => '双选PK',
        );
        foreach ($types_args as $k => $v) {
            $_class = $k == $opt['type'] ? ' active' : '';
            $type_html .= '<span data-for="vote[type]" data-value="' . $k . '" class="badg mr6 p2-10 pointer' . $_class . '">' . $v . '</span>';
        }
        $type_html .= '<input type="hidden" name="vote[type]" value="' . esc_attr($opt['type']) . '">';
        $type_html = '<div class="mb10"><div class="em09 muted-2-color mb6">投票类型</div>' . $type_html . '</div>';

        //投票有效期
        $time_limit_html = '';
        $time_limit_args = array(
            0  => '永久',
            1  => '1天',
            7  => '7天',
            30 => '30天',
        );
        foreach ($time_limit_args as $k => $v) {
            $_class = $k == $opt['time_limit'] ? ' active' : '';
            $time_limit_html .= '<span data-for="vote[time_limit]" data-value="' . $k . '" class="badg mr6 p2-10 pointer' . $_class . '">' . $v . '</span>';
        }
        $time_limit_html .= '<input type="hidden" name="vote[time_limit]" value="' . $opt['time_limit'] . '">';
        $time_limit_html = '<div class=""><div class="em09 muted-2-color mb6">投票有效期</div>' . $time_limit_html . '</div>';

        if ($opt['time']) {
            $time_limit_html .= '<div class="mt20 flex ac jsb muted-color"><div class="em09 muted-2-color">投票开始时间</div>' . esc_attr($opt['time']) . '</div>';
        }

        $html = '<div class="dependency-box vote-set ' . $class . '">';
        $html .= '<div class="flex ac jsb">
                    <div class="flex ac"><i class="fa fa-bar-chart mr6"></i>投票</div>
                    <label style="margin: 0;"><input class="hide"' . ($is_vote ? ' checked="checked"' : '') . 'name="vote_s" type="checkbox"><div class="form-switch flex0"></div></label>
                </div>';

        $html .= '<div class="vote-set" data-controller="vote_s" data-condition="!=" data-value="" ' . (!$is_vote ? ' style="display: none;"' : '') . '>';
        $html .= $title_html;
        $html .= $type_html;
        $html .= $option_html;
        $html .= $time_limit_html;
        $html .= '</div>';
        $html .= '</div>';
        return $html;

    }

    public static function status($in_args = array(), $class = 'zib-widget mb10-sm')
    {
        //权限判断
        if (empty($in_args['post_id']) || !zib_current_user_can('posts_audit', $in_args['post_id'])) {
            return;
        }
        $lists       = '';
        $status_args = array(
            'pending' => '待审核',
            'draft'   => '草稿',
            'publish' => '发布',
        );
        $in_status = isset($status_args[$in_args['post_status']]) ? $in_args['post_status'] : 'pending';

        foreach ($status_args as $k => $v) {
            $_class = $k == $in_status ? ' active' : '';
            $lists .= '<span data-for="post_status" data-value="' . $k . '" class="badg mr3 p2-10 pointer' . $_class . '">' . $v . '</span>';
        }

        $more = '';
        $html = '<div class="type-drop ' . $class . '">
                    <div class="flex ac jsb">
                        <div class="flex ac c-red"><i class="fa fa-thumb-tack mr10"></i>设置状态</div>
                        <div class="em09">
                        ' . $lists . '
                        </div>
                    </div>
                        ' . $more . '
                        <input type="hidden" name="post_status" value="' . $in_status . '">
                    </div>';
        return $html;
    }

    //发布类型
    public static function type_select($id = 0, $class = 'zib-widget mb10-sm')
    {
        $type = '';
        if ($id) {
            $type = get_post_meta($id, 'bbs_type', true);
        }

        $type       = $type ? $type : '';
        $post_types = self::post_types($id);
        if (!$post_types) {
            return;
        }
        $lists = '';
        foreach ($post_types as $k => $v) {
            $_class = $k == $type ? ' active' : '';
            $lists .= '<span data-for="bbs_type" data-value="' . $k . '" class="but p2-10 pointer' . $_class . '">' . $v . '</span>';

            switch ($k) {
                case 'question':

                    break;
            }
        }
        if (count($post_types) < 2) {
            $class .= ' hide';
        }
        $more = '';
        $html = '<div class="type-drop ' . $class . '">
                    <div class="flex ac jsb">
                        <div class="flex ac"><i class="fa fa-delicious mr6"></i>发布类型</div>
                        <div class="but-average radius em09">
                        ' . $lists . '
                        </div>
                    </div>
                        ' . $more . '
                        <input type="hidden" name="bbs_type" value="' . $type . '">
                    </div>';
        return $html;
    }

    //获取用户允许的发布类型
    public static function post_types($id)
    {

        $default = array('' => '标准');
        $types   = array();

        if (zib_bbs_current_user_can('posts_type_question', $id)) {
            $types['question'] = '提问';
        }
        //暂未启用,待处理
        /**
        if (zib_bbs_current_user_can('posts_type_atlas', $id)) {
        $types['atlas'] = '图集';
        }
        //暂未启用
        if (zib_bbs_current_user_can('posts_type_video', $id)) {
        $types['video'] = '视频';
        }
         */
        return $types ? array_merge($default, $types) : false;
    }

    //选择标签
    public static function tag_select($in = array(), $class = 'zib-widget mb10-sm')
    {
        global $zib_bbs;
        $in_html = '';
        $input   = '';
        $name    = $zib_bbs->tag_name;

        if (!get_current_user_id()) {
            //未登录
            return '<div class="topic-drop signin-loader">
                        <div class="flex ac jsb drop-btn ' . $class . '">
                            <div class="flex ac"><icon class="mr6">' . zib_get_svg('tags') . '</icon>' . $name . '</div><div class="flex ac"><span name="plate" class="flex ac em09 muted-2-color">请选择</span><i class="ml6 fa fa-angle-right em12"></i></div>
                        </div>
                    </div>';
        }

        $wp_is_mobile = wp_is_mobile();
        $args         = array(
            'type'   => 'ias',
            'class'  => 'padding-10 select-drop-box posts-tag-select',
            'loader' => str_repeat('<div class="placeholder s1 mr3 ml3" style="height: 30px;width: calc(20% - 6px);"></div>', 10), // 加载动画
            'query'  => array(
                'action' => 'tag_select_lists',
            ),
        );

        $lists_box = zib_get_ias_ajaxpager($args);

        if ($in) {
            $query = array(
                'taxonomy'   => 'forum_tag', //分类法
                'order'      => 'ASC',
                'hide_empty' => false,
                'orderby'    => 'include',
                'include'    => $in,
            );
            $new_query = new WP_Term_Query($query);
            if (!empty($new_query->terms)) {
                foreach ($new_query->terms as $term) {
                    $in_html .= '<span class="badg mm3">' . $term->name . '</span>';
                    $input .= '<input type="hidden" name="tag[]" value="' . $term->term_id . '">';
                }
            }

        }
        $btn_attr = $wp_is_mobile ? 'data-drawer="show" drawer-title="选择' . $name . '" drawer-selector=".tag-drawer"' : 'data-toggle-class="open" data-target=".tag-drop"';
        $btn      = '<div ' . $btn_attr . ' class="drop-btn ' . $class . '">
                    <div class="flex ac jsb">
                        <div class="flex ac"><icon class="mr6">' . zib_get_svg('tags') . '</icon>' . $name . '</div>
                        <i class="ml6 fa fa-angle-right em12"></i>
                        </div>
                    <div class="">
                        <span name="tag" class="">' . $in_html . '</span>
                    </div>
                </div>';

        $aciton = '<div class="flex ac jsb padding-10">';
        $aciton .= '<div class="muted-color pointer" ' . ($wp_is_mobile ? 'data-drawer="hide" drawer-selector=".tag-drawer"' : 'data-toggle-class="open" data-target=".tag-drop"') . '>完成</div>';
        $aciton .= zib_bbs_get_term_edit_link('forum_tag', false, 'focus-color', zib_get_svg('add', null, 'icon mr3') . '创建新' . $name, 'a');
        $aciton .= '</div>';

        $dropdown = '<div class="dropdown-menu fluid">' . $aciton . $lists_box . '</div>';

        $input = $input ? $input : '<input type="hidden" name="tag[]" value="">';

        $html = '<div class="tag-drop drop-select dropdown">' . $btn . $dropdown . $input . '</div>';

        if ($wp_is_mobile) {
            $dropdown = '<div class="drawer-sm right tag-drawer"><div>' . $aciton . $lists_box . '</div></div>';
            $html     = '<div class="tag-drop drop-select">' . $btn . $input . '</div>' . $dropdown;
        }

        return $html;
    }

    public static function tag_select_lists()
    {
        global $zib_bbs;
        $paged        = zib_get_the_paged();
        $query        = self::tag_query($paged);
        $lists        = '';
        $multiple_max = 5;

        if (!empty($query->terms)) {
            foreach ($query->terms as $term) {
                $lists .= '<span data-multiple="' . $multiple_max . '" data-for="tag" data-value="' . $term->term_id . '" class="tag-list ajax-item pointer">';
                $lists .= '<span class="badg mm3">' . $term->name . '</span>';
                $lists .= '</span>';
            }

            //分页paginate
            $ajax_url = add_query_arg(array(
                'action' => 'tag_select_lists',
            ), admin_url('/admin-ajax.php'));

            $all_count = $query->found_term;
            $paginate  = zib_get_ajax_next_paginate($all_count, $paged, 20, $ajax_url, 'text-center theme-pagination ajax-pag', 'next-page ajax-next', '', 'paged', 'no');
            if ($paginate) {
                $lists .= $paginate;
            } else {
                $lists .= '<div class="ajax-pag hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
            }
        }

        return $lists;
    }

    //标签查询
    public static function tag_query($paged = 1, $orderby = 'count', $show_count = true)
    {
        return self::posts_term_query('forum_tag', $paged, $orderby, $show_count);
    }

    //选择版块
    public static function plate_select($in_args = array(), $class = 'zib-widget mb10-sm')
    {
        global $zib_bbs;
        $in_plate     = $in_args['plate'];
        $posts_id     = $in_args['post_id'];
        $in_html      = '请选择';
        $wp_is_mobile = wp_is_mobile();

        if ($in_plate) {
            $plate = get_post($in_plate);
            if (isset($plate->post_title)) {
                $in_plate = $plate->ID;
                $thumb    = zib_bbs_get_thumbnail($plate->ID, 'radius-cover');
                $in_html  = '<div class="flex ac"><div class="square-box mr10">' . $thumb . '</div><div class="">' . esc_attr($plate->post_title) . '</div></div>';
            } else {
                $in_plate = 0;
            }
        }

        if (!get_current_user_id()) {
            //未登录
            return '<div class="plate-drop signin-loader">
                        <div class="flex ac jsb drop-btn ' . $class . '">
                            <div class="flex ac"><icon class="mr6">' . zib_get_svg('plate-fill') . '</icon>' . $zib_bbs->plate_name . '</div>
                            <div class="flex ac"><span name="plate" class="flex ac em09 muted-2-color">' . $in_html . '</span><i class="ml6 fa fa-angle-right em12"></i></div>
                        </div>
                    </div>';
        }

        if ($posts_id && !zib_bbs_current_user_can('posts_plate_move', $posts_id)) {
            $roles_lists = zib_bbs_get_cap_roles_lists('posts_plate_move');

            $lists_box = zib_get_null('抱歉！您暂无修改' . $zib_bbs->plate_name . '的权限', 10, 'null-cap.svg', 'flex1', 160);
            $lists_box .= $roles_lists ? '<div class="text-center mb10 em09"><div class="mb10 muted-2-color">成为以下用户组可拥有此权限</div>' . $roles_lists . '</div>' : '';
        } else {
            $aciton = '<div class="flex ac jsb padding-10">';
            $aciton .= '<div data-for="topic" data-value="null" class="muted-2-color">' . zib_get_svg('plate-fill', null, 'icon mr3') . '请选择' . $zib_bbs->plate_name . '</div>';
            $aciton .= zib_bbs_get_plate_add_link(false, 'focus-color', zib_get_svg('add', null, 'icon mr3') . '创建新' . $zib_bbs->plate_name, 'a');
            $aciton .= '</div>';

            $lists_box = $aciton . self::plate_select_lists_tab($posts_id, 'padding-10 posts-plate-select');
        }

        $btn_attr  = $wp_is_mobile ? 'data-drawer="show" drawer-title="选择' . $zib_bbs->plate_name . '" drawer-selector=".plate-drawer"' : 'data-toggle-class="open" data-target=".plate-drop"';
        $add_limit = '';
        if ($in_plate && !zib_bbs_current_user_can('select_plate', $in_plate, $posts_id)) {
            $add_limit = '<div data-controller="plate" data-condition="==" data-value="' . $in_plate . '" class="mt20 c-yellow em09">抱歉！您暂时没有在此' . $zib_bbs->plate_name . '发布的权限，请切换到其它' . $zib_bbs->plate_name . '</div>';
        }

        $btn = '<div ' . $btn_attr . ' class="drop-btn ' . $class . '"><div class="flex ac jsb ">
                    <div class="flex ac"><icon class="mr6">' . zib_get_svg('plate-fill') . '</icon>' . $zib_bbs->plate_name . '</div>
                    <div class="flex ac"><span name="plate" class="flex ac em09 muted-2-color">' . $in_html . '</span><i class="ml6 fa fa-angle-right em12"></i></div>
                </div>' . $add_limit . '</div>';

        $dropdown = '<div class="dropdown-menu fluid">' . $lists_box . '</div>';

        $input = '<input type="hidden" name="plate" value="' . $in_plate . '">';
        $html  = '<div class="plate-drop drop-select dropdown dependency-box">' . $btn . $dropdown . $input . '</div>';

        if ($wp_is_mobile) {
            $dropdown = '<div class="drawer-sm right plate-drawer"><div>' . $lists_box . '</div></div>';
            $html     = '<div class="plate-drop drop-select dependency-box">' . $btn . $input . '</div>' . $dropdown;
        }

        return $html;
    }

    public static function plate_select_lists_tab($posts_id = 0, $class = '', $con_class = '')
    {

        $ajax_loader = str_repeat('<div class="flex padding-10"><div class="square-box mr10 placeholder radius"></div><div class="flex1"><div class="placeholder k1" style="margin-bottom: 3px;height: 18px;"></div><div class="placeholder s1"></div></div></div>', 4); // 加载动画
        $ajax_url    = admin_url('admin-ajax.php');
        $ajax_tabs   = array();
        $cat_objs    = zib_bbs_get_plate_cats_orderby('views');
        if ($cat_objs) {
            foreach ($cat_objs as $cat_obj) {
                $ajax_tabs[] = array(
                    'name'     => $cat_obj->name,
                    'id'       => 'plate_select_tab_cat_' . $cat_obj->term_id,
                    'ajax_url' => add_query_arg('cat', $cat_obj->term_id, $ajax_url),
                    'action'   => 'plate_select_lists',
                    'class'    => 'example',
                );
            }
        }

        $ajax_args = array(
            'ajax_url'  => '',
            'nav_class' => 'but p2-10',
            'loader'    => $ajax_loader,
        );

        $tab_nav_lists = '<li class="active"><a class="' . $ajax_args['nav_class'] . '" data-toggle="tab" data-ajax="" href="#plate_select_tab_main">热门</a></li>';
        $tab_nav_lists .= zib_get_ajax_tab('nav', $ajax_tabs, $ajax_args);

        $tab_con = '<div class="tab-pane fade ajaxpager in active" id="plate_select_tab_main">' . self::plate_select_lists($posts_id) . '</div>';
        $tab_con .= zib_get_ajax_tab('con', $ajax_tabs, $ajax_args);

        return '<div class="plate-select-tab ' . $class . '"><div class="relative"><ul class="list-inline scroll-x no-scrollbar tab-nav-but">' . $tab_nav_lists . '</ul></div><div class="tab-content ' . $con_class . '" for-group="plate">' . $tab_con . '</div></div>';
    }

    public static function plate_select_lists($posts_id = false)
    {
        global $zib_bbs;
        $paged          = zib_get_the_paged();
        $posts_id       = isset($_REQUEST['posts_id']) ? (int) $_REQUEST['posts_id'] : $posts_id;
        $cat_id         = isset($_REQUEST['cat']) ? (int) $_REQUEST['cat'] : 0;
        $posts_per_page = 20;
        $plate_query    = self::plate_query($paged, 'views', $posts_per_page, $cat_id);
        $lists          = '';
        $drawer_attr    = wp_is_mobile() ? ' data-drawer="hide" drawer-selector=".plate-drawer"' : ' data-toggle-class="open" data-target=".plate-drop"';

        if ($plate_query->have_posts()) {
            foreach ($plate_query->posts as $posts) {
                $id               = $posts->ID;
                $name             = $posts->post_title;
                $thumb            = zib_bbs_get_thumbnail($id, 'radius-cover');
                $can_select_plate = zib_bbs_current_user_can('select_plate', $id, $posts_id);
                $_attr            = $can_select_plate ? $drawer_attr . ' data-for="plate" data-value="' . $id . '"' : ' disabled="disabled"';
                $add_limit_btn    = zib_bbs_get_plate_add_limit_btn($id, 'p2-10 mr6', '<i class="fa fa-unlock-alt fa-fw"></i>');

                if ($can_select_plate) {
                    $all_reply_count = zib_bbs_get_plate_reply_cut_count($id);
                    $posts_count     = zib_bbs_get_plate_posts_cut_count($id);
                    $views_count     = zib_bbs_get_plate_views_cut_count($id);

                    $info = '';
                    $info .= '<span class="mr10">' . $zib_bbs->posts_name . ':' . $posts_count . '</span>';
                    $info .= '<span class="mr10">互动:' . $all_reply_count . '</span>';
                    $info .= '<span class="">热度:' . $views_count . '</span>';

                } else {
                    $info = '<span class="c-red">暂无选择权限</span>';
                }

                $lists .= '<div' . $_attr . ' class="flex padding-10 plate-list ajax-item ' . ($can_select_plate ? 'pointer' : 'opacity5') . '">';
                $lists .= '<div class="square-box mr10 thumb">' . $thumb . '</div>';
                $lists .= '<div class="info">';
                $lists .= '<div class="name">' . $add_limit_btn . $name . '</div>';
                $lists .= '<div class="muted-3-color em09 desc mt3">' . $info . '</div>';
                $lists .= '</div>';
                $lists .= '</div>';
            }

            //帖子分页paginate
            $ajax_url = add_query_arg(array(
                'action'   => 'plate_select_lists',
                'posts_id' => $posts_id,
                'cat'      => $cat_id,
            ), admin_url('/admin-ajax.php'));
            $paginate = zib_get_ajax_next_paginate($plate_query->found_posts, $paged, $posts_per_page, $ajax_url, 'text-center theme-pagination ajax-pag', 'next-page ajax-next', '', 'paged', 'no');
            if ($paginate) {
                $lists .= $paginate;
            } else {
                $lists .= '<div class="ajax-pag hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
            }

            wp_reset_query();
        }

        return $lists;
    }

    //专题选择
    public static function topic_select($in = 0, $class = 'zib-widget mb10-sm')
    {
        global $zib_bbs;
        $icon         = zib_get_svg('topic');
        $in_html      = '<icon class="mr6">' . $icon . '</icon>' . $zib_bbs->topic_name . '';
        $wp_is_mobile = wp_is_mobile();

        if (!get_current_user_id()) {
            //未登录
            return '<div class="topic-drop signin-loader">
                        <div class="flex ac jsb drop-btn ' . $class . '">
                            <div class="flex ac">' . $in_html . '</div><div class="flex ac"><span name="plate" class="flex ac em09 muted-2-color">请选择</span><i class="ml6 fa fa-angle-right em12"></i></div>
                        </div>
                    </div>';
        }

        $args = array(
            'type'   => 'ias',
            'class'  => 'padding-10 select-drop-box posts-topic-select',
            'loader' => str_repeat('<div class="flex padding-10"><div class="square-box mr10 placeholder radius"></div><div class="flex1"><div class="placeholder k1" style="margin-bottom: 3px;height: 18px;"></div><div class="placeholder s1"></div></div></div>', 4), // 加载动画
            'query'  => array(
                'action' => 'topic_select_lists',
            ),
        );
        $lists_box = zib_get_ias_ajaxpager($args);

        if ($in) {
            $term = get_term($in);
            if (isset($term->name)) {
                $in          = $term->term_id;
                $thumb       = zib_bbs_get_term_thumbnail($in, 'fit-cover radius4');
                $thumb       = $thumb ? '<div class="square-box mr10">' . $thumb . '</div>' : '';
                $posts_count = _cut_count($term->count);
                $views_count = zib_bbs_get_term_views_cut_count($in);

                $info = '';
                $info .= '<span class="mr10">' . $zib_bbs->posts_name . ':' . $posts_count . '</span>';
                $info .= '<span class="">热度:' . $views_count . '</span>';

                $in_html = '<div class="flex ac flex1 jsb">
                                <div class="flex ac">' . $thumb . '
                                    <div class="">
                                        <div class="">' . $icon . esc_attr($term->name) . $icon . '</div>
                                        <div class="muted-3-color em09">' . $info . '</div>
                                    </div>
                                </div>
                                <div class="em09 muted-2-color">' . $zib_bbs->topic_name . '</div>
                            </div>';
            }
        }

        $drawer_attr = wp_is_mobile() ? ' data-drawer="hide" drawer-selector=".topic-drawer" ' : '';

        $aciton = '<div class="flex ac jsb padding-10">';
        $aciton .= '<div' . $drawer_attr . ' data-for="topic" data-value="null" class="pointer but p2-10"><i class="fa fa-ban mr6"></i>不添加' . $zib_bbs->topic_name . '</div>';
        $aciton .= zib_bbs_get_term_edit_link('forum_topic', false, 'focus-color', zib_get_svg('add', null, 'icon mr3') . '创建新' . $zib_bbs->topic_name, 'a');
        $aciton .= '</div>';
        $btn_attr = $wp_is_mobile ? 'data-drawer="show" drawer-title="选择' . $zib_bbs->topic_name . '" drawer-selector=".topic-drawer"' : 'data-toggle="dropdown"';
        $btn      = '<div ' . $btn_attr . ' class="flex ac jsb drop-btn ' . $class . '">
                    <span name="topic" class="flex ac flex1">' . $in_html . '</span>
                    <div class="flex ac"><i class="ml6 fa fa-angle-right em12"></i></div>
                </div>';

        $dropdown = '<div class="dropdown-menu fluid">' . $aciton . $lists_box . '</div>';
        $input    = '<input type="hidden" name="topic" value="' . $in . '">';
        $html     = '<div class="topic-drop drop-select dropdown">' . $btn . $input . $dropdown . '</div>';

        if ($wp_is_mobile) {
            $dropdown = '<div class="drawer-sm right topic-drawer"><div>' . $aciton . $lists_box . '</div></div>';
            $html     = '<div class="topic-drop drop-select">' . $btn . $input . '</div>' . $dropdown;
        }
        return $html;
    }

    public static function topic_select_lists()
    {
        global $zib_bbs;
        $paged       = zib_get_the_paged();
        $query       = self::topic_query($paged);
        $lists       = '';
        $icon        = zib_get_svg('topic');
        $drawer_attr = wp_is_mobile() ? ' data-drawer="hide" drawer-selector=".topic-drawer" ' : '';
        if (!empty($query->terms)) {
            foreach ($query->terms as $term) {
                $id   = $term->term_id;
                $name = $term->name;

                $thumb       = zib_bbs_get_term_thumbnail($term, 'fit-cover radius4');
                $posts_count = _cut_count($term->count);
                $views_count = zib_bbs_get_term_views_cut_count($id);

                $info = '';
                $info .= '<span class="mr10">' . $zib_bbs->posts_name . ':' . $posts_count . '</span>';
                $info .= '<span class="">热度:' . $views_count . '</span>';

                $lists .= '<div' . $drawer_attr . ' data-for="topic" data-value="' . $id . '" class="flex padding-10 topic-list ajax-item pointer">';
                $lists .= '<div class="square-box mr10 thumb">' . $thumb . '</div>';
                $lists .= '<div class="info">';
                $lists .= '<div class="name">' . $icon . $name . $icon . '</div>';
                $lists .= '<div class="muted-3-color em09 desc">' . $info . '</div>';
                $lists .= '</div>';
                $lists .= '</div>';
            }

            //分页paginate
            $ajax_url = add_query_arg(array(
                'action' => 'topic_select_lists',
            ), admin_url('/admin-ajax.php'));

            $all_count = $query->found_term;
            $paginate  = zib_get_ajax_next_paginate($all_count, $paged, 20, $ajax_url, 'text-center theme-pagination ajax-pag', 'next-page ajax-next', '', 'paged', 'no');
            if ($paginate) {
                $lists .= $paginate;
            } else {
                $lists .= '<div class="ajax-pag hide"><div class="next-page ajax-next"><a href="#"></a></div></div>';
            }
        }

        return $lists;
    }

    //获取用户允许选择的plate_query
    public static function plate_query($paged = 1, $orderby = 'views', $posts_per_page = 20, $cat_id = 0)
    {
        $paged = $paged ? $paged : 1;

        $args = array(
            'post_type'      => 'plate',
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
        );

        if ($cat_id) {
            $args['tax_query'][] = array(
                'taxonomy'         => 'plate_cat',
                'field'            => 'id',
                'terms'            => (array) $cat_id,
                'include_children' => true,
            );
        }

        $args = zib_bbs_query_orderby_filter($orderby, $args);
        return new WP_Query($args);
    }

    //获取用户允许选择的topic_query
    public static function topic_query($paged = 1, $orderby = 'count', $show_count = true)
    {
        return self::posts_term_query('forum_topic', $paged, $orderby, $show_count);
    }

    //获取用户允许选择的topic_query
    public static function posts_term_query($taxonomy = 'forum_topic', $paged = 1, $orderby = 'count', $show_count = true)
    {
        $posts_per_page = 20;
        $paged          = $paged ? $paged : 1;
        $offset         = ($paged - 1) * $posts_per_page;
        $user_id        = get_current_user_id();
        $hide_empty     = false;
        $args           = array(
            'taxonomy'   => $taxonomy, //分类法
            'orderby'    => $orderby, //默认为版块数量
            'count'      => true,
            'number'     => $posts_per_page,
            'offset'     => $offset,
            'hide_empty' => $hide_empty,
        );
        $args = zib_bbs_query_orderby_filter($orderby, $args);

        $wp_query = new WP_Term_Query($args);

        if ($show_count) {
            $args['number'] = 0;
            $args['offset'] = 0;
            $args['count']  = 0;

            $count_query          = new WP_Term_Query($args);
            $wp_query->found_term = isset($count_query->terms) ? count((array) $count_query->terms) : 0;
        }
        return $wp_query;
    }

    //帖子置顶的radio选项
    public static function topping_radio($value = 0)
    {
        //类型选择
        if (!$value) {
            $value = 0;
        }

        $options = zib_bbs_get_posts_topping_options();

        $html = '<div class="form-radio form-but-radio">';

        foreach ($options as $val => $name) {
            $html .= '<label><input type="radio" name="topping" value="' . $val . '" ' . checked($value, $val, false) . ' /><span class="p2-10 mr6 but but-radio">' . $name . '</span></label>';
        }

        $html .= '</div>';

        return $html;
    }

    //帖子置顶
    public static function topping($posts_id = 0)
    {

        if (!$posts_id || !zib_bbs_current_user_can('posts_topping_set', $posts_id)) {
            return;
        }

        global $zib_bbs;
        $plate_name = $zib_bbs->plate_name;
        $posts_name = $zib_bbs->posts_name;

        $header  = zib_get_modal_colorful_header('jb-blue', zib_get_svg('topping'), '设置置顶');
        $topping = get_post_meta($posts_id, 'topping', true);

        $con = '';
        $con .= '<div class="em12 mb20">为该' . $posts_name . '设置置顶</div>';

        $con .= '<div class="muted-color em09 mb10">请选择置顶模式</div>';
        $con .= self::topping_radio($topping);
        $con .= '<div class="muted-3-color px12">选择全局置顶则会在所有' . $plate_name . '置顶显示</div>';

        $acton = 'posts_topping_set';

        $hidden_html = '';
        $hidden_html .= '<input type="hidden" name="action" value="' . $acton . '">';
        $hidden_html .= '<input type="hidden" name="id" value="' . $posts_id . '">';
        $hidden_html .= wp_nonce_field('posts_topping_set', '_wpnonce', false, false); //安全效验

        $footer = '<div class="mt20 but-average">';
        $footer .= $hidden_html;
        $footer .= '<button class="but jb-blue padding-lg wp-ajax-submit"><i class="fa fa-check" aria-hidden="true"></i>确认提交</button>';
        $footer .= '</div>';
        $html = '<form class="bbs-modal-form">';
        $html .= $header;
        $html .= '<div>';
        $html .= $con;
        $html .= $footer;
        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }

    //帖子切换版块
    public static function plate_move($posts_id = 0)
    {

        if (!$posts_id || !zib_bbs_current_user_can('posts_plate_move', $posts_id)) {
            return;
        }

        global $zib_bbs;
        $plate_name = $zib_bbs->plate_name;
        $posts_name = $zib_bbs->posts_name;

        $plate_id = zib_bbs_get_plate_id($posts_id);
        $header   = zib_get_modal_colorful_header('jb-blue', zib_get_svg('plate-fill'), '切换' . $plate_name);

        $con = '';
        $con .= '<div class="muted-3-color mb6">请选择新' . $plate_name . '</div>';
        $con .= '<div class="">';
        $con .= self::plate_select_lists_tab($plate_id, '', 'scroll-y mini-scrollbar muted-box max-vh3 padding-6');
        $con .= '</div>';
        $acton = 'plate_move';

        $hidden_html = '<input type="hidden" name="plate" value="">';
        $hidden_html .= '<input type="hidden" name="action" value="' . $acton . '">';
        $hidden_html .= '<input type="hidden" name="id" value="' . $posts_id . '">';
        $hidden_html .= wp_nonce_field('save_bbs', '_wpnonce', false, false); //安全效验

        $footer = '<div class="mt20 but-average">';
        $footer .= $hidden_html;
        $footer .= '<button class="but jb-blue padding-lg wp-ajax-submit"><i class="fa fa-check" aria-hidden="true"></i>确认提交</button>';
        $footer .= '</div>';

        $html = '<form class="plate-delete-form">';
        $html .= $header;
        $html .= '<div>';
        $html .= $con;
        $html .= $footer;
        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }

    //帖子切换版块
    public static function add_limit_modal($type, $id)
    {

        global $zib_bbs;
        $input_type = 'plate_cat' === $type ? 'plate' : 'posts';
        $type_name  = $zib_bbs->plate_name;

        $add_limit_html = zib_bbs_edit::input_add_limit($input_type, $id);
        if (!$add_limit_html) {
            return;
        }
        if ('plate_cat' === $type) {
            $term = get_term($id, 'plate_cat');
            if (!isset($term->name)) {
                return;
            }
            $header_title = '设置' . $zib_bbs->plate_name . '创建权限';
            $title        = $term->name;
            $type_name .= '分类';
            $desc = '为此' . $type_name . '单独' . $header_title . '，只有此处设置的的用户组才能在此' . $type_name . '创建' . $zib_bbs->plate_name;
        } else {
            $plate = get_post($id);
            if (!isset($plate->post_title)) {
                return;
            }
            $header_title = '设置发布权限';
            $title        = $plate->post_title;
            $desc         = '为此' . $type_name . '单独' . $header_title . '，只有此处设置的的用户组才能在此' . $type_name . '发布' . $zib_bbs->posts_name;
        }

        $acton  = 'save_add_limit';
        $header = zib_get_modal_colorful_header('jb-yellow', '<i class="fa fa-unlock-alt"></i>', $header_title);

        $con = '';
        $con .= '<div class="mb10 font-bold">为' . $type_name . '[' . $title . ']' . $header_title . '</div>';
        $con .= '<div class="em09 muted-2-color mb20">' . $desc . '</div>';
        $con .= '<div class="muted-color em09 mb10">请选择限制模式</div>';
        $con .= $add_limit_html;

        $hidden_html = '';
        $hidden_html .= '<input type="hidden" name="action" value="' . $acton . '">';
        $hidden_html .= '<input type="hidden" name="id" value="' . $id . '">';
        $hidden_html .= '<input type="hidden" name="type" value="' . $type . '">';
        $hidden_html .= wp_nonce_field($acton, '_wpnonce', false, false); //安全效验

        $footer = '<div class="mt20 but-average">';
        $footer .= $hidden_html;
        $footer .= '<button class="but jb-yellow padding-lg wp-ajax-submit"><i class="fa fa-check" aria-hidden="true"></i>确认提交</button>';
        $footer .= '</div>';
        $html = '<form class="bbs-modal-form">';
        $html .= $header;
        $html .= '<div>';
        $html .= $con;
        $html .= $footer;
        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }

    //版块分类
    public static function posts_plate_select($in_id = 0, $class = 'form-control', $name = 'plate_id')
    {

        global $zib_bbs;
        $plate_name = $zib_bbs->plate_name;

        $plate_query = self::plate_query(0, 'views', -1);

        $option_html = '';
        $option_html .= '<option value="">请选择' . $plate_name . '</option>';

        if (!empty($plate_query->posts)) {
            foreach ($plate_query->posts as $posts) {
                $id   = $posts->ID;
                $name = $posts->post_title;
                $option_html .= '<option' . selected($in_id, $id, false) . ' value="' . $id . '">' . esc_attr($name) . '</option>';
            }
        }
        $select_html = '<select class="' . $class . '" name="plate_id">' . $option_html . '</select>';
        return $select_html;
    }

    //版块分类
    public static function plate_cat($plate_cat_id = 0)
    {

        global $zib_bbs;
        $name = $zib_bbs->plate_name . '分类';

        return self::term($plate_cat_id, 'plate_cat', $name);
    }

    //话题
    public static function topic($topic_id = 0)
    {
        global $zib_bbs;
        $name = $zib_bbs->topic_name;

        return self::term($topic_id, 'forum_topic', $name);
    }

    //标签
    public static function tag($tag_id = 0)
    {
        global $zib_bbs;
        $name = $zib_bbs->tag_name;

        return self::term($tag_id, 'forum_tag', $name);
    }

    //term添加、修改通用
    public static function term($term_id = 0, $taxonomy = '', $name = '分类')
    {
        //前缀
        $prefix             = $term_id ? '编辑' : '创建';
        $title              = $prefix . $name;
        $slug_v             = '';
        $title_v            = '';
        $content_v          = '';
        $image_v            = '';
        $nomust_upload_attr = 'forum_topic' != $taxonomy ? ' zibupload-nomust="true"' : '';
        //头部标题
        $icons = array(
            'forum_topic' => 'topic',
            'forum_tag'   => 'tags',
            'plate_cat'   => 'plate-fill',
        );
        $header = self::edit_header($title, $icons[$taxonomy]);

        //编辑权限判断
        if (($term_id && !zib_bbs_current_user_can($taxonomy . '_edit', $term_id))) {
            return $header . '<div class="touch">' . zib_bbs_get_nocan_info(get_current_user_id(), $taxonomy . '_edit', '无法编辑此' . $name, 20, 0, 200) . '</div>';
        }
        //新建权限判断
        if ((!$term_id && !zib_bbs_current_user_can($taxonomy . '_add'))) {
            return $header . '<div class="touch">' . zib_bbs_get_nocan_info(get_current_user_id(), $taxonomy . '_add', '无法创建' . $name, 20, 0, 200) . '</div>';
        }

        if ($term_id) {
            $term_obj = get_term($term_id, $taxonomy);
            if (!empty($term_obj->term_id)) {
                $title_v   = esc_attr($term_obj->name);
                $content_v = esc_attr($term_obj->description);
                $slug_v    = isset($term_obj->slug) ? apply_filters('editable_slug', $term_obj->slug, $term_obj) : '';

                $thumbnail_url      = get_option('_taxonomy_image_' . $term_id);
                $image_v            = $thumbnail_url ? $thumbnail_url : $image_v;
                $nomust_upload_attr = $thumbnail_url ? ' zibupload-nomust="true"' : $nomust_upload_attr;
            }
        }

        $con = '';
        //标题
        $con .= self::input_title($name, $title_v);
        //简介
        $con .= self::input_desc($name, $content_v);
        //图像上传
        $con .= self::input_image($name, $image_v);
        //发帖权限设置
        $add_limit_html = '';
        if ('plate_cat' === $taxonomy && zib_bbs_current_user_can('plate_cat_set_add_limit', $term_id)) {
            global $zib_bbs;
            $add_limit_html = self::input_add_limit('plate', $term_id);
            $add_limit_html = $add_limit_html ? '<div class="mb20"><div class="em09 muted-2-color mb6">' . $zib_bbs->plate_name . '创建权限</div>' . $add_limit_html . '<div class="px12 muted-3-color mb6">为此' . $name . '单独设置发布权限，只有此处设置的的用户组才能在此' . $name . '发布内容</div></div>' : '';
        }
        $con .= $add_limit_html;

        //别名
        //允许修改或者允许添加
        if (zib_bbs_current_user_can('edit_url_slug') || (!$term_id && zib_bbs_current_user_can('add_url_slug'))) {
            $con .= '<div class="">';
            $con .= '<div class="em09 muted-2-color mb6">' . $name . 'URL别名</div>';
            $con .= '<input type="text" class="form-control" name="slug" tabindex="3" value="' . $slug_v . '" placeholder="请输入URL别名">';
            $con .= '<div class="px12 muted-3-color mt6">URL别名是显示在URL网址中的后缀内容，自定义别名可以让网址更加美观，更加有利于SEO，但不建议修改，只能使用小写字母、数字</div>';
            $con .= '</div>';
        }

        //底部提交
        $footer = self::edit_footer(
            'save_' . $taxonomy,
            array(
                'term_id'  => $term_id,
                'taxonomy' => $taxonomy,
            ),
            $nomust_upload_attr . ' term-taxonomy="' . $taxonomy . '"'
        );

        $html = '<form class="term-form form-upload">';
        $html .= '' . $header . '';
        $html .= '<div class="mb20 mini-scrollbar scroll-y max-vh7">' . $con . '</div>';
        $html .= '' . $footer . '';
        $html .= '</form>';
        return $html;
    }

    //修改/创建版块
    public static function plate($plate_id, $cat_id = 0)
    {
        global $zib_bbs;
        $name = $zib_bbs->plate_name;

        //编辑权限判断
        if ($plate_id && !zib_bbs_current_user_can('plate_edit', $plate_id)) {
            return self::edit_header('编辑' . $name, 'plate-fill') . '<div class="touch">' . zib_bbs_get_nocan_info(get_current_user_id(), 'plate_edit', '无法编辑此' . $name, 20, 0, 220) . '</div>';
        }

        //新建版块权限判断
        if ((!$plate_id && !zib_bbs_current_user_can('plate_add'))) {
            return self::edit_header('创建' . $name, 'plate-fill') . '<div class="touch">' . zib_bbs_get_nocan_info(get_current_user_id(), 'plate_add', '无法创建' . $name, 20, 0, 220) . '</div>';
        }

        //前缀
        $title_v            = '';
        $content_v          = '';
        $image_v            = ZIB_TEMPLATE_DIRECTORY_URI . '/img/upload-add.svg';
        $cat_v_obj          = false;
        $nomust_upload_attr = '';
        $type_v             = '';

        if ($plate_id) {
            $plate_obj = get_post($plate_id);
            if (!empty($plate_obj->ID)) {
                $title_v            = $plate_obj->post_title;
                $content_v          = $plate_obj->post_excerpt;
                $type_v             = get_post_meta($plate_id, 'plate_type', true);
                $plate_cat          = get_the_terms($plate_id, 'plate_cat');
                $thumbnail_url      = get_post_meta($plate_id, 'thumbnail_url', true);
                $image_v            = $thumbnail_url ? $thumbnail_url : $image_v;
                $nomust_upload_attr = $thumbnail_url ? ' zibupload-nomust="true"' : $nomust_upload_attr;

                if (!empty($plate_cat[0]->term_id)) {
                    $cat_v_obj = $plate_cat[0];
                    $cat_id    = $cat_v_obj->term_id;
                }
                $plate_id = $plate_obj->ID;
            } else {
                $plate_id = 0;
            }
        }
        $prefix = $plate_id ? '修改' : '创建';
        $title  = $prefix . $name;
        $type_v = $type_v ? $type_v : '';

        //分类选择
        $cat_html           = '';
        $can_edit_plate_cat = !$plate_id || ($plate_id && zib_bbs_current_user_can('plate_plate_cat_edit', $plate_id)); //判断用户是否有更换分类的权限
        $cat_opt_html       = '';
        if ($can_edit_plate_cat) {
            $cat_obj = zib_bbs_get_user_can_plate_cat($plate_id);
            if ($cat_obj) {
                foreach ($cat_obj as $opt) {
                    $_is_can        = zib_bbs_current_user_can('select_plate_cat', $opt->term_id, $plate_id);
                    $taxonomy_badge = zib_bbs_get_plate_cat_add_limit_btn($opt->term_id, 'p2-10 mr6' . (!$_is_can ? ' c-yellow badg' : ' but but-radio'), '<i class="fa fa-unlock-alt fa-fw"></i>' . esc_attr($opt->name) . '');
                    $_name          = $taxonomy_badge ? $taxonomy_badge : '<span class="p2-10 mr6 but but-radio">' . esc_attr($opt->name) . '</span>';
                    $cat_opt_html .= $_is_can ? '<label><input type="radio" name="cat" value="' . $opt->term_id . '" ' . checked($opt->term_id, $cat_id, false) . ' />' . $_name . '</label>' : '<label>' . $_name . '</label>';
                }
            }
        } else {
            if ($cat_v_obj) {
                //如果不允许更换，且有cat_id
                $cat_opt_html = '<label><input type="radio" name="cat" value="' . $cat_v_obj->term_id . '" checked="checked" /><span class="p2-10 mr6 but but-radio">' . esc_attr($cat_v_obj->name) . '</span></label>';
            }
        }

        if (!$cat_opt_html) {
            $cat_opt_html = '<span class="badg c-red mr6 px12 radio-null">暂无可选择的' . $name . '分类</span>';
        }

        $cat_add_link = zib_bbs_get_plate_cat_add_link(0, 'px12 but c-blue p2-10', zib_get_svg('add') . '创建新分类', 'a', true); //添加分类的按钮
        $cat_html     = '<div class="form-radio mb20 form-but-radio">';
        $cat_html .= '<div class="flex ac jsb mb6"><div class="em09 muted-2-color">' . $name . '分类</div>' . $cat_add_link . '</div>';
        $cat_html .= '<div class="plate-cat-radio">' . $cat_opt_html . '</div>';
        $cat_html .= '</div>';

        //类型选择
        $type_html      = '';
        $plate_type_obj = zib_bbs_get_user_can_plate_type($plate_id);
        if ($plate_type_obj) {
            $type_html = '<div class="form-radio form-but-radio">';
            $type_html .= '<div class="em09 muted-2-color mb6">' . $name . '类型</div>';
            foreach ($plate_type_obj as $opt_k => $opt_v) {
                $type_html .= '<label><input type="radio" name="type" value="' . $opt_k . '" ' . checked($opt_k, $type_v, false) . ' /><span class="p2-10 mr6 but but-radio">' . $opt_v . '</span></label>';
            }
            $type_html .= '</div>';
        }

        //版主
        $moderator_html = '';
        if ($plate_id) {
            $moderator_btn     = zib_bbs_get_edit_plate_moderator_link($plate_id, 'but mr10', '管理' . $zib_bbs->plate_moderator_name . '<i class="fa fa-angle-right ml6"></i>');
            $moderator_add_btn = zib_bbs_get_add_plate_moderator_link($plate_id, 'but', '添加' . $zib_bbs->plate_moderator_name . '<i class="fa fa-angle-right ml6"></i>');
            if ($moderator_btn) {
                $moderator_html = '<div class="mt20">';
                $moderator_html .= '<div class="em09 muted-2-color mb6">' . $zib_bbs->plate_moderator_name . '</div>';
                $moderator_html .= $moderator_btn;
                $moderator_html .= $moderator_add_btn;
                $moderator_html .= '</div>';
            }
        }

        //发帖权限设置
        $add_limit_html = '';
        if (zib_bbs_current_user_can('plate_set_add_limit', $plate_id)) {
            $add_limit_html = self::input_add_limit('posts', $plate_id);
            $add_limit_html = $add_limit_html ? '<div class="mt20"><div class="em09 muted-2-color mb6">发布权限</div>' . $add_limit_html . '<div class="px12 muted-3-color mb6">为此' . $name . '单独设置发布权限，只有此处设置的的用户组才能在此' . $name . '发布内容</div></div>' : '';
        }

        //查看权限
        $allow_view_set = '';
        if ((!$plate_id && zib_bbs_current_user_can('plate_set_allow_view')) || ($plate_id && zib_bbs_current_user_can('plate_set_allow_view', $plate_id))) {
            $allow_view_set = zib_bbs_edit::plate_allow_view_set_content($plate_id, 'muted-box padding-10');
            $allow_view_set = $allow_view_set ? '<div class="mt20 dependency-box"><div class="em09 muted-2-color mb6">查看权限</div>' . $allow_view_set . '<div class="px12 muted-3-color mb6">为此' . $name . '设置阅读限制，只有满足条件的用户才能查看此版块的' . $zib_bbs->posts_name . '内容</div></div>' : '';
        }

        $con = '';
        //标题
        $con .= self::input_title($name, $title_v);
        //简介
        $con .= self::input_desc($name, $content_v);
        //图像上传
        $con .= self::input_image($name, $image_v);
        $con .= $cat_html;
        $con .= $type_html;
        $con .= $add_limit_html;
        $con .= $allow_view_set;
        $con .= $moderator_html;

        //头部标题
        $header = self::edit_header($title, 'plate-fill');
        //底部提交
        $footer = self::edit_footer(
            'save_plate',
            array(
                'plate_id' => $plate_id,
            ),
            $nomust_upload_attr . ' plate-save="' . ($plate_id ? 'update' : 'add') . '"'
        );

        $html = '<form class="plate-form form-upload">';
        $html .= '' . $header . '';
        $html .= '<div class="mb20 mini-scrollbar scroll-y max-vh7">' . $con . '</div>';
        $html .= '' . $footer . '';
        $html .= '</form>';

        return $html;
    }

    public static function input_add_limit($type = 'posts', $id = 0)
    {
        $options = zib_bbs_get_add_limit_options($type);
        $input   = '';
        $desc    = '';
        $in      = 0;
        if ($id) {
            $in = 'posts' === $type ? (int) get_post_meta($id, 'add_limit', true) : (int) get_term_meta($id, 'add_limit', true);
        }
        if ($options) {
            global $zib_bbs;
            foreach ($options as $key => $name) {
                $roles_lists = '<div class="muted-2-color">不单独设置，保持系统默认</div>';
                if ($key) {
                    $roles_lists = zib_bbs_get_cap_roles_lists($type . '_add_limit_' . $key);
                    $roles_lists = $roles_lists ? '<div class="em09 muted-2-color">以下用户组可' . ('posts' === $type ? '发布' . $zib_bbs->posts_name : '新建' . $zib_bbs->plate_name) . '</div>' . $roles_lists : '<div class="c-yellow">仅管理员可发布</div>';
                }
                $desc .= '<div class="" data-controller="add_limit" data-condition="==" data-value="' . $key . '"' . ($key == $in ? '' : ' style="display: none;"') . ' >' . $roles_lists . '</div>';
                $input .= '<label class="badg p2-10 mr10 pointer"><input type="radio"' . (checked($in, $key, false)) . ' name="add_limit" value="' . $key . '"> ' . $name . '</label>';
            }
        }
        if (!$input) {
            return;
        }

        return '<div class="dependency-box"><div>' . $input . '</div><div class="muted-box padding-6">' . $desc . '</div></div>';
    }

    public static function input_image($name, $val = '')
    {
        $val      = $val ? $val : ZIB_TEMPLATE_DIRECTORY_URI . '/img/upload-add.svg';
        $img_html = '<div class="form-upload mb20">';
        $img_html .= '<div class="em09 muted-2-color mb6">' . $name . '图像</div>';
        $img_html .= '<label class="pointer">';
        $img_html .= '<div class="preview preview-square">'; //正方形
        $img_html .= '<img class="fit-cover" src="' . $val . '" alt="添加图片">';
        $img_html .= '</div>';
        $img_html .= '<input class="hide" type="file" zibupload="image_upload" accept="image/gif,image/jpeg,image/jpg,image/png" name="image" action="image_upload">';
        $img_html .= '</label>';
        $img_html .= '</div>';

        return $img_html;
    }

    public static function input_title($name, $val = '', $tabindex = 1)
    {
        $con = '';
        $con .= '<div class="mb20">';
        $con .= '<div class="em09 muted-2-color mb6">' . $name . '标题</div>';
        $con .= '<input type="text" class="form-control" name="title" tabindex="' . $tabindex . '" value="' . $val . '" placeholder="请输入标题">';
        $con .= '</div>';
        return $con;
    }

    public static function input_desc($name, $val = '', $tabindex = 2)
    {
        $con = '';
        $con .= '<div class="mb20">';
        $con .= '<div class="em09 muted-2-color mb6">' . $name . '简介</div>';
        $con .= '<textarea class="form-control" name="desc" tabindex="' . $tabindex . '" placeholder="请输入简介" rows="2">' . $val . '</textarea>';
        $con .= '</div>';
        return $con;
    }

    public static function edit_header($title, $icon_name = '')
    {
        $icon = '';
        if ($icon_name) {
            $icon = zib_get_svg($icon_name, null, 'icon');
            $icon = '<span class="toggle-radius mr10 b-theme">' . $icon . '</span>';
        }

        $title = $icon . $title;

        $header = '<div class="mb20 edit-header touch">';
        $header .= '<button class="close" data-dismiss="modal">' . zib_get_svg('close', null, 'ic-close') . '</button>';
        $header .= '<b class="modal-title flex ac">' . $title . '</b>';
        $header .= '</div>';

        return $header;
    }

    public static function edit_footer($acton = '', $hidden = array(), $attr = '', $_wpnonce = 'save_bbs')
    {
        $hidden_html = '';
        $hidden_html .= $acton ? '<input type="hidden" name="action" value="' . $acton . '">' : '';
        $hidden_html .= wp_nonce_field($_wpnonce, '_wpnonce', false, false); //安全效验
        foreach ($hidden as $name => $val) {
            $hidden_html .= '<input type="hidden" name="' . $name . '" value="' . $val . '">';
        }

        $footer = '<div class="text-right edit-footer">';
        $footer .= '<button class="but jb-blue padding-lg" zibupload="submit"' . $attr . '><i class="fa fa-check" aria-hidden="true"></i>确认提交</button>';
        $footer .= $hidden_html;
        $footer .= '</div>';

        return $footer;
    }

    //删除帖子
    public static function posts_delete($post)
    {
        global $zib_bbs;
        $name         = $zib_bbs->plate_name;
        $posts_name   = $zib_bbs->posts_name;
        $comment_name = $zib_bbs->comment_name;

        $post    = get_post($post);
        $post_id = $post->ID;

        $title = esc_attr($post->post_title);

        $all_comment_count = get_comments_number($post_id);

        $desc = '<div class="c-red mb20">当前' . $posts_name . '下，共有' . $all_comment_count . '次' . $comment_name . '，确认要删除吗？</div>';

        $html = self::delete($post_id, $posts_name, 'posts_delete', $title, $desc);
        return $html;
    }

    //审核版块或者帖子
    public static function audit($post)
    {
        global $zib_bbs;
        if (!is_object($post)) {
            $post = get_post($post);
        }
        $post_id = $post->ID;

        if ('forum_post' === $post->post_type) {
            $action = 'posts_audit';
            $name   = $zib_bbs->posts_name;
        }
        if ('plate' === $post->post_type) {
            $action = 'plate_audit';
            $name   = $zib_bbs->plate_name;
        }
        $title = '<b>' . esc_attr($post->post_title) . '</b>';
        if ('pending' === $post->post_status) {
            $header = zib_get_modal_colorful_header('jb-blue', zib_get_svg('approve'), '审核' . $name);
            $con    = '<div class="mb10">确认批准此' . $name . '[' . $title . ']？</div><div class="muted-2-color mb10">批准后此内容将切换为<span class="badg badg-sm c-blue">已发布</span>状态</div>';
        } else {
            $header = zib_get_modal_colorful_header('jb-yellow', zib_get_svg('approve'), '审核' . $name);
            $con    = '<div class="mb10">确认将此' . $name . '[' . $title . ']的状态切换为<span class="badg badg-sm c-yellow">待审核</span>状态？</div>';
        }

        $hidden_html = '';
        $hidden_html .= '<input type="hidden" name="post_type" value="' . $post->post_type . '">';
        $hidden_html .= '<input type="hidden" name="action" value="' . $action . '">';
        $hidden_html .= '<input type="hidden" name="id" value="' . $post_id . '">';
        $hidden_html .= wp_nonce_field($action, '_wpnonce', false, false); //安全效验

        $footer = '<div class="mt20 but-average">';
        $footer .= $hidden_html;
        $footer .= '<button type="button" data-dismiss="modal" href="javascript:;" class="but">取消</button>';
        $footer .= '<button class="but c-blue wp-ajax-submit">' . zib_get_svg('approve') . '确认</button>';
        $footer .= '</div>';

        $html = '<form class="plate-delete-form">';
        $html .= $header;
        $html .= '<div>';
        $html .= $con;
        $html .= $footer;
        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }

    //删除版块
    public static function plate_delete($post)
    {
        global $zib_bbs;
        $name         = $zib_bbs->plate_name;
        $posts_name   = $zib_bbs->posts_name;
        $comment_name = $zib_bbs->comment_name;

        if (!is_object($post)) {
            $post = get_post($post);
        }
        $plate_id = $post->ID;

        $title = esc_attr($post->post_title);

        $all_posts_count   = zib_bbs_get_plate_posts_cut_count($plate_id);
        $all_comment_count = zib_bbs_get_plate_reply_cut_count($plate_id);
        $desc              = '<div class="c-red mb20">当前' . $name . '下，共有' . $all_posts_count . '个' . $posts_name . '和' . $all_comment_count . '次' . $comment_name . '，确认要删除吗？</div>';

        if ($all_posts_count) {
            $desc .= '<div class="mt20 dependency-box">';
            $desc .= '<div class="form-radio mb10 form-but-radio">';
            $desc .= '<div class="em09 muted-2-color mb6">删除后如何操作该' . $name . '下的' . $posts_name . '？</div>';
            $desc .= '<label><input type="radio" name="posts_action" value="trash" checked="checked"><span class="p2-10 mr6 but but-radio">移至回收站</span></label>';
            $desc .= '<label><input type="radio" name="posts_action" value="move"><span class="p2-10 mr6 but but-radio">移到新的' . $name . '</span></label>';
            $desc .= '</div>';

            $desc .= '<div data-controller="posts_action" data-condition="==" data-value="move" style="display: none;">';
            $desc .= '<div class="muted-3-color em09 mb6 ml3">请选择新' . $name . '</div>';
            $desc .= '<input type="hidden" name="plate" value="">';
            $desc .= self::plate_select_lists_tab(0, '', 'scroll-y mini-scrollbar muted-box max-vh3 padding-6');
            $desc .= '</div>';
            $desc .= '</div>';
        }

        $html = self::delete($plate_id, $name, 'plate_delete', $title, $desc);
        return $html;
    }

    //删除版块
    public static function term_delete($term)
    {
        global $zib_bbs;
        $term_obj = get_term($term);

        $term_id  = $term_obj->term_id;
        $title    = esc_attr($term_obj->name);
        $taxonomy = $term_obj->taxonomy;
        $name     = zib_bbs_get_taxonomy_name($taxonomy);

        if ('plate_cat' == $taxonomy) {
            $posts_name = $zib_bbs->plate_name;
        } else {
            $posts_name = $zib_bbs->posts_name;
        }

        $count = _cut_count($term_obj->count);
        $desc  = '<div class="c-red mb20">当前' . $name . '下，共有' . $count . '个' . $posts_name . '，删除后不可恢复！<br>确认要删除吗？</div>';

        $html = self::delete($term_id, $name, $taxonomy . '_delete', $title, $desc);
        return $html;
    }

    //删除分类term
    public static function delete($id, $name, $acton, $title = '', $desc = '')
    {

        $header = zib_get_modal_colorful_header('jb-red', '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>', '确认删除此' . $name . '？');

        $title = $title ? '<b>（' . $title . '）</b>' : '';
        $con   = '';
        $con .= '<div class="em12 mb10">您正在删除' . $name . $title . '</div>';
        $con .= $desc;

        $hidden_html = '';
        $hidden_html .= '<input type="hidden" name="action" value="' . $acton . '">';
        $hidden_html .= '<input type="hidden" name="id" value="' . $id . '">';
        $hidden_html .= wp_nonce_field('save_bbs', '_wpnonce', false, false); //安全效验

        $footer = '<div class="mt20 but-average">';
        $footer .= $hidden_html;
        $footer .= '<button type="button" data-dismiss="modal" href="javascript:;" class="but">取消</button>';
        $footer .= '<button class="but c-red wp-ajax-submit"><i class="fa fa-trash-o" aria-hidden="true"></i>确认删除</button>';
        $footer .= '</div>';

        $html = '<form class="plate-delete-form">';
        $html .= $header;
        $html .= '<div>';
        $html .= $con;
        $html .= $footer;
        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }

    ///
    public static function xxx2()
    {
    }
}
