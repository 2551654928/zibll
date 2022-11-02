/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:40
 * @LastEditTime: 2022-10-29 23:17:46
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */


//libs-插件
//jQuery.cookie
jQuery.cookie = function (e, o, t) {
    if (void 0 === o) {
        var i = null;
        if (document.cookie && "" != document.cookie)
            for (var r = document.cookie.split(";"), n = 0; n < r.length; n++) {
                var c = jQuery.trim(r[n]);
                if (c.substring(0, e.length + 1) == e + "=") {
                    i = decodeURIComponent(c.substring(e.length + 1));
                    break
                }
            }
        return i
    }
    t = t || {}, null === o && (o = "", t.expires = -1);
    var a = "";
    if (t.expires && ("number" == typeof t.expires || t.expires.toUTCString)) {
        var l;
        "number" == typeof t.expires ? (l = new Date(), l.setTime(l.getTime() + 24 * t.expires * 60 * 60 * 1e3)) : l = t.expires, a = "; expires=" + l.toUTCString()
    }
    var u = t.path ? "; path=" + t.path : "",
        s = t.domain ? "; domain=" + t.domain : "",
        m = t.secure ? "; secure" : "";
    document.cookie = [e, "=", encodeURIComponent(o), a, u, s, m].join("")
};
var lcs = {
    get: function (e) {
        return window.localStorage ? localStorage.getItem(e) : $.cookie(e)
    },
    set: function (e, o) {
        window.localStorage ? localStorage[e] = o : $.cookie(e, o)
    },
    remove: function (e) {
        window.localStorage ? localStorage.removeItem(e) : $.cookie(e, "")
    }
};

//lazySizes
! function (a, b) {
    var c = b(a, a.document, Date);
    a.lazySizes = c, "object" == typeof module && module.exports && (module.exports = c)
}("undefined" != typeof window ? window : {}, function (a, b, c) {
    "use strict";
    var d, e;
    if (function () {
            var b, c = {
                lazyClass: "lazyload",
                loadedClass: "lazyloaded",
                loadingClass: "lazyloading",
                preloadClass: "lazypreload",
                errorClass: "lazyerror",
                autosizesClass: "lazyautosizes",
                srcAttr: "data-src",
                srcsetAttr: "data-srcset",
                sizesAttr: "data-sizes",
                minSize: 40,
                customMedia: {},
                init: !0,
                expFactor: 1.5,
                hFac: 0.8,
                loadMode: 2,
                loadHidden: !0,
                ricTimeout: 0,
                throttleDelay: 125
            };
            e = a.lazySizesConfig || a.lazysizesConfig || {};
            for (b in c) b in e || (e[b] = c[b])
        }(), !b || !b.getElementsByClassName) return {
        init: function () {},
        cfg: e,
        noSupport: !0
    };
    var f = b.documentElement,
        g = a.HTMLPictureElement,
        h = "addEventListener",
        i = "getAttribute",
        j = a[h].bind(a),
        k = a.setTimeout,
        l = a.requestAnimationFrame || k,
        m = a.requestIdleCallback,
        n = /^picture$/i,
        o = ["load", "error", "lazyincluded", "_lazyloaded"],
        p = {},
        q = Array.prototype.forEach,
        r = function (a, b) {
            return p[b] || (p[b] = new RegExp("(\\s|^)" + b + "(\\s|$)")), p[b].test(a[i]("class") || "") && p[b]
        },
        s = function (a, b) {
            r(a, b) || a.setAttribute("class", (a[i]("class") || "").trim() + " " + b)
        },
        t = function (a, b) {
            var c;
            (c = r(a, b)) && a.setAttribute("class", (a[i]("class") || "").replace(c, " "))
        },
        u = function (a, b, c) {
            var d = c ? h : "removeEventListener";
            c && u(a, b), o.forEach(function (c) {
                a[d](c, b)
            })
        },
        v = function (a, c, e, f, g) {
            var h = b.createEvent("Event");
            return e || (e = {}), e.instance = d, h.initEvent(c, !f, !g), h.detail = e, a.dispatchEvent(h), h
        },
        w = function (b, c) {
            var d;
            !g && (d = a.picturefill || e.pf) ? (c && c.src && !b[i]("srcset") && b.setAttribute("srcset", c.src), d({
                reevaluate: !0,
                elements: [b]
            })) : c && c.src && (b.src = c.src)
        },
        x = function (a, b) {
            return (getComputedStyle(a, null) || {})[b]
        },
        y = function (a, b, c) {
            for (c = c || a.offsetWidth; c < e.minSize && b && !a._lazysizesWidth;) c = b.offsetWidth, b = b.parentNode;
            return c
        },
        z = function () {
            var a, c, d = [],
                e = [],
                f = d,
                g = function () {
                    var b = f;
                    for (f = d.length ? e : d, a = !0, c = !1; b.length;) b.shift()();
                    a = !1
                },
                h = function (d, e) {
                    a && !e ? d.apply(this, arguments) : (f.push(d), c || (c = !0, (b.hidden ? k : l)(g)))
                };
            return h._lsFlush = g, h
        }(),
        A = function (a, b) {
            return b ? function () {
                z(a)
            } : function () {
                var b = this,
                    c = arguments;
                z(function () {
                    a.apply(b, c)
                })
            }
        },
        B = function (a) {
            var b, d = 0,
                f = e.throttleDelay,
                g = e.ricTimeout,
                h = function () {
                    b = !1, d = c.now(), a()
                },
                i = m && g > 49 ? function () {
                    m(h, {
                        timeout: g
                    }), g !== e.ricTimeout && (g = e.ricTimeout)
                } : A(function () {
                    k(h)
                }, !0);
            return function (a) {
                var e;
                (a = true === a) && (g = 33), b || (b = !0, e = f - (c.now() - d), e < 0 && (e = 0), a || e < 9 ? i() : k(i, e))
            }
        },
        C = function (a) {
            var b, d, e = 99,
                f = function () {
                    b = null, a()
                },
                g = function () {
                    var a = c.now() - d;
                    a < e ? k(g, e - a) : (m || f)(f)
                };
            return function () {
                d = c.now(), b || (b = k(g, e))
            }
        },
        D = function () {
            var g, m, o, p, y, D, F, G, H, I, J, K, L = /^img$/i,
                M = /^iframe$/i,
                N = "onscroll" in a && !/(gle|ing)bot/.test(navigator.userAgent),
                O = 0,
                P = 0,
                Q = 0,
                R = -1,
                S = function (a) {
                    Q--, (!a || Q < 0 || !a.target) && (Q = 0)
                },
                T = function (a) {
                    return null == K && (K = "hidden" == x(b.body, "visibility")), K || !("hidden" == x(a.parentNode, "visibility") && "hidden" == x(a, "visibility"))
                },
                U = function (a, c) {
                    var d, e = a,
                        g = T(a);
                    for (G -= c, J += c, H -= c, I += c; g && (e = e.offsetParent) && e != b.body && e != f;)(g = (x(e, "opacity") || 1) > 0) && "visible" != x(e, "overflow") && (d = e.getBoundingClientRect(), g = I > d.left && H < d.right && J > d.top - 1 && G < d.bottom + 1);
                    return g
                },
                V = function () {
                    var a, c, h, j, k, l, n, o, q, r, s, t, u = d.elements;
                    if ((p = e.loadMode) && Q < 8 && (a = u.length)) {
                        for (c = 0, R++; c < a; c++)
                            if (u[c] && !u[c]._lazyRace)
                                if (!N || d.prematureUnveil && d.prematureUnveil(u[c])) ba(u[c]);
                                else if ((o = u[c][i]("data-expand")) && (l = 1 * o) || (l = P), r || (r = !e.expand || e.expand < 1 ? f.clientHeight > 500 && f.clientWidth > 500 ? 500 : 370 : e.expand, d._defEx = r, s = r * e.expFactor, t = e.hFac, K = null, P < s && Q < 1 && R > 2 && p > 2 && !b.hidden ? (P = s, R = 0) : P = p > 1 && R > 1 && Q < 6 ? r : O), q !== l && (D = innerWidth + l * t, F = innerHeight + l, n = -1 * l, q = l), h = u[c].getBoundingClientRect(), (J = h.bottom) >= n && (G = h.top) <= F && (I = h.right) >= n * t && (H = h.left) <= D && (J || I || H || G) && (e.loadHidden || T(u[c])) && (m && Q < 3 && !o && (p < 3 || R < 4) || U(u[c], l))) {
                            if (ba(u[c]), k = !0, Q > 9) break
                        } else !k && m && !j && Q < 4 && R < 4 && p > 2 && (g[0] || e.preloadAfterLoad) && (g[0] || !o && (J || I || H || G || "auto" != u[c][i](e.sizesAttr))) && (j = g[0] || u[c]);
                        j && !k && ba(j)
                    }
                },
                W = B(V),
                X = function (a) {
                    var b = a.target;
                    if (b._lazyCache) return void delete b._lazyCache;
                    S(a), s(b, e.loadedClass), t(b, e.loadingClass), u(b, Z), v(b, "lazyloaded")
                },
                Y = A(X),
                Z = function (a) {
                    Y({
                        target: a.target
                    })
                },
                $ = function (a, b) {
                    try {
                        a.contentWindow.location.replace(b)
                    } catch (c) {
                        a.src = b
                    }
                },
                _ = function (a) {
                    var b, c = a[i](e.srcsetAttr);
                    (b = e.customMedia[a[i]("data-media") || a[i]("media")]) && a.setAttribute("media", b), c && a.setAttribute("srcset", c)
                },
                aa = A(function (a, b, c, d, f) {
                    var g, h, j, l, m, p;
                    (m = v(a, "lazybeforeunveil", b)).defaultPrevented || (d && (c ? s(a, e.autosizesClass) : a.setAttribute("sizes", d)), h = a[i](e.srcsetAttr), g = a[i](e.srcAttr), f && (j = a.parentNode, l = j && n.test(j.nodeName || "")), p = b.firesLoad || "src" in a && (h || g || l), m = {
                        target: a
                    }, s(a, e.loadingClass), p && (clearTimeout(o), o = k(S, 2500), u(a, Z, !0)), l && q.call(j.getElementsByTagName("source"), _), h ? a.setAttribute("srcset", h) : g && !l && (M.test(a.nodeName) ? $(a, g) : a.src = g), f && (h || l) && w(a, {
                        src: g
                    })), a._lazyRace && delete a._lazyRace, t(a, e.lazyClass), z(function () {
                        var b = a.complete && a.naturalWidth > 1;
                        p && !b || (b && s(a, "ls-is-cached"), X(m), a._lazyCache = !0, k(function () {
                            "_lazyCache" in a && delete a._lazyCache
                        }, 9)), "lazy" == a.loading && Q--
                    }, !0)
                }),
                ba = function (a) {
                    if (!a._lazyRace) {
                        var b, c = L.test(a.nodeName),
                            d = c && (a[i](e.sizesAttr) || a[i]("sizes")),
                            f = "auto" == d;
                        (!f && m || !c || !a[i]("src") && !a.srcset || a.complete || r(a, e.errorClass) || !r(a, e.lazyClass)) && (b = v(a, "lazyunveilread").detail, f && E.updateElem(a, !0, a.offsetWidth), a._lazyRace = !0, Q++, aa(a, b, f, d, c))
                    }
                },
                ca = C(function () {
                    e.loadMode = 3, W()
                }),
                da = function () {
                    3 == e.loadMode && (e.loadMode = 2), ca()
                },
                ea = function () {
                    if (!m) {
                        if (c.now() - y < 999) return void k(ea, 999);
                        m = !0, e.loadMode = 3, W(), j("scroll", da, !0)
                    }
                };
            return {
                _: function () {
                    y = c.now(), d.elements = b.getElementsByClassName(e.lazyClass), g = b.getElementsByClassName(e.lazyClass + " " + e.preloadClass), j("scroll", W, !0), j("resize", W, !0), j("pageshow", function (a) {
                        if (a.persisted) {
                            var c = b.querySelectorAll("." + e.loadingClass);
                            c.length && c.forEach && l(function () {
                                c.forEach(function (a) {
                                    a.complete && ba(a)
                                })
                            })
                        }
                    }), a.MutationObserver ? new MutationObserver(W).observe(f, {
                        childList: !0,
                        subtree: !0,
                        attributes: !0
                    }) : (f[h]("DOMNodeInserted", W, !0), f[h]("DOMAttrModified", W, !0), setInterval(W, 999)), j("hashchange", W, !0), ["focus", "mouseover", "click", "load", "transitionend", "animationend"].forEach(function (a) {
                        b[h](a, W, !0)
                    }), /d$|^c/.test(b.readyState) ? ea() : (j("load", ea), b[h]("DOMContentLoaded", W), k(ea, 2e4)), d.elements.length ? (V(), z._lsFlush()) : W()
                },
                checkElems: W,
                unveil: ba,
                _aLSL: da
            }
        }(),
        E = function () {
            var a, c = A(function (a, b, c, d) {
                    var e, f, g;
                    if (a._lazysizesWidth = d, d += "px", a.setAttribute("sizes", d), n.test(b.nodeName || ""))
                        for (e = b.getElementsByTagName("source"), f = 0, g = e.length; f < g; f++) e[f].setAttribute("sizes", d);
                    c.detail.dataAttr || w(a, c.detail)
                }),
                d = function (a, b, d) {
                    var e, f = a.parentNode;
                    f && (d = y(a, f, d), e = v(a, "lazybeforesizes", {
                        width: d,
                        dataAttr: !!b
                    }), e.defaultPrevented || (d = e.detail.width) && d !== a._lazysizesWidth && c(a, f, e, d))
                },
                f = function () {
                    var b, c = a.length;
                    if (c)
                        for (b = 0; b < c; b++) d(a[b])
                },
                g = C(f);
            return {
                _: function () {
                    a = b.getElementsByClassName(e.autosizesClass), j("resize", g)
                },
                checkElems: g,
                updateElem: d
            }
        }(),
        F = function () {
            !F.i && b.getElementsByClassName && (F.i = !0, E._(), D._())
        };
    return k(function () {
        e.init && F()
    }), d = {
        cfg: e,
        autoSizer: E,
        loader: D,
        init: F,
        uP: w,
        aC: s,
        rC: t,
        hC: r,
        fire: v,
        gW: y,
        rAF: z
    }
});

