<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-11-11 11:35:21
 * @LastEditTime: 2022-04-13 21:58:22
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//载入文件
$class_list = array(
    'file-class',
    'sms-class',
    'api-audit-class',
    'tx-sdk-send',
);

foreach ($class_list as $class) {
    require_once plugin_dir_path(__FILE__) . $class . '.php';
}
