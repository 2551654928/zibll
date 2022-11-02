/*
 * @Author: Qinver
 * @Url: zibll.com
 * @Date: 2021-08-11 16:08:49
 * @LastEditTime: 2022-09-06 14:28:54
 */
/*
 * @Author: Qinver
 * @Url: zibll.com
 * @Date: 2021-05-01 17:51:47
 * @LastEditTime: 2021-10-26 14:15:29
 */

(function () {
    var _body = $('body');

    _body.on('click', '[ajax-action]', function () {
        var _this = $(this);
        if (_this.attr('disabled')) return false;
        var id = _this.attr("data-id");
        var action = _this.attr("ajax-action");
        var _text = _this.find('text');
        _text = _text.length ? _text : _this.siblings('text');
        var _this_html = _text.html();
        _this.attr('disabled', true);
        _text.html('<i class="loading-spot"><i></i></i>');
        $.ajax({
            type: "POST",
            url: _win.ajax_url,
            dataType: "json",
            data: {
                id: id,
                action: action,
            },
            error: function (n) {
                var _msg = "操作失败 " + n.status + ' ' + n.statusText + '，请刷新页面后重试';
                if (n.responseText && n.responseText.indexOf("致命错误") > -1) {
                    _msg = '网站遇到致命错误，请检查插件冲突或通过错误日志排除错误';
                }
                notyf(_msg, 'danger');
                _this.attr('disabled', false);
                _text.html(_this_html);
            },
            success: function (n) {
                _this.attr('disabled', false);
                if (!n.data || !n.success) {
                    _text.html(_this_html);
                    if (n.data && n.data.msg) {
                        notyf(n.data.msg, n.data.ys || 'danger');
                    }
                } else {
                    n = n.data;
                    $('[ajax-action="' + action + '"][data-id="' + id + '"]').each(function () {
                        var _this = $(this);
                        var _text = _this.find('text');
                        _text = _text.length ? _text : _this.siblings('text');

                        _text.html(n.text || _this_html).addClass('ajaxed');

                        if (n.active) {
                            _this.addClass('active').siblings('.active').removeClass('active');
                        } else {
                            _this.removeClass('active');
                        }
                    })
                }
            }
        })
    })

    //发布文章提交
    _body.on("click", '.bbs-posts-submit', function () {
        var _this = $(this),
            type = _this.attr('action'),
            form = _this.parents('form'),
            data = form.serializeObject();
        data.action = type;
        if (tinyMCE) {
            data.post_content = tinyMCE.activeEditor.getContent();
        }
        zib_ajax(_this, data, function (n) {
            if (n.html) {
                form.find('.submit-text').html(n.html);
            }
            n.post_id && form.find('input[name="post_id"]').val(n.post_id);
        });
    })

    //可复制的
    $.fn.cloneable = function () {
        var cloneable_text = 'cloneable';
        var cloneable_add_e = '.' + cloneable_text + '-add';
        var cloneable_remove_e = '.' + cloneable_text + '-remove';
        var cloneable_item_e = '.' + cloneable_text + '-item';

        function max_min(_this, max, min) {
            var count = _this.children().length;
            if (max && max <= count) {
                _this.nextAll(cloneable_add_e).hide()
            } else {
                _this.nextAll(cloneable_add_e).show()
            }
            if (min && min >= count) {
                _this.find(cloneable_remove_e).hide()
            } else {
                _this.find(cloneable_remove_e).show()
            }
        }

        return this.each(function () {
            var _this = $(this);
            var _item = _this.children();
            var is_on = 'is-on';
            var click = 'click';

            var max = _this.data('max') || 0;
            var min = _this.data('min') || 0;
            max_min(_this, max, min);

            if (!_item.length || _this.attr(is_on)) return;

            var item = $(_item[0]).clone(false);
            item.find('input').val('');

            _this.attr(is_on, true).on(click, cloneable_remove_e, function () {
                $($(this).parents(cloneable_item_e)[0]).remove();
                max_min(_this, max, min);
            }).nextAll(cloneable_add_e).on(click, function () {
                _this.append(item.clone(false));
                max_min(_this, max, min);
            });
        })
    }

    $('.cloneable').cloneable();

    _body.on('change', "[name='vote[type]']", function (elem) {
        vote_change($(this))
    });

    function vote_change(_this) {
        var _vote = $('.vote-options');
        if (_this.length && _vote.length) {
            if (_this.val() == 'pk') {
                _vote.children().eq(1).nextAll().remove();
                _vote.data('max', 2).cloneable();
            } else {
                _vote.data('max', 10).cloneable();
            }
        }
    }
    vote_change($("[name='vote[type]']"));

    //投票组件
    $.fn.vote = function () {
        var ajax_url = _win.ajax_url;
        var text = 'vote';
        var voted_text = text + 'd';
        var start_text = text + '-start';
        var click = 'click';
        var allow = text + '-allow';
        var loading = text + '-loading';
        var user_cuont = text + '-user-count';
        var ok = text + '-ok';
        var progress = text + '-progress'; //进度条
        var percentage = text + '-percentage'; //百分比
        var number = text + '-number'; //百分比
        var ajax_action = 'submit_' + text;
        var item_e = '.' + text + '-item';
        var submit_e = '.' + text + '-submit';
        var is_choice = 'is-' + voted_text;
        var is_on = 'is-on';

        function show(_this, add = 0) {
            var voted_all = _this.data(voted_text + '-all') + add;
            var type = _this.data('type');

            _this.find(item_e).each(function (index, e) {
                var _item = $(this);
                var voted = _item.data(voted_text);
                if (!_item.children('.' + progress).length) {
                    _item.prepend('<div class="' + progress + '"></div>')
                }
                var percentage_data = (voted / voted_all * 100).toFixed(4);
                if (type == 'px' && !voted_all) percentage_data = '50%';
                setTimeout(function () {
                    _item.children('.' + progress).css('width', percentage_data + '%');
                }, 200)
                _item.children('.' + percentage).html(~~(percentage_data) + '%');
                _item.children('.' + number).html(voted + '票');
            })
        }

        function ajax(_main_this, data) {
            data.action = ajax_action;
            if (!_main_this.hasClass(loading)) { //防止多次点击
                _main_this.addClass(loading);
                $.post(ajax_url, data, function (result) {
                    if (result.data) {
                        _main_this.off(click).removeClass(loading + ' ' + allow).addClass(ok);
                        show(_main_this, data.voted.length);
                        var _user_cuont = _main_this.find('.' + user_cuont);
                        if (_user_cuont.length) {
                            _user_cuont.text(~~(_user_cuont.text()) + 1);
                        }
                        _main_this.find('.' + start_text).html('投票成功');
                        _main_this.find(submit_e).hide();
                    } else {
                        _main_this.removeClass(loading);
                    }
                }, 'json');
            }
        }

        return this.each(function () {
            var _this = $(this);
            var type = _this.data('type');
            var pist_id = _this.data('post-id');
            var is_ajax_ing = false;
            var data = {
                id: pist_id
            };
            show(_this);
            if (!_this.hasClass(allow) || _this.data(is_on)) return;

            if (type === 'multiple') {
                var _submit = _this.find(submit_e);
                _this.data(is_on, true).on(click, item_e, function () {
                    var _item = $(this);
                    var voted = _item.data(voted_text);
                    if (_item.hasClass(is_choice)) {
                        _item.removeClass(is_choice).data(voted_text, voted - 1);
                    } else {
                        _item.addClass(is_choice).data(voted_text, voted + 1);
                    }

                    if (_this.find(item_e + '.' + is_choice).length) {
                        _submit.show()
                    } else {
                        _submit.hide()
                    }
                }).on(click, submit_e, function () {
                    if (!_this.hasClass(loading)) { //防止多次点击
                        var voted = [];
                        _this.find(item_e).each(function (index, e) {
                            if ($(this).hasClass(is_choice)) {
                                voted.push(index);
                            }
                        })
                        data.voted = voted;
                        ajax(_this, data);
                    }
                })
            } else {
                _this.data(is_on, true).on(click, item_e, function () {
                    if (!_this.hasClass(loading)) { //防止多次点击
                        var _item = $(this).addClass(is_choice);
                        var voted = _item.data(voted_text);
                        _item.data(voted_text, voted + 1);
                        var index = _item.data('index');
                        data.voted = [index];
                        ajax(_this, data);
                    }
                })
            }
        })
    }

    //自动搜索组件
    $.fn.AutoSearch = function () {
        return this.each(function () {
            var _this = $(this);
            var ajax_url = _this.attr('ajax-url') || _win.ajax_url;
            var min_length = 2;
            var search = 'search-';
            var d_search = '.' + search;
            var loading = search + 'loading';
            var input = d_search + 'input';
            var centent = search + 'centent';
            var _container = _this.find(d_search + 'container');
            var _remind = _this.find(d_search + 'remind');
            var _icon = _this.find(d_search + 'icon');
            var _icon_html = _icon.html();
            var min_length_remind = '请至少输入' + min_length + '个字符';
            var loading_icon = '<i class="loading em12"></i>';
            var loading_remind = loading_icon + '<span class="ml6">正在搜索，请稍候...</span>';
            var is_on = 'is-on';
            if (_this.data(is_on)) return;

            function ajax() {
                var data = {};
                var serializeObject = _this.serializeObject();
                $.each(serializeObject, function (key, val) {
                    data[key] = val;
                })
                //循环插入_POST内容
                _this.find('input[name]').each(function () {
                    var _th = $(this);
                    var v = _th.val() || '';
                    data[_th.attr('name')] = v;
                });

                _this.addClass(loading);
                _remind.html(loading_remind);
                _icon.html(loading_icon);
                _container.css({
                    'height': _container.outerHeight(),
                    'overflow': 'hidden'
                })
                $.post(ajax_url, data, function (result) {
                    if (result.data) {
                        _container.html('<div class="' + centent + '">' + result.data + '</div>').animate({
                            'height': _container.children('.' + centent).outerHeight(),
                        }, 200, 'swing', function () {
                            _container.css({
                                'height': '',
                                'overflow': '',
                                'transition': ''
                            })
                        });
                    }
                    _this.removeClass(loading)
                    _remind.html(result.remind || '');
                    _icon.html(_icon_html);
                }, 'json');
            }

            _this.data(is_on, true).find(input).on('input', debounce(function () {
                var _input = $(this);
                if (_input.val().length >= min_length) {
                    ajax();
                } else {
                    _remind.html(min_length_remind);
                }
            }, 200));
        })
    }

    _body.on("loaded.bs.modal", ".modal", function () {
        $('.modal .auto-search').AutoSearch();
    });
    $('.auto-search').AutoSearch();

    //图片延迟懒加载-ias自动加载
    document.addEventListener('lazybeforeunveil', function (e) {
        var _this = $(e.target);
        if (_this.hasClass('vote-box')) {
            setTimeout(function () {
                _this.vote();
            }, 500);
        }
    });


    //挂钩添加term后的处理动作
    _body.on('miniuploaded', '[term-taxonomy]', function (a, data) {
        if (!data.term_id) return;
        if (data.type === 'add') {
            switch (data.taxonomy) {
                case 'plate_cat':
                    var container = $('.plate-cat-radio');
                    if (container.length) {
                        container.find('.container-null').remove();
                        var label = $('<label><input type="radio" name="cat" value="' + data.term_id + '"><span class="p2-10 mr6 but but-radio">' + data.term.name + '</span></label>');
                        container.prepend(label);
                        return label.click();
                    }
                    break;
                case 'forum_tag':
                    var container = $('.posts-tag-select');
                    if (container.length) {
                        var label = $('<span data-multiple="5" data-for="tag" data-value="' + data.term_id + '" class="tag-list ajax-item pointer"><span class="badg mm3">' + data.term.name + '</span></span>');
                        container.prepend(label);
                        return label.click();
                    }
                    break;
                case 'forum_topic':
                    var container = $('.posts-topic-select');
                    if (container.length) {
                        var label = $('<div data-for="topic" data-value="' + data.term_id + '" class="flex padding-10 topic-list ajax-item pointer"><div class="square-box mr10 thumb">' + (data.image_url ? '<img src="' + data.image_url + '" class="fit-cover radius4">' : '') + '</div><div class="info"><div class="name"><svg class="icon" aria-hidden="true"><use xlink:href="#icon-topic"></use></svg>' + data.term.name + '<svg class="icon" aria-hidden="true"><use xlink:href="#icon-topic"></use></svg></div><div class="muted-3-color em09 desc"><span class="mr20">帖子:0</span><span class="">2秒前创建</span></div></div></div>');
                        container.prepend(label);
                        return label.click();
                    }
                    break;
            }
        }

        window.location.href = data.term_url;
        window.location.reload;
    });
    _body.on('miniuploaded', '[plate-save]', function (a, data) {
        if (!data.id) return;

        if (data.type === 'add') {
            var container = $('#plate_select_tab_main');
            if (container.length) {
                var label = $('<div data-for="plate" data-value="' + data.id + '" class="flex padding-10 plate-list ajax-item pointer"><div class="square-box mr10 thumb">' + (data.image_url ? '<img src="' + data.image_url + '" class="radius-cover">' : '') + '</div><div class="info"><div class="name">' + data.post.post_title + '</div><div class="muted-3-color em09 desc mt3">3秒前创建</div></div></div>');
                container.prepend(label);
                $('[href="#plate_select_tab_main"]').click();
                return label.click();
            }
        }
        window.location.href = data.url;
        window.location.reload;
    });


    //回答采纳后，将按钮标记为已采纳
    _body.on('zib_ajax.success', '.answer-adopt-submit', function (e, n) {
        if (n && n.comment_id && n.badeg) {
            $('.answer-adopt-id-' + n.comment_id).prop('outerHTML', n.badeg);

        }
    })

})()