//滑动手势minitouch
$.fn.minitouch = function (options) {
    options = $.extend({
            direction: 'bottom',
            selector: '',
            start_selector: '',
            depreciation: 50,
            stop: false,
            onStart: false,
            onIng: false,
            onEnd: false,
            inEnd: false,
        },
        options);
    var _e = $(this);
    var is_stop = false;
    var dep = options.depreciation;
    var startX = 0;
    var startY = 0;
    var endX = 0;
    var endY = 0;
    var angle = 0;
    var distanceX = 0;
    var distanceY = 0;
    var dragging = false;

    var cssTransition = function (a, b, c, d, s) {
        var e, f, g;
        d && (b += "px", c += "px", e = "translate3D(" + b + "," + c + " , 0)", f = {},
            g = cssT_Support(),
            f[g + "transform"] = e,
            f[g + "transition"] = g + "transform 0s linear",
            f["cursor"] = s,
            "null" == d && (f[g + "transform"] = "", f[g + "transition"] = ""), a.css(f));
    }
    var cssT_Support = function () {
        var a = document.body || document.documentElement;
        a = a.style;
        return "" == a.WebkitTransition ? "-webkit-" : "" == a.MozTransition ? "-moz-" : "" == a.OTransition ? "-o-" : "" == a.transition ? "" : void 0;
    }

    var touch_selector = options.start_selector || options.selector;
    _e.on('touchstart pointerdown MSPointerDown', touch_selector, function (e) {
            startX = startY = endX = endY = angle = distanceX = distanceY = 0;
            startX = e.originalEvent.pageX || e.originalEvent.touches[0].pageX;
            startY = e.originalEvent.pageY || e.originalEvent.touches[0].pageY;
            dragging = !0;
            //兼容swiper
            if ($(e.target).parentsUntil(touch_selector, '.swiper-container,.scroll-x').length) {
                dragging = !1;
            }
        })
        .on("touchmove pointermove MSPointerMove", touch_selector, function (a) {
            var _move = options.start_selector ? (options.selector ? _e.find(options.selector) : _e.find(options.start_selector)) : $(this);
            if ($.isFunction(options.stop)) {
                is_stop = options.stop(_e, _move, startX, startY);
            }
            if (dragging && !is_stop) {
                endX = a.originalEvent.pageX || a.originalEvent.touches[0].pageX;
                endY = a.originalEvent.pageY || a.originalEvent.touches[0].pageY;
                distanceX = endX - startX;
                distanceY = endY - startY;
                angle = 180 * Math.atan2(distanceY, distanceX) / Math.PI;
                "right" == options.direction && (distanceY = 0, distanceX = ((angle > -40 && angle < 40) && distanceX > 0) ? distanceX : 0);
                "left" == options.direction && (distanceY = 0, distanceX = ((angle > 150 || angle < -150) && 0 > distanceX) ? distanceX : 0);
                "top" == options.direction && (distanceX = 0, distanceY = ((angle > -130 && angle < -50) && 0 > distanceY) ? distanceY : 0);
                "bottom" == options.direction && (distanceX = 0, distanceY = ((angle > 50 && angle < 130) && distanceY > 0) ? distanceY : 0);
                if (distanceX !== 0 || distanceY !== 0) {
                    a.preventDefault ? a.preventDefault() : a.returnValue = !1;
                    cssTransition(_move, distanceX, distanceY, dragging, 'grab');
                    $.isFunction(options.onIng) && options.onIng(_e, _move, distanceX, distanceY);
                }
            }
        })
        .on('touchend touchcancel pointerup MSPointerUp', touch_selector, function (e) {
            var _move = options.start_selector ? (options.selector ? _e.find(options.selector) : _e.find(options.start_selector)) : $(this);
            if (dragging && !is_stop) {
                cssTransition(_move, 0, 0, "null", '');
                $.isFunction(options.inEnd) && options.inEnd(_e, _move, distanceX, distanceY);
                if (Math.abs(distanceX) > dep || Math.abs(distanceY) > dep) {
                    $.isFunction(options.onEnd) && options.onEnd(_e, _move, distanceX, distanceY);
                }
                startX = startY = endX = endY = angle = distanceX = distanceY = 0;
                dragging = !1;
            }
        });
}


//全局WIN变量
_win.bd = $('body');
_win.is_signin = !!_win.bd.hasClass('logged-in');

_win.bd.on('click', '[data-close]', function () {
    var e = $(this).attr('data-close')
    return $(e).removeClass('show in'), !1;
})

_win.bd.on('click', '[data-toggle-class]', function () {
    var c = $(this).attr('data-toggle-class') || 'show';
    var e = $(this).attr('data-target') || this;
    return $(e).toggleClass(c).trigger('toggleClass'), !1;
})


//只允许一个dropup弹出框
_win.bd.on("click", only_drop);
_win.bd.on("toggleClass", '.dropup,.dropdown', only_drop);

function only_drop(e) {
    var drop = $(e.target).closest(".dropup,.dropdown");
    $('.dropup.open,.dropdown.open').not(drop).removeClass('open')
}


//按钮点击触发另一个按钮点击
_win.bd.on('click', "[data-onclick]", function () {
    var e = $(this).attr('data-onclick');
    return $(e).click();
});

//单次绑定事件，只能绑定一次事件
$.fn.OnlyOn = function (type, selector, data, fun) {
    var rnotwhite = (/\S+/g);
    var is_oned = false;
    //提取第一个绑定类型
    type = type.match(rnotwhite)[0];
    //获取目前已经绑定的全部事件
    var events = $._data(this[0], 'events');

    if (events && typeof events['type'] != 'undefined') {
        $.each(events[type], function (i, item) {
            if (item.selector == selector && item.data == data) {
                is_oned = true;
                return false;
            }
        })
    }
    if (!is_oned) {
        this.on(type, selector, data, fun);
    } else {
        console.log('事件重复绑定{type：' + type + ',selector:' + selector + ',data:' + data + '}');
    }
}

//倒计时功能
function countdown(_this) {
    var endtime = _this.attr('data-countdown');

    function getTimeRemaining(endtime) {

        var date = new Date();
        var now = date.getTime();
        var endDate = new Date(endtime); //设置截止时间
        var end = endDate.getTime();
        var total = end - now;

        var seconds = ((total / 1000) % 60);
        seconds = seconds.toFixed(2);
        seconds = seconds < 10 ? '0' + seconds : seconds;
        var mseconds = ~~(total % 1000);
        var minutes = ~~((total / 1000 / 60) % 60);
        var hours = ~~((total / (1000 * 60 * 60)) % 24);
        var days = ~~(total / (1000 * 60 * 60 * 24));

        return {
            total,
            days,
            hours,
            minutes,
            seconds,
            mseconds,
        };
    }

    function updateClock() {
        var t = getTimeRemaining(endtime);
        var html = t.days + '天' + t.hours + '小时' + t.minutes + '分' + t.seconds + '秒';
        _this.html(html);
        if (t.total <= 0) {
            clearInterval(timeinterval);
            var over_text = _this.attr('data-over-text') || '已结束';
            _this.html(over_text);
        }
    }

    updateClock();
    var timeinterval = setInterval(updateClock, 20);
}

//视频
_win.dplayer = {};

