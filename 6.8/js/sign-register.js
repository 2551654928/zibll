/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:40
 * @LastEditTime: 2022-06-24 17:32:34
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */


$('.sign form').keydown(function (e) {
    var e = e || event,
        keycode = e.which || e.keyCode;
    if (keycode == 13) {
        $(this).find('.signsubmit-loader').trigger("click");
    }
})

//输入检测
_win.bd.on('input porpertychange', 'input[change-show]', function () {
    var _this = $(this);
    var val = _this.val();
    if (val.length > 5) {
        var e = _this.attr('change-show') || '.change-show';
        _this.parents('form').find(e).slideDown()
    }
})

/**发送验证码 */
_win.bd.on('click', ".captchsubmit", function () {
    _this = $(this);
    _win.captchsubmit_wait = 60;
    var _text = _this.html();
    var captchsubmit = function () {
        zib_ajax(_this, 0, function (n) {
            n.error || captchdown();
            if (n.remaining_time) {
                _win.captchsubmit_wait = n.remaining_time;
                captchdown();
            }
        });
        return !1;
    }

    var captchdown = function () {
        var _captchsubmit = $(".captchsubmit");
        if (_win.captchsubmit_wait > 0) {
            _captchsubmit.html(_win.captchsubmit_wait + '秒后可重新发送').attr('disabled', !0);
            _win.captchsubmit_wait--;
            setTimeout(captchdown, 1000);
        } else {
            _captchsubmit.html(_text).attr('disabled', !1);
            _win.captchsubmit_wait = 60
        }
    }

    captchsubmit();
})

//提交
_win.bd.on("click", ".signsubmit-loader", function () {
    zib_ajax($(this), 0, function (n) {
        n.error || (window.location.reload());
    })
    return !1;
})

//更换绑定的手机号或者邮箱
_win.bd.on('click', '.user-verify-submit', function (e) {
    var _this = $(this);
    var _next = _this.parents('.tab-pane').next('.tab-pane').attr('id');
    zib_ajax(_this, 0, function (n) {
        if (!n.error) {
            $('a[href="#' + _next + '"]').tab('show');
            if (_win.captchsubmit_wait) {
                _win.captchsubmit_wait -= 40;
            }
        }
    })
})

//邀请码注册
_win.bd.on('zib_ajax.success', '.invit-code-verify', function (e, n) {
    if (n && n.code) {
        $('#tab-signup-signup form').append('<input type="hidden" name="invit_code" value="' + n.code + '">');
    }
})