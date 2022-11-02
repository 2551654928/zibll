/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:40
 * @LastEditTime: 2022-09-16 19:40:20
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题|文章目录树
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

//严格模式
'use strict';

function section_navs(selector) {
    var navbox_selector = '.posts-nav-box';
    var _body = _win.bd;
    var nav_class = 'posts-nav-lists';
    var nav_selector = '.' + nav_class;
    var selector_s = selector + ' h1,' + selector + ' h2,' + selector + ' h3,' + selector + ' h4';

    var index = 0;

    function aotu_scrollTo() {
        var hash = window.location.hash;
        if (hash && $(hash).length) {
            scrollTo(hash);
        }
    }

    var add_box = function () {
        $(navbox_selector).each(function () {
            var _this = $(this);
            var name = _this.attr('data-title') || '';
            name = name && '<div class="box-body notop"><div class="title-theme">' + name + '</div></div>';
            _this.append(name + '<div class="zib-widget"><div class="' + nav_class + ' scroll-y mini-scrollbar list-unstyled"></div></div>')
        });
        add_lists();
        setTimeout(function () {
            aotu_scrollTo();
        }, 20);

    }

    var add_lists = function () {
        var navs_lists = '';
        $(selector_s).each(function (indexInArray) {
            var _this = $(this);
            var tag = _this.prop("tagName");
            var text = _this.text();
            var _class = 'n-' + tag;
            _class += indexInArray == 1 ? ' active' : '';
            index = indexInArray;
            _this.attr('id', 'wznav_' + index);
            navs_lists += '<li class="' + _class + '"><a  class="text-ellipsis" href="#wznav_' + index + '">' + text + '</a></li>';
        });

        $(nav_selector).html('<ul class="bl relative nav">' + navs_lists + '</ul>');
        _body.on("click", nav_selector + ' a', function () {
            var _href = $($(this).attr("href"));
            _href.parents('.panel').each(function (indexInArray) {
                var _collapse = $(this).find('.collapse');
                if (_collapse.length) {
                    _collapse.collapse('show')
                }
            });

            maxh_k();
            scrollTo(_href);
            return false;
        })

        _body.scrollspy({
            target: nav_selector,
            offset: 105
        });
        collapse();
    }

    //显示折叠内容
    var collapse = function () {
        var find_selector = '.n-H2';
        $(nav_selector + ' .bl').each(function () {
            var _this = $(this);
            if (_this.innerHeight() > 380) {
                _this.find(find_selector).each(function () {
                    var _this = $(this);
                    var _n_h2 = _this.nextUntil(find_selector);
                    if (_n_h2.length) {
                        _n_h2.addClass('yc');
                        _this.append('<i class="fa fa-angle-right nav-toggle-collapse"></i>');
                    }
                });
            }
        });

        _body.on("click", nav_selector + ' .nav-toggle-collapse', function () {
            $(this).toggleClass('fa-rotate-90').parent().nextUntil(find_selector).toggleClass('yc');
        })
    }

    if ($(selector_s).length > 2) {
        add_box();
    }
}

section_navs('[data-nav]');