function new_dplayer() {
    var selector = '.new-dplayer:not(.dplayer-initialized),[data-dplayer]:not(.dplayer-initialized)'; //选择器
    if ($(selector).length) {
        $('link#dplayer').length || $('dplayer').append('<link type="text/css" id="dplayer" rel="stylesheet" href="' + _win.uri + '/css/dplayer.min.css?ver=' + _win.ver + '">');

        var dplayer_tbquire = [];

        $(selector).each(function (i) {
            var option = {};
            var _this = $(this);
            var video_url = _this.attr("video-url") || _this.data("video-url");
            var data_volume = _this.attr("data-volume");

            if (_this.find('.dplayer-video-wrap').length || !video_url) return;
            _this.attr("id", 'dplayer-' + i);
            option.container = document.getElementById('dplayer-' + i);
            option.theme = '#f04494';
            option.video = {
                url: video_url,
                pic: _this.attr("video-pic") || '',
                type: _this.attr("video-type") || 'auto',
            }
            _this.attr("data-loop") && (option.loop = true);
            _this.attr("data-autoplay") && (option.autoplay = true);
            if (data_volume && data_volume < 1) {
                option.volume = data_volume
            }

            var option_attr = _this.attr("video-option");
            if (option_attr) {
                try {
                    option_attr = JSON.parse(option_attr);
                } catch (e) {}
                $.extend(option, option_attr);
            }

            //第三方插件判断添加
            var v_u = video_url.toLowerCase();
            v_u.indexOf(".m3u") != -1 && dplayer_tbquire.push('hls');
            v_u.indexOf(".mpd") != -1 && dplayer_tbquire.push('dash');
            v_u.indexOf(".flv") != -1 && dplayer_tbquire.push('flv');

            dplayer_tbquire.push('dplayer');
            tbquire(dplayer_tbquire, function () {
                try {
                    _win.dplayer[i] = (new DPlayer(option))
                } catch (e) {}
            })

            //视频剧集
            _this.addClass('dplayer-initialized').siblings('.dplayer-featured').find('.switch-video').attr('dplayer-id', i);

        });
    }
}
new_dplayer();

//视频剧集
_win.bd.on('click', '.switch-video', function (e) {
    var _this = $(this);
    var dplayer_id = _this.attr('dplayer-id');
    var dplayer = _win.dplayer[dplayer_id];
    var video_url = _this.attr('video-url');
    if (!video_url || !dplayer || _this.hasClass('active')) return;
    _this.addClass('active').siblings().removeClass('active');
    //第三方插件判断添加
    var dplayer_tbquire = [];
    var v_u = video_url.toLowerCase();
    v_u.indexOf(".m3u") != -1 && dplayer_tbquire.push('hls');
    v_u.indexOf(".mpd") != -1 && dplayer_tbquire.push('dash');
    v_u.indexOf(".flv") != -1 && dplayer_tbquire.push('flv');
    tbquire(dplayer_tbquire, function () {
        try {
            dplayer.switchVideo({
                url: video_url,
                pic: _this.attr("video-pic") || '',
                type: 'auto',
            })
            dplayer.play();
        } catch (e) {}
    })
});

//文章古腾堡TAB
_win.bd.on('click', '.post-tab-toggle', function (e) {
    var _this = $(this);
    var tab_id = _this.attr('tab-id');
    if (_this.parent().hasClass('active')) return;
    var _con = _this.parent().addClass('active').siblings().removeClass('active').parent().next().find('[tab-id="' + tab_id + '"]');
    _con.siblings().removeClass('in');
    setTimeout(function () {
        _con.addClass('active').siblings().removeClass('active');
    }, 150);
    setTimeout(function () {
        _con.addClass('in')
    }, 160);
});

//幻灯片
_win.swiper = [];
_win.swiper.tab = {};
_win.swiper.scroll = {};
_win.swiper.new = {};

function new_swiper() {
    if ($('.swiper-scroll:not(.swiper-container-initialized),.new-swiper:not(.swiper-container-initialized),.swiper-tab:not(.swiper-container-initialized)').length) {
        $('link#swiper').length || $('head').append('<link type="text/css" id="swiper" rel="stylesheet" href="' + _win.uri + '/css/swiper.min.css?ver=' + _win.ver + '">');
        tbquire(['swiper'], function () {
            $('.swiper-scroll').each(function (e) {
                if ($(this).hasClass('swiper-container-initialized')) return;
                var option = {};
                var _this = $(this);
                var _eq = 'swiper-scroll-eq-' + e;
                var slideClass = _this.attr("data-slideClass") || false;
                slideClass && (option.slideClass = slideClass);

                if (!_this.attr('scroll-nogroup')) {
                    var c_w = _this.width();
                    var i_w = _this.find('.swiper-slide').outerWidth(true);
                    var slidesPerGroup = ~~(c_w / i_w);
                    option.slidesPerGroup = slidesPerGroup || 1;
                }

                option.slidesPerView = 'auto';
                option.mousewheel = {
                    forceToAxis: true,
                };
                option.freeMode = true;
                option.freeModeSticky = true;

                option.navigation = {
                    nextEl: '.swiper-scroll.' + _eq + ' .swiper-button-next',
                    prevEl: '.swiper-scroll.' + _eq + ' .swiper-button-prev',
                };

                _this.addClass(_eq).attr('swiper-scroll-index', e);
                _win.swiper.scroll[e] = (new Swiper('.swiper-scroll.' + _eq, option))
            })
            $('.swiper-tab').each(function (e) {
                if ($(this).hasClass('swiper-container-initialized')) return;
                var _eq = 'swiper-eq-' + e;
                var _this = $(this);

                var speed = ~~(_this.attr("data-speed")) || ~~((_this.width() + 1200) / 170) * 50;
                var initialSlide = ~~(_this.attr("active-index"));
                speed = speed < 300 ? 300 : speed;

                var option = {
                    loop: false,
                    initialSlide: initialSlide,
                    autoHeight: true,
                    spaceBetween: 20,
                    autoplay: false,
                    speed: speed,
                    pagination: {
                        el: '.swiper-tab.' + _eq + ' .swiper-pagination',
                        clickable: true,
                    },
                    on: {
                        init: function () {
                            auto_maxHeight();
                        },
                        slideChangeTransitionEnd: function (bvv) {
                            var b = $('.swiper-tab.' + _eq + ' .swiper-slide-active .post_ajax_trigger .ajax-open');
                            if (b.length) {
                                scrollTo($('.swiper-tab.' + _eq), -35);
                                b.click();
                            }

                            var r = bvv.onStart && $('[swiper-scroll-index="' + e + '"] .swiper-slide-thumb-active [data-route]');
                            if (r && r.length) {
                                routeGo(r.attr('data-route'))
                            }
                            bvv.onStart = true;
                        }
                    }
                };

                var tab_id = _this.attr("swiper-tab");
                var tab_nav = $('[swiper-tab-nav="' + tab_id + '"]');
                if (tab_nav.length) {
                    var tab_nav_index = tab_nav.attr("swiper-scroll-index");
                    option['thumbs'] = {
                        swiper: _win.swiper.scroll[tab_nav_index],
                        autoScrollOffset: 2,
                    };
                }

                _this.addClass(_eq).attr('swiper-tab-index', e);
                _win.swiper.tab[e] = (new Swiper('.swiper-tab.' + _eq, option));
            });
            $('.new-swiper').each(function (e) {

                if ($(this).hasClass('swiper-container-initialized')) return;

                var _eq = 'swiper-eq-' + e;
                var _this = $(this);
                var delay = ~~(_this.attr("data-interval")) || 6000;
                var autoplay = _this.attr("data-autoplay") ? {
                    delay: delay,
                    disableOnInteraction: false
                } : false;
                var auto_h = _this.attr("auto-height") ? true : false;
                var speed = ~~(_this.attr("data-speed")) || ~~((_this.width() + 1200) / 250) * 100;
                speed = speed < 400 ? 400 : speed;

                var loop = _this.attr("data-loop") ? true : false;
                var parallax = _this.find('[data-swiper-parallax],[data-swiper-parallax-scale]').length ? true : false;
                var effect = _this.attr("data-effect") || 'slide';
                var direction = _this.attr("data-direction") || 'horizontal';
                var spaceBetween = ~~(_this.attr("data-spaceBetween")) || 0;

                _this.addClass(_eq).attr('swiper-new-index', e);

                _win.swiper.new[e] = new Swiper('.new-swiper.' + _eq, {
                    loop: loop,
                    autoHeight: auto_h,
                    direction: direction,
                    spaceBetween: spaceBetween,
                    parallax: parallax,
                    effect: effect,
                    lazy: {
                        loadPrevNext: !0
                    },
                    autoplay: autoplay,
                    speed: speed,
                    pagination: {
                        el: '.new-swiper.' + _eq + ' .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.new-swiper.' + _eq + ' .swiper-button-next',
                        prevEl: '.new-swiper.' + _eq + ' .swiper-button-prev',
                    },
                    on: {
                        lazyImageReady: function (slideEl, imageEl) {
                            $(imageEl).addClass('lazyloaded');
                        },
                    }
                })
            });
        })
    }
}

window.swiper_tab_AutoHeight = debounce(function ($con) {
    var tab = $($con.parents('[swiper-tab-index]')[0]);
    if (tab.length) {
        var tab_index = tab.attr('swiper-tab-index');
        _win.swiper.tab[tab_index].updateAutoHeight(500);
    }
}, 100);


$.fn.scrollX = function () {
    return this.each(function () {
        var _this = $(this);
        var is_on = 'is-on';
        if (!_this.data(is_on)) {
            _this.data(is_on, true);
            var btn_class = 'scroll-x-';
            var prev_btn_class = btn_class + 'prev';
            var next_btn_class = btn_class + 'next';
            var prev = $('<div class="' + prev_btn_class + '" style="display: none;"></div>');
            var next = $('<div class="' + next_btn_class + '" style="display: none;"></div>');
            init();
        }

        function scroll(type) {
            var data = get_data();
            _this.animate({
                scrollLeft: (type === 'prev' ? data[0] : data[1])
            }, 300, 'linear', hover_show);
        }

        function init() {
            _this.after(prev).after(next);
            prev.on('click', function () {
                scroll('prev')
            });
            next.on('click', scroll);
            _this.parent().hover(hover_show, function () {
                prev.hide()
                next.hide()
            });
        }

        function hover_show() {
            var data = get_data();
            if (data[3]) {
                prev.show()
            } else {
                prev.hide()
            }
            if (data[3] < data[2]) {
                next.show()
            } else {
                next.hide()
            }
        }

        function get_data() {
            var _this_w = _this.innerWidth();
            var _this_scrollLeft = _this.scrollLeft();
            var max = ~~(_this.prop('scrollWidth') - _this_w);

            var prev_scroll = ~~(_this_scrollLeft - _this_w);
            prev_scroll = prev_scroll > 0 ? prev_scroll : 0;
            var next_scroll = ~~(_this_scrollLeft + _this_w);
            next_scroll = next_scroll > max ? max : next_scroll;

            return [prev_scroll, next_scroll, max, _this_scrollLeft];
        }
    })
}


