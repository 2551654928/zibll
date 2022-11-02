<?php
/*
 * @Author: Qinver
 * @Url: zibll.com
 * @Date: 2020-11-29 21:35:52
 * @LastEditTime: 2022-01-10 14:44:07
 */

class Zib_CFSwidget
{
    public static function create($id, $args = array())
    {
        $args = self::args($args);
        CSF::createWidget($id, $args);
    }
    public static function is_size_limit($wp_args = array())
    {
        if (!empty($wp_args['size']) && !empty($wp_args['id']) && is_super_admin()) {
            if ($wp_args['size'] == 'mini' && (!strstr($wp_args['id'], 'sidebar') && !strstr($wp_args['id'], 'nav'))) {
                return true;
            } elseif ($wp_args['size'] == 'big' && (strstr($wp_args['id'], 'sidebar') || strstr($wp_args['id'], 'nav'))) {
                return true;
            }
        }
        return false;
    }
    public static function echo_before($args = array(), $class = 'mb20', $wp_args = array())
    {
        $show_class = self::show_class($args);
        if ($show_class && $show_class !== true) {
            $class .= ' ' . $show_class;
        }
        $affix = !empty($args['sidebar_affix']) ? ' data-affix="true"' : '';

        $title = self::show_title($args);
        do_action('zib_cfswidget_echo_before', $args, $class);
        $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';

        $size_limit = '<div class="badg btn-block c-red padding-lg"><i class="fa fa-fw fa-info-circle mr10"></i>此模块不推荐放置在此位置！</div>';
        if (self::is_size_limit($wp_args)) {
            echo '<div style="padding:6px;background:rgba(255, 0, 143, 0.1);">' . $size_limit;
        }
        echo '<div' . $affix . $class_attr . '>';
        echo $title;
    }

    public static function echo_after($args = array(), $wp_args = array())
    {
        echo '</div>';
        if (self::is_size_limit($wp_args)) {
            echo '</div>';
        }
        do_action('zib_cfswidget_echo_after', $args);
    }

    public static function show_title($args = array())
    {
        if (empty($args['title'])) {
            return;
        }

        $title    = $args['title'];
        $subtitle = !empty($args['subtitle']) ? $args['subtitle'] : '';
        $subtitle = $subtitle ? '<small class="ml10">' . $subtitle . '</small>' : '';

        $link_url   = !empty($args['title_link']['url']) ? $args['title_link']['url'] : '';
        $link_text  = !empty($args['title_link']['text']) ? $args['title_link']['text'] : '<i class="fa fa-angle-right fa-fw"></i>更多';
        $link_blank = !empty($args['title_link']['target']) && $args['title_link']['target'] == '_blank' ? ' target="_blank"' : '';

        $more_but = $link_url ? '<div class="pull-right em09 mt3"><a' . $link_blank . ' href="' . $link_url . '" class="muted-2-color">' . $link_text . '</a></div>' : '';
        return '<div class="box-body notop"><div class="title-theme">' . $title . $subtitle . $more_but . '</div></div>';
    }
    public static function show_class($show_type = '')
    {
        if (is_array($show_type) && isset($show_type['show_type'])) {
            $show_type = $show_type['show_type'];
        }
        if ($show_type == 'only_pc' && wp_is_mobile()) {
            return '';
        }

        if ($show_type == 'only_sm' && !wp_is_mobile()) {
            return '';
        }

        if ($show_type == 'only_pc') {
            return 'hidden-xs';
        }

        if ($show_type == 'only_sm') {
            return 'visible-xs-block';
        }

        return true;
    }
    public static function args($args = array())
    {
        $args['title'] = 'Zibll ' . $args['title'];
        $more_args     = array();
        if (!empty($args['zib_title'])) {
            unset($args['zib_title']);
            $more_args[] = array(
                'title'      => '模块标题',
                'id'         => 'title',
                'default'    => '',
                'attributes' => array(
                    'rows' => 1,
                ),
                'type'       => 'textarea',
            );
            $more_args[] = array(
                'dependency' => array('title', '!=', ''),
                'title'      => ' ',
                'subtitle'   => '副标题',
                'class'      => 'compact',
                'id'         => 'subtitle',
                'default'    => '',
                'attributes' => array(
                    'rows' => 1,
                ),
                'type'       => 'textarea',
            );
            $more_args[] = array(
                'dependency'   => array('title', '!=', ''),
                'title'        => '标题右侧链接',
                'class'        => 'compact',
                'id'           => 'title_link',
                'desc'         => '标题、副标题均支持HTML代码，请注意代码规范！',
                'type'         => 'link',
                'add_title'    => '添加链接',
                'edit_title'   => '编辑链接',
                'remove_title' => '删除链接',
            );
        }
        if (!empty($args['zib_affix'])) {
            unset($args['zib_affix']);
            $more_args[] = array(
                'title'    => '侧栏随动',
                'subtitle' => '',
                'id'       => 'sidebar_affix',
                'label'    => '仅在侧边栏有效',
                'type'     => 'switcher',
                'default'  => false,
            );
        }
        if (!empty($args['zib_show'])) {
            unset($args['zib_show']);
            $more_args[] = array(
                'title'   => __("显示规则", 'zib_language'),
                'id'      => 'show_type',
                'type'    => 'radio',
                'default' => 'all',
                'options' => array(
                    'all'     => 'PC端/移动端均显示',
                    'only_pc' => '仅在PC端显示',
                    'only_sm' => '仅在移动端显示',
                ),
            );
        }
        $args['fields'] = array_merge($more_args, $args['fields']);
        return $args;
    }
}
