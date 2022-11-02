<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-11-03 16:09:18
 * @LastEditTime: 2022-06-12 10:34:15
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|微信公众号模板消息
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

/**
 * @description: 发送微信模板消息统一接口
 * @param {*} $user_id
 * @param {*} $template_id
 * @param {*} $url
 * @param {*} $data
 * @param {*} $topcolor
 * @return {*}
 */
function zib_send_wechat_template_msg($user_id, $template_id, $data, $url = '', $topcolor = '')
{
    if (!_pz('wechat_template_msg_s')) {
        return false;
    }

    $wxConfig = get_oauth_config('weixingzh');

    if (!$wxConfig['appid'] || !$wxConfig['appkey']) {
        return false;
    }

    $open_id = zib_get_user_wechat_open_id($user_id);
    if (!$open_id) {
        return false;
    }

    require_once get_theme_file_path('/oauth/sdk/weixingzh.php');
    $WeChat      = new \Weixin\GZH\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
    $accessToken = $WeChat->getAccessToken();
    $url         = $url ?: home_url();

    //-----------------------------------
    $api_url   = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $accessToken;
    $curl_data = array(
        "touser"      => $open_id,
        "template_id" => $template_id,
        'url'         => $url,
        'topcolor'    => $topcolor,
        'data'        => $data,
    );

    $http     = new Yurun\Util\HttpRequest;
    $response = $http->timeout(10000)->post($api_url, json_encode($curl_data));
    if (empty($response->success)) {
        return false;
    }
    return $response->json(true);
    //-----------------------------------

    $send = $WeChat->sendTemplateMsg($open_id, $template_id, $data, $url, $topcolor);

    return $send;
}

/**
 * @description: 给所有网站管理员发送模板消息
 * @param {*} $template_id
 * @param {*} $data
 * @param {*} $url
 * @param {*} $topcolor
 * @return {*}
 */
function zib_send_wechat_template_msg_to_admin($template_id, $data, $url = '', $topcolor = '')
{
    $ids = zib_get_admin_user_ids();
    if ($ids) {
        foreach ($ids as $user_id) {
            zib_send_wechat_template_msg($user_id, $template_id, $data, $url, $topcolor);
        }
    }
}

/**
 * @description: 获取用户的微信open_id
 * @param {*} $user_id
 * @return {*}
 */
function zib_get_user_wechat_open_id($user_id)
{
    return get_user_meta($user_id, 'oauth_weixingzh_openid', true);
}

/**
 * @description: 获取模板ID，可以用作判断函数
 * @param {*} $type
 * @return {*}
 */
function zib_get_wechat_template_id($type)
{
    if (!_pz('wechat_template_msg_s')) {
        return false;
    }

    $_pz = _pz('wechat_template_ids');

    if (empty($_pz[$type . '_s'])) {
        return false;
    }

    return isset($_pz[$type]) ? trim($_pz[$type]) : false;
}
