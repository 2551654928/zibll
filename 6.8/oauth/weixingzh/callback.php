<?php
/*
 * @Author: Qinver
 * @Url: zibll.com
 * @Date: 2021-06-18 12:16:27
 * @LastEditTime: 2022-11-02 13:52:06
 */

//启用 session
@session_start();

//引入核心文件
require_once get_theme_file_path('/inc/code/require.php');
require_once get_theme_file_path('/inc/code/file.php');

//微信配置接口验证
if (!empty($_REQUEST['echostr']) && !empty($_REQUEST['signature'])) {
    //微信接口校验
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce     = $_GET["nonce"];

    $token  = _pz('oauth_weixingzh_option', '', 'token');
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);

    if ($tmpStr == $signature) {
        echo $_REQUEST['echostr'];
        exit();
    }
}

//获取后台配置
$current_user_id = get_current_user_id();
$wxConfig        = get_oauth_config('weixingzh');

//微信APP内跳转登录
if (!zib_weixingzh_is_qrcode()) {
    // 在微信APP内使用无感登录接口
    try {
        $wxOAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
        // 获取accessToken，把之前存储的state传入，会自动判断。获取失败会抛出异常！
        $accessToken = $wxOAuth->getAccessToken($_SESSION['YURUN_WEIXIN_STATE']);
        $userInfo    = $wxOAuth->getUserInfo(); //第三方用户信息
        $openid      = $wxOAuth->openid; // 唯一ID
    } catch (Exception $err) {
        zib_oauth_die($err->getMessage());
    }

    // 处理本地业务逻辑
    if ($openid && $userInfo) {
        $userInfo['name'] = !empty($userInfo['nickname']) ? $userInfo['nickname'] : '';

        $oauth_data = array(
            'type'        => 'weixingzh',
            'openid'      => $openid,
            'name'        => $userInfo['name'],
            'avatar'      => !empty($userInfo['headimgurl']) ? $userInfo['headimgurl'] : '',
            'description' => '',
            'getUserInfo' => $userInfo,
        );
        //代理登录
        zib_agent_callback($oauth_data);

        $oauth_result = zib_oauth_update_user($oauth_data);

        if ($oauth_result['error']) {
            zib_oauth_die($oauth_result['msg']);
        } else {
            $rurl = !empty($_SESSION['oauth_rurl']) ? $_SESSION['oauth_rurl'] : $oauth_result['redirect_url'];
            wp_safe_redirect($rurl);
            exit;
        }
    } else {
        zib_oauth_die();
    }
    exit();
}

//扫码登录流程
require_once get_theme_file_path('/oauth/sdk/weixingzh.php');

$wxOAuth = new \Weixin\GZH\OAuth2($wxConfig['appid'], $wxConfig['appkey']);
$action  = !empty($_REQUEST['action']) ? $_REQUEST['action'] : 'callback';