//让tab栏目的高亮元素显示在中间
$.fn.Tabcenter = function () {
    return this.each(function () {
        var _this = $(this);
        var _active = _this.find('.active');
        if (_active.length) {
            var _this_w = _this.innerWidth();
            var _active_w = _active.innerWidth();
            var _this_scrollLeft = _this.scrollLeft();
            var _active_left = _active.position().left;
            var max = ~~(_this.prop('scrollWidth') - _this_w);

            var _yd = ~~((_this_scrollLeft + _active_left) - (_this_w / 2) + (_active_w / 2));

            _yd = _yd > 0 ? _yd : 0;
            _yd = _yd > max ? max : _yd;

            if (_yd != _this_scrollLeft) {
                _this.animate({
                    scrollLeft: _yd
                }, 300)
            }
        }
    })
}

_win.bd.on('shown.bs.tab', 'a[data-ajax]', function (e) {
    $($(this).parents('.tab-nav-theme,.tab-nav-but,.index-tab>ul')[0]).Tabcenter()
});
$('.tab-nav-theme,.tab-nav-but,.index-tab>ul').Tabcenter();

/**
 * @description: AJAX获取包装函数
 * @param {*} _this 传入点击按钮的自己,需要有href或ajax-href下一页的链接(必须)
 * @param {*} con 需要插入的父元素选择器 (必须)
 * @param {*} jcon 获取内容的父元素选择器 (必须)
 * @param {*} item 获取的列表选择器   (必须)
 * @param {*} loader 加载动画的内容 （非必须，有默认值）
 * @param {*} pag 获取的分页内容选择器 （必须）
 * @param {*} next 如果需要重新插入下一页链接：获取分页内容中的下一页 选择器
 * @param {*} trigger 将获取的下一页链接从新插入到的新的 按钮中-的class值
 * @param {*} replace 替换列表内容而不是追加|'ajax-replace'
 * @param {*} nomore 全部加载完成之后的文案
 * @param {*} top 将内容追加到顶部而不是底部
 * @return {*}
 */
function post_ajax(_this, con, jcon, item, loader, pag, next, trigger, replace, nomore, top, ajax_trigger) {
    //准备参数
    var $con = $(_this.parents(con)[0]);
    if ($con.attr('ing')) return !1;
    var $loader = $con.find('.post_ajax_loader');
    var $item = $con.find(item);
    var href = _this.attr("ajax-href") || _this.attr("href") || _this.find('a').attr("ajax-href") || _this.find('a').attr("href");
    jcon = jcon || con;
    ajax_trigger = ajax_trigger || (_win.ajax_trigger ? _win.ajax_trigger : '<i class="fa fa-arrow-right"></i>加载更多');
    replace = _this.attr('ajax-replace') || replace;
    nomore = nomore || _win.ajax_nomore;
    if ($loader.length) {
        loader = $loader.last().prop("outerHTML");
    } else {
        loader = loader || '<div class="theme-box box-body ajax-loading text-center"><h2 class="loading zts"></h2></div>';
        loader = '<div class="post_ajax_loader">' + loader + "</div>";
    }

    href && $.ajax({
        type: "GET",
        url: href,
        dataType: "html",
        beforeSend: function () {
            if (replace) {
                $con.find(item).remove();
            }

            $con.attr('ing', true).find(".post_ajax_trigger,.no-more,.post_ajax_loader,.noajax-pag," + pag).remove();
            if ($con.find(item).length) {
                top ? $item.first().before(loader) : $item.last().after(loader);
            } else {
                $con.append(loader);
            }
            $con.find(".post_ajax_loader").fadeIn();
            swiper_tab_AutoHeight($con);
        },
        success: function (a) {
            a = $.parseHTML(a);
            $item = $con.find(item);
            var $jcon = $(a).find(jcon);
            var c_c = $jcon.find(item); //列表
            var c_p = $jcon.find(pag); //下一页
            var n_h = c_p.find(next).attr("href") || c_p.find(next).find('a').attr("href"); //下一页链接
            c_p = c_p.length ? c_p : '<div class="text-center mb20 padding-h10 muted-2-color no-more separator">' + nomore + '</div>'; //是否还有下一页
            c_p = $jcon.find('.noajax-pag').length ? $jcon.find('.noajax-pag') : c_p;
            // console.log(c_c, c_p, n_h);
            //全局替换
            $(a).find('[win-ajax-replace]').each(function () {
                var replace_key = $(this).attr('win-ajax-replace');
                var replace_e = $('[win-ajax-replace="' + replace_key + '"]');
                if (replace_e.length) {
                    replace_e.html($(this).html())
                }
            })

            if (trigger) {
                //将获取的下一页链接重新插入到的新的 按钮中
                c_p = "undefined" != typeof n_h ? '<span class="post_ajax_trigger"><a class="' + trigger + '" href="' + n_h + '">' + ajax_trigger + "</a></span>" : c_p
            }

            $con.find(".post_ajax_trigger,.post_ajax_loader").remove(); //删除老的下一页链接/加载动画
            if ($item.length) {
                //原本有列表则追加
                if (top) {
                    //追加方向为顶部
                    $item.first().before(c_c);
                    $con.find(item).first().before(c_p);
                } else {
                    //追加方向为底部
                    $item.last().after(c_c);
                    $con.find(item).last().after(c_p);
                }
            } else {
                //原本无列表则添加
                if (top) {
                    $con.append(c_p).append(c_c);
                } else {
                    $con.append(c_c).append(c_p);
                }
            }

            $con.append(loader).removeAttr('ing').trigger('post_ajax.ed', $(a)).find(">.post_ajax_loader").hide();

            if (!_this.attr('no-scroll')) {
                var _scroll_To = replace ? $con : $(c_c[0]);
                scrollTo(_scroll_To, -40);
            }

            swiper_tab_AutoHeight($con);
            auto_fun();
        }
    })
    return !1;
}

_win.bd.on('click', '[remote-box]', function (e) {
    var _this = $(this);
    var remote = _this.attr('remote-box');
    if (remote) {
        $.get(remote, null, function (data, status) {
            _this.html(data).removeAttr('remote-box');
            auto_fun();
        });
    }
});

_win.bd.on('show.bs.tab', 'a[data-ajax]', function (e) {
    var _this = $(this);
    var a = e.target.hash;
    var selector = a + ' .post_ajax_trigger .ajax-next';
    var b = $(selector).attr('ajax-replace', true);
    if (b.length) {
        b.click();
    } else {
        var href = _this.attr('data-ajax');
        if (href) {
            var trigger = '<span class="post_ajax_trigger hide"><a ajax-href="' + href + '" class="ajax_load ajax-next ajax-open" ajax-replace="true"></a></span>';
            $(a).append(trigger);
            $(selector).click();
        }
    }
    if (_this.attr('only-one')) {
        _this.removeAttr('data-ajax');
    }
});

_win.bd.on('click', "[ajax-tab],[ajax-target]", function () {
    var _this = $(this);
    var replace = _this.attr('ajax-replace') ? ' ajax-replace="true"' : '';
    var target = _this.attr('ajax-tab') || _this.attr('ajax-target');
    var href = _this.attr('data-ajax');
    var trigger = '<span class="post_ajax_trigger hide"><a ajax-href="' + href + '" class="ajax_load ajax-next ajax-open"' + replace + '></a></span>';
    $(target).append(trigger);
    $(target + ' .post_ajax_trigger .ajax-next').click();
    $('a[href="' + target + '"]').click();
});

//文章手动ajax
_win.bd.on('click', ".ajax-next", function () {
    var _this = $(this);
    var _loader = '<div class="text-center muted-2-color mt20"><i class="loading mr10"></i>加载中...</div>';
    if (_this.attr('ajax-replace')) {
        _loader = '<div class="posts-item flex"><div class="post-graphic"><div class="radius8 item-thumbnail placeholder"></div> </div><div class="item-body flex xx flex1 jsb"> <p class="placeholder t1"></p> <h4 class="item-excerpt placeholder k1"></h4><p class="placeholder k2"></p><i><i class="placeholder s1"></i><i class="placeholder s1 ml10"></i></i></div></div>';
        _loader += _loader;
        _loader += _loader;
    }
    _loader = '<div class="mb20">' + _loader + '</div>';
    return post_ajax(_this, '.ajaxpager', '.ajaxpager', '.ajax-item', _loader, '.ajax-pag');
});

var _wid = $(window).width();
var _hei = $(window).height();


