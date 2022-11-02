<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-10-31 20:07:39
 * @LastEditTime: 2022-09-30 15:33:06
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//定义全局变量
global $wpdb;
$wpdb->zib_message = $wpdb->prefix . 'zib_message';

class ZibMsg
{
    /**
     * @description: 创建数据库
     * @param {*}
     * @return {*}
     */
    public static function create_db()
    {
        global $wpdb;
        /**判断没有则创建 */
        if ($wpdb->get_var("show tables like '{$wpdb->zib_message}'") != $wpdb->zib_message) {

            $wpdb->query("CREATE TABLE $wpdb->zib_message (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `send_user` varchar(2550) DEFAULT NULL COMMENT '发件人',
                    `receive_user` longtext DEFAULT NULL COMMENT '收件人',
                    `readed_user` longtext DEFAULT NULL COMMENT '已读用户',
                    `type` varchar(50) DEFAULT NULL COMMENT '消息类型',
                    `title` longtext DEFAULT NULL COMMENT '标题',
                    `content` longtext DEFAULT NULL COMMENT '内容',
                    `create_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
                    `modified_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
                    `parent` int(11) DEFAULT NULL COMMENT '父级',
                    `status` int(11) DEFAULT 0 COMMENT '消息状态',
                    `meta` longtext DEFAULT NULL COMMENT '元数据',
                    `other` longtext DEFAULT NULL COMMENT '其它',
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COMMENT='站内消息';");
        }
    }

    /**
     * @description: 新增消息
     * @param arrar $values 数组
     * @return {*}
     */
    public static function add($values)
    {
        return self::update($values);
    }

    /**
     * @description: 更新消息
     * @param arrar $values 数组
     * @return {*}
     */
    public static function update($values)
    {
        global $wpdb;
        $defaults = array(
            'send_user'     => '',
            'receive_user'  => '',
            'type'          => '',
            'title'         => '',
            'content'       => '',
            'create_time'   => current_time('mysql'),
            'modified_time' => current_time('mysql'),
            'parent'        => '',
            'status'        => '',
            'meta'          => '',
            'other'         => '',
        );
        $values = wp_parse_args((array) $values, $defaults);
        if (!$values['send_user']) {
            $values['send_user'] = get_current_user_id();
        }

        //准备数据

        //根据需要压缩数据
        $values['meta']  = $values['meta'] ? maybe_serialize($values['meta']) : '';
        $values['other'] = $values['other'] ? maybe_serialize($values['other']) : '';

        $values = apply_filters('zib_add_message_values', $values);
        $values = wp_unslash($values);

        //判断更新还是新增
        if (!empty($values['id'])) {
            //更新数据库
            unset($values['create_time']); //清除创建时间
            $values = array_filter($values); //清除为空的数组键。
            $where  = array('id' => $values['id']);
            //执行更新
            if (false !== $wpdb->update($wpdb->zib_message, $values, $where)) {
                //挂钩添加
                do_action('zib_update_message', $values);
                return self::get_row(array('id' => $values['id']));
            }
            return false;
        }
        //如果不是更新，则新增数据库
        //执行新增
        if (false !== $wpdb->insert($wpdb->zib_message, $values)) {
            //挂钩添加
            do_action('zib_add_message', $values);
            return $values;
        }
        return false;
    }

    /**
     * @description: 消息内容格式化
     * @param {*}
     * @return {*}
     */
    public static function get_content($msg, $type = '')
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = (array) $msg;
            $con = isset($msg['content']) ? $msg['content'] : '';
        } else {
            $con = $msg;
        }

        if (!$con) {
            return '';
        }

