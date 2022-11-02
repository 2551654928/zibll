<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:50
 * @LastEditTime: 2022-07-15 12:21:24
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

global $wpdb;
$wpdb->zibpay_order = $wpdb->prefix . 'zibpay_order';

/**
 * @description: 支付的订单系统
 * @param {*}
 * @return {*}
 */
class ZibPay
{

    //授权验证判断函数
    public static function required()
    {
        return !ZibAut::is_aut() && !zib_is_local();
    }

    /**
     * @description: 创建数据库
     * @param {*}
     * @return {*}
     */
    public static function create_db()
    {
        global $wpdb;
        /**判断没有则创建 */
        if ($wpdb->get_var("show tables like '{$wpdb->zibpay_order}'") != $wpdb->zibpay_order) {
            $wpdb->query("CREATE TABLE $wpdb->zibpay_order (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) DEFAULT NULL COMMENT '用户id',
                    `ip_address` varchar(50) DEFAULT NULL COMMENT 'ip地址',
                    `product_id` varchar(50) DEFAULT NULL COMMENT '产品id',
                    `post_id` int(11) DEFAULT NULL COMMENT '文章id',
                    `post_author` int(11) DEFAULT NULL COMMENT '文章作者',
                    `order_num` varchar(50) DEFAULT NULL COMMENT '订单号',
                    `order_price` double(10,2) DEFAULT 0 COMMENT '订单价格',
                    `order_type` varchar(50) DEFAULT '0' COMMENT '订单类型',
                    `create_time` datetime DEFAULT NULL COMMENT '创建时间',
                    `pay_num` varchar(50) DEFAULT NULL COMMENT '支付订单号',
                    `pay_type` varchar(50) DEFAULT '0' COMMENT '支付类型',
                    `pay_price` double(10,2) DEFAULT NULL COMMENT '支付金额',
                    `pay_detail` longtext DEFAULT NULL COMMENT '支付详情',
                    `pay_time` datetime DEFAULT NULL COMMENT '支付时间',
                    `referrer_id` int(11) DEFAULT NULL COMMENT '推荐人id',
                    `rebate_price` double(10,2) DEFAULT 0 COMMENT '推荐佣金',
                    `rebate_status` varchar(50) DEFAULT 0 COMMENT '佣金提现状态',
                    `rebate_detail` longtext DEFAULT NULL COMMENT '佣金提现详情',
                    `income_price` double(10,2) DEFAULT 0 COMMENT '作者分成',
                    `income_status` varchar(50) DEFAULT 0 COMMENT '分成状态',
                    `income_detail` longtext DEFAULT NULL COMMENT '分成详情',
                    `status` varchar(50) DEFAULT 0 COMMENT '订单状态',
                    `other` longtext DEFAULT NULL COMMENT '其它',
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=" . DB_CHARSET . " COMMENT='授权明细';");
        }

        // 判断数据库推荐返利功能字段，无则添加
        if (!$wpdb->get_row("SELECT column_name FROM information_schema.columns WHERE table_name='$wpdb->zibpay_order' and column_name ='post_author'")) {
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD post_author int(11) DEFAULT 0 COMMENT '文章作者'");
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD income_price double(10,2) DEFAULT 0 COMMENT '作者分成'");
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD income_status varchar(50) DEFAULT 0 COMMENT '分成状态'");
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD income_detail longtext DEFAULT NULL COMMENT '分成详情'");
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD pay_detail longtext DEFAULT NULL COMMENT '收款详情'");

            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD referrer_id int(11) DEFAULT NULL COMMENT '推荐人id'");
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD rebate_price double(10,2) DEFAULT NULL COMMENT '返利金额'");
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD rebate_status varchar(255) DEFAULT 0 COMMENT '提现状态'");
            @$wpdb->query("ALTER TABLE $wpdb->zibpay_order ADD rebate_detail varchar(2550) DEFAULT NULL COMMENT '提现详情'");
        }
    }

    /**
     * @description: 获取用户IP地址
     * @param {*}
     * @return {*}
     */
    public static function get_ip()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }

    /**
     * @description: 删除订单
     * @param int $order_num 订单号
     * @param int $id 订单ID
     * @return
     */
    public static function delete_order($order_num = '', $id = '')
    {
        if (!$order_num && !$id) {
            return false;
        }

        global $wpdb;
        if ($order_num) {
            $delete_db = $wpdb->query("DELETE FROM $wpdb->zibpay_order WHERE `order_num` = '$order_num'");
        } elseif ($id) {
            $delete_db = $wpdb->query("DELETE FROM $wpdb->zibpay_order WHERE `id` = $id");
        }
        return $delete_db ? true : false;
    }

    /**
     * @description: 清理无效订单
     * @param int $days_ago 时间
     * @return {*}
     */
    public static function clear_order($days_ago = 15)
    {
        global $wpdb;
        $ago_time     = date("Y-m-d H:i:s", strtotime("-$days_ago day", strtotime(current_time('Y-m-d H:i:s'))));
        $delete_count = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->zibpay_order WHERE  `status` = 0 and `create_time` < '$ago_time'");
        $delete_db    = $wpdb->query("DELETE FROM $wpdb->zibpay_order WHERE `status` = 0 and `create_time` < '$ago_time'");

        return $delete_db ? $delete_count : false;
    }

