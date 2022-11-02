<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2021-11-09 13:59:52
 * @LastEditTime: 2022-05-30 13:19:57
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|论坛系统|AJAX执行类函数
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//edit选择版块的tab
function zib_bbs_ajax_plate_select_lists_tab()
{
    zib_ajax_send_ajaxpager(zib_bbs_edit::plate_select_lists_tab());
}
add_action('wp_ajax_plate_select_lists_tab', 'zib_bbs_ajax_plate_select_lists_tab');
add_action('wp_ajax_nopriv_plate_select_lists_tab', 'zib_bbs_ajax_plate_select_lists_tab');

//执行删除版块
function zib_bbs_ajax_plate_or_posts_delete()
{
    $plate_id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;

    $plate = get_post($plate_id);

    if (!$plate_id || !$plate) {
        echo json_encode((array('error' => 1, 'ys' => 'danger', 'msg' => '参数传入错误')));
        exit;
    }

    //执行安全验证检查，验证不通过自动结束并返回提醒
    zib_ajax_verify_nonce('save_bbs');

    global $zib_bbs;

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;

    if ('plate_delete' == $action) {
        $goto = zib_bbs_get_home_url();

        $name = $zib_bbs->plate_name;
        if (!zib_bbs_current_user_can('plate_delete', $plate_id)) {
            echo json_encode((array('error' => 1, 'ys' => 'danger', 'msg' => '您没有删除此' . $name . '的权限')));
            exit;
        }

        $posts_action = isset($_REQUEST['posts_action']) ? $_REQUEST['posts_action'] : 'trash';
        if ('move' == $posts_action) {
            $new_plate_id = isset($_REQUEST['plate']) ? (int) $_REQUEST['plate'] : 0;
            if (!$new_plate_id || $new_plate_id == $plate_id) {
                zib_send_json_error('请选择需要移动到的新' . $name . '');
            }

            $new_obj = get_post($new_plate_id);
            if (empty($new_obj->ID)) {
                zib_send_json_error('所选新' . $name . '不存在或已删除');
            }
            if ('trash' === $new_obj->post_status) {
                zib_send_json_error('所选新' . $name . '已删除，请重新选择');
            }

            //执行批量移动版块
            zib_bbs_plates_move($new_plate_id, $plate_id);
        }
    } else {
        $goto = get_permalink(zib_bbs_get_plate_id($plate_id));

        $name = $zib_bbs->posts_name;
        if (!zib_bbs_current_user_can('posts_delete', $plate_id)) {
            echo json_encode((array('error' => 1, 'ys' => 'danger', 'msg' => '您没有删除此' . $name . '的权限')));
            exit;
        }
    }

    //执行删除
    wp_trash_post($plate_id);

    echo (json_encode(array('error' => 0, 'ys' => '', 'reload' => true, 'goto' => $goto, 'msg' => '该' . $name . '已移至回收站')));
    exit;
}
add_action('wp_ajax_plate_delete', 'zib_bbs_ajax_plate_or_posts_delete');
add_action('wp_ajax_posts_delete', 'zib_bbs_ajax_plate_or_posts_delete');

//编辑版块的弹窗
function zib_bbs_ajax_plate_edit_modal()
{
    $plate_id = isset($_REQUEST['plate_id']) ? (int) $_REQUEST['plate_id'] : 0;
    $cat_id   = isset($_REQUEST['cat_id']) ? (int) $_REQUEST['cat_id'] : 0;

    echo zib_bbs_edit::plate($plate_id, $cat_id);
    exit;
}
add_action('wp_ajax_plate_edit_modal', 'zib_bbs_ajax_plate_edit_modal');

//获取设置发布限制的模态框
function zib_bbs_ajax_set_add_limit_modal()
{
    $id   = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
    $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'plate';

    //权限检查
    if (!zib_bbs_current_user_can($type . '_set_add_limit', $id)) {
        zib_ajax_notice_modal('danger', '您没有此操作权限');
    }

    //发帖权限设置
    $add_limit_html = zib_bbs_edit::add_limit_modal($type, $id);

    if (!$add_limit_html) {
        zib_ajax_notice_modal('warning', '没有可配置的选项');
    }

    echo $add_limit_html;
    exit;
}
add_action('wp_ajax_set_add_limit_modal', 'zib_bbs_ajax_set_add_limit_modal');

//保存发布权限
function zib_bbs_ajax_save_add_limit()
{
    $id        = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
    $type      = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'plate';
    $add_limit = isset($_REQUEST['add_limit']) ? (string) $_REQUEST['add_limit'] : '0';

    //执行安全验证检查，验证不通过自动结束并返回提醒
    zib_ajax_verify_nonce();

    //权限检查
    if (!zib_bbs_current_user_can($type . '_set_add_limit', $id)) {
        zib_send_json_error('您没有此操作权限');
    }

    //执行保存设置
    if ('plate_cat' === $type) {
        update_term_meta($id, 'add_limit', $add_limit);
    } else {
        update_post_meta($id, 'add_limit', $add_limit);
    }

    zib_send_json_success(array('msg' => '设置成功', 'hide_modal' => true));
    exit;
}
add_action('wp_ajax_save_add_limit', 'zib_bbs_ajax_save_add_limit');

//编辑或者添加版块
function zib_bbs_ajax_save_plate()
{
    global $zib_bbs;
    $name = $zib_bbs->plate_name;

    //执行安全验证检查，验证不通过自动结束并返回提醒
    zib_ajax_verify_nonce('save_bbs');

    //用户基本权限验证
    $plate_id = isset($_REQUEST['plate_id']) ? (int) $_REQUEST['plate_id'] : 0;
    if ($plate_id && !zib_bbs_current_user_can('plate_edit', $plate_id)) {
        echo json_encode((array('error' => 1, 'ys' => 'danger', 'msg' => '您没有编辑此' . $name . '的权限')));
        exit;
    }

    if (!$plate_id && !zib_bbs_current_user_can('plate_add')) {
        echo json_encode((array('error' => 1, 'ys' => 'danger', 'msg' => '您没有创建' . $name . '的权限')));
        exit;
    }

    $cat = !empty($_POST['cat']) ? (int) $_POST['cat'] : false;
    if (!$cat) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '您未选择' . $name . '分类或暂无权限')));
        exit();
    }

    if (!zib_bbs_current_user_can('select_plate_cat', $cat, $plate_id)) {
        zib_send_json_error('您没有在此分类创建' . $name . '的权限，请重新选择' . $name . '分类');
    }

    $title   = !empty($_POST['title']) ? strip_tags(trim($_POST['title'])) : false;
    $content = !empty($_POST['desc']) ? strip_tags(trim($_POST['desc'])) : false;

    //标题验证
    if (!$title) {
        echo (json_encode(array('error' => 1, 'ys' => 'warning', 'msg' => '请输入' . $name . '标题')));
        exit();
    }
    if (zib_new_strlen($title) > 10) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '标题太长，不能超过10个字')));
        exit();
    }
    if (zib_new_strlen($title) < 2) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '标题太短！')));
        exit();
    }

    //简介验证
    if (!$content) {
        echo (json_encode(array('error' => 1, 'ys' => 'warning', 'msg' => '请输入' . $name . '简介')));
        exit();
    }
    if (zib_new_strlen($content) > 60) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '简介太长，不能超过60个字')));
        exit();
    }
    if (zib_new_strlen($content) < 6) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '简介太短！')));
        exit();
    }

    //类型权限判断
    $type = !empty($_POST['type']) ? $_POST['type'] : '';
    if ($type && !isset(zib_bbs_get_user_can_plate_type($plate_id)[$type])) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '您暂无选择该' . $name . '类型的权限')));
        exit();
    }

    //图像判断:如果没有ID则必须要有图片
    if (empty($_FILES['file']) && !$plate_id) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请选择' . $name . '图像')));
        exit();
    }

    //图片api审核
    if (!empty($_FILES['file']) && _pz('audit_upload_img')) {
        ZibAudit::ajax_image('file');
    }

    //文字API审核
    if (_pz('audit_bbs_plate')) {
        $is_audit = ZibAudit::ajax_text($title . $content);
    }

    //版块状态，版块无需审核
    $post_status = 'publish';

    $post_args = array(
        'ID'           => $plate_id,
        'post_type'    => 'plate',
        'post_title'   => $title,
        'post_status'  => $post_status,
        'post_content' => '',
        'post_excerpt' => $content,
        'meta_input'   => array(
            'plate_type' => $type,
        ),
    );

    if (!$plate_id) {
        //新建时候，添加作者
        $cuid                     = get_current_user_id();
        $post_args['post_author'] = $cuid;
    } else {
        $post_obj = get_post($plate_id, ARRAY_A);
        if (isset($post_obj['ID'])) {
            $post_args = array_merge($post_obj, $post_args);
        }
    }

    $in_id = wp_insert_post($post_args, 1);
    if (is_wp_error($in_id)) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => $in_id->get_error_message())));
        exit();
    }

    //执行保存分类
    wp_set_post_terms($in_id, (array) $cat, 'plate_cat');

    //图片处理
    $image_url = '';
    if (!empty($_FILES['file']) && $in_id) {
        //开始上传图像
        $img_id = zib_php_upload('file', $in_id, false);
        if (!empty($img_id['error'])) {
            echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => $img_id['msg'])));
            exit();
        }
        $image_urls = wp_get_attachment_image_src($img_id, 'medium');
        $image_url  = isset($image_urls[0]) ? $image_urls[0] : '';
        update_post_meta($in_id, 'thumbnail_url', $image_url);
    }

    //执行保存发布限制
    if (isset($_POST['add_limit'])) {
        update_post_meta($in_id, 'add_limit', (string) $_POST['add_limit']);
    }

    //执行保存阅读权限
    zib_bbs_ajax_plate_edit_allow_view($in_id, 0);

    $text = $plate_id ? '修改' : '创建';
    $goto = get_permalink($in_id);

    $data = array(
        'image_url'  => $image_url,
        'url'        => $goto,
        'msg'        => $name . $text . '成功',
        'post'       => get_post($in_id),
        'id'         => $in_id,
        'type'       => ($plate_id ? 'update' : 'add'),
        'hide_modal' => true,
    );
    zib_send_json_success($data);
}
add_action('wp_ajax_save_plate', 'zib_bbs_ajax_save_plate');

