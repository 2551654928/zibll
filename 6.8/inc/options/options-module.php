<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-11-11 11:41:45
 * @LastEditTime: 2022-09-30 16:52:55
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

class CFS_Module
{

    public static function footer_tabbar()
    {
        $args = array(
            array(
                'id'      => 'icon_fa',
                'class'   => 'compact',
                'type'    => 'color_group',
                'title'   => '',
                'desc'    => '自定义颜色请注意日间、夜间模式的适配',
                'options' => array(
                    'color' => '自定义图标颜色',
                    'bg'    => '自定义背景颜色',
                ),
            ),
        );
        return $args;
    }

    public static function float_btn($true = true)
    {
        $args = array(
            array(
                'id'      => 'pc_s',
                'title'   => '',
                'label'   => __('PC端显示', 'zib_language'),
                'type'    => 'switcher',
                'default' => true,
            ),
            array(
                'id'      => 'm_s',
                'class'   => 'compact',
                'title'   => '',
                'label'   => __('移动端端显示', 'zib_language'),
                'type'    => 'switcher',
                'default' => true,
            ),
            array(
                'id'      => 'color',
                'class'   => 'compact',
                'type'    => 'color_group',
                'title'   => '',
                'desc'    => '自定义颜色请注意日间、夜间模式的适配',
                'options' => array(
                    'color' => '自定义图标颜色',
                    'bg'    => '自定义背景颜色',
                ),
            ),
        );
        return $args;
    }

    public static function add_slider()
    {
        $f_imgpath = get_template_directory_uri() . '/inc/csf-framework/assets/images/';
        $args      = array();
        $args[]    = array(
            'title'   => __('背景图片', 'zib_language'),
            'id'      => 'background',
            'default' => '',
            'preview' => true, 'library' => 'image',
            'type'    => 'upload',
        );
        $args[] = array(
            'dependency'   => array('background', '!=', ''),
            'id'           => 'link',
            'type'         => 'link',
            'title'        => '幻灯片链接',
            'default'      => array(),
            'add_title'    => '添加链接',
            'edit_title'   => '编辑链接',
            'remove_title' => '删除链接',
        );
        $args[] = array(
            'dependency'             => array('background', '!=', '', '', 'visible'),
            'id'                     => 'image_layer',
            'type'                   => 'group',
            'accordion_title_number' => true,
            'accordion_title_auto'   => false,
            'accordion_title_prefix' => '图层',
            'button_title'           => '添加图层',
            'title'                  => '叠加图层',
            'subtitle'               => '添加更多图层',
            'desc'                   => '添加额外的幻灯片图层，配合图层设置及幻灯片其它设置，可轻松制作出漂亮无比的幻灯片',
            'fields'                 => array(
                array(
                    'title'   => __('图层图片', 'zib_language'),
                    'id'      => 'image',
                    'default' => '',
                    'preview' => true,
                    'library' => 'image',
                    'type'    => 'upload',
                ),
                array(
                    'title'   => '自由尺寸',
                    'type'    => 'switcher',
                    'id'      => 'free_size',
                    'class'   => 'compact',
                    'desc'    => '如果图层的尺寸和背景图的尺寸不一致，可开启此项以自定义图层对齐方向',
                    'default' => false,
                    'type'    => 'switcher',
                ),
                array(
                    'dependency' => array('image|free_size', '!=|!=', '|'),
                    'title'      => '图层对齐',
                    'id'         => 'align',
                    'inline'     => true,
                    'type'       => 'radio',
                    'class'      => 'compact',
                    'default'    => 'center',
                    'options'    => array(
                        'left'   => '靠左显示',
                        'center' => '居中显示',
                        'right'  => '靠右显示',
                    ),
                ), array(
                    'dependency' => array('image', '!=', ''),
                    'id'         => 'parallax',
                    'class'      => 'compact',
                    'desc'       => '提前或延后进入视线，负值为延后，正值为提前，0为关闭[-200~200]',
                    'title'      => '视差滚动',
                    'default'    => 0,
                    'max'        => 200,
                    'min'        => -200,
                    'step'       => 5,
                    'unit'       => '%',
                    'type'       => 'spinner',
                ), array(
                    'dependency' => array('image', '!=', ''),
                    'id'         => 'parallax_scale',
                    'desc'       => '放大或缩小进入视线，原图大小的百分之多少[1~200]',
                    'class'      => 'compact',
                    'title'      => '视差缩放',
                    'default'    => 100,
                    'max'        => 200,
                    'min'        => 1,
                    'step'       => 5,
                    'unit'       => '%',
                    'type'       => 'spinner',
                ), array(
                    'dependency' => array('image|parallax', '!=|!=', '|'),
                    'id'         => 'parallax_opacity',
                    'desc'       => '以百分之多少的透明度进入视线[1~100]<br>视差功能对浏览器性能有一定影响，如果图层较多，不建议全部开启',
                    'class'      => 'compact',
                    'title'      => '视差透明',
                    'default'    => 100,
                    'max'        => 100,
                    'min'        => 1,
                    'step'       => 5,
                    'unit'       => '%',
                    'type'       => 'spinner',
                ),
            ),
        );
        $args[] = array(
            'dependency' => array('background', '!=', ''),
            'id'         => 'text',
            'type'       => 'accordion',
            'title'      => '叠加文案',
            'accordions' => array(
                array(
                    'title'  => '幻灯片叠加文案',
                    'fields' => array(
                        array(
                            'title'      => '幻灯片文案',
                            'subtitle'   => '幻灯片标题',
                            'id'         => 'title',
                            'default'    => '',
                            'attributes' => array(
                                'rows' => 1,
                            ),
                            'type'       => 'textarea',
                        ),
                        array(
                            'dependency' => array('title', '!=', ''),
                            'title'      => '幻灯片简介',
                            'id'         => 'desc',
                            'class'      => 'compact',
                            'default'    => '',
                            'desc'       => '标题、简介均支持HTML代码，请注意代码规范及标签闭合',
                            'attributes' => array(
                                'rows' => 1,
                            ),
                            'type'       => 'textarea',
                        ),
                        array(
                            'dependency' => array('title', '!=', ''),
                            'title'      => '显示位置',
                            'id'         => 'text_align',
                            'type'       => 'image_select',
                            'class'      => 'compact image-miniselect',
                            'default'    => 'left-bottom',
                            'desc'       => '前景图显示位置及文案位置需合理搭配',
                            'options'    => array(
                                'left-bottom'   => $f_imgpath . 'left-bottom.jpg',
                                'left-conter'   => $f_imgpath . 'left-conter.jpg',
                                'conter-conter' => $f_imgpath . 'conter-conter.jpg',
                                'conter-bottom' => $f_imgpath . 'conter-bottom.jpg',
                                'right-conter'  => $f_imgpath . 'right-conter.jpg',
                                'right-bottom'  => $f_imgpath . 'right-bottom.jpg',
                            ),
                        ),
                        array(
                            'dependency' => array('title', '!=', ''),
                            'id'         => 'text_size_pc',
                            'class'      => 'compact',
                            'title'      => 'PC端字体大小',
                            'default'    => 30,
                            'max'        => 50,
                            'min'        => 12,
                            'step'       => 1,
                            'unit'       => 'PX',
                            'type'       => 'spinner',
                        ),
                        array(
                            'dependency' => array('title', '!=', ''),
                            'id'         => 'text_size_m',
                            'class'      => 'compact',
                            'title'      => '移动端字体大小',
                            'desc'       => '在此设置标题的字体大小，简介的大小为标题大小的60%，最小12px<br>字体越大，文案周边的间距也越大！字体大小请根据内容合理调整，避免在某些设备显示不全',
                            'default'    => 20,
                            'max'        => 50,
                            'min'        => 12,
                            'step'       => 1,
                            'unit'       => 'PX',
                            'type'       => 'spinner',
                        ),
                        array(
                            'dependency' => array('title', '!=', ''),
                            'id'         => 'parallax',
                            'class'      => 'compact',
                            'desc'       => '视差滚动功能为较背景滚动的时间差，为负数则滚动慢一拍，为正数则滚动快一拍，为0则关闭',
                            'title'      => '文案视差滚动',
                            'default'    => 40,
                            'max'        => 200,
                            'min'        => -200,
                            'step'       => 10,
                            'unit'       => '%',
                            'type'       => 'spinner',
                        ),
                    ),
                ),
            ),
        );

        return $args;
    }
    public static function page_type()
    {
        return array(
            'home'     => '首页',
            'topics'   => '专题页',
            'category' => '分类页',
            'tag'      => '标签页',
            'author'   => '用户页',
            'single'   => '文章页',
            'search'   => '搜索页',
            'page'     => '其它页面',
        );
    }
    public static function posts_orderby($orderby = array())
    {
        return array_merge($orderby, array(
            'modified'      => '更新时间',
            'date'          => '发布时间',
            'views'         => '浏览数量',
            'like'          => '点赞数量',
            'comment_count' => '评论数量',
            'favorite'      => '收藏数量',
            'rand'          => '随机排序',
        ));
    }
    public static function zib_palette($palette = array(), $show = array('c', 'b', 'jb'))
    {
        if (in_array('c', $show)) {
            $palette = array_merge($palette, array(
                'c-red'      => array('rgba(255, 84, 115, .4)'),
                'c-red-2'    => array('rgba(194, 41, 46, 0.4)'),
                'c-yellow'   => array('rgba(255, 111, 6, 0.4)'),
                'c-yellow-2' => array('rgba(179, 103, 8, 0.4)'),
                'c-cyan'     => array('rgba(8, 196, 193, 1)'),
                'c-blue'     => array('rgba(41, 151, 247, .4)'),
                'c-blue-2'   => array('rgba(77, 130, 249, .4)'),
                'c-green'    => array('rgba(18, 185, 40, .4)'),
                'c-green-2'  => array('rgba(72, 135, 24, .4)'),
                'c-purple'   => array('rgba(213, 72, 245, 0.4)'),
                'c-purple-2' => array('rgba(154, 72, 245, 0.4)'),
            ));
        }
        if (in_array('b', $show)) {
            $palette = array_merge($palette, array(
                'b-red'    => array('#f74b3d'),
                'b-yellow' => array('#f3920a'),
                'b-cyan'   => array('#08c4c1'),
                'b-blue'   => array('#0a8cf3'),
                'b-green'  => array('#1fd05a'),
                'b-purple' => array('#c133f5'),
            ));
        }
        if (in_array('c', $show)) {
            $palette = array_merge($palette, array(
                'jb-red'    => array('linear-gradient(135deg, #ffbeb4 10%, #f61a1a 100%)'),
                'jb-pink'   => array('linear-gradient(135deg, #ff5e7f 30%, #ff967e 100%)'),
                'jb-yellow' => array('linear-gradient(135deg, #ffd6b2 10%, #ff651c 100%)'),
                'jb-cyan'   => array('linear-gradient(140deg, #039ab3 10%, #58dbcf 90%)'),
                'jb-blue'   => array('linear-gradient(135deg, #b6e6ff 10%, #198aff 100%)'),
                'jb-green'  => array('linear-gradient(135deg, #ccffcd 10%, #52bb51 100%)'),
                'jb-purple' => array('linear-gradient(135deg, #fec2ff 10%, #d000de 100%)'),
                'jb-vip1'   => array('linear-gradient(25deg, #eab869 10%, #fbecd4 60%, #ffe0ae 100%)'),
                'jb-vip2'   => array('linear-gradient(317deg, #4d4c4c 30%, #878787 70%, #5f5c5c 100%)'),
            ));
        }
        return $palette;
    }