        $con = convert_smilies($con);
        if ('mini' == $type) {
            $con = preg_replace('/(\[img=|\<img)(.*?)(\]|\>)/', '[图片]', $con);
            $con = preg_replace('/\[code]([\s\S]*)\[\/code]/', '[代码]', $con);
            $con = preg_replace('/\[g=(.*?)\]/', '[表情:$1]', $con);
        } else {
            $con = preg_replace('/\[img=(.*?)\]/', '<img class="alone-imgbox-img lazyload" src="$1">', $con);
            if (zib_is_lazy('lazy_private', true)) {
                $con = str_replace(' src=', ' src="' . zib_get_lazy_thumb() . '" data-src=', $con);
            }
            $con = preg_replace('/\[g=(.*?)\]/', '<img class="smilie-icon" src="' . ZIB_TEMPLATE_DIRECTORY_URI . '/img/smilies/$1.gif">', $con);
            $con = preg_replace('/\[code]([\s\S]*)\[\/code]/', '<pre><code>$1</code></pre>', $con);
        }
        return wp_kses_post(trim($con));
    }

    /**
     * @description: 格式化Array为SQL语言
     * @param array $where 例如：array('id' => '10');
     * @return int $count
     */
    public static function format_data($where)
    {
        $conditions = array();
        $values     = array();
        if (!is_array($where)) {
            return array(
                'conditions' => $where,
                'values'     => '',
            );
        }
        $format_int = array('parent', 'id', 'status');
        foreach ($where as $field => $value) {
            if (is_null($value)) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }
            $format = in_array($field, $format_int) ? '%d' : '%s';

            if ('no_readed_user' == $field || 'readed_user' == $field) {
                $value = '[' . $value . ']';
                if ('no_readed_user' == $field) {
                    $conditions[] = "(`readed_user` NOT LIKE '%$value%' OR `readed_user` is null)";
                } elseif ('readed_user' == $field) {
                    $conditions[] = "`readed_user` LIKE '%$value%'";
                }
                continue;
            }

            //数组判断-》转为SQL IN语句
            if (is_array($value)) {
                $arrar_field = array();
                foreach ($value as $arrar_f) {
                    $arrar_field[] = $format;
                    $values[]      = $arrar_f;
                }
                $arrar_field  = implode(',', $arrar_field);
                $conditions[] = "`$field` IN ($arrar_field)";
            } elseif (stristr($value, '|')) {
                $arrar_field  = explode('|', $value);
                $conditions[] = "`$field` $arrar_field[0] $format";
                $values[]     = $arrar_field[1];
            } else {
                $conditions[] = "`$field` = $format";
                $values[]     = $value;
            }
        }

        $conditions = implode(' AND ', $conditions);

        return array(
            'conditions' => $conditions,
            'values'     => $values,
        );
    }

    /**
     * @description: 根据Array数据获取计数
     * @param array $where 例如：array('id' => '10');
     * @return int $count
     */
    public static function get_count($where)
    {
        if (!is_array($where)) {
            return false;
        }
        global $wpdb;

        $format_data = self::format_data($where);
        $conditions  = $format_data['conditions'];
        $values      = $format_data['values'];

        $sql = "SELECT COUNT(*) FROM {$wpdb->zib_message} WHERE $conditions";

        if ($values) {
            $count = $wpdb->get_var($wpdb->prepare($sql, $values));
        } else {
            $count = $wpdb->get_var($sql, $values);
        }
        return $count;
    }

    /**
     * @description: 根据Array数据获取1条消息
     * @param array $where 例如：array('id' => '10');
     * @return {*}
     */
    public static function get_row($where)
    {

        $msg_db = self::get($where, 'id', 0, 1);

        if (isset($msg_db[0])) {
            $msg_db        = $msg_db[0];
            $msg_db->meta  = maybe_unserialize($msg_db->meta);
            $msg_db->other = maybe_unserialize($msg_db->other);
        }
        return $msg_db;
    }

    /**
     * @description: 根据数据获取消息
     * @param array $where 例如：array('id' => '10');
     * @param mixed $orderby 排序依据
     * @param int $offset 跳过前几个
     * @param int||mixed  $ice_perpage 加载数量| 'all' 代表加载全部
     * @param mixed $decs 'DESC'降序 | 'ASC'降序
     * @return {*}
     */
    public static function get($where, $orderby = 'id', $offset = 0, $ice_perpage = 10, $decs = 'DESC')
    {

        global $wpdb;
        $format_data = self::format_data($where);
        $conditions  = $format_data['conditions'];
        $values      = $format_data['values'];
        $decs        = 'DESC' == $decs ? 'DESC' : '';
        $limit       = '';
        if ('all' != $ice_perpage) {
            $limit = 'limit ' . $offset . ',' . $ice_perpage;
        }
        $sql = "SELECT * FROM {$wpdb->zib_message} WHERE $conditions order by $orderby $decs $limit";

        if ($values) {
            $msg_db = $wpdb->get_results($wpdb->prepare($sql, $values));
        } else {
            $msg_db = $wpdb->get_results($sql);
        }

        return $msg_db;
    }

    /**
     * @description: 根据数据查找删除消息
     * @param array $where 例如：array('id' => '10');
     * @return {*}
     */
    public static function delete($where)
    {
        global $wpdb;
        //挂钩添加
        do_action('zib_delete_message', $where);

        $format_data = self::format_data($where);
        $conditions  = $format_data['conditions'];
        $values      = $format_data['values'];

        $sql = "DELETE FROM `$wpdb->zib_message` WHERE $conditions";

        return $wpdb->query($wpdb->prepare($sql, $values));
    }

    /**
     * @description: 获取消息的Meta值
     * @param int $id 消息ID
     * @param mixed $key Meta键名
     * @param mixed $defaults 默认值
     * @return {*}
     */
    public static function get_meta($id, $key, $defaults = false)
    {
        $msg_db = self::get_row(array('id' => $id));
        if ($msg_db) {
            $metas = (array) $msg_db->meta;
            if (isset($metas[$key])) {
                return $metas[$key];
            }
        }
        return $defaults;
    }

    /**
     * @description: 设置消息的Meta值
     * @param int $id 消息ID
     * @param mixed $key Meta键名
     * @param mixed $values Meta键值
     * @return {*}
     */
    public static function set_meta($id, $key, $values)
    {
        global $wpdb;
        $msg_db = self::get_row(array('id' => $id));
        $metas  = array();
        if (is_array($msg_db->meta)) {
            $metas = $msg_db->meta;
        }
        $metas[$key] = $values;
        $metas       = maybe_serialize($metas);

        $where = array('id' => $id);
        //挂钩添加
        do_action('zib_message_set_meta', $id, $key, $values);

        return $wpdb->update($wpdb->zib_message, array('meta' => $metas), $where);
    }

    /**
     * @description: 设置消息状态
     * @global $wpdb;
     * @param int||array $id 允许多选数组
     * @param mixed $values 值
     * @return boolr
     */
    public static function set_status($id, $values)
    {
        global $wpdb;

        $where         = array('id' => $id);
        $modified_time = current_time('mysql');
        //挂钩添加
        do_action('zib_message_set_status', $id, $values);

        if (is_array($id)) {
            $id = implode(',', $id);
            return $wpdb->query("update $wpdb->zib_message set modified_time = $modified_time , status = $values where id IN ($id)");
        } else {
            return $wpdb->update($wpdb->zib_message, array('status' => $values, 'modified_time' => current_time('mysql')), $where);
        }
    }

    /**
     * @description: 批量设置消息状态
     * @global $wpdb;
     * @param int||array $id 允许多选数组
     * @param mixed $values 值
     * @return boolr
     */
    public static function set_status_batch($where, $values)
    {
        global $wpdb;
        //挂钩添加
        do_action('zib_message_set_status_batch', $where, $values);

        return $wpdb->update($wpdb->zib_message, array('status' => $values, 'modified_time' => current_time('mysql')), $where);
    }

    /**
     * @description: 添加已经阅读的用户ID
     * @param int||array $id 允许多选数组
     * @param mixed $values 值
     * @return boolr
     */
    public static function add_readed_user($id, $_user_id)
    {
        global $wpdb;
        $msg_db  = self::get_row(array('id' => $id));
        $user_id = '[' . $_user_id . ']';

        $readed_user = $msg_db->readed_user;
        if (!strstr($readed_user, $user_id)) {
            $readed_user = $readed_user ? $readed_user . ',' . $user_id : $user_id;
            $where       = array('id' => $id);
            //挂钩添加
            do_action('zib_message_readed', $id, $_user_id);

            return $wpdb->update($wpdb->zib_message, array('readed_user' => $readed_user), $where);
        }
    }

    /**
     * @description: 根据$where将数据全部标记为已读
     * @param array $where 例如：array('id' => '10');
     * @param int $user_id 用户id
     * @return boolr
     */
    public static function user_all_readed($where, $user_id)
    {
        global $wpdb;
        //挂钩添加
        do_action('zib_message_all_readed', $where, $user_id);

        $user_id     = "',[$user_id]'";
        $format_data = self::format_data($where);
        $conditions  = $format_data['conditions'];
        $values      = $format_data['values'];
        $sql         = "UPDATE `$wpdb->zib_message` SET `readed_user` = concat(IFNULL(readed_user,''),$user_id) WHERE $conditions";
        if ($values) {
            return $wpdb->query($wpdb->prepare($sql, $values));
        } else {
            return $wpdb->query($sql, $values);
        }
    }
    /**
     * @description: 获取已经阅读的用户和阅读数量
     * @param int||array $id_or_msg_db 消息ID或者消息数组
     * @param mixed $values 值
     * @return boolr
     */
    public static function get_readed_user($id_or_msg_db)
    {
        if (is_object($id_or_msg_db) || is_array($id_or_msg_db)) {
            $id_or_msg_db = (array) $id_or_msg_db;
            $readed_user  = $id_or_msg_db['readed_user'];
        } else {
            $msg_db      = self::get_row(array('id' => (int) $id_or_msg_db));
            $readed_user = $msg_db->readed_user;
        }
        if (!$readed_user) {
            return false;
        }

        $readed_user = str_replace("[", "", $readed_user);
        $readed_user = str_replace("]", "", $readed_user);
        $readed_user = explode(',', $readed_user);
        return array(
            'count'       => count($readed_user),
            'readed_user' => $readed_user,
        );
    }

    //over
}
