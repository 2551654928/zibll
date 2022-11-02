/**
 * zib支付JS
 */

(function ($) {
    var _win = window._win;
    var _body = $("body");
    var _modal = false;
    var is_verify = false;
    var order_result = {};
    var pay_inputs = {};
    var pay_ajax_url = _win.ajax_url;
    var modal_id = 'zibpay_modal';

    init();

    function init() {
        var _modal_html = '<div class="modal fade flex jc" style="display:none;" id="' + modal_id + '" tabindex="-1" role="dialog" aria-hidden="false">\
        <div class="modal-dialog" role="document">\
            <div class="pay-payment alipay">\
                <div class="modal-body modal-pay-body">\
                    <div class="row-5 hide-sm">\
                        <img class="lazyload pay-sys t-wechat" alt="alipay" src="" data-src="' + _win.uri + '/zibpay/assets/img/alipay-sys.png">\
                        <img class="lazyload pay-sys t-alipay" alt="wechat" src="" data-src="' + _win.uri + '/zibpay/assets/img/wechat-sys.png">\
                    </div>\
                    <div class="row-5">\
                    <div class="pay-qrcon">\
                        <div class="qrcon">\
                            <div class="pay-logo-header mb10"><span class="pay-logo"></span><span class="pay-logo-name t-wechat">支付宝</span><span class="pay-logo-name t-alipay">微信支付</span></div>\
                            <div class="pay-title em09 muted-2-color padding-h6"></div>\
                            <div><span class="em09">￥</span><span class="pay-price em14"></span></div>\
                            <div class="pay-qrcode">\
                                <img src="" alt="pay-qrcode">\
                            </div>\
                        </div>\
                    <div class="pay-switch"></div>\
                    <div class="pay-notice"><div class="notice load">正在生成订单，请稍候</div></div>\
                    </div>\
				</div>\
                </div>\
            </div>\
        </div>\
    </div>';

        $("link#zibpay_css").length || $("head").append('<link type="text/css" id="zibpay_css" rel="stylesheet" href="' + _win.uri + '/zibpay/assets/css/main.css?ver=' + _win.ver + '">');
        $("#" + modal_id).length || _body.append(_modal_html);

        $(document).ready(weixin_auto_send);
        _body.on("click", '.initiate-pay', initiate_pay);
        _body.on("click", '.pay-vip', vip_pay);

        //模态框关闭停止查询登录
        _body.on("hide.bs.modal", "#" + modal_id, function () {
            order_result.order_num = false;
            is_verify = false;
        });

        _modal = $('#' + modal_id);
    }

    function ajax_send(data, _this) {
        data.openid && notyf("正在发起支付，请稍等...", "load", "", "pay_ajax"); //微信JSAPI支付

        zib_ajax(_this, data, function (n) {
            //1.遇到错误
            if (n.error) {
                return;
            }

            //2.打开链接
            if (n.url && n.open_url) {
                window.location.href = n.url;
                window.location.reload;
                notyf("正在跳转到支付页面");
                return;
            }

            //3.微信JSAPI支付
            if (n.jsapiParams) {
                var jsapiParams = n.jsapiParams;
                if (typeof WeixinJSBridge == "undefined") {
                    //安卓手机需要挂载
                    if (document.addEventListener) {
                        document.addEventListener('WeixinJSBridgeReady', weixin_bridge_ready(jsapiParams), false);
                    } else if (document.attachEvent) {
                        document.attachEvent('WeixinJSBridgeReady', weixin_bridge_ready(jsapiParams));
                        document.attachEvent('onWeixinJSBridgeReady', weixin_bridge_ready(jsapiParams));
                    }
                } else {
                    weixin_bridge_ready(jsapiParams);
                }
                notyf("请完成支付", "", "", (data.openid ? 'pay_ajax' : ''));
                return;
            }

            //4.扫码支付
            if (n.url_qrcode) {
                _modal.find('.more-html').remove(); //隐藏更多内容
                $(".modal:not(#" + modal_id + ")").modal('hide'); //隐藏其他模态框
                _modal.find('.pay-qrcode img').attr('src', n.url_qrcode); //加载二维码
                qrcode_notice('请扫码支付，支付成功后会自动跳转', '');
                n.more_html && _modal.find('.pay-notice').before('<div class="more-html">' + n.more_html + '</div>');
                n.order_name && _modal.find('.pay-title').html(n.order_name);
                n.order_price && _modal.find('.pay-price').html(n.order_price);
                n.payment_method && _modal.find('.pay-payment').removeClass('wechat alipay').addClass(n.payment_method);

                _modal.modal('show');

                //开始ajax检测是否付费成功
                order_result = n;
                if (!is_verify) {
                    verify_pay();
                    is_verify = true;
                }
            }

        }, 'stop');
    }

    //扫码支付检测是否支付成功
    function verify_pay() {
        if (order_result.order_num) {
            $.ajax({
                type: "POST",
                url: pay_ajax_url,
                data: {
                    "action": "check_pay",
                    "order_num": order_result.order_num,
                },
                dataType: "json",
                success: function (n) {
                    if (n.status == "1") {
                        qrcode_notice('支付成功，页面跳转中', 'success');
                        setTimeout(function () {
                            if ("undefined" != typeof pay_inputs.return_url && pay_inputs.return_url) {
                                window.location.href = delQueStr('openid', delQueStr('zippay', pay_inputs.return_url));
                                window.location.reload;
                            } else {
                                location.href = delQueStr('openid', delQueStr('zippay'));
                                location.reload;
                            }
                        }, 300);
                    } else {
                        setTimeout(function () {
                            verify_pay();
                        }, 2000);
                    }
                }
            });
        }
    }

    function initiate_pay() {
        var _this = $(this);
        var form = _this.parents('form');
        pay_inputs = form.serializeObject();
        pay_inputs.action = 'initiate_pay';
        pay_inputs.return_url || (pay_inputs.return_url = window.location.href);
        ajax_send(pay_inputs, _this);
        return false;
    }

    //扫码支付通知显示
    function qrcode_notice(msg, type) {
        var notice_box = _modal.find('.pay-notice .notice');
        msg = type == 'load' ? '<i class="loading mr6"></i>' + msg : msg;
        notice_box.removeClass('load warning success danger').addClass(type).html(msg);
    }

    //微信JSAPI支付
    function weixin_bridge_ready(jsapiParams) {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest', jsapiParams,
            function (res) {
                if (res.err_msg == "get_brand_wcpay_request:ok") {
                    // 使用以上方式判断前端返回,微信团队郑重提示：
                    //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                    //支付成功刷新页面
                } else {
                    //取消支付，或者支付失败


                }
                location.href = delQueStr('openid', delQueStr('zippay'));
                location.reload; //刷新页面

            });
    }

    //微信JSAPI支付收到回调之后，再次自动提交
    function weixin_auto_send() {
        var zippay = GetRequest('zippay');
        var openid = GetRequest('openid');

        if (zippay && openid && is_weixin_app()) {
            pay_inputs.pay_type = 'wechat';
            pay_inputs.openid = openid;
            pay_inputs.action = 'initiate_pay';

            ajax_send(pay_inputs, $('<div></div>'))
        }
    }

    //判断是否在微信浏览器内
    function is_weixin_app() {
        var ua = window.navigator.userAgent.toLowerCase();
        return (ua.match(/MicroMessenger/i) == 'micromessenger');
    }

    function vip_pay() {
        var _this = $(this);

        var _modal = '<div class="modal fade flex jc" id="modal_pay_uservip" tabindex="-1" role="dialog" aria-hidden="false">\
    <div class="modal-dialog" role="document">\
    <div class="modal-content">\
    <div class="modal-body"><h4 style="padding:20px;" class="text-center"><i class="loading zts em2x"></i></h4></div>\
    </div>\
    </div>\
    </div>\
    </div>';
        $("#modal_pay_uservip").length || _body.append(_modal);
        var modal = $('#modal_pay_uservip');
        var vip_level = _this.attr('vip-level') || 1;
        if (modal.find('.payvip-modal').length) {
            $('a[href="#tab-payvip-' + vip_level + '"]').tab('show');
            modal.modal('show');
        } else {
            notyf("加载中，请稍等...", "load", "", "payvip_ajax");
            $.ajax({
                type: "POST",
                url: pay_ajax_url,
                data: {
                    "action": "pay_vip",
                    "vip_level": vip_level,
                },
                dataType: "json",
                success: function (n) {
                    var msg = n.msg || '请选择会员选项';
                    if ((msg.indexOf("登录") != -1)) {
                        modal.remove()
                        $('.signin-loader').click();
                    }
                    notyf(msg, (n.ys ? n.ys : (n.error ? 'danger' : "")), 3, "payvip_ajax");
                    if (!n.error) {
                        modal.find('.modal-content').html(n.html);
                        if (!modal.find('.modal-content .tab-pane.active').length) {
                            modal.find('.modal-content a[data-toggle="tab"]:eq(0)').click();
                        }
                        modal.trigger('loaded.bs.modal').modal('show');
                        auto_fun();
                    }
                }
            });
        }
        return !1;
    }

    //卡密充值的内容切换
    _body.on("controller.change", '[data-controller="payment_method"][data-value="card_pass"]', function (e, a) {
        var _this = $(this);
        var form = _this.parents('form');
        if (a) {
            form.find('.charge-box').hide()
        } else {
            form.find('.charge-box').show()
        }
    });

})(jQuery);

function GetRequest(name) {
    var url = window.parent.location.search || _win.url_request || ''; //获取url中"?"符后的字串
    // var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        if (str.indexOf("#" != -1)) {
            str = str.substr(0);
        }
        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            if (strs[i].indexOf(name) != -1) {
                return strs[i].split("=")[1];
            }
        }
    }
    return null;
}


//从链接中删除参数
function delQueStr(ref, url) {
    var str = "";
    url = url || window.location.href;
    if (url.indexOf('?') != -1) {
        str = url.substr(url.indexOf('?') + 1);
    } else {
        return url;
    }
    var arr = "";
    var returnurl = "";
    if (str.indexOf('&') != -1) {
        arr = str.split('&');
        for (var i in arr) {
            if (arr[i].split('=')[0] != ref) {
                returnurl = returnurl + arr[i].split('=')[0] + "=" + arr[i].split('=')[1] + "&";
            }
        }
        return url.substr(0, url.indexOf('?')) + "?" + returnurl.substr(0, returnurl.length - 1);
    } else {
        arr = str.split('=');
        if (arr[0] == ref) {
            return url.substr(0, url.indexOf('?'));
        } else {
            return url;
        }
    }
}