    public static function gzh_menu()
    {
        $con = '<div class="options-notice"><div class="explain">
            <p>微信公众号启用服务器之后，且微信登录功能正常后，请在此设置微信自定义菜单</p>
            <li>在下方粘贴公众号自定义菜单的json配置代码后提交即可</li>
            <li>如果失败，会直接显示微信返回的错误码，可按照错误进行分析处理</li>
            <li>设置好成功后会有几分钟的延迟才能生效，请耐心等待</li>
            <li><a target="_blank" href="https://www.zibll.com/2916.html">点此查看官网教程</a></li>
            <ajaxform class="ajax-form" ajax-url="' . admin_url("admin-ajax.php") . '">
                <p><textarea ajax-name="json" row="5" placeholder="请粘贴公众号自定义菜单的json配置代码" style="width: 100%;height: 299px;"></textarea></p>
                <div class="ajax-notice"></div>
                <p><a href="javascript:;" class="but jb-blue ajax-submit"><i class="fa fa-paper-plane-o"></i> 设置自定义菜单</a></p>
                <input type="hidden" ajax-name="action" value="weixin_gzh_menu">
            </ajaxform>
        </div></div>';

        return array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => $con,
        );
    }

    public static function audit_test()
    {
        $test_img = get_template_directory_uri() . '/inc/csf-framework/assets/images/audit_test.jpg';

        $con = '<div class="options-notice">
        <div class="explain">
        <p>如果您已经完成了接口配置，可以在此处进行审核测试，只要有审核结果显示则表示接入正常</p>
        <p><b>· 文本审核测试</b></p>
        <ajaxform class="ajax-form" ajax-url="' . admin_url("admin-ajax.php") . '">
        <p><textarea ajax-name="content" placeholder="请输入一些内容进行测试" style="width: 100%;max-width: 500px;"></textarea></p>
        <div class="ajax-notice"></div>
        <p><a href="javascript:;" class="but jb-yellow ajax-submit"><i class="fa fa-paper-plane-o"></i> 文本测试</a></p>
        <input type="hidden" ajax-name="action" value="text_audit_test">
        </ajaxform>

        <p><b>· 使用以下示例图片进行图片审核测试</b></p>
        <ajaxform class="ajax-form" ajax-url="' . admin_url("admin-ajax.php") . '">
        <p><img alt="图片测试" src="' . $test_img . '" width="99" height="99"></p>
        <div class="ajax-notice"></div>
        <p><a href="javascript:;" class="but jb-yellow ajax-submit"><i class="fa fa-paper-plane-o"></i> 图片测试</a></p>
        <input type="hidden" ajax-name="action" value="img_audit_test">
        </ajaxform>

        </div></div>';

        return array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => $con,
        );
    }

    public static function email_test()
    {
        $con = '<div class="options-notice">
        <div class="explain">
        <p><b>您可以在下方测试邮件发送功能是否正常，请输入您的邮箱帐号：</b></p>
        <ajaxform class="ajax-form" ajax-url="' . admin_url("admin-ajax.php") . '">
        <p><input type="text" style="max-width:300px;" ajax-name="email" value="' . get_option('admin_email') . '" placeholder="88888888@qq.com"></p>
        <div class="ajax-notice"></div>
        <p><a href="javascript:;" class="but jb-yellow ajax-submit"><i class="fa fa-paper-plane-o"></i> 发送测试邮件</a></p>
        <input type="hidden" ajax-name="action" value="test_send_mail">
        </ajaxform>
        </div></div>';
        return array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => $con,
        );
    }

    public static function wechat_template_msg_args()
    {
        return array(
            'payment_order'             => ['新订单通知用户', '对应模板库编号：<code>OPENTM415269507</code> 搜索关键词：<code>支付成功通知</code>'],
            'payment_order_admin'       => ['新订单通知管理员', '对应模板库编号：<code>OPENTM415699551</code> 搜索关键词：<code>新订单通知</code>'],
            'payment_order_to_income'   => ['获得创作分成通知作者', '对应模板库编号：<code>OPENTM405465388</code> 搜索关键词：<code>收入提醒</code> （和下方“获得推荐佣金通知推荐人”一致）'],
            'payment_order_to_referrer' => ['获得推荐佣金通知推荐人', '对应模板库编号：<code>OPENTM405465388</code> 搜索关键词：<code>收入提醒</code> （和上方“获得创作分成通知作者”一致）'],
            'apply_withdraw_admin'      => ['用户发起提现通知管理员', '对应模板库编号：<code>OPENTM418318073</code> 搜索关键词：<code>提现申请审批通知</code>'],
            'withdraw_process'          => ['提现处理后通知提现用户', '对应模板库编号：<code>OPENTM411556514</code> 搜索关键词：<code>提现申请处理通知</code>'],
            'auth_apply_admin'          => ['用户提交身份认证通知管理员', '对应模板库编号：<code>OPENTM406010012</code> 搜索关键词：<code>认证通知</code>'],
            'auth_apply_process'        => ['身份认证处理后通知用户', '对应模板库编号：<code>OPENTM204875750</code> 搜索关键词：<code>身份认证结果通知</code>'],
            'report_user_admin'         => ['收到举报后通知管理员', '对应模板库编号：<code>OPENTM418130560</code> 搜索关键词：<code>举报待处理提醒</code>'],
            'report_process'            => ['处理用户举报后通知举报人', '对应模板库编号：<code>TM202801753</code> 搜索关键词：<code>举报结果通知</code>'],
            'bind_phone'                => ['绑定或修改手机号通知用户', '对应模板库编号：<code>OPENTM415757701</code> 搜索关键词：<code>绑定成功通知</code> （和下方“绑定或修改邮箱通知用户”一致）'],
            'bind_email'                => ['绑定或修改邮箱通知用户', '对应模板库编号：<code>OPENTM415757701</code> 搜索关键词：<code>绑定成功通知</code> （和上方“绑定或修改手机号通知用户”一致）'],
            //定制开始
            'comment_to_postauthor'     => ['有新评论通知文章作者', '对应模板库编号：<code>OPENTM416236512</code> 搜索关键词：<code>提问结果通知</code>'],
            'comment_to_parent'         => ['评论有新回复通知用户', '对应模板库编号：<code>OPENTM416236512</code> 搜索关键词：<code>提问结果通知</code>'],
            //定制结束
        );
    }

    public static function wechat_template_id()
    {
        $args = array();

        foreach (self::wechat_template_msg_args() as $k => $v) {
            $args[] = array(
                'id'      => $k . '_s',
                'type'    => 'switcher',
                'default' => false,
                'title'   => $v[0],
            );
            $args[] = array(
                'dependency' => array($k . '_s', '!=', ''),
                'id'         => $k,
                'class'      => 'compact',
                'title'      => ' ',
                'subtitle'   => '模板ID',
                'default'    => '',
                'type'       => 'text',
                'desc'       => $v[1],
            );
        }

        return $args;
    }

    public static function wechat_template_test()
    {
        $type_option = '<option value="">请选择需测试的模板</option>';
        foreach (self::wechat_template_msg_args() as $k => $v) {
            $type_option .= '<option value="' . $k . '"> ' . $v[0] . '</option>';
        }

        $con = '<div class="options-notice">
        <div class="explain">
        <p><b>您可以在此处测试微信公众号模板消息能否正常推送</b></p>
        <p>请选择测试的模板，提交后系统将会自动发送对应的测试模板到您绑定的微信中</p>
        <p>测试前请确保您当前账号已扫码绑定微信公众号</p>
        <ajaxform class="ajax-form" ajax-url="' . admin_url("admin-ajax.php") . '">
        <div class="ajax-notice"></div>
        <p class="flex ac"><select ajax-name="type" style="margin-right: 20px;">' . $type_option . '</select><a href="javascript:;" class="but jb-yellow ajax-submit"><i class="fa fa-paper-plane-o"></i> 提交测试</a></p>
        <input type="hidden" ajax-name="action" value="test_wechat_template_test">
        </ajaxform>
        </div></div>';
        return array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => $con,
        );
    }

    public static function vip_product()
    {
        return array(
            array(
                'id'      => 'price',
                'title'   => '执行价',
                'default' => '699',
                'type'    => 'number',
                'unit'    => '元',
            ),
            array(
                'id'      => 'show_price',
                'title'   => '原价',
                'desc'    => '显示在执行价格前面，并划掉',
                'default' => '999',
                'type'    => 'number',
                'unit'    => '元',
                'class'   => 'compact',
            ),
            array(
                'id'         => 'tag',
                'title'      => '促销标签',
                'class'      => 'compact',
                'desc'       => '支持HTML，请注意控制长度',
                'attributes' => array(
                    'rows' => 1,
                ),
                'type'       => 'textarea',
            ),
            array(
                'dependency' => array('tag', '!=', ''),
                'title'      => '标签颜色',
                'id'         => "tag_class",
                'class'      => 'compact skin-color',
                'default'    => "jb-yellow",
                'type'       => "palette",
                'options'    => CFS_Module::zib_palette(),
            ),
            array(
                'dependency' => array('time', '==', 0),
                'type'       => 'submessage',
                'style'      => 'success',
                'content'    => '<strong>会员有效时间已设置为：<code>永久会员</code></strong>',
            ),
            array(
                'title'   => '会员有效时间',
                'id'      => 'time',
                'class'   => 'compact',
                'desc'    => '开通会员的时长。填<code>0</code>则为永久会员',
                'default' => 3,
                'max'     => 36,
                'min'     => 0,
                'step'    => 1,
                'unit'    => '个月',
                'type'    => 'spinner',
            ),

        );
    }
    public static function rebate_type()
    {
        return
        array(
            'all' => '全部订单',
            '1'   => '付费阅读',
            '2'   => '付费资源',
            '4'   => '购买会员',
            '5'   => '付费图片',
            '6'   => '付费视频',
            '9'   => '购买积分',
        );
    }
    public static function slide($hide = array())
    {
        $args = array();
        return array(
            array(
                'id'      => 'direction',
                'default' => 'horizontal',
                'title'   => '幻灯片方向',
                'inline'  => true,
                'type'    => 'radio',
                'options' => array(
                    'horizontal' => '左右切换',
                    'vertical'   => '上下切换',
                ),
            ),
            array(
                'title'   => '循环切换',
                'class'   => 'compact',
                'id'      => 'loop',
                'default' => true,
                'type'    => 'switcher',
            ),
            array(
                'title'   => '显示翻页按钮',
                'class'   => 'compact',
                'id'      => 'button',
                'default' => false,
                'type'    => 'switcher',
            ),
            array(
                'title'   => '显示指示器',
                'type'    => 'switcher',
                'id'      => 'pagination',
                'class'   => 'compact',
                'default' => false,
                'type'    => 'switcher',
            ),
            array(
                'id'      => 'effect',
                'default' => 'slide',
                'class'   => 'compact',
                'title'   => '切换动画',
                'type'    => "select",
                'options' => array(
                    'slide'     => __('滑动', 'zib_language'),
                    'fade'      => __('淡出淡入', 'zib_language'),
                    'cube'      => __('3D方块', 'zib_language'),
                    'coverflow' => __('3D滑入', 'zib_language'),
                    'flip'      => __('3D翻转', 'zib_language'),
                ),
            ),
            array(
                'dependency' => array(
                    array('direction', '!=', 'vertical'),
                ),
                'title'      => '按比例自动高度',
                'type'       => 'switcher',
                'id'         => 'scale_height',
                'default'    => false,
                'type'       => 'switcher',
            ),
            array(
                'dependency' => array(
                    array('direction', '!=', 'vertical'),
                    array('scale_height', '!=', ''),
                ),
                'id'         => 'scale',
                'class'      => 'compact',
                'title'      => '长宽比例',
                'default'    => 40,
                'max'        => 300,
                'min'        => 10,
                'step'       => 5,
                'unit'       => '%',
                'type'       => 'spinner',
            ),
            array(
                'dependency' => array(
                    array('direction', '!=', 'vertical'),
                    array('scale_height', '!=', ''),
                ),
                'type'       => 'submessage',
                'style'      => 'warning',
                'content'    => '<i class="fa fa-info-circle fa-fw"></i> 开启按比例自动高度后，幻灯片会按照设置的比例保持高度<br>同时下方PC端高度和移动端高端将失效',
            ),
            array(
                'dependency' => array(
                    array('direction', '!=', 'vertical'),
                    array('scale_height', '==', ''),
                ),
                'title'      => '自动高度',
                'class'      => 'compact',
                'type'       => 'switcher',
                'id'         => 'auto_height',
                'default'    => false,
                'type'       => 'switcher',
            ),
            array(
                'dependency' => array(
                    array('auto_height', '!=', ''),
                    array('direction', '!=', 'vertical'),
                    array('scale_height', '==', ''),
                ),
                'id'         => 'max_height',
                'class'      => 'compact',
                'title'      => '最大高度',
                'default'    => 500,
                'max'        => 800,
                'min'        => 120,
                'step'       => 20,
                'unit'       => 'PX',
                'type'       => 'spinner',
            ),
            array(
                'dependency' => array(
                    array('auto_height', '!=', ''),
                    array('direction', '!=', 'vertical'),
                    array('scale_height', '==', ''),
                ),
                'id'         => 'min_height',
                'title'      => '最小高度',
                'class'      => 'compact',
                'default'    => 180,
                'max'        => 500,
                'min'        => 100,
                'step'       => 20,
                'unit'       => 'PX',
                'type'       => 'spinner',
            ),
            array(
                'dependency' => array(
                    array('auto_height', '!=', ''),
                    array('direction', '!=', 'vertical'),
                    array('scale_height', '==', ''),
                ),
                'type'       => 'submessage',
                'style'      => 'warning',
                'content'    => '<i class="fa fa-info-circle fa-fw"></i> 开启自动高度后，会根据幻灯片背景图自动调节每张幻灯片高度<br>请注意幻灯片图片的长宽比例不能差距太大，否则会显示不佳！<br>请在上方设置最大、最小高度，避免幻灯片过大过小，同时下方的PC端高度和移动端高端将失效',
            ),
            array(
                'id'      => 'pc_height',
                'class'   => 'compact',
                'title'   => '电脑端高度',
                'default' => 400,
                'max'     => 800,
                'min'     => 120,
                'step'    => 20,
                'unit'    => 'PX',
                'type'    => 'spinner',
            ),
            array(
                'id'      => 'm_height',
                'title'   => '移动端高度',
                'class'   => 'compact',
                'default' => 200,
                'max'     => 500,
                'min'     => 100,
                'step'    => 20,
                'unit'    => 'PX',
                'type'    => 'spinner',
            ),
            array(
                'id'      => 'spacebetween',
                'title'   => '幻灯片间距',
                'default' => 15,
                'max'     => 500,
                'min'     => 0,
                'step'    => 5,
                'unit'    => 'PX',
                'type'    => 'spinner',
            ),
            array(
                'id'       => 'speed',
                'title'    => '切换速度',
                'subtitle' => '切换过程的时间(越小越快)',
                'desc'     => '设置为“0”，则为自动模式：根据幻灯片大小自动设置最佳速度',
                'class'    => 'compact',
                'default'  => 0,
                'max'      => 3000,
                'min'      => 0,
                'step'     => 100,
                'unit'     => '毫秒',
                'type'     => 'slider',
            ),
            array(
                'title'   => '自动播放',
                'type'    => 'switcher',
                'id'      => 'autoplay',
                'class'   => 'compact',
                'default' => true,
                'type'    => 'switcher',
            ),
            array(
                'dependency' => array('autoplay', '!=', ''),
                'id'         => 'interval',
                'title'      => '停顿时间',
                'subtitle'   => '自动切换的时间间隔(越小越快)',
                'class'      => 'compact',
                'default'    => 4,
                'max'        => 20,
                'min'        => 0,
                'step'       => 1,
                'unit'       => '秒',
                'type'       => 'slider',
            ),
        );
    }
    public static function orderby()
    {
        return array(
            array(
                'id'          => 'lists',
                'title'       => '显示排序方式',
                'options'     => array(
                    'modified'      => '更新',
                    'date'          => '发布',
                    'views'         => '浏览',
                    'like'          => '点赞',
                    'comment_count' => '评论',
                    'favorite'      => '收藏',
                    'rand'          => '随机',
                ),
                'type'        => 'select',
                'placeholder' => '选择需要的排序方式按钮',
                'default'     => array('modified', 'views', 'like', 'comment_count'),
                'chosen'      => true,
                'multiple'    => true,
                'sortable'    => true,
            ),
            array(
                'title'   => __('更多排序方式', 'zib_language'),
                'id'      => 'dropdown',
                'class'   => 'compact',
                'default' => false,
                'label'   => '用下拉框显示全部排序方式',
                'type'    => 'switcher',
            ),
        );
    }

    public static function ajax_but($type = '')
    {
        $ajax       = true;
        $query_args = array();
        if ('topics' == $type) {
            $type       = 'tag';
            $query_args = array('taxonomy' => 'topics');
        }

        $desc = '选择并排序需要显示的按钮';
        $desc .= 'tags' == $type ? '' : '，建议选择同级内容，会自动获取二级三级内容<br/>所选按钮内没有文章则不会显示';

        $placeholder = $ajax ? '输入关键词搜索内容' : '选择并排序需要显示的按钮';
        return array(
            array(
                'id'          => 'lists',
                'title'       => '按钮列表',
                'options'     => $type,
                'query_args'  => $query_args,
                'type'        => 'select',
                'placeholder' => $placeholder,
                'chosen'      => true,
                'desc'        => $desc,
                'multiple'    => true,
                'sortable'    => true,
                'ajax'        => $ajax,
                'settings'    => array(
                    'min_length' => 2,
                ),
            ),
            array(
                'title'   => __('下拉列表', 'zib_language'),
                'id'      => 'dropdown',
                'class'   => 'compact',
                'default' => false,
                'label'   => '用下拉框显示更多内容',
                'type'    => 'switcher',
            ),
            array(
                'dependency'  => array('dropdown', '!=', ''),
                'id'          => 'dropdown_lists',
                'desc'        => '请勿添加过多，避免显示很难看',
                'class'       => 'compact',
                'title'       => '下拉菜单列表',
                'options'     => $type,
                'query_args'  => $query_args,
                'type'        => 'select',
                'placeholder' => $placeholder,
                'chosen'      => true,
                'multiple'    => true,
                'sortable'    => true,
                'ajax'        => $ajax,
                'settings'    => array(
                    'min_length' => 2,
                ),
            ),
        );
    }

    public static function invit_code_reward()
    {
        $args   = array();
        $args[] = array(
            'title'   => '经验值',
            'id'      => 'level_integral',
            'default' => 0,
            'type'    => 'number',
            'unit'    => '经验值',
        );
        $args[] = array(
            'title'   => '积分',
            'id'      => 'points',
            'default' => 0,
            'class'   => 'compact',
            'type'    => 'number',
            'unit'    => '积分',
        );
        $args[] = array(
            'title'   => '余额',
            'id'      => 'balance',
            'default' => 0,
            'class'   => 'compact',
            'type'    => 'number',
            'unit'    => '余额',
        );
        $args[] = array(
            'title'   => 'VIP会员',
            'id'      => 'vip',
            'type'    => 'radio',
            'default' => '',
            'options' => array(
                ''  => '无',
                '1' => _pz('pay_user_vip_1_name'),
                '2' => _pz('pay_user_vip_2_name'),
            ),
        );
        $args[] = array(
            'dependency' => array('vip', '!=', ''),
            'title'      => ' ',
            'subtitle'   => '会员赠送时长',
            'desc'       => '单位为天，填<code>Permanent</code>为永久',
            'id'         => 'vip_time',
            'default'    => '',
            'class'      => 'compact',
            'type'       => 'text',
        );

        return $args;
    }

    public static function points_free()
    {
        $args   = array();
        $args[] = array(
            'content' => '设置每个任务可免费获取的积分值<br><i class="fa fa-fw fa-info-circle fa-fw"></i> 如果以下对应的功能未开启，请将分值设置为0',
            'style'   => 'warning',
            'type'    => 'submessage',
            'class'   => 'text-center',
        );

        $args[] = array(
            'title'   => '每日上限',
            'desc'    => '一个用户每天最多可获取多少免费积分，请勿低于单项值(包含签到奖励)',
            'id'      => 'day_max',
            'default' => 100,
            'max'     => 1000,
            'min'     => 0,
            'step'    => 1,
            'type'    => 'spinner',
        );

        $group_k_2 = null;

        foreach (zib_get_user_integral_add_options() as $k => $v) {
            $group_k = $v[3];
            $args[]  = array(
                'title'   => '[' . $group_k . ']' . $v[0],
                'class'   => $group_k === $group_k_2 ? 'compact' : '',
                'id'      => $k,
                'default' => $v[1],
                'max'     => 1000,
                'min'     => 0,
                'step'    => 1,
                'type'    => 'spinner',
            );
            $group_k_2 = $v[3];
        }

        return $args;
    }

    public static function user_integral()
    {

        //分组
        $args   = array();
        $args[] = array(
            'content' => '在此设置经验值的获取得分方式<br><i class="fa fa-fw fa-info-circle fa-fw"></i> 如果以下对应的功能未开启，请将分值设置为0',
            'style'   => 'warning',
            'type'    => 'submessage',
            'class'   => 'text-center',
        );

        $args[] = array(
            'title'   => '每日上限',
            'desc'    => '一个用户每天最多加多少经验值，请勿低于单项值(包含签到奖励)',
            'id'      => 'day_max',
            'default' => 100,
            'max'     => 1000,
            'min'     => 0,
            'step'    => 1,
            'type'    => 'spinner',
        );
        $group_k_2 = null;

        foreach (zib_get_user_integral_add_options() as $k => $v) {
            $group_k = $v[3];
            $args[]  = array(
                'title'   => '[' . $group_k . ']' . $v[0],
                'class'   => $group_k === $group_k_2 ? 'compact' : '',
                'id'      => $k,
                'default' => $v[1],
                'max'     => 1000,
                'min'     => 0,
                'step'    => 1,
                'type'    => 'spinner',
            );
            $group_k_2 = $v[3];
        }
        return $args;
    }

    public static function checkin_reward()
    {
        $tab = array();
        for ($i = 2; $i <= 7; $i++) {
            $tab[] = array(
                'title'  => '第' . $i . '天',
                'fields' => array(
                    array(
                        'id'      => 'points_' . $i,
                        'title'   => '奖励积分',
                        'default' => $i * 20,
                        'type'    => 'number',
                        'unit'    => '积分',
                    ),
                    array(
                        'id'      => 'integral_' . $i,
                        'title'   => '奖励经验值',
                        'default' => $i * 30,
                        'type'    => 'number',
                        'unit'    => '经验值',
                        'class'   => 'compact',
                    ),
                ),
            );
        }
        return $tab;
    }

    public static function user_level_tab()
    {
        $max = _pz('user_level_max', 10);
        $tab = array();
        for ($i = 1; $i <= $max; $i++) {
            $tab[] = array(
                'title'  => 'Lv ' . $i,
                'fields' => array(
                    array(
                        'title'   => __('等级图标', 'zib_language'),
                        'id'      => 'icon_img_' . $i,
                        'desc'    => __('自定义等级的小图标，显示在昵称后方(建议尺寸120x50)') . ($i > 10 ? '<br>主题内置了10个等级图标，如需开启更高等级需要自己制作等级图标' : ''),
                        'default' => ($i < 11 ? ZIB_TEMPLATE_DIRECTORY_URI . '/img/user-level-' . $i . '.png' : ''),
                        'preview' => true,
                        'library' => 'image',
                        'type'    => 'upload',
                    ),
                    array(
                        'title'   => '等级名称',
                        'id'      => 'name_' . $i,
                        'default' => 'LV' . $i,
                        'type'    => 'text',
                    ),
                    array(
                        'title'   => '升级经验',
                        'class'   => ((1 === $i) ? 'hide' : ''),
                        'desc'    => '当用户的等级经验值达到多少时，升级到此等级<div class="c-yellow"><i class="fa fa-fw fa-info-circle fa-fw"></i>经此验值必须高于上一级的经验值，否则会出现错误</div>',
                        'id'      => 'upgrade_integral_' . $i,
                        'default' => ($i - 1) * 200,
                        'max'     => 10000000000000000,
                        'min'     => 0,
                        'step'    => 50,
                        'type'    => 'spinner',
                    ),
                ),
            );
        }

        return $tab;
    }

    public static function user_can_user_fields()
    {
        $user_fields        = array();
        $user_fields['all'] = array(
            'title'   => '所有人',
            'default' => false,
            'label'   => '包含未登录的游客(开启后任何人都拥有此权限)',
            'class'   => 'compact mini',
            'id'      => 'all',
            'type'    => 'switcher',
        );
        $user_fields['logged'] = array(
            'title'   => '已登录用户',
            'default' => false,
            'label'   => '开启后只要用户登录就拥有此权限',
            'class'   => 'compact mini',
            'id'      => 'logged',
            'type'    => 'switcher',
        );

        $user_level_max = _pz('user_level_max', 10);
        if (_pz('user_level_s', true)) {
            $user_fields['level'] = array(
                'class'   => 'compact mini',
                'title'   => '用户等级',
                'id'      => 'level',
                'default' => 0,
                'max'     => $user_level_max,
                'min'     => -1,
                'step'    => 1,
                'unit'    => '级',
                'type'    => 'spinner',
            );
        }
        if (_pz('pay_user_vip_1_s', true)) {
            $user_fields['vip'] = array(
                'title'   => '会员等级',
                'id'      => 'vip',
                'default' => 0,
                'max'     => $user_level_max,
                'min'     => -1,
                'class'   => 'compact mini',
                'step'    => 1,
                'unit'    => '级',
                'type'    => 'spinner',
            );
        }
        $user_fields['auth'] = array(
            'title'   => '认证用户',
            'default' => false,
            'id'      => 'auth',
            'class'   => 'compact mini',
            'type'    => 'switcher',
        );
        $user_fields['moderator'] = array(
            'title'   => '版主',
            'default' => false,
            'class'   => 'compact mini',
            'id'      => 'moderator',
            'type'    => 'switcher',
        );
        $user_fields['plate_author'] = array(
            'title'   => '超级版主',
            'default' => false,
            'label'   => '版块作者',
            'class'   => 'compact mini',
            'id'      => 'plate_author',
            'type'    => 'switcher',
        );
        $user_fields['cat_moderator'] = array(
            'title'   => '分区版主',
            'default' => false,
            'class'   => 'compact mini',
            'id'      => 'cat_moderator',
            'type'    => 'switcher',
        );
        return $user_fields;
    }

    public static function user_caps()
    {
        $new_badge                     = zib_get_csf_option_new_badge();
        $roles_all                     = array('all', 'logged', 'level', 'vip', 'auth', 'cat_moderator', 'plate_author', 'moderator');
        $user_all_caps                 = array();
        $user_all_caps['用户功能'] = array(
            array(
                'id'      => 'user_report',
                'name'    => '举报其它用户(举报不良信息)',
                'help'    => '此权限依赖于[用户举报]功能',
                'default' => array(
                    'logged' => true,
                ),
            ),
        );
        $user_all_caps['用户操作'] = array(
            array(
                'id'    => 'set_user_ban',
                'name'  => '设置用户封禁状态(将其它用户封号、拉入小黑屋)',
                'roles' => array('cat_moderator'),
                'help'  => '默认为管理员权限，论坛管理员也拥有此权限，此权限依赖于[用户封禁]功能',
            ),
            array(
                'id'    => 'medal_manually_set',
                'name'  => '为用户授予徽章',
                'roles' => array('cat_moderator'),
                'help'  => '默认为管理员权限，此权限依赖于[用户徽章]功能',
            ),
        );
        $user_all_caps['前台投稿'] = array(
            array(
                'id'      => 'new_post_add',
                'name'    => '发布新的文章',
                'default' => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'new_post_audit_no',
                'name'          => '发布投稿无需审核直接发布',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'new_post_audit_no_manual',
                'name'          => '发布投稿无需[人工审核]直接发布',
                'desc'          => '需启用<a href="' . zib_get_admin_csf_url('扩展增强/api内容审核') . '">api内容审核</a>功能，API审核通过后直接发布',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'new_post_upload_img',
                'name'          => '发布投稿允许在编辑器上传图片',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'new_post_upload_video',
                'name'          => '发布投稿允许在编辑器上传视频',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'new_post_iframe_video',
                'name'          => '发布投稿允许在编辑器插入iframe嵌入视频' . $new_badge['6.3'],
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'      => 'new_post_hide',
                'name'    => '发布投稿允许在编辑器发布隐藏内容',
                'default' => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'new_post_pay',
                'name'          => '发布投稿允许在设置付费内容' . $new_badge['6.3'],
                'desc'          => '此功能建议与<a href="' . zib_get_admin_csf_url('商城付费/创作分成') . '">创作分成</a>功能配合使用，如果未开启<a href="' . zib_get_admin_csf_url('商城付费/创作分成') . '">创作分成</a>功能，则用户设置的付费收益全部属于站长',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'            => 'new_post_edit',
                'name'          => '修改自己发布的投稿' . $new_badge['6.3'],
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'new_post_delete',
                'name'          => '删除自己发布的投稿' . $new_badge['6.3'],
                'exclude_roles' => array('all'),
            ),
        );

        $user_all_caps['评论'] = array(
            array(
                'id'            => 'comment_view',
                'name'          => '查看评论' . $new_badge['6.7'],
                'default'       => array(
                    'all' => true,
                ),
                'desc' => '拥有编辑自己文章或他人文章评论权限的用户会直接拥有此权限',
            ),
            array(
                'id'            => 'comment_edit',
                'name'          => '修改自己发布的评论',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'logged' => true,
                ),
            ),
            array(
                'id'            => 'comment_delete',
                'name'          => '删除自己发布的评论',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'   => 'comment_audit_no',
                'name' => '发布评论无需审核直接发布',
                'desc' => '此权限会完全覆盖wp设置-讨论中的审核规则，请谨慎开启<div class="c-yellow">拥有所在文章【批准、驳回评论权限】的用户直接拥有此权限</div>',
            ),
            array(
                'id'   => 'comment_audit_no_manual',
                'name' => '发布评论无需[人工审核]直接发布',
                'desc' => '需启用<a href="' . zib_get_admin_csf_url('扩展增强/api内容审核') . '">api内容审核</a>功能，API审核通过后直接发布<br/>此审核优先级大于wp默认审核规则，如果API审核未通过，则按照wp默认(<a href="' . admin_url('options-discussion.php') . '">wp设置-讨论</a>)规则进行判断',
            ),
        );
        $user_all_caps['评论管理'] = array(
            array(
                'id'            => 'comment_edit_my_post',
                'name'          => '修改[自己发布的文章(帖子)]下的评论',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'      => 'comment_edit_other',
                'name'    => '修改自己管理下的帖子评论',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'            => 'comment_audit_my_post',
                'name'          => '审核(批准、驳回)[自己发布的文章(帖子)]下的评论',
                'exclude_roles' => array('all'),
                'default'       => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'      => 'comment_audit_other',
                'name'    => '审核(批准、驳回)自己管理下的帖子评论',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'moderator'     => true,
                    'plate_author'  => true,
                    'cat_moderator' => true,
                ),
            ),
            array(
                'id'            => 'comment_delete_my_post',
                'name'          => '删除[自己发布的文章(帖子)]下的评论',
                'exclude_roles' => array('all'),
            ),
            array(
                'id'      => 'comment_delete_other',
                'name'    => '删除自己管理下的帖子评论',
                'roles'   => array('moderator', 'plate_author', 'cat_moderator'),
                'default' => array(
                    'cat_moderator' => true,
                ),
            ),
        );

        return $user_all_caps;
    }

    //用户权限fields
    public static function user_can_fields($caps = array(), $con = '')
    {
        $fields   = array();
        $fields[] = array(
            'content' => $con . '<div style="color:#f97113;"><i class="fa fa-fw fa-info-circle fa-fw"></i>注意事项：
            <br/> 1、每一项用户能力（权限）启用都是按照自上而下的顺序设置，例如 用户评论：开启登录用户，那么只要登录的用户即可拥有此权限，就无需再设置等级或者其它
            <br/> 2、部分权限涉及到一些敏感功能，请注意相关风险！
            <br/> 3、由于功能权限较多，部分权限逻辑稍微复杂，请一定要仔细查看、设置！
            <br/> 4、设置的过程中如果出现混乱，可以重置选区将当前页面的用户权限恢复到初始值
            <br/><a target="_blank" href="https://www.zibll.com/3090.html">查看官方教程</a>
            </div>',
            'style'   => 'warning',
            'type'    => 'submessage',
        );

        $user_fields = self::user_can_user_fields();
        foreach ($caps as $group_key => $group) {
            //分组
            $caps = array();
            foreach ($group as $key => $val) {
                $_fields = $user_fields;
                if (isset($val['roles'])) {
                    $_fields = array();
                    foreach ($val['roles'] as $roles_key) {
                        $_fields[] = $user_fields[$roles_key];
                    }
                } elseif (isset($val['exclude_roles'])) {
                    foreach ($val['exclude_roles'] as $exclude_roles_key) {
                        unset($_fields[$exclude_roles_key]);
                    }
                }

                $caps[] = array(
                    'title'  => $val['name'],
                    'fields' => array(array(
                        'id'      => $val['id'],
                        'default' => isset($val['default']) ? $val['default'] : array(),
                        'desc'    => isset($val['desc']) ? $val['desc'] : '',
                        'help'    => isset($val['help']) ? $val['help'] : '',
                        'type'    => 'fieldset',
                        'fields'  => $_fields,
                    )),
                );
            }
            $fields[] = array(
                'id'         => 'user_cap',
                'type'       => 'accordion',
                'class'      => 'accordion-mini',
                'title'      => $group_key,
                'accordions' => $caps,
            );
        }

        return $fields;
    }

    public static function vip_tab($level = 1)
    {
        return array(
            array(
                'dependency' => array('pay_user_vip_' . $level . '_s', '!=', '', 'all', 'visible'),
                'id'         => 'pay_user_vip_' . $level . '_equity',
                'title'      => '会员权益简介',
                'subtitle'   => _pz('pay_user_vip_' . $level . '_name') . '简介',
                'default'    => '<li>全站资源折扣购买</li>
<li>部分内容免费阅读</li>
<li>一对一技术指导</li>
<li>VIP用户专属QQ群</li>',
                'help'       => '使用自定义HTML代码，每行用li标签包围',
                'attributes' => array(
                    'rows' => 4,
                ),
                'sanitize'   => false,
                'type'       => 'textarea',
            ),
            array(
                'dependency'             => array('pay_user_vip_' . $level . '_s', '!=', '', 'all', 'visible'),
                'id'                     => 'vip_' . $level . '_product',
                'title'                  => '会员商品',
                'subtitle'               => _pz('pay_user_vip_' . $level . '_name') . '的商品选项',
                'type'                   => 'group',
                'accordion_title_prefix' => '价格：￥',
                'max'                    => 8,
                'button_title'           => '添加会员商品',
                'class'                  => 'compact',
                'default'                => array(
                    array(
                        'price'      => '99',
                        'show_price' => '199',
                        'tag'        => '<i class="fa fa-fw fa-bolt"></i> 限时特惠',
                        'time'       => 3,
                    ),
                    array(
                        'price'      => '199',
                        'show_price' => '299',
                        'tag'        => '<i class="fa fa-fw fa-bolt"></i> 站长推荐',
                        'time'       => 6,
                    ),
                ),
                'fields'                 => CFS_Module::vip_product(),
            ),
            array(
                'dependency' => array('pay_user_vip_' . $level . '_s', '!=', '', 'all', 'visible'),
                'title'      => __('会员图标', 'zib_language'),
                'id'         => 'vip_' . $level . 'img_icon',
                'desc'       => __('自定义' . _pz('pay_user_vip_' . $level . '_name') . '的图标，(建议尺寸300x300)'),
                'default'    => ZIB_TEMPLATE_DIRECTORY_URI . '/img/vip-' . $level . '.svg',
                'preview'    => true,
                'library'    => 'image', 'type' => 'upload',
            ),
        );
    }

    public static function aut()
    {
        if (ZibAut::is_aut()) {
            $con = '<div id="authorization_form" class="ajax-form" ajax-url="' . esc_url(admin_url('admin-ajax.php')) . '">
            <div class="ok-icon"><svg t="1585712312243" class="icon" style="width: 1em; height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3845" data-spm-anchor-id="a313x.7781069.0.i0"><path d="M115.456 0h793.6a51.2 51.2 0 0 1 51.2 51.2v294.4a102.4 102.4 0 0 1-102.4 102.4h-691.2a102.4 102.4 0 0 1-102.4-102.4V51.2a51.2 51.2 0 0 1 51.2-51.2z m0 0" fill="#FF6B5A" p-id="3846"></path><path d="M256 13.056h95.744v402.432H256zM671.488 13.056h95.744v402.432h-95.744z" fill="#FFFFFF" p-id="3847"></path><path d="M89.856 586.752L512 1022.72l421.632-435.2z m0 0" fill="#6DC1E2" p-id="3848"></path><path d="M89.856 586.752l235.52-253.952h372.736l235.52 253.952z m0 0" fill="#ADD9EA" p-id="3849"></path><path d="M301.824 586.752L443.136 332.8h137.216l141.312 253.952z m0 0" fill="#E1F9FF" p-id="3850"></path><path d="M301.824 586.752l209.92 435.2 209.92-435.2z m0 0" fill="#9AE6F7" p-id="3851"></path></svg></div>
            <p style=" color: #0087e8; font-size: 15px; "><svg class="icon" style="width: 1em;height: 1em;vertical-align: -.2em;fill: currentColor;overflow: hidden;font-size: 1.4em;" viewBox="0 0 1024 1024"><path d="M492.224 6.72c11.2-8.96 26.88-8.96 38.016 0l66.432 53.376c64 51.392 152.704 80.768 243.776 80.768 27.52 0 55.104-2.624 81.92-7.872a30.08 30.08 0 0 1 24.96 6.4 30.528 30.528 0 0 1 11.008 23.424V609.28c0 131.84-87.36 253.696-228.288 317.824L523.52 1021.248a30.08 30.08 0 0 1-24.96 0l-206.464-94.08C151.36 862.976 64 741.12 64 609.28V162.944a30.464 30.464 0 0 1 36.16-29.888 425.6 425.6 0 0 0 81.92 7.936c91.008 0 179.84-29.504 243.712-80.768z m19.008 62.528l-47.552 38.208c-75.52 60.8-175.616 94.144-281.6 94.144-19.2 0-38.464-1.024-57.472-3.328V609.28c0 107.84 73.92 208.512 192.768 262.72l193.856 88.384 193.92-88.384c118.912-54.208 192.64-154.88 192.64-262.72V198.272a507.072 507.072 0 0 1-57.344 3.328c-106.176 0-206.144-33.408-281.728-94.08l-47.488-38.272z m132.928 242.944c31.424 0 56.832 25.536 56.832 56.832H564.544v90.944h121.92a56.448 56.448 0 0 1-56.384 56.384H564.48v103.424h150.272a56.832 56.832 0 0 1-56.832 56.832H365.056a56.832 56.832 0 0 1-56.832-56.832h60.608v-144c0-33.92 27.52-61.44 61.44-61.44v205.312h71.68V369.024H324.8c0-31.424 25.472-56.832 56.832-56.832z" p-id="4799"></path></svg> 恭喜您! 已完成授权</p>
            <input type="hidden" ajax-name="action" value="admin_delete_aut">
            <a id="authorization_submit" class="but c-red ajax-submit">撤销授权</a>
            <div class="ajax-notice"></div>
            </div>';
        } else {
            $con = '<div id="authorization_form" class="ajax-form" ajax-url="' . esc_url(admin_url('admin-ajax.php')) . '">
            <div class="ok-icon"><svg class="icon" style="font-size: 1.2em;width: 1em; height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024"><path d="M880 502.3V317.1c0-34.9-24.4-66-60.8-77.4l-80.4-30c-37.8-14.1-73.4-32.9-105.7-55.7l-84.6-60c-19.2-15.2-47.8-15.2-67 0l-84.7 59.9c-32.3 22.8-67.8 41.6-105.7 55.7l-80.4 30c-36.4 11.4-60.8 42.5-60.8 77.4v185.2c0 123.2 63.9 239.2 172.5 313.2l158.5 108c20.2 13.7 47.9 13.7 68.1 0l158.5-108C816.1 741.6 880 625.5 880 502.3z" fill="#0DCEA7" p-id="17337"></path><path d="M150 317.1v3.8c13.4-27.6 30-53.3 49.3-76.7C169.4 258 150 286 150 317.1zM880 317.1c0-34.9-24.4-66-60.8-77.4l-43.5-16.2c57.7 60.6 95.8 140 104.2 228.1l0.1-134.5zM572.8 111.2L548.5 94c-19.2-15.2-47.8-15.2-67 0l-15.3 10.8c10-0.8 20.2-1.2 30.5-1.2 26 0.1 51.5 2.7 76.1 7.6zM496.7 873.9c-39.5 0-77.6-5.9-113.4-17l97.7 66.6c20.2 13.7 47.9 13.7 68.1 0l158.5-108c92.3-62.9 152.3-156.1 168.2-258.3C843.5 737.3 686 873.9 496.7 873.9z" fill="#0DCEA7" p-id="17338"></path><path d="M875.8 557.2c2.8-18.1 4.3-36.4 4.3-54.9v-50.8c-8.5-88.1-46.6-167.4-104.2-228.1L739 209.6c-37.8-14.1-73.4-32.9-105.7-55.7l-60.5-42.7c-24.6-4.9-50-7.5-76.1-7.5-10.3 0-20.4 0.4-30.5 1.2l-58.7 41.5c23.4-5.2 47.7-8 72.7-8 183.6 0 332.4 148.8 332.4 332.4S663.9 803 480.3 803c-170.8 0-311.5-128.9-330.2-294.7 2 121 65.6 234.5 172.4 307.2l60.8 41.4c35.9 11 74 17 113.4 17 189.3 0 346.8-136.6 379.1-316.7zM261.2 220.8l-50.4 18.8c-4 1.3-7.8 2.8-11.5 4.5-19.3 23.4-35.9 49.2-49.3 76.7v112.7c9.4-84.5 50.5-159.4 111.2-212.7z" fill="#1DD49C" p-id="17339"></path><path d="M480.3 803c183.6 0 332.4-148.8 332.4-332.4S663.9 138.3 480.3 138.3c-25 0-49.3 2.8-72.7 8l-10.7 7.6c-32.3 22.8-67.8 41.6-105.7 55.7l-30 11.2C200.5 274.1 159.4 349 150 433.6v68.8c0 2 0 4 0.1 6C168.8 674.1 309.5 803 480.3 803z m-16.4-630c154.4 0 279.6 125.2 279.6 279.6S618.3 732.2 463.9 732.2 184.3 607 184.3 452.6 309.5 173 463.9 173z" fill="#2DDB92" p-id="17340"></path><path d="M463.9 732.2c154.4 0 279.6-125.2 279.6-279.6S618.3 173 463.9 173 184.3 298.2 184.3 452.6s125.2 279.6 279.6 279.6z m-16.4-524.5c125.3 0 226.8 101.5 226.8 226.8S572.8 661.3 447.5 661.3 220.7 559.8 220.7 434.5s101.6-226.8 226.8-226.8z" fill="#3DE188" p-id="17341" data-spm-anchor-id="a313x.7781069.0.i7"></path><path d="M447.5 661.3c125.3 0 226.8-101.5 226.8-226.8S572.8 207.7 447.5 207.7 220.7 309.2 220.7 434.5s101.6 226.8 226.8 226.8z m-16.4-419c96.1 0 174 77.9 174 174s-77.9 174-174 174-174-77.9-174-174 77.9-174 174-174z" fill="#4CE77D" p-id="17342"></path><path d="M431.1 590.4c96.1 0 174-77.9 174-174s-77.9-174-174-174-174 77.9-174 174 77.9 174 174 174zM414.7 277c67 0 121.3 54.3 121.3 121.3s-54.3 121.3-121.3 121.3-121.3-54.3-121.3-121.3S347.8 277 414.7 277z" fill="#5CEE73" p-id="17343"></path><path d="M414.7 398.3m-121.3 0a121.3 121.3 0 1 0 242.6 0 121.3 121.3 0 1 0-242.6 0Z" fill="#6CF468" p-id="17344"></path><path d="M515 100.7c8.3 0 16.2 2.7 22.3 7.5l0.4 0.3 0.4 0.3 84.7 59.9c33.5 23.7 70.5 43.2 109.8 57.9l80.4 30 0.4 0.2 0.5 0.1c28.8 9.1 48.2 33.3 48.2 60.3v185.2c0 28.9-3.7 57.8-11.1 86-7.3 27.8-18.1 54.8-32.2 80.4-14.1 25.6-31.5 49.8-51.7 71.8-20.5 22.4-43.9 42.6-69.6 60.1L539 908.6c-6.8 4.6-15.3 7.2-23.9 7.2s-17.1-2.6-23.9-7.2l-158.5-108c-25.7-17.5-49.1-37.7-69.6-60.1-20.2-22-37.6-46.2-51.7-71.8-14.1-25.6-24.9-52.6-32.2-80.4-7.4-28.1-11.1-57-11.1-86V317.1c0-27 19.4-51.2 48.2-60.3l0.5-0.1 0.4-0.2 80.4-30c39.3-14.7 76.2-34.1 109.8-57.9l84.7-59.9 0.4-0.3 0.4-0.3c5.9-4.8 13.9-7.4 22.1-7.4m0-18c-11.9 0-23.9 3.8-33.5 11.4L396.8 154c-32.3 22.8-67.8 41.6-105.7 55.7l-80.4 30c-36.4 11.4-60.8 42.5-60.8 77.4v185.2c0 123.2 63.9 239.2 172.5 313.2l158.5 108c10.1 6.9 22.1 10.3 34 10.3 12 0 24-3.4 34-10.3l158.5-108c108.6-74 172.5-190 172.5-313.2V317.1c0-34.9-24.4-66-60.8-77.4l-80.4-30c-37.8-14.1-73.4-32.9-105.7-55.7l-84.5-60c-9.6-7.5-21.5-11.3-33.5-11.3z" fill="#0EC69A" p-id="17345"></path><path d="M688.8 496.7V406c0-17.1-11.6-32.3-28.9-37.9l-38.3-14.7c-18-6.9-35-16.1-50.3-27.3L531 296.8c-9.1-7.4-22.8-7.4-31.9 0l-40.3 29.3a218.45 218.45 0 0 1-50.3 27.3l-38.3 14.7c-17.3 5.6-28.9 20.8-28.9 37.9v90.7c0 60.3 30.4 117.1 82.1 153.3l75.5 52.9c9.6 6.7 22.8 6.7 32.4 0l75.5-52.9c51.6-36.2 82-93 82-153.3z" fill="#9CFFBD" p-id="17346"></path><path d="M325.6 287.5c-7.2 0-14.1-4.4-16.8-11.6-3.5-9.3 1.1-19.7 10.4-23.2 68.5-26.2 110.5-60.3 110.9-60.6 7.7-6.3 19-5.2 25.3 2.5s5.2 19-2.5 25.3c-1.9 1.5-47 38.2-120.9 66.4-2.1 0.8-4.2 1.2-6.4 1.2z" fill="#FFFFFF" p-id="17347"></path><path d="M260.2 311.7c-7.3 0-14.2-4.5-16.9-11.7-3.5-9.3 1.3-19.7 10.6-23.1l10.5-3.9c9.3-3.5 19.7 1.3 23.1 10.6 3.5 9.3-1.3 19.7-10.6 23.1l-10.5 3.9c-2.1 0.7-4.2 1.1-6.2 1.1z" fill="#FFFFFF" p-id="17348"></path></svg></div>
            <p style="color:#fd4c73;">激动人心的时候到了！即将开启优雅的建站之旅！</p>
            <div class="hide-box">
            <p>请输入购买主题时获取的授权码：</p>
            <input class="regular-text" type="text" ajax-name="cut_code" value="" placeholder="请输入授权码">
            <input type="hidden" ajax-name="action" value="admin_curl_aut">
            </div>
            <a id="authorization_submit" class="but c-blue ajax-submit curl-aut-submit" data-depend-id="zib_submit_aut">一键授权</a>
            <div class="ajax-notice"></div>
            </div>';
        }
        if (!ZibAut::is_local()) {
            return array(
                'type'    => 'content',
                'content' => $con,
            );
        } else {
            return array(
                'type'    => 'content',
                'style'   => 'info',
                'content' => '<div id="authorization_form">
            <div class="ok-icon"><svg t="1585712312243" class="icon" style="width: 1em; height: 1em;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3845" data-spm-anchor-id="a313x.7781069.0.i0"><path d="M115.456 0h793.6a51.2 51.2 0 0 1 51.2 51.2v294.4a102.4 102.4 0 0 1-102.4 102.4h-691.2a102.4 102.4 0 0 1-102.4-102.4V51.2a51.2 51.2 0 0 1 51.2-51.2z m0 0" fill="#FF6B5A" p-id="3846"></path><path d="M256 13.056h95.744v402.432H256zM671.488 13.056h95.744v402.432h-95.744z" fill="#FFFFFF" p-id="3847"></path><path d="M89.856 586.752L512 1022.72l421.632-435.2z m0 0" fill="#6DC1E2" p-id="3848"></path><path d="M89.856 586.752l235.52-253.952h372.736l235.52 253.952z m0 0" fill="#ADD9EA" p-id="3849"></path><path d="M301.824 586.752L443.136 332.8h137.216l141.312 253.952z m0 0" fill="#E1F9FF" p-id="3850"></path><path d="M301.824 586.752l209.92 435.2 209.92-435.2z m0 0" fill="#9AE6F7" p-id="3851"></path></svg></div>
            <p style="color:#1a7cf3;margin: 30px 1px;">您当前正处于本地环境，暂时无需授权！</p>
            </div>',
            );
        }
    }
    public static function backup()
    {
        $csf            = array();
        $prefix         = 'zibll_options';
        $options        = get_option($prefix . '_backup');
        $lists          = '暂无备份数据！';
        $admin_ajax_url = admin_url('admin-ajax.php');
        $delete_but     = '';
        if ($options) {
            $lists   = '';
            $options = array_reverse($options);
            $count   = 0;
            foreach ($options as $key => $val) {
                $ajax_url = add_query_arg('key', $key, $admin_ajax_url);
                $del      = '<a href="' . add_query_arg('action', 'options_backup_delete', $ajax_url) . '" data-confirm="确认要删除此备份[' . $key . ']？删除后不可恢复！" class="but c-yellow ajax-get ml10">删除</a>';
                $restore  = '<a href="' . add_query_arg('action', 'options_backup_restore', $ajax_url) . '" data-confirm="确认将主题设置恢复到此备份吗？[' . $key . ']？" class="but c-blue ajax-get ml10">恢复</a>';
                $lists .= '<div class="backup-item flex ac jsb">';
                $lists .= '<div class="item-left"><div>' . $val['time'] . '</div><div> [' . $val['type'] . ']</div></div>';
                $lists .= '<span class="shrink-0">' . $restore . $del . '</span>';
                $lists .= '</div>';
                $count++;
            }
            if ($count > 3) {
                $delete_but = '<a href="' . add_query_arg(array('action' => 'options_backup_delete_surplus', 'key' => 'all'), $admin_ajax_url) . '" data-confirm="确认要删除多余的备份数据吗？删除后不可恢复！" class="but jb-red ajax-get">删除备份 保留最新三份</a>';
            }
        }
        $csf[] = array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => '<h3 style="color:#fd4c73;"><i class="csf-tab-icon fa fa-fw fa-copy"></i> 备份&恢复</h3>
            <ajaxform class="ajax-form">
            <div style="margin:10px 0">
            <p>系统会在重置、更新等重要操作时自动备份主题设置，您可以此进行恢复备份或手动备份</p>
            <p>恢复备份后，请先保存一次主题设置，然后刷新后再做其它操作！</p>
            <p><b>备份列表：</b></p>
            <div class="card-box backup-box">
            ' . $lists . '
            </div>
            </div>
            <a href="' . add_query_arg('action', 'options_backup', $admin_ajax_url) . '" class="but jb-blue ajax-get">备份当前配置</a>
            ' . $delete_but . '
            <div class="ajax-notice" style="margin-top: 10px;"></div>
            </ajaxform>',
        );

        $csf[] = array(
            'type'    => 'submessage',
            'style'   => 'warning',
            'content' => '<h3 style="color:#fd4c73;"><i class="csf-tab-icon fa fa-fw fa-copy"></i> 导入&导出</h3>
            <ajaxform class="ajax-form" ajax-url="' . admin_url('admin-ajax.php') . '">
            <div style="margin:10px 0">
            <p>您可以在此处将主题配置导出为json文件，同时也可以使用json格式的配置内容进行配置导入，导入时请确保json格式正确</p>
            <textarea ajax-name="import_data" style="width: 100%;min-height: 200px;" placeholder="粘贴导出的json数据以进行导入"></textarea>
            </div>
            <input type="hidden" ajax-name="action" value="options_import">
            <a href="javascript:;" class="but jb-yellow ajax-submit"><i class="fa fa-paper-plane-o"></i> 导入配置</a>
            <a href="' . add_query_arg(array('action' => 'csf-export', 'unique' => $prefix, 'nonce' => wp_create_nonce('csf_backup_nonce')), $admin_ajax_url) . '" class="but jb-green" target="_blank">导出当前配置</a>
            <div class="ajax-notice" style="margin-top: 10px;"></div>
            </ajaxform>',
        );

        return $csf;
    }

    public static function update()
    {
        $csf           = array();
        $data          = ZibAut::is_update();
        $theme_data    = wp_get_theme();
        $theme_version = $theme_data['Version'];

        if ($data) {
            $notice = '<div class="ajax-form" ajax-url="' . esc_url(admin_url('admin-ajax.php')) . '">
                        <p style="color:#ff2f86"><i class="csf-tab-icon fa fa-cloud-upload fa-2x"></i></p>
                        <p><b>当前主题版本：V' . $theme_version . '，可更新到最新版本：<code style="color:#ff1919;background: #fbeeee; font-size: 16px; ">V' . $data['version'] . '</code></b></p>'
                . ($data['update_description'] ? '<p>' . $data['update_description'] . '</p>' : '') . '
                        <div>
                            <input type="hidden" ajax-name="action" value="admin_skip_update">
                            <div class="progress"><div class="progress-bar"></div></div>
                            <p class="ajax-notice"></p>
                            <a href="javascript:;" class="but jb-blue mr10 online-update"><i class="fa fa-cloud-download fa-fw"></i> 在线更新</a><a href="javascript:;" class="but c-yellow ajax-submit"><i class="fa fa-ban fa-fw"></i> 忽略此次更新</a>
                        </div>
                        <div style="text-align: right;font-size: 12px;opacity: .5;"><a style="color: inherit;" target="_blank" href="https://www.zibll.com/1411.html">遇到问题？点此查看官网教程</a></div>
                    </div>';

            $log = '<div class="box-theme">';
            $log .= $data['update_content'];
            $log .= '</div><div><a class="but c-blue" target="_blank" href="https://www.zibll.com/375.html">查看更多更新日志</a></div>';
            $csf[] = array(
                'type'    => 'notice',
                'style'   => 'info',
                'content' => $notice,
            );
            $csf[] = array(
                'title'   => '更新日志',
                'type'    => 'content',
                'content' => $log,
            );
        } else {
            $notice = '<div class="ajax-form" ajax-url="' . esc_url(admin_url('admin-ajax.php')) . '">
            <h3 class="c-red"><i class="fa fa-thumbs-o-up fa-fw" aria-hidden="true"></i> 当前主题已经是最新版啦</h3>
            <p><b>当前主题版本：V' . wp_get_theme()['Version'] . ' </b></p>
            <p class="ajax-notice"></p>
            <p><a href="javascript:;" class="but jb-blue ajax-submit">检测更新</a></p>
            <input type="hidden" ajax-name="action" value="admin_detect_update">
            </div>';

            $docs = '<div style="margin-left:14px;">';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/375.html">Zibll子比主题历史更新日志</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/3025.html">网站伪静态及固定链接设置教程-解决404错误问题</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/8663.html">卡密充值到余额功能教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/7327.html">用户发布付费内容参与创作分成教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/7342.html">余额充值、余额支付功能教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/7349.html">积分、签到功能教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/?s=人机验证">人机验证相关功能教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/3090.html">用户权限管理系统使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/8673.html">用户邀请码注册功能使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/zibll_word/%e7%a4%be%e5%8c%ba%e8%ae%ba%e5%9d%9b">社区论坛系列教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2997.html">API内容审核使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2983.html">手机底部TAB栏目配置教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2976.html">用户等级系统配置教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2956.html">用户身份认证功能使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1816.html">网站布局设置、模块配置教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2290.html">第三方帐号登录：代理登录教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2234.html">主题强大漂亮的代码高亮功能教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1802.html">添加广告位教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1717.html">文章目录树使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/?s=短信">短信验证码功能相关教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/?s=视频">视频功能、视频剧集功能教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1071.html">V5.0推广返佣、推荐奖励使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1246.html">V5.0新版幻灯片使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1244.html">消息系统-站内通知-用户私信功能详解</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1012.html">导航菜单添加自定义徽章及多种样式菜单教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1222.html">正确使用自定义代码示例及教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/8681.html">微信公众号模板消息推送教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2916.html">微信公众号配置自定义菜单教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/7297.html">微信分享有图教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2206.html">主题接入微信登录图文教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/1001.html">主题接入Github登录图文教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/979.html">主题接入QQ登录图文教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/958.html">文章列表显示模式设置教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/951.html">友情链接页面创建配置教程></a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/886.html">海报分享功能详细教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/2195.html">古腾堡编辑器-在文章中插入TAB栏目教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/860.html">古腾堡编辑器-在文章中插入其他文章卡片教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/853.html">古腾堡编辑器-隐藏内容模块使用教程></a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/767.html">主题VIP会员系统详细使用教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/720.html">邮件SMTP发送邮件教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/689.html">编辑器增强-古腾堡编辑器块入门详解</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/683.html">强大的图片灯箱功能详解</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/675.html">使用古腾堡块在文章中插入幻灯片教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/580.html">主题付费阅读、付费资源功能详解</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/529.html">主题导航菜单设置教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/519.html">主题常用功能设置教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/498.html">主题前端显示配置教程</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/46.html">主题详细安装教程/更新教程/首次配置指南</a></li>';
            $docs .= '<li><a target="_blank" href="https://www.zibll.com/zibll_word">更多主题文档及教程</a></li>';
            $docs .= '</div>';
            $csf[] = array(
                'type'    => 'notice',
                'style'   => 'info',
                'content' => $notice,
            );

            $db_update = get_option('zibll_new_version');
            if (!empty($db_update['update_content'])) {
                $csf[] = array(
                    'type' => 'tabbed',
                    'id'   => 'theme_text',
                    'tabs' => array(
                        array(
                            'title'  => '主题文档',
                            'icon'   => 'fa fa-file-text-o fa-fw',
                            'fields' => array(
                                array(
                                    'title'   => '主题文档',
                                    'type'    => 'content',
                                    'style'   => 'success',
                                    'content' => $docs,
                                ),
                            ),
                        ),
                        array(
                            'title'  => '更新日志',
                            'icon'   => 'fa fa-cloud-upload fa-fw',
                            'fields' => array(
                                array(
                                    'title'   => '更新日志',
                                    'type'    => 'content',
                                    'style'   => 'success',
                                    'content' => ($db_update['update_description'] ? '<p>' . $db_update['update_description'] . '</p>' : '') . $db_update['update_content'] . '<p><a class="but c-blue" target="_blank" href="https://www.zibll.com/375.html">查看更多更新日志</a></p>',
                                ),
                            ),
                        ),
                    ),
                );
            } else {
                $csf[] = array(
                    'title'   => '主题文档',
                    'type'    => 'content',
                    'style'   => 'success',
                    'content' => $docs,
                );
            }
        }

        $csf[] = array(
            'title'   => '系统环境',
            'type'    => 'content',
            'content' => '<div style="margin-left:14px;"><li><strong>操作系统</strong>： ' . PHP_OS . ' </li>
            <li><strong>运行环境</strong>： ' . $_SERVER["SERVER_SOFTWARE"] . ' </li>
            <li><strong>PHP版本</strong>： ' . PHP_VERSION . ' </li>
            <li><strong>WordPress版本</strong>： ' . get_bloginfo('version') . '</li>
            <li><strong>系统信息</strong>： ' . php_uname() . ' </li>
            <li><strong>服务器时间</strong>： ' . current_time('mysql') . '</li></div>
            <a class="but c-yellow" href="' . admin_url('site-health.php?tab=debug') . '">查看更多系统信息</a>',
        );
        $csf[] = array(
            'title'   => '推荐环境',
            'type'    => 'content',
            'content' => '<div style="margin-left:14px;"><li><strong>WordPress</strong>：5.0+，推荐使用最新版</li>
            <li><strong>PHP</strong>：PHP5.6及以上，推荐使用7.0以上</li>
            <li><strong>服务器配置</strong>：无要求，最低配都行</li>
            <li><strong>操作系统</strong>：无要求，不推荐使用Windows系统</li></div>',
        );
        return $csf;
    }
}