    /**
     * @description: 更新订单数据库的主函数
     * @param {*}
     * @return {*}
     */
    public static function update_order($values)
    {
        global $wpdb;
        $defaults = array(
            'id'            => '',
            'user_id'       => '',
            'ip_address'    => '',
            'product_id'    => '',
            'post_id'       => '',
            'post_author'   => '',
            'income_price'  => '',
            'income_status' => '',
            'income_detail' => '',
            'order_num'     => '',
            'order_price'   => '',
            'order_type'    => '',
            'create_time'   => '',
            'pay_num'       => '',
            'pay_type'      => '',
            'pay_price'     => '',
            'pay_detail'    => '',
            'pay_time'      => '',
            'status'        => 0,
            'other'         => '',
            'referrer_id'   => '',
            'rebate_price'  => '',
            'rebate_status' => '',
            'rebate_detail' => '',
        );
        $values = wp_parse_args((array) $values, $defaults);

        $order_data = array(
            'user_id'       => $values['user_id'],
            'ip_address'    => $values['ip_address'],
            'product_id'    => $values['product_id'],
            'post_id'       => $values['post_id'],
            'post_author'   => $values['post_author'],
            'income_price'  => $values['income_price'],
            'income_status' => $values['income_status'],
            'income_detail' => maybe_serialize($values['income_detail']),
            'order_price'   => $values['order_price'],
            'order_type'    => $values['order_type'],
            'create_time'   => current_time('mysql'),
            'pay_num'       => $values['pay_num'],
            'pay_type'      => $values['pay_type'],
            'pay_price'     => $values['pay_price'],
            'pay_detail'    => maybe_serialize($values['pay_detail']),
            'pay_time'      => $values['pay_time'],
            'status'        => $values['status'],
            'other'         => maybe_serialize($values['other']),
            'referrer_id'   => $values['referrer_id'],
            'rebate_price'  => $values['rebate_price'],
            'rebate_status' => $values['rebate_status'],
            'rebate_detail' => maybe_serialize($values['rebate_detail']),
        );
        $order_data = wp_unslash($order_data);

        if (!empty($values['id'])) {
            //更新数据库
            unset($order_data['create_time']); //清除创建时间

            $order_data = array_filter($order_data); //清除为空的数组键。
            if (!$order_data) {
                return false;
            }

            $where = array('id' => $values['id']);
            //挂钩添加
            do_action('zib_update_order', $order_data['id'], $order_data);
            //执行更新
            if (false !== $wpdb->update($wpdb->zibpay_order, $order_data, $where)) {
                return $order_data;
            }
        }

        //如果上面未更新，则创建新订单
        //添加商品作者
        if (!$order_data['post_author'] && $order_data['post_id']) {
            $post = get_post($order_data['post_id']);
            if (!empty($post->post_author)) {
                $order_data['post_author'] = $post->post_author;
            }
        }
        $order_data['user_id'] = $order_data['user_id'] ? $order_data['user_id'] : get_current_user_id();
        /**用户id */
        $order_data['create_time'] = current_time("Y-m-d H:i:s");
        /** 创建时间 **/
        $order_data['ip_address'] = self::get_ip();
        /**记录IP地址 */
        $order_data['order_num'] = current_time('ymdHis') . mt_rand(10, 99) . mt_rand(10, 99) . mt_rand(100, 999); // 订单号
        /**创建订单号 */

        //执行新增
        if (false !== $wpdb->insert($wpdb->zibpay_order, $order_data)) {
            return $order_data;
        }
        return false;
    }

    /**
     * @description: 新增订单
     * @param {*}
     * @return {*}
     */
    public static function add_order($values)
    {
        return self::update_order($values);
    }

    /**
     * @description: 支付订单
     * @param {*}
     * @return {*}
     */
    public static function payment_order($values)
    {
        global $wpdb;
        $defaults = array(
            'order_num' => '',
            'pay_type'  => '',
            'pay_price' => '',
            'pay_num'   => '',
            'other'     => '',
        );
        $values = wp_parse_args((array) $values, $defaults);
        if (empty($values['order_num'])) {
            return false;
        }

        //准备参数
        $order_data = array(
            'pay_type'  => $values['pay_type'],
            'pay_price' => $values['pay_price'],
            'pay_num'   => $values['pay_num'],
            'status'    => 1,
            'other'     => maybe_serialize($values['other']),
            'pay_time'  => current_time("Y-m-d H:i:s"),
        );
        //准备查询参数
        $where = array('order_num' => $values['order_num'], 'status' => 0);
        if ($wpdb->update($wpdb->zibpay_order, $order_data, $where)) {
            $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->zibpay_order} WHERE order_num = %s AND status = %d", $values['order_num'], 1));
            if ($order) {
                do_action('payment_order_success', $order);
                return $order;
            }
        }

        return false;
    }

    /**
     * @description: 设置提现状态
     * @param int||arrat $id 允许多选数组
     * @param mixed $values 值
     * @return boolr
     */
    public static function set_rebate_status($id, $values)
    {
        global $wpdb;

        $where = array('id' => $id);
        if (is_array($id)) {
            $id = implode(',', $id);
            return $wpdb->query("update $wpdb->zibpay_order set rebate_status = $values where id IN ($id)");
        } else {
            return $wpdb->update($wpdb->zibpay_order, array('rebate_status' => $values), $where);
        }
    }
}