switch ($action) {
    case 'code_check':

        //代理登录
        zib_agent_login();

        if (!empty($wxConfig['gzh_type']) && $wxConfig['gzh_type'] === 'not') {

            $code = !empty($_REQUEST['code']) ? esc_sql(strip_tags(trim($_REQUEST['code']))) : '';
            if (!$code) {
                echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请输入验证码')));
                exit();
            }

            $user_key = $wxOAuth->getUserKey($code);

            if (!$user_key) {
                echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '验证码错误或已过期，请重新获取')));
                exit();
            }

            if ($user_key === -1) {
                echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '验证码已过期，请重新获取')));
                exit();
            }

            $goto_uery_arg = array(
                'action' => 'code_login',
                'openid' => $user_key,
            );
            if (!empty($_REQUEST['oauth_rurl'])) {
                $goto_uery_arg['oauth_rurl'] = $_REQUEST['oauth_rurl'];
            }

            echo (json_encode(array('reload' => 1, 'msg' => '验证成功', 'openid' => $user_key, 'goto' => add_query_arg($goto_uery_arg, $wxConfig['backurl']))));
            exit();

        }
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '功能未启用')));
        exit();
        break;

    case 'code_login':
        //前台登录或者绑定

        //代理登录验证
        zib_agent_login();

        $openId = !empty($_REQUEST['openid']) ? $_REQUEST['openid'] : '';
        if (!$openId) {
            zib_oauth_die('参数传入错误');
        }

        // 处理本地业务逻辑
        $oauth_data = array(
            'type'        => 'weixingzh',
            'openid'      => $openId,
            'name'        => '',
            'avatar'      => '',
            'description' => '',
            'getUserInfo' => array(
                'openid'      => $openId,
                'name'        => '',
                'avatar'      => '',
                'description' => '',
            ),
        );

        //代理登录回调
        zib_agent_callback($oauth_data);

        $oauth_result = zib_oauth_update_user($oauth_data);

        if ($oauth_result['error']) {
            zib_oauth_die($oauth_result['msg']);
        } else {
            $rurl = !empty($_SESSION['oauth_rurl']) ? $_SESSION['oauth_rurl'] : (!empty($_REQUEST['oauth_rurl']) ? $_REQUEST['oauth_rurl'] : $oauth_result['redirect_url']);
            wp_safe_redirect($rurl);
            exit;
        }

        break;

    case 'callback':
        //接受微信发过来的信息
        $callback = $wxOAuth->callback();

        //未认证模式：发送验证码
        if (!empty($wxConfig['gzh_type']) && $wxConfig['gzh_type'] === 'not') {
            $wxOAuth->code_keyword        = !empty($wxConfig['code_keyword']) ? $wxConfig['code_keyword'] : '登录';
            $wxOAuth->code_reply_template = !empty($wxConfig['code_reply']) ? $wxConfig['code_reply'] : "您的验证码为：%code%\n有效期%time%秒，如过期或验证失败可以重新发送“%keyword%”获取验证码";

            if (isset($wxOAuth->callback['Event']) && $wxOAuth->callback['Event'] == 'subscribe') {
                //如果是首次关注扫码
                $wxOAuth->code_reply_template = !empty($wxConfig['code_subscribe_reply']) ? $wxConfig['code_subscribe_reply'] : $wxOAuth->code_reply_template;
            }

            if ($wxOAuth->CodeReply()) {
                exit();
            }
        }

        if ($callback) {
            $EventKey = str_replace('qrscene_', '', $callback['EventKey']);
            update_option('weixingzh_event_key_' . $EventKey, $callback); //储存临时数据
            //给用户回复消息
            if (!empty($wxConfig['subscribe_msg']) && $callback['Event'] == 'subscribe') {
                $wxOAuth->sendMessage($wxConfig['subscribe_msg']);
                exit();
            } elseif (!empty($wxConfig['scan_msg']) && $callback['Event'] == 'SCAN') {
                $wxOAuth->sendMessage($wxConfig['scan_msg']);
                exit();
            }
        }

        //自动回复
        $wxOAuth->autoReply($wxConfig['auto_reply']);
        exit();
        break;

    case 'check_callback':
        //代理登录
        zib_agent_login();
        //前端验证是否回调
        $state = !empty($_REQUEST['state']) ? $_REQUEST['state'] : '';
        if (!$state) {
            echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '参数传入错误')));
            exit();
        }

        $option = get_option('weixingzh_event_key_' . $state); //读取临时数据
        if (!$option) {
            echo (json_encode(array('error' => 1)));
            exit();
        }
        delete_option('weixingzh_event_key_' . $state); //删除临时数据
        $goto_uery_arg = array(
            'action' => 'login',
            'openid' => $option['FromUserName'],
        );
        if (!empty($_REQUEST['oauth_rurl'])) {
            $goto_uery_arg['oauth_rurl'] = $_REQUEST['oauth_rurl'];
        }

        echo (json_encode(array('goto' => add_query_arg($goto_uery_arg, $wxConfig['backurl']), 'option' => $option)));
        exit();

        break;

    case 'login':
        //前台登录或者绑定

        //代理登录验证
        zib_agent_login();

        $openId = !empty($_REQUEST['openid']) ? $_REQUEST['openid'] : '';
        if (!$openId) {
            wp_die('参数传入错误');
        }

        try {
            $userInfo = $wxOAuth->getUserInfo($openId); //第三方用户信息
        } catch (Exception $err) {
            zib_oauth_die($err->getMessage());
        }

        // 处理本地业务逻辑
        if (!empty($userInfo['openid'])) {
            $userInfo['name']   = !empty($userInfo['nickname']) ? $userInfo['nickname'] : '';
            $userInfo['avatar'] = !empty($userInfo['headimgurl']) ? $userInfo['headimgurl'] : '';

            $oauth_data = array(
                'type'        => 'weixingzh',
                'openid'      => $userInfo['openid'],
                'name'        => $userInfo['name'],
                'avatar'      => $userInfo['avatar'],
                'description' => '',
                'getUserInfo' => $userInfo,
            );

            //代理登录回调
            zib_agent_callback($oauth_data);

            $oauth_result = zib_oauth_update_user($oauth_data);

            if ($oauth_result['error']) {
                zib_oauth_die($oauth_result['msg']);
            } else {
                $rurl = !empty($_SESSION['oauth_rurl']) ? $_SESSION['oauth_rurl'] : (!empty($_REQUEST['oauth_rurl']) ? $_REQUEST['oauth_rurl'] : $oauth_result['redirect_url']);
                wp_safe_redirect($rurl);
                exit;
            }
        } else {
            zib_oauth_die();
            //   file_put_contents(__DIR__ . '/error.log', var_export($userInfo, TRUE));
        }

        break;
}

wp_safe_redirect(home_url());
exit;