if (_wid < 768 && $('.drawer-sm').length) {
    _win.bd.on('click', '[data-drawer]', function (e) {
        var _this = $(this);
        var title = _this.attr('drawer-title') || '';
        var drawer_selector = _this.attr('drawer-selector') || '.drawer-sm';
        var action = _this.attr('data-drawer');
        var route = _this.attr('route');
        var route_back = _this.attr('route-back');
        $(drawer_selector).Drawer(action, title, route, route_back);
    });

    var this_url = (window.parent.location.origin + window.parent.location.pathname).replace(/(\/*$)/g, "");
    _win.url_request = window.parent.location.search; //兼容支付的GetRequest函数
    setTimeout(function () {
        $('[data-drawer][route="' + this_url + '"]').click();
    }, 20)
} else {
    _win.bd.on('click', '[data-drawer][route]', tabOnRoute);
}

_win.bd.on('shown.bs.tab', '[data-route]', tabOnRoute);

function tabOnRoute() {
    var _this = $(this);
    if (_this.attr('onpopstate')) {
        _this.attr('onpopstate', false);
    } else {
        var route = _this.attr('route') || _this.attr('data-route');
        var tab_id = _this.attr('href');
        route && routeGo(route, {
            tab_id: tab_id,
        });
    }
}

window.onpopstate = function (event) {
    var tab = event.state && $('[data-toggle="tab"][href="' + event.state.tab_id + '"]');
    if (tab && tab.length) {
        tab.attr('onpopstate', true).click();
    } else {
        location.reload()
    }
};

/**
 * @description: 设置浏览器网址
 * @param {*} url
 * @param {*} title
 * @return {*}
 */
function routeGo(url, data, title) {
    title = title || document.title;
    if (url) {
        history.pushState(data, title, url);
    }
}


$.fn.Drawer = function (action, title, route, route_back) {
    title = title || '';
    var _this = $(this);

    if (!_this.length) return;

    action = (action == 'show' || action == 'hide') ? action : (_this.hasClass('show') ? 'hide' : 'show');
    var _body = $('body');

    if (!_this.attr('on-start')) {
        _this.attr('on-start', true).minitouch({
            direction: 'right',
            onEnd: function (e) {
                _this.Drawer('hide');
            }
        })
    }

    switch (action) {
        case "show":
            var header = $('<div class="drawer-header flex ac"><div class="drawer-close"><i class="fa fa-angle-left em12"></i></div><div class="drawer-title">' + title + '</div></div>').on('click', '.drawer-close', function () {
                _this.Drawer('hide');
            })
            _body.append(header);
            setTimeout(function () {
                _body.addClass('drawer-show');
            }, 10)
            route_back && _this.data('route_back', route_back);
            route && routeGo(route);
            return _this.addClass('show');
        case "hide":
            _body.removeClass('drawer-show');
            setTimeout(function () {
                $('.drawer-header').remove();
            }, 400)
            routeGo(_this.data('route_back'));
            return _this.removeClass('show');
        default:
    }

}


//文章幻灯片
if ($('.wp-block-carousel').length) {
    if (!$("link#swiper").length) {
        $('head').append('<link type="text/css" id="swiper" rel="stylesheet" href="' + _win.uri + '/css/swiper.min.css?ver=' + _win.ver + '">');
    }
    var _sc = $('.wp-block-carousel');
    var Sw = [];

    tbquire(['swiper'], function () {
        _sc.each(function (si) {

            var _this = $(this);
            var _eq = 'swiper-post-' + si;

            if (_this.find('.wp-block-gallery>.blocks-gallery-grid').length) {
                _this.find('.wp-block-gallery').html(_this.find('.wp-block-gallery>.blocks-gallery-grid').html())
            }

            _this.find('.wp-block-gallery').removeClass().addClass('swiper-wrapper').find('.blocks-gallery-item,>.wp-block-image').addClass('swiper-slide');
            _this.find('.carousel-control.left').replaceWith('<div class="swiper-button-next"></div>');
            _this.find('.carousel-control.right').replaceWith('<div class="swiper-button-prev"></div><div class="swiper-pagination"></div>');

            var _ss = _this.find('.carousel');
            var proportion = _ss.attr('proportion') || 0.6;
            var style = _ss.attr('style');

            _ss.addClass(_eq + ' new-swiper scale-height').attr('style', '--scale-height:' + (proportion * 100) + '%;');
            _this.attr('style', style);

            var delay = ~~(_ss.attr("data-interval")) || 6000,
                loop = _ss.attr("data-jyloop") || false,
                effect = _ss.attr("data-effect") || 'slide';

            var speed = ~~((_ss.width() + 1200) / 250) * 100;

            Sw['swiper_wz_' + si] = new Swiper('.wp-block-carousel .' + _eq, {
                spaceBetween: 10,
                speed: speed,
                loop: loop,
                effect: effect,
                autoplay: {
                    delay: delay,
                    disableOnInteraction: false
                },
                pagination: {
                    el: '.' + _eq + ' .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.' + _eq + ' .swiper-button-next',
                    prevEl: '.' + _eq + ' .swiper-button-prev',
                },
            })
        })
    })
}

/**模态框居中 */
_win.bd.on("show.bs.modal loaded.bs.modal", ".modal:not(.flex)", function () {
    var o = $(this);
    var i = o.find(".modal-dialog");
    o.css("display", "block"), i.css({
        "margin-top": Math.max(0, (_hei - i.height()) / 2)
    });
});

_win.bd.on("show.bs.modal loaded.bs.modal", ".modal", function () {
    auto_fun();
});

//社交帐号解绑
_win.bd.on("click", '.oauth-untying', function () {
    if (confirm("确认要解除帐号绑定吗？") == true) {
        var _this = $(this),
            data = {
                'action': 'user_oauth_untying',
                'user_id': _this.attr('user-id'),
                'type': _this.attr('untying-type'),
            };
        return zib_ajax(_this, data), !1;
    }
})

//为用户授予勋章
_win.bd.on("click", '.medal-add-link', function () {
    if (confirm("确认要授予此徽章吗？") == true) {
        return zib_ajax($(this)), !1;
    }
})

//为用户收回勋章
_win.bd.on("click", '.medal-remove-link', function () {
    if (confirm("确认要收回此徽章吗？") == true) {
        return zib_ajax($(this)), !1;
    }
})

//打开对应的tab
_win.bd.on("click", '[tab-id]', function () {
    var tab = $(this).attr('tab-id');
    tab && $('a[href="#' + tab + '"]').tab('show');
})

//每次都刷新的模态框
_win.bd.on("click", '[data-toggle="RefreshModal"]', function () {
    var _this = $(this);
    var dataclass = _this.attr('data-class') || '';
    var remote = _this.attr('data-remote');
    var height = _this.attr('data-height') || 300;
    var mobile_bottom = _this.attr('mobile-bottom') && _wid < 769 ? ' bottom' : '';
    var modal_class = 'modal flex jc fade' + mobile_bottom;
    var id = 'refresh_modal';
    var is_new = _this.attr('new');
    id += is_new ? parseInt((Math.random() + 1) * Math.pow(10, 4)) : '';

    var _id = '#' + id;

    dataclass += ' modal-dialog';
    var modal_html = '<div class="' + modal_class + '" id="' + id + '" tabindex="-1" role="dialog" aria-hidden="false">\
    <div class="' + dataclass + '" role="document">\
    <div class="modal-content">\
    </div>\
    </div>\
    </div>\
    </div>';

    var loading = '<div class="modal-body" style="display:none;"></div><div class="flex jc loading-mask absolute main-bg radius8"><div class="em2x opacity5"><i class="loading"></i></div></div>';

    var _modal = $(_id);
    if (_modal.length) {
        if (_modal.hasClass('in')) modal_class += ' in';
        _modal.removeClass().addClass(modal_class);
        _modal.find('.modal-dialog').removeClass().addClass(dataclass);
        _modal.find('.loading-mask').fadeIn(200);
        _modal.find('.modal-content').css({
            'overflow': 'hidden'
        }).animate({
            'height': height,
        });
    } else {
        _win.bd.append(modal_html);
        _modal = $(_id);
        if (is_new) {
            _modal.on('hidden.bs.modal', function () {
                $(this).remove();
            })
        }
        _modal.find('.modal-content').html(loading).css({
            'height': height,
            'overflow': 'hidden'
        });
        if (_wid < 769) {
            _modal.minitouch({
                direction: 'bottom',
                selector: '.modal-dialog',
                start_selector: '.modal-colorful-header,.touch-close,.touch',
                onEnd: function (e) {
                    _modal.modal('hide');
                },
                stop: function (e) {
                    return !_modal.hasClass('bottom');
                },
            })
        }
    }
    _modal.find('.touch-close').remove();
    var touch_close = '<div class="touch-close"></div>';
    if (mobile_bottom && !_this.attr('no-touch')) {
        _modal.find('.modal-dialog').append(touch_close);
    }

    _modal.modal('show');

    $.get(remote, null, function (data, status) {
        _modal.find('.modal-body').html(data).slideDown(200, function () {
            _modal.trigger('loaded.bs.modal').find('.loading-mask').fadeOut(200);
            var b_height = $(this).outerHeight();
            _modal.find('.modal-content').animate({
                'height': b_height,
            }, 200, 'swing', function () {
                _modal.find('.modal-content').css({
                    'height': '',
                    'overflow': '',
                    'transition': ''
                })
            });
        });
    });

    return false;
});

window.auto_maxHeight = debounce(z_auto, 100);

function z_auto() {
    _win.do_hh = document.documentElement.clientHeight;
    if (_win.do_hh > document.body.clientHeight) {
        var min_h = _win.do_hh - $('.footer').outerHeight() - $('.header').outerHeight() - 20;
        $('main').animate({
            'min-height': min_h,
        });
    }
}

window.auto_fun = debounce(z_auto_fun, 100);

function z_auto_fun() {
    //textarea自动高度
    $('textarea[autoHeight]').autoHeight();

    auto_maxHeight();
    //兼容性图片懒加载
    $("img[data-src]:not(.lazyload,.lazyloaded,.lazyloading,.swiper-lazy)").addClass('lazyload');
    //支付功能
    $(".initiate-pay,.cashier-link,.pay-vip").length && tbquire(["pay"]);

    if (window.parent.location.search.indexOf('zippay')) {
        tbquire(["pay"])
    }

    //消息功能
    $(".from-private,.msg-center").length && tbquire(["message"], function () {
        scroll_down();
    });
    $(".dropdown-smilie,.dropdown-code,.dropdown-image").length && tbquire(["input-expand"]);
    /**上传图片 */
    $('[zibupload]').length && tbquire(["mini-upload"]);

    //海报分享
    $('[poster-share]').length && tbquire(["poster-share"]); //海报分享

    //人机验证
    $('[machine-verification]').length && tbquire(["captcha"]); //人机验证

    /* 提示工具*/
    $("[data-toggle='tooltip']").tooltip({
        container: 'body',
    });

    //登录注册
    $('.signsubmit-loader').length && tbquire(["sign-register"]);

    //复制内容
    $('[data-clipboard-text]').length && tbquire(["clipboard"]);

    $("[data-toggle='popover']").popover({
        sanitize: false,
    });

    //浮动功能
    if (_wid > 768) {
        $('.fixed-wrap-nav').autoAffix(20, 38);
        $('.relative>.scroll-x.no-scrollbar').scrollX();
    } else {
        $('.affix-header-sm').autoAffix(0, 20);
    }
    if (_wid > 900) {
        $('.sidebar').sidebarAffix();
    }

    //幻灯片检测
    new_swiper();

    //视频检测
    new_dplayer();

    // SVG-图标
    tbquire(["svg-icon"], () => {
        show_svg()
    });

    //高亮代码
    var _h_e = _win.highlight_kg ? 'pre code:not(.enlighter-origin)' : 'pre code.gl:not(.enlighter-origin),pre code.special:not(.enlighter-origin)';
    $(_h_e).length && tbquire(["enlighterjs"], function () {
        var lin = _win.highlight_hh ? !0 : !1;
        $("link#enlighterjs").length || $("head").append('<link type="text/css" rel="stylesheet" href="' + _win.uri + '/js/enlighter/enlighterjs.min.css?ver=' + _win.ver + '" id="enlighterjs">');
        $(_h_e).enlight({
            linenumbers: lin,
            indent: 2,
            textOverflow: 'scroll',
            rawcodeDbclick: !0,
            rawButton: !1,
            infoButton: !1,
            windowButton: !1,
            theme: _win.highlight_zt,
        });
    });

    //图片灯箱
    ($('[data-imgbox],.comment-content .box-img,.wp-posts-content img,.alone-imgbox-img').length && _win.imgbox) && tbquire(["imgbox"]);
}

//页面滚动监听函数
$(window).scroll(function () {
    var h = document.documentElement.scrollTop + document.body.scrollTop,
        ontop = $('.ontop');
    h > 100 ? _win.bd.addClass('body-scroll') : _win.bd.removeClass('body-scroll');
    h > 400 ? ontop.addClass('show') : ontop.removeClass('show');
})

$(window).scroll(debounce(function () {
    $("[data-toggle='tooltip']").tooltip('hide');
}, 500, true));

if ($('.scrolling-hide').length) {
    $(window).scroll(debounce(function () {
        _win.bd.addClass('scroll-ing');
    }, 500, true)).scroll(debounce(function () {
        _win.bd.removeClass('scroll-ing');
    }, 500));
}

//滚动执行函数
function scrollTo(o, t = 0, l = 300) {
    var scrollTop = 0;
    var _body = $('body,html');

    if (o) {
        var _o = (o instanceof jQuery) ? o : $(o);
        scrollTop = (_o.length ? _o.offset().top + t - (_body.hasClass("nav-fixed") ? $(".header").innerHeight() + 20 : 0) : 0);
    }
    _body.animate({
        scrollTop: scrollTop
    }, l, 'swing')
}

_win.bd.on('click', '.toggle-radius,.float-right a,.but-ripple,.but,.item-thumbnail,.menu-item >a,.yiyan-box,.relates-thumb li a', function (e) {
    var _th = $(this);
    if (!_th.hasClass('nowave')) {
        _th.css({
            "overflow": "hidden",
            "position": "relative"
        })
        var R;
        var waveWidth = ~~(_th.outerWidth());
        var waveHeight = ~~(_th.outerHeight());
        if (waveWidth < waveHeight) {
            R = waveHeight;
        } else {
            R = waveWidth;
        }
        var cllor = _th.css('color') || "rgba(200, 200, 200)";
        var wave = $("<div></div>").css({
            "display": "block",
            //涟漪的颜色
            "background": cllor,
            "border-radius": "50%",
            "position": " absolute",
            "-webkit-transform": "scale(0)",
            "transform": "scale(0)",
            "opacity": ".3",
            //涟漪的速度
            "-webkit-transition": "all 1.5s cubic-bezier(0.22, 0.61, 0.36, 1)",
            "transition": "all 1.5s cubic-bezier(0.22, 0.61, 0.36, 1)",
            "z-index": "1",
            "overflow": "hidden",
            "pointer-events": "none",
        });
        _th.append(wave);
        wave.css({
            "width": (R * 2) + "px",
            "height": (R * 2) + "px",
            "top": (e.pageY - _th.offset().top - R) + 'px',
            "left": (e.pageX - _th.offset().left - R) + 'px',
            "transform": "scale(1)",
            "-webkit-transform": "scale(1)",
            "opacity": 0,
        });
        setTimeout(function () {
            wave.remove()
        }, 2000);
    }
});

//侧栏浮动
$.fn.sidebarAffix = function () {
    var _this = $(this);
    var _affix = _this.find('[data-affix]');
    if (!_affix.length) return _this;
    var _body = $('body');
    var nav_Height = _body.hasClass("nav-fixed") ? $(".header").outerHeight(true) : 20
    var affix_width = _affix.innerWidth();
    var sidebarAffix = _this.children('.sidebar-affix');

    function get_offset() {
        var _wrap = _this.parent();
        bottom = document.body.clientHeight - _wrap.outerHeight(true) - _wrap.offset().top;
        return {
            top: _this.outerHeight(true) + _this.offset().top - nav_Height,
            bottom: bottom + 2
        }
    }

    if (_this.children('.sidebar-affix').length) {
        sidebarAffix.data('bs.affix').options.offset = get_offset();
        $(window).trigger('scroll');
        return _this;
    }

    var offset = get_offset();
    var _clone = _affix.clone();

    sidebarAffix = $('<div class="sidebar-affix" style="width:' + affix_width + 'px;"></div>').append(_clone);
    _this.prepend(sidebarAffix);

    sidebarAffix.affix({
        offset: offset
    }).on("affix.bs.affix", function () {
        sidebarAffix.css({
            top: nav_Height,
        })
    }).find('[lazyload-action]:not(.lazyload)').addClass('lazyload');
    if (sidebarAffix.hasClass('affix')) {
        sidebarAffix.css({
            top: nav_Height,
        })
    }
    $(window).trigger('scroll');
    return _this.data('is.on')
}

//内容浮动
$.fn.autoAffix = function (top, bottom, auto_m) {
    top = ~~(top);
    bottom = ~~(bottom);
    var _this = $(this);
    if (!_this.length) return;
    var _body = $('body');
    var offset_top = ~~(_this.attr('offset-top')); //偏移
    var _wrap_attr = _this.attr('data-wrap');
    var _wrap = _wrap_attr ? $(_wrap_attr) : _this.parent();

    function get_nav_Height() {
        return (_body.hasClass("nav-fixed") ? $(".header").outerHeight() : 0) + top;
    }

    function get_offset() {
        return {
            top: _this.parent().offset().top - get_nav_Height() + offset_top,
            bottom: document.body.clientHeight - _wrap.outerHeight() - _wrap.offset().top + bottom
        };
    }


    if (_this.data('bs.affix')) {
        _this.data('bs.affix').options.offset = get_offset();
        return _this.trigger('scroll.bs.affix.data-api');
    }

    _wrap.css('min-height', _this.outerHeight() + bottom);

    _this.affix({
        offset: get_offset()
    }).on("affix-top.bs.affix", function () {
        _this.css({
            top: '',
            width: '',
        })
        auto_m && _this.next().css({
            'margin-top': '',
        })
    }).on("affix.bs.affix", function () {
        var set_css = {
            top: get_nav_Height(),
        }
        if (auto_m) {
            set_css.width = _this.width();
            _this.next().css({
                'margin-top': _this.outerHeight(),
            })
        }
        _this.css(set_css)
    });

    if (_this.hasClass('affix')) {
        _this.css({
            top: get_nav_Height(),
        })
    }
    return _this.trigger('scroll.bs.affix.data-api');
}

//主题切换
$('.toggle-theme').click(function () {
    var a = $('body').hasClass('dark-theme') ? !0 : !1;
    var highlight_white_zt = 'enlighter-t-' + _win.highlight_white_zt;
    var highlight_dark_zt = 'enlighter-t-' + _win.highlight_dark_zt;

    $('img[switch-src]').each(function () {
        var _this = $(this);
        var _src = _this.attr('data-src') || _this.attr('src');
        var _s_src = _this.attr('switch-src');
        _this.attr('src', _s_src).attr('switch-src', _src).attr('data-src', '');
    })

    var _enlighter = $('.enlighter-default');
    var _body = $('body');
    var _tinymce_body = $("#post_content_ifr").contents().find('body');

    if (!a) {
        _tinymce_body.addClass('dark-theme')
        _enlighter.addClass(highlight_dark_zt).removeClass(highlight_white_zt);
        _body.addClass('dark-theme'), $.cookie("theme_mode", 'dark-theme', {
            path: '/'
        });
    } else {
        _tinymce_body.removeClass('dark-theme')

        _enlighter.addClass(highlight_white_zt).removeClass(highlight_dark_zt);
        _body.removeClass('dark-theme'), $.cookie("theme_mode", 'white-theme', {
            path: '/'
        });
    }
})


/*==============点赞===收藏===关注===========*/
_win.bd.on('click', '[data-action]', function () {
    var _this = $(this);
    var s = _this.attr("data-pid");
    var key = _this.attr("data-action");
    var type = key;
    var _type = 'zibll' + type;
    var data = {
        type: type,
        key: key,
        pid: s
    };
    if (!_win.is_signin) {
        var t = lcs.get(_type) || "";
        if (-1 !== t.indexOf("," + s + ",")) return notyf("已赞过此" + (type == 'like' ? '文章' : '评论') + "了！", "warning");
        t ? t.length >= 160 ? (t = t.substring(0, t.length - 1), t = t.substr(1).split(","),
            t.splice(0, 1), t.push(s), t = t.join(","), lcs.set(_type, "," + t + ",")) : lcs.set(_type, t + s + ",") : lcs.set(_type, "," + s + ",");

    }
    action_ajax(_this, data, s, type, '已赞！感谢您的支持')
})

function action_ajax(_this, data, pid, type, text) {
    var c = text || "处理完成";
    $.ajax({
        type: "POST",
        url: _win.uri + "/action/action.php",
        dataType: "json",
        data: data,
        beforeSend: function () {
            _this.find("count").html('<i class="loading zts"></i>')
        },
        success: function (n) {
            // console.log(n);
            var ys = (n.error ? 'danger' : "");
            if (n.action && n.action == "remove") {
                _this.removeClass('actived action-animation');
                ys = 'warning';
            }
            if (n.action && n.action == "add") {
                _this.addClass('actived action-animation')
            }
            notyf(n.msg || c, ys);
            _this.find("count").html(n.cuont || '0');
            if (type == "follow_user") {
                $('[data-action="follow_user"][data-pid="' + pid + '"]').each(function () {
                    $(this).find("count").html(n.cuont);
                })
            }
        }
    })
}

//登录注册
_win.bd.on('click', '.signin-loader', function () {
    if (_win.sign_type == 'page') {
        window.location.href = _win.signin_url;
        window.location.reload;
    } else {
        $('.modal:not(#u_sign)').modal('hide');
        $('#u_sign').modal('show');

        if (_win.signin_wx_priority) {
            $('a[href="#tab-qrcode-signin"]').tab('show')
            $($('.social-login-item.weixingzh')[0]).click();
        } else {
            $('a[href="#tab-sign-in"]').tab('show');
        }
    }
})

_win.bd.on('click', '.signup-loader', function () {
    if (_win.sign_type == 'page') {
        window.location.href = _win.signup_url;
        window.location.reload;
    } else {
        $('.modal:not(#u_sign)').modal('hide');
        $('#u_sign').modal('show');
        $('a[href="#tab-sign-up"]').tab('show')
    }
})

//扫码登录、绑定
_win.bd.on('click', '.qrcode-signin', function () {
    $('.modal:not(#u_sign)').modal('hide');
    $('#u_sign').modal('show');
    $('a[href="#tab-qrcode-signin"]').tab('show')

    var _this = $(this);
    var url = _this.attr('href');
    var container = $('.qrcode-signin-container');
    if (container.find('[get-only-one]').length) {
        container.find('[name="code"]').val('').trigger('change');
        return !1;
    }

    container.html('<p class="placeholder" style="height:180px;width:180px;margin:auto;"></p><p class="placeholder" style="height:27px;width:200px;margin:15px auto 0;"></p>');

    $.post(url, null, function (n) {
        n || notyf('二维码获取失败，请稍后再试', 'danger');
        var ys = (n.ys ? n.ys : (n.error ? 'danger' : ""));
        n.msg && notyf(n.msg, ys);
        if (n && n.html) {
            container.html(n.html);
            _win.qrcode_signin = {
                url: n.url,
                state: n.state,
            };
            if (!_win.check_login) {
                //仅执行一次
                checkLogin();
                _win.check_login = !0;
            }
        }
    }, "json");
    return !1;
})

//循环查询确认登录
function checkLogin() {
    var url = _win.qrcode_signin.url;
    var state = _win.qrcode_signin.state;
    if (!url || !state) return;
    $.post(url, {
        state: state,
        oauth_rurl: window.location.href,
        action: "check_callback"
    }, function (n) {
        //做逻辑判断，登录跳转
        if (n && n.goto) {
            window.location.href = n.goto;
            window.location.reload;
        } else {
            setTimeout(function () {
                checkLogin();
            }, 2000);
        }
    }, "json");
}

//模态框关闭停止查询登录
_win.bd.on("hide.bs.modal", ".modal", function () {
    _win.check_login = false;
    _win.check_pay = false;
    _win.qrcode_signin = {
        url: false,
        state: false,
    }
});

//防抖函数
function throttle(fn, delay) {
    let valid = true
    return function (...args) {
        if (!valid) {
            //休息时间 暂不接客
            return false
        }
        // 工作时间，执行函数并且在间隔期内把状态位设为无效
        valid = false
        setTimeout(() => {
            fn.apply(this, args)
            valid = true;
        }, delay)
    }
}

/**
 * @description: 节流函数
 * @param {*} callback 函数
 * @param {*} delay 时间
 * @param {*} immediate  是否立即执行 为true则计时开始就就执行
 * @return {*}
 */
function debounce(callback, delay, immediate) {
    var timeout;
    return function () {
        var context = this,
            args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) {
                callback.apply(context, args);
            }
        };
        var callNow = (immediate && !timeout);
        clearTimeout(timeout);
        timeout = setTimeout(later, delay);
        if (callNow) {
            callback.apply(context, args);
        }
    };
}

//textarea自动高度
$.fn.autoHeight = function () {
    function autoHeight(elem) {
        //elem.scrollTop = 0; //防抖动
        var max = ~~($(elem).attr('maxHeight'));
        elem.style.height = 'auto';

        if (max > elem.scrollHeight) {
            elem.style.height = elem.scrollHeight + 'px';
            elem.style.overflow = 'hidden';
        } else {
            elem.style.height = max + 'px';
            elem.style.overflow = 'auto';
        }
    }
    return this.each(function () {
        var _this = $(this);
        autoHeight(this);
        if (!_this.data('is-on')) {
            _this.data('is-on', true).on('input focus', function () {
                autoHeight(this);
            });
        }
    });
}


//赞赏模态框
_win.bd.on('click', '.rewards', function () {
    $('#rewards-popover').modal('show');
})
//用户中心
_win.bd.hasClass("author") && tbquire(["author"]);

//文章导航
_win.bd.hasClass("page-template-postsnavs") && _win.bd.hasClass("logged-admin") && tbquire(["page-navs"]);
//前台编辑
$("#modal_admin_set").length && tbquire(["page-edit"]);
//评论
$("#commentform,#postcomments").length && tbquire(["comment"]);
//通用模板js文件
_win.bd.hasClass("page-template") && tbquire(["page-template"]);

//微信分享
window.WeChatShareDate && tbquire(["weixin-share"]);

//搜索关键词高亮
if (_win.bd.hasClass('search')) {
    var val = $('.search-key').text();
    try {
        var reg = eval('/' + val + '/i');
        $('.item-heading a,.item-excerpt,.forum-title a,.excerpt,.display-name').each(function () {
            $(this).html($(this).text().replace(reg, function (w) {
                return '<b class="focus-color">' + w + '</b>'
            }))
        })
    } catch (e) {}
}


//控制器
$.fn.dependency = function () {
    function checkBoolean(v) {
        switch (v) {
            case true:
            case 'true':
            case 1:
            case '1':
                v = true;
                break;

            case null:
            case false:
            case 'false':
            case 0:
            case '0':
                v = false;
                break;
        }
        return v;
    };

    function _number(val) {
        return Number(val)
    }

    function evalCondition(condition, val1, val2) {
        if (condition == '==') {
            return checkBoolean(val1) == checkBoolean(val2);
        } else if (condition == '!=') {
            return checkBoolean(val1) != checkBoolean(val2);
        } else if (condition == '>=') {
            return _number(val2) >= _number(val1);
        } else if (condition == '<=') {
            return _number(val2) <= _number(val1);
        } else if (condition == '>') {
            return _number(val2) > _number(val1);
        } else if (condition == '<') {
            return _number(val2) < _number(val1);
        } else if (condition == 'any') {
            if ($.isArray(val2)) {
                for (var i = val2.length - 1; i >= 0; i--) {
                    if ($.inArray(val2[i], val1.split(',')) !== -1) {
                        return true;
                    }
                }
            } else {
                if ($.inArray(val2, val1.split(',')) !== -1) {
                    return true;
                }
            }
        } else if (condition == 'not-any') {
            if ($.isArray(val2)) {
                for (var i = val2.length - 1; i >= 0; i--) {
                    if ($.inArray(val2[i], val1.split(',')) == -1) {
                        return true;
                    }
                }
            } else {
                if ($.inArray(val2, val1.split(',')) == -1) {
                    return true;
                }
            }
        }
        return false;
    };

    return this.each(function () {

        var $this = $(this),
            $fields = $this.find('[data-controller]');

        if ($fields.length) {
            var is_on = 'is-on';

            $fields.each(function () {

                var $field = $(this);
                if ($field.attr(is_on)) return;

                var controllers = $field.attr(is_on, true).data('controller').split('|'),
                    conditions = $field.data('condition').split('|'),
                    values = $field.data('value').toString().split('|');

                $.each(controllers, function (index, depend_id) {

                    var value = values[index] || '',
                        condition = conditions[index] || conditions[0] || '==';

                    $this.on('change', "[name='" + depend_id + "']", function (elem) {
                        var $elem = $(this);
                        var _type = $elem.attr('type');
                        var val2 = (_type == 'checkbox') ? $elem.is(':checked') : $elem.val();
                        var is_show = evalCondition(condition, value, val2);

                        $field.trigger('controller.change', is_show);
                        if (is_show) {
                            //  $field.slideDown(300)
                            $field.show()
                        } else {
                            $field.hide()
                            // $field.slideUp(300)
                        }
                    });
                });
            });
        }
    });
};
//控制器启动
_win.bd.on("loaded.bs.modal", ".modal", function () {
    $('.modal .dependency-box').dependency();
});
$('.dependency-box').dependency();

//搜索多选择
_win.bd.on('click', '[data-for]', function () {
    var _this = $(this);
    var _tt;
    var _for = _this.attr('data-for');
    var _f = _this.parents('form');
    var _v = _this.attr('data-value');
    var multiple = _this.attr('data-multiple');
    if (multiple) {
        _tt = '';
        var _input = '';
        if (!_this.hasClass('active')) {
            //添加
            if (_this.siblings('.active').length >= multiple) {
                return notyf('最多可选择' + multiple + '个', 'danger');
            }
        }
        _this.toggleClass('active');
        _this.parent().find('.active').each(function () {
            var _this_2 = $(this);
            _tt += _this_2.html();
            _input += '<input type="hidden" name="' + _for + '[]" value="' + _this_2.attr('data-value') + '">';
        });
        _f.find("input[name='" + _for + "[]']").remove();
        _f.append(_input);
    } else {
        _group = $(_this.parents('[for-group]')[0]);
        if (_group.length) {
            _group.find('[data-for="' + _for + '"]').removeClass('active');
        } else {
            _this.siblings().removeClass('active');
        }

        _this.addClass('active');
        _tt = _this.html();
        _f.find("input[name='" + _for + "']").val(_v).trigger('change');
    }

    _f.find("span[name='" + _for + "']").html(_tt);
    _f.find('input[name=s]').focus();
})


_win.bd.on('input', 'input[limit-min]', debounce(function () {
    var _this = $(this);
    var min = Number(_this.attr('limit-min'));
    var max = Number(_this.attr('limit-max'));
    var val = Number(_this.val());
    var text;

    var warning_class = 'limit-warning';
    _this.next('.' + warning_class).length || _this.after('<div class="limit-warning" style="display: none;"></div>');
    var _warning = _this.next('.' + warning_class);

    if (_this.val().length < 1) {
        _warning.html('').hide();
    } else if (val > max) {
        text = _this.attr('warning-max') || '最大可输入1$';
        _warning.html(text.replace('1$', max)).fadeIn(150);
    } else if (val < min) {
        text = _this.attr('warning-min') || '最小可输入1$';
        _warning.html(text.replace('1$', min)).fadeIn(150);
    } else {
        _warning.fadeOut(150);
    }
}, 500));

//搜索功能-恢复默认占位符
_win.bd.on('click', ".main-search [data-for]", function () {
    var _placeholder = $('.main-search').find('.scale-placeholder');
    if (_placeholder.length) {
        _placeholder.text(_placeholder.attr('default'));
    }
})

//搜索功能->删除多余的input
_win.bd.on('submit', ".main-search form", function () {
    var _this = $(this);
    var inputs = _this.serializeObject();
    $.each(inputs, function (k, v) {
        if ((!v || v == 'null') && k != 's') {
            _this.find('input[name="' + k + '"]').remove();
        }
    })
})

//搜索功能
_win.bd.on('click', ".main-search-btn", function () {
    var _this = $(this);
    var _search = $('.main-search').addClass('show');
    var _search_form = $('.main-search form');
    if (_search_form.length) {
        var attr_ = 'search-';
        var obj = {
            type: _this.attr(attr_ + 'type'),
            user: _this.attr(attr_ + 'user'),
            trem: _this.attr(attr_ + 'trem'),
        }

        $.each(obj, function (k, v) {
            if (v) {
                var _k = _search_form.find('input[name="' + k + '"]').val(v);
                var _data_for = _search_form.find('[data-for="' + k + '"][data-value="' + v + '"]').click();

                if (!_data_for.length && k == 'trem') {
                    var cat_name = _this.attr('trem-name');
                    var _cat_drop = _search_form.find('.cat-drop');

                    if (cat_name && _cat_drop.length) {
                        _cat_drop.append('<li data-for="trem" data-value="' + v + '"><a href="javascript:;">' + cat_name + '</a></li>');
                        _cat_drop.find('[data-value="' + v + '"]').click();
                    }
                }

                if (!_k.length) {
                    _search_form.append('<input type="hidden" name="' + k + '" value="' + v + '">');
                }
            }
        })

        var placeholder = _this.attr(attr_ + 'placeholder') || _search_form.find('.scale-placeholder').attr('default');
        _search_form.find('.scale-placeholder').text(placeholder);

        setTimeout(function () {
            _search_form.find('[name="s"]').focus();
        }, 100);
    }
});


/*菜单*/
$(".navbar-top li.menu-item-has-children>a").each(function () {
    $(this).append('<i class="fa fa-angle-down ml6"></i>');
});

//系统通知
function notyf(str, ys, time, id) {
    $('.notyn').length || _win.bd.append('<div class="notyn"></div>');
    ys = ys || "success";
    time = time || 5000;
    time = time < 100 ? time * 1000 : time;
    var id_attr = id ? ' id="' + id + '"' : '';
    var _html = $('<div class="noty1"' + id_attr + '><div class="notyf ' + ys + '">' + str + '</div></div>');
    var is_close = !id;
    if (id && $('#' + id).length) {
        $('#' + id).find('.notyf').removeClass().addClass('notyf ' + ys).html(str);
        _html = $('#' + id);
        is_close = true;
    } else {
        $('.notyn').append(_html);
    }
    is_close && setTimeout(function () {
        notyf_close(_html)
    }, time);
}

function notyf_close(_e) {
    _e.addClass('notyn-out')
    setTimeout(function () {
        _e.remove()
    }, 1000);
}
_win.bd.on('click', '.noty1', function () {
    notyf_close($(this))
})

//切换密码显示
_win.passw = 1;
_win.bd.on('click', '.passw', function () {
    var _this = $(this);
    if (_win.passw == 1) {
        _this.find('.fa').addClass("fa-eye-slash");
        _this.siblings('input').attr('type', 'text');
        _win.passw = 2
    } else {
        _this.find('.fa').removeClass("fa-eye-slash");
        _this.siblings('input').attr('type', 'password');
        _win.passw = 1
    }
})

//绑定placeholder_scale操作
_win.bd.on('input change focus', '.line-form-input', debounce(function () {
    placeholder_scale($(this));
}, 100));

//执行placeholder_scale操作
function placeholder_scale(_this) {
    var val = _this.val();
    var placeholder = _this.siblings('.scale-placeholder');
    if (val.length > 0) {
        placeholder.addClass('is-focus');
    } else {
        placeholder.removeClass('is-focus');
    }
}

function zib_is_url(str) {
    return /^((http|https)\:\/\/)([a-z0-9-]{1,}.)?[a-z0-9-]{2,}.([a-z0-9-]{1,}.)?[a-z0-9]{2,}$/.test(str)
}

function is_mail(str) {
    return /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/.test(str)
}

$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

//清空搜索关键词
_win.bd.on('click', '.trash-history-search', function (e) {
    if (confirm("确认要清空全部搜索记录？") == true) {
        $('.history-search').slideUp().delay(1000, function () {
            $(this).remove()
        });
        $.cookie('history_search', '');
    }
})

//用户签到
_win.bd.on('click', '.initiate-checkin', function (e) {
    zib_ajax($(this), 0, function (data) {
        if (data.details_link) {
            $('.initiate-checkin').each(function () {
                var $link = $(data.details_link);
                var _this = $(this);
                var _class = _this.attr('class');
                var _text = _this.attr('ed-text');
                $link.addClass(_class).removeClass('initiate-checkin').html(_text);
                _this.prop('outerHTML', $link.prop('outerHTML'));
            });
        }
    }, '正在签到，请稍后...');
})

//底部tabbar，购买
_win.bd.on('click', '.single-pay-tabbar', function (e) {
    scrollTo('#posts-pay', -50);
    $('#posts-pay .cashier-link').click();
})

/**wp-ajax-action */
_win.bd.on('submit', '[ajax-submit]', function (e) {
    var _this = $(this);
    var but = _this.attr('ajax-submit') || '.wp-ajax-submit,[zibajax="submit"]';
    return _this.find(but).click(), !1;
})

_win.bd.on('click', '.wp-ajax-submit,[zibajax="submit"]', function (e) {
    return zib_ajax($(this)), !1;
})

function is_captcha(mode) {
    return window.captcha && !window.captcha.ticket;
}

function zib_ajax(_this, data, success, noty) {
    if (_this.attr('disabled')) {
        return !1;
    }
    if (!data) {
        var _data = _this.attr('form-data')
        if (_data) {
            try {
                data = $.parseJSON(_data);
            } catch (e) {}
        }
        if (!data) {
            var form = _this.parents('form');
            data = form.serializeObject()
        }
    }

    var _action = _this.attr('form-action')
    if (_action) {
        data.action = _action;
    }

    //人机验证
    if (data.captcha_mode && is_captcha(data.captcha_mode)) {
        tbquire(["captcha"], function () {
            CaptchaOpen(_this, data.captcha_mode)
        })
        return !1;
    }

    if (window.captcha) {
        data.captcha = JSON.parse(JSON.stringify(window.captcha));
        data.captcha._this && delete(data.captcha._this);
        window.captcha = {}; //只能使用一次
    }

    var _text = _this.html();
    noty != 'stop' && notyf((noty || "正在处理请稍后..."), "load", "", "wp_ajax");
    _this.attr('disabled', true).html('<i class="loading mr6"></i>请稍候');
    var _url = _this.attr('ajax-href') || _win.ajax_url;

    $.ajax({
        type: "POST",
        url: _url,
        data: data,
        dataType: "json",
        error: function (n) {
            var _msg = "操作失败 " + n.status + ' ' + n.statusText + '，请刷新页面后重试';
            if (n.responseText && n.responseText.indexOf("致命错误") > -1) {
                _msg = '网站遇到致命错误，请检查插件冲突或通过错误日志排除错误';
            }
            notyf(_msg, 'danger', '', (noty != 'stop' ? "wp_ajax" : ''));
            _this.attr('disabled', false).html(_text);
        },
        success: function (n) {
            var ys = (n.ys ? n.ys : (n.error ? 'danger' : ""));
            if (n.error) {
                _win.slidercaptcha = false;
                data.tcaptcha_ticket && (tcaptcha = {});
            }
            if (noty != 'stop') {
                notyf(n.msg || "处理完成", ys, '', 'wp_ajax');
            } else if (n.msg) {
                notyf(n.msg, ys);
            }

            _this.attr('disabled', false).html(_text).trigger('zib_ajax.success', n); //完成
            $.isFunction(success) && success(n, _this, data);

            if (n.hide_modal) {
                $(_this.parents('.modal')[0]).modal('hide');
            }
            if (n.reload) {
                if (n.goto) {
                    window.location.href = n.goto;
                    window.location.reload;
                } else {
                    window.location.reload();
                }
            }
        }
    });
}


//AJAX执行完成后自动切换到下一个tab
_win.bd.on('zib_ajax.success', '[next-tab]', function (e, n) {
    var _next = $(this).attr('next-tab');
    if (_next && n && !n.error) {
        $('a[href="#' + _next + '"]').tab('show');
    }
})

/* erphpdown 登录使用弹出登录框
 * =========================================
 */
$('.erphp-login-must').each(function () {
    $(this).addClass('signin-loader')
});

//浏览器窗口调整自动化
$(window).resize(function (event) {
    _wid = $(window).width();
    auto_fun();
});

$('.collapse').on('shown.bs.collapse', function () {
    auto_fun();
})

//文章限制高度
function maxh_k() {
    $('.limit-height').css({
        height: '',
        'max-height': ''
    }).find('.read-more').remove();
}

function posts_limit_height() {
    if ($('.limit-height').length) {
        var r = $('.limit-height');
        var r_h = r.height();
        var r_m = r.attr('data-maxheight');
        var nn = '<div class="read-more"><a href="javascript:;" onclick="maxh_k()">展开阅读全文<i class="fa ml10 fa-angle-down"></i></a></div>'
        if (~~(r_h) >= (~~(r_m) + 79)) {
            r.append(nn);
        }
    }
}

auto_fun();
posts_limit_height();

//页面加载之后自动点击
$('[load-click]').attr('no-scroll', true).click();
//页面加载完毕之后加载的函数
$(document).ready(function () {
    setTimeout(function () {
        //页面加载完毕之后再延迟两秒加载的函数

        //检测placeholder_scale操作
        $('.line-form-input').each(function () {
            placeholder_scale($(this))
        });

    }, 1000);

    auto_maxHeight();
    //文章亮点保持相同高度
    if ($(".feature").length) {
        var _feh = 0,
            _fehm = 0;
        $(".feature").each(function () {
            var _th = $(this);
            (_feh = _th.find(".feature-icon").innerHeight() + _th.find(".feature-title").innerHeight() + _th.find(".feature-note").innerHeight()) > _fehm && (_fehm = _feh);
        });
        $(".feature").css("height", _fehm);
    }

    //js二维码
    $(".qrcode").length && tbquire(["qrcode"], function () {
        $('.qrcode').each(function () {
            var _this = $(this),
                text = _this.attr('data-qrcode'),
                size = _this.attr('data-size') || 160;

            _this.qrcode({
                width: size,
                height: size,
                correctLevel: 0,
                text: text || document.URL,
                background: "#fff",
                foreground: "#333"
            });
        })
    });

    //图片延迟懒加载-ias自动加载
    document.addEventListener('lazybeforeunveil', function (e) {
        var _this = $(e.target);
        var lazyload_action = _this.attr('lazyload-action');
        setTimeout(function () {
            if (lazyload_action === 'ias') {
                if (_this.attr('remote-box')) {
                    _this.click();
                } else {
                    var _a = _this.is('.ajax-next,.ias-btn') ? _this : _this.find('.ajax-next,.ias-btn');
                    _a.attr('no-scroll', true).click();
                }
            }
            if (lazyload_action == 'animated') {
                var animated = _this.attr('data-animated');
                animated && _this.addClass('animated ' + animated).css('visibility', 'unset');
            }
        }, 200);
        var bg = _this.attr('data-bg');
        if (bg) {
            _this.css('background-image', 'url(' + bg + ')')
        }
    });

    //图片延迟懒加载-ias自动加载
    document.addEventListener('lazyloaded', function (e) {
        var _this = $(e.target);
        swiper_tab_AutoHeight(_this);
    });

    _win.qj_loading && $(".qj_loading").fadeOut(500).delay(1e3, function () {
        $(this).remove(), $("#qj_dh_css").remove();
    });
    /*一言功能*/
    function yiyan_nr(n) {
        var yylink = _win.uri + "/yiyan/qv-yiyan.php";
        $.ajax({
            url: yylink
        }).done(function (i) {
            var lines = i.replace(/\r\n|\r/g, "/&/").trim().split("/&/");
            if (lines) {
                var y_nr = '<div class="cn">' + lines[0] + '</div><div class="en">' + lines[1] + "</div>";
                n.html(y_nr);
            }
        });
    }

    //文章阅读数量记录
    if (_win.views) {
        $.get(_win.ajax_url, {
            action: "views_record",
            id: _win.views
        });
    }

    //获取登录用户数据
    $.get(_win.ajax_url, {
        action: 'get_current_user',
    });

    $(".yiyan").each(function () {
        yiyan_nr($(this));
    }), setInterval(function () {
        $(".yiyan").each(function () {
            yiyan_nr($(this));
        });
    }, 3e4);
    $(".yiyan").on('click', function () {
        yiyan_nr($(this));
    });

    /*文章目录*/
    $("[data-nav] h1,[data-nav] h2,[data-nav] h3,[data-nav] h4").length > 2 && tbquire(["section-navs"]);

    //手势控制
    $('[mini-touch]').each(function () {
        var _this = $(this);
        var fx = _this.attr('touch-direction');
        _this.minitouch({
            direction: fx,
            onEnd: function (e) {
                e.removeClass('show')
            }
        })
    });

    //手势控制
    $('[data-countdown]').each(function () {
        var _this = $(this);
        countdown(_this);
    });
});

console.log('\n' + ' %c ZibllTheme %c https://zibll.com ' + '\n', 'color: #fadfa3; background: #030307; padding:5px 0; font-size:12px;', 'background: #fadfa3; padding:5px 0; font-size:12px;');