//版块设置查看权限的模态框
function zib_bbs_ajax_plate_allow_view_set_modal()
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
    $id     = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
    global $zib_bbs;
    $name = $zib_bbs->plate_name;

    $post = get_post($id);
    if (empty($post->ID)) {
        zib_ajax_notice_modal('danger', '内容不存在或参数传入错误');
    }
    if (!zib_bbs_current_user_can('plate_set_allow_view', $id)) {
        zib_ajax_notice_modal('danger', '权限不足');
    }
    $allow_view_set = zib_bbs_edit::plate_allow_view_set_content($id);
    $header         = zib_get_modal_colorful_header('jb-yellow', '<i class="fa fa-eye-slash"></i>', $name . '查看限制');
    $hidden_html    = '';
    $hidden_html .= '<input type="hidden" name="action" value="plate_edit_allow_view">';
    $hidden_html .= '<input type="hidden" name="plate_id" value="' . $id . '">';

    $footer = '<div class="mt20 but-average">';
    $footer .= $hidden_html;
    $footer .= '<button class="but jb-yellow padding-lg wp-ajax-submit"><i class="fa fa-check" aria-hidden="true"></i>确认提交</button>';
    $footer .= '</div>';

    echo '<form class="dependency-box">';
    echo $header;
    echo '<div class="mini-scrollbar scroll-y max-vh5"><div class="em09 muted-2-color mb20">为' . $name . '设置阅读限制，只有满足条件的用户才能查看此版块的' . $zib_bbs->posts_name . '内容</div>' . $allow_view_set . '</div>';
    echo $footer;
    echo '</form>';
    exit;
}
add_action('wp_ajax_plate_allow_view_set_modal', 'zib_bbs_ajax_plate_allow_view_set_modal');

//保存阅读权限
function zib_bbs_ajax_plate_edit_allow_view($plate_id = 0, $echo_success = true)
{
    global $zib_bbs;
    $name = $zib_bbs->plate_name;

    if (!isset($_REQUEST['allow_view']) && !isset($_REQUEST['allow_view_roles'])) {
        return;
    }

    $plate_id = $plate_id ? $plate_id : (!empty($_REQUEST['plate_id']) ? $_REQUEST['plate_id'] : 0);

    $allow_view       = !empty($_REQUEST['allow_view']) ? $_REQUEST['allow_view'] : '';
    $allow_view_roles = !empty($_REQUEST['allow_view_roles']) ? (array) $_REQUEST['allow_view_roles'] : array();
    $allow_view_roles = array_filter($allow_view_roles);

    if ('roles' == $allow_view) {
        if (!$allow_view_roles) {
            zib_send_json_error('请至少选择一项允许查看的用户类型');
        }
    }

    update_post_meta($plate_id, 'allow_view', $allow_view);
    update_post_meta($plate_id, 'allow_view_roles', $allow_view_roles);
    if ($echo_success) {
        zib_send_json_success(array('msg' => $name . '查看权限已保存', 'reload' => true, 'allow_view_roles' => $allow_view_roles, 'allow_view' => $allow_view, 'plate_id' => $plate_id));
    }
}
add_action('wp_ajax_plate_edit_allow_view', 'zib_bbs_ajax_plate_edit_allow_view');
