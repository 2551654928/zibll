<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:36
 * @LastEditTime: 2022-07-01 11:46:47
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//给文章分类添加封面图像
$dir = get_bloginfo('template_directory');
if (!defined('Z_PLUGIN_URL')) {
    define('Z_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));
}

add_action('admin_head', 'zib_admin_add_term_img_init');
function zib_admin_add_term_img_init()
{
    $z_taxonomies = get_taxonomies();
    if (is_array($z_taxonomies)) {
        foreach ($z_taxonomies as $z_taxonomy) {
            if ('link_category' == $z_taxonomy) {
                continue;
            }

            if ('plate_cat' == $z_taxonomy) {
                continue;
            }

            add_action($z_taxonomy . '_add_form_fields', 'zib_admin_add_term_img_form_field');
            add_action($z_taxonomy . '_edit_form_fields', 'zib_admin_add_term_img_form_field_edit');
            add_filter('manage_edit-' . $z_taxonomy . '_columns', 'zib_admin_add_term_img_edit_columns');
            add_filter('manage_' . $z_taxonomy . '_custom_column', 'zib_admin_add_term_img_custom_column', 10, 3);
        }
    }
}

function zib_admin_add_term_img_add_style()
{
    echo '<style type="text/css" media="screen">
		th.column-thumb {width:60px;}
		.form-field img.taxonomy-image,.taxonomy-image{width:95%;max-width:500px;max-height:300px;}
		.inline-edit-row fieldset .thumb label span.title {display:inline-block;}
		.column-thumb span {display:inline-block;}
		.inline-edit-row fieldset .thumb img,.column-thumb img {width:55px;height:28px;}
	</style>';
}

// 添加分类时候的添加图像
function zib_admin_add_term_img_form_field()
{
    if (get_bloginfo('version') >= 3.5) {
        wp_enqueue_media();
    } else {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
    }
    echo '<div class="form-field">
		<label for="taxonomy_image">' . __('封面图像', 'zci') . '</label>
		<input type="text" name="taxonomy_image" id="taxonomy_image" value="" />
        <br/>
        <p>设置封面图，建议尺寸为1000x400,如果分类页未开启侧边栏，请选择更大的尺寸，需要在主题设置-分类、标签页：开启分类、标签封面显示功能</p>
		<button class="z_upload_image_button button">' . __('上传/添加图像', 'zci') . '</button>
	</div>' . z_edit_texonomy_script();
}

// 编辑分类时候的添加图像
define('Z_IMAGE_PLACEHOLDER', ZIB_TEMPLATE_DIRECTORY_URI . '/img/thumbnail-lg.svg');
function zib_admin_add_term_img_form_field_edit($taxonomy)
{
    if (get_bloginfo('version') >= 3.5) {
        wp_enqueue_media();
    } else {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
    }
    $image_text = zib_get_taxonomy_img_url($taxonomy->term_id, null);
    echo '<tr class="form-field">
		<th scope="row" valign="top"><label for="taxonomy_image">' . __('图像', 'zci') . '</label></th>
		<td><img class="taxonomy-image" src="' . $image_text . '"/><br/><input type="text" name="taxonomy_image" id="taxonomy_image" value="' . $image_text . '" /><br />
        <p>设置封面图，建议尺寸为1000x400,如果分类页未开启侧边栏，请选择更大的尺寸，需要在主题设置-分类、标签页：开启分类、标签封面显示功能</p>
        <button class="z_upload_image_button button">' . __('上传/添加图像', 'zci') . '</button>
		<button class="z_remove_image_button button">' . __('删除图像', 'zci') . '</button>
		</td>
	</tr>' . z_edit_texonomy_script();
}

// 上传按钮的js函数
function z_edit_texonomy_script()
{
    return '<script type="text/javascript">
    (function ($, window, document) {
	    jQuery(document).ready(function($) {
			var wordpress_ver = "' . get_bloginfo("version") . '", upload_button;
			$(".z_upload_image_button").click(function(event) {
				upload_button = $(this);
				var frame;
				if (wordpress_ver >= "3.5") {
					event.preventDefault();
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media();
					frame.on( "select", function() {
						// Grab the selected attachment.
						var attachment = frame.state().get("selection").first();
						frame.close();
						if (upload_button.parent().prev().children().hasClass("tax_list")) {
							upload_button.parent().prev().children().val(attachment.attributes.url);
							upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
						}
						else
                            $("#taxonomy_image").val(attachment.attributes.url);
                            upload_button.parent().find(".taxonomy-image").attr("src", attachment.attributes.url);
					});
					frame.open();
				}
				else {
					tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
					return false;
				}
			});

			$(".z_remove_image_button").click(function() {
				$("#taxonomy_image").val("");
                $(this).parent().siblings(".title").children("img").attr("src","' . Z_IMAGE_PLACEHOLDER . '");
                $(this).parent().find(".taxonomy-image").attr("src", "' . Z_IMAGE_PLACEHOLDER . '");

				$(".inline-edit-col :input[name=\'taxonomy_image\']").val("");
				return false;
			});

			if (wordpress_ver < "3.5") {
				window.send_to_editor = function(html) {
					imgurl = $("img",html).attr("src");
					if (upload_button.parent().prev().children().hasClass("tax_list")) {
						upload_button.parent().prev().children().val(imgurl);
						upload_button.parent().prev().prev().children().attr("src", imgurl);
					}
					else
						$("#taxonomy_image").val(imgurl);
					tb_remove();
				}
			}

			$(".editinline").on("click", function(){
			    var tax_id = $(this).parents("tr").attr("id").substr(4);
			    var thumb = $("#tag-"+tax_id+" .thumb img").attr("src");
				if (thumb != "' . Z_IMAGE_PLACEHOLDER . '") {
					$(".inline-edit-col :input[name=\'taxonomy_image\']").val(thumb);
				} else {
					$(".inline-edit-col :input[name=\'taxonomy_image\']").val("");
				}
				$(".inline-edit-col .title img").attr("src",thumb);
			});
	    });
    })(jQuery, window, document);
	</script>';
}

// 保存函数
add_action('edit_term', 'zib_admin_save_term_img');
add_action('create_term', 'zib_admin_save_term_img');
function zib_admin_save_term_img($term_id)
{
    if (isset($_POST['taxonomy_image'])) {
        update_option('_taxonomy_image_' . $term_id, $_POST['taxonomy_image']);
        wp_cache_delete($term_id, 'taxonomy_image_');
        wp_cache_delete($term_id, 'taxonomy_image_full');
        wp_cache_delete($term_id, 'taxonomy_image_thumbnail');
        wp_cache_delete($term_id, 'taxonomy_image_medium');
        wp_cache_delete($term_id, 'taxonomy_image_large');
    }
}

function zib_admin_add_term_img_quick_edit_custom_box($column_name, $screen, $name)
{
    if ('thumb' == $column_name) {
        echo '<fieldset>
		<div class="thumb inline-edit-col">
			<label>
				<span class="title"><img src="" alt="Thumbnail"/></span>
				<span class="input-text-wrap"><input type="text" name="taxonomy_image" value="" class="tax_list" /></span>
                <span class="input-text-wrap">
                <p>设置封面图，建议尺寸为1000x400,如果分类页未开启侧边栏，请选择更大的尺寸，需要在主题设置-分类、标签页：开启分类、标签封面显示功能</p>
					<button class="z_upload_image_button button">' . __('上传/添加图像', 'zci') . '</button>
					<button class="z_remove_image_button button">' . __('删除图像', 'zci') . '</button>
				</span>
			</label>
		</div>
	</fieldset>';
    }

}

function zib_admin_add_term_img_edit_columns($columns)
{
    $new_columns          = array();
    $new_columns['thumb'] = __('图像', 'zci');
    return array_merge($new_columns, $columns);
}

function zib_admin_add_term_img_custom_column($columns, $column, $id)
{
    if ('thumb' == $column) {
        $columns = '<span><img src="' . zib_get_taxonomy_img_url($id, null, Z_IMAGE_PLACEHOLDER) . '" alt="' . __('Thumbnail', 'zci') . '" class="wp-post-image" /></span>';
    }

    return $columns;
}

// change 'insert into post' to 'use this image'
function zib_admin_add_term_img_change_insert_button_text($safe_text, $text)
{
    return str_replace("Insert into Post", "Use this image", $text);
}

// style the image in category list
add_action('admin_head', 'zib_admin_add_term_img_add_style');

if (strpos($_SERVER['SCRIPT_NAME'], 'edit-tags.php')) {
    add_action('quick_edit_custom_box', 'zib_admin_add_term_img_quick_edit_custom_box', 10, 3);
    add_filter("attribute_escape", "zib_admin_add_term_img_change_insert_button_text", 10, 2);
}

// editor style
add_editor_style(get_locale_stylesheet_uri() . '/css/editor-style.min.css', array(), THEME_VERSION, 'all');

// 后台Ctrl+Enter提交评论回复
add_action('admin_footer', '_admin_comment_ctrlenter');
function _admin_comment_ctrlenter()
{
    echo '<script type="text/javascript">
        jQuery(document).ready(function($){
            $("textarea").keypress(function(e){
                if(e.ctrlKey&&e.which==13||e.which==10){
                    $("#replybtn").click();
                }
            });
        });
    </script>';
};

//后台评论数据优化显示
function zib_admin_comment_table_text_filter($comment_text, $comment, $args)
{
    $comment_text = get_comment_text($comment, $args);
    return '<div class="zib-comment-cont">' . zib_comment_filters($comment_text, '', false) . '</div>';
}
add_action('manage_comments_nav', function () {
    add_filter('comment_text', 'zib_admin_comment_table_text_filter', 10, 3);
    echo '<style type="text/css">
    .zib-comment-cont .smilie-icon {width: 22px;vertical-align: -6px;}
    .zib-comment-cont pre {
        background: rgba(0, 0, 0, .04);
        border-radius: 6px;
        font-size: 11px;
        line-height: 1.4;
        padding: 5px 10px;
        max-height: 180px;
        overflow-y: auto;
      }
    .zib-comment-cont pre code {
        background: 0 0;
        padding: 0;
        margin: 0;
        font-size: 11px;
        line-height: 1.4;
      }
    .zib-comment-cont img{
        max-height: 80px;
      }
</style>
    ';
});

// 禁用WP Editor Google字体css
function zib_remove_gutenberg_styles($translation, $text, $context, $domain)
{
    if ('Google Font Name and Variants' != $context || 'Noto Serif:400,400i,700,700i' != $text) {
        return $translation;
    }
    return 'off';
}
add_filter('gettext_with_context', 'zib_remove_gutenberg_styles', 10, 4);
// 古腾堡编辑器扩展
function zibll_block()
{
    wp_register_script(
        'zibll_block',
        ZIB_TEMPLATE_DIRECTORY_URI . '/js/gutenberg-extend.min.js',
        array('wp-blocks', 'wp-element', 'wp-rich-text'), THEME_VERSION
    );

    wp_register_style(
        'zibll_block',
        ZIB_TEMPLATE_DIRECTORY_URI . '/css/editor-style.min.css',
        array('wp-edit-blocks'), THEME_VERSION
    );

    wp_register_style(
        'font_awesome',
        ZIB_TEMPLATE_DIRECTORY_URI . '/css/font-awesome.min.css',
        array('zibll_block'), THEME_VERSION
    );

    register_block_type('zibll/block', array(
        'editor_script' => 'zibll_block',
        'editor_style'  => 'zibll_block',
    ));
}

if (function_exists('register_block_type')) {
    add_action('init', 'zibll_block');
    $wp_version = get_bloginfo('version', 'display');

    if (version_compare('5.7.9', $wp_version) == -1) {
        add_filter('block_categories_all', function ($categories, $post) {
            return array_merge(
                array(
                    array(
                        'slug'  => 'zibll_block_cat',
                        'title' => __('Zibll主题模块', 'zibll-blocks'),
                    ),
                ),
                $categories
            );
        }, 10, 2);
    } else {
        add_filter('block_categories', function ($categories, $post) {
            return array_merge(
                array(
                    array(
                        'slug'  => 'zibll_block_cat',
                        'title' => __('Zibll主题模块', 'zibll-blocks'),
                    ),
                ),
                $categories
            );
        }, 10, 2);
    }
}

//分类及专题设置SEO
class zib_admin_add_term_seo
{

    public function __construct()
    {
        add_action('category_add_form_fields', array($this, 'add_tax_field'));
        add_action('category_edit_form_fields', array($this, 'edit_tax_field'));
        add_action('topics_add_form_fields', array($this, 'add_tax_field'));
        add_action('topics_edit_form_fields', array($this, 'edit_tax_field'));

        add_action('edit_term', array($this, 'save_tax_meta'), 10, 2);
        add_action('create_term', array($this, 'save_tax_meta'), 10, 2);
    }

    public function add_tax_field()
    {
        echo '
        <div class="form-field">
            <label for="term_meta[title]">SEO 标题</label>
            <input type="text" name="term_meta[title]" id="term_meta[title]" />
        </div>
        <div class="form-field">
            <label for="term_meta[keywords]">SEO 关键字keywords）（用英文逗号分开）</label>
            <input type="text" name="term_meta[keywords]" id="term_meta[keywords]" />
        </div>
        <div class="form-field">
            <label for="term_meta[keywords]">SEO 描述（description）</label>
            <textarea name="term_meta[description]" id="term_meta[description]" rows="4" cols="40"></textarea>
            <p>主题默认会自动设置SEO内容，推荐自定义内容。相关建议请参考文章SEO设置</p>
            </div>
        ';
    }

    public function edit_tax_field($term)
    {

        $term_id   = $term->term_id;
        $term_meta = get_option("_taxonomy_meta_$term_id");

        $meta_title       = isset($term_meta['title']) ? $term_meta['title'] : '';
        $meta_keywords    = isset($term_meta['keywords']) ? $term_meta['keywords'] : '';
        $meta_description = isset($term_meta['description']) ? $term_meta['description'] : '';

        echo '
      <tr class="form-field">
        <th scope="row">
            <label for="term_meta[title]">SEO 标题</label>
            <td>
                <input type="text" name="term_meta[title]" id="term_meta[title]" value="' . $meta_title . '" />
            </td>
        </th>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="term_meta[keywords]">SEO 关键字（keywords）</label>
            <td>
                <input type="text" name="term_meta[keywords]" id="term_meta[keywords]" value="' . $meta_keywords . '" />
            </td>
        </th>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="term_meta[description]">SEO 描述（description）</label>
            <td>
                <textarea name="term_meta[description]" id="term_meta[description]" rows="4">' . $meta_description . '</textarea>
                <p>主题默认会自动设置SEO内容，推荐自定义内容。相关建议请参考文章SEO设置</p>
            </td>
        </th>
    </tr>
    ';
    }

    public function save_tax_meta($term_id)
    {

        if (isset($_POST['term_meta'])) {

            $term_meta = array();

            $term_meta['title']       = isset($_POST['term_meta']['title']) ? esc_sql($_POST['term_meta']['title']) : '';
            $term_meta['keywords']    = isset($_POST['term_meta']['keywords']) ? esc_sql($_POST['term_meta']['keywords']) : '';
            $term_meta['description'] = isset($_POST['term_meta']['description']) ? esc_sql($_POST['term_meta']['description']) : '';

            update_option("_taxonomy_meta_$term_id", $term_meta);
        }
    }
}
if (_pz('post_keywords_description_s')) {
    $tax_cat = new zib_admin_add_term_seo();
}

add_filter('admin_footer_text', 'zib_admin_footer_thank', 99999);
function zib_admin_footer_thank()
{
    return '感谢您使用<a href="https://wordpress.org">WordPress</a>和<a href="https://zibll.com">子比主题</a>进行创作。';
}
