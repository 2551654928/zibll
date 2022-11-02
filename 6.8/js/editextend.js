(function ($) {
    var PluginManagerAdd = tinymce.PluginManager.add;
    PluginManagerAdd('zib_hide', function (editor, url) {
        var menu = [{
            text: '评论后可查看',
            onclick: function () {
                setContent('reply')
            }
        }, {
            text: '登录后可查看',
            onclick: function () {
                setContent('logged')
            }
        }, {
            text: '会员可查看',
            onclick: function () {
                setContent('vip1')
            }
        }];

        if (mce.hide_pay) {
            menu.push({
                text: '付费后可查看',
                onclick: function (event) {
                    setContent('payshow', event)
                }
            });
        }

        function setContent(type, event) {
            var type_desc = {
                payshow: '付费阅读',
                vip1: '会员可查看',
                logged: '登录后可查看',
                reply: '评论后查看',
            };

            var getNode = editor.selection.getNode();
            var val = $.trim(editor.selection.getContent()) || '';
            var suffix = '<p></p>';
            val = '<p>' + val + '</p>';
            if (is_own(getNode)) {
                suffix = '';
                val = $(getNode).find('[contenteditable="true"]').html();
            } else if (getNode.nodeName === 'P') {
                var outerHTML = $.trim(getNode.outerHTML);
                if (outerHTML) {
                    editor.dom.remove(getNode);
                    val = outerHTML;
                }
            }
            editor.insertContent('<div class="tinymce-hide" contenteditable="false"><p class="hide-before">[hidecontent type="' + type + '" desc="隐藏内容：' + type_desc[type] + '"]</p><div contenteditable="true">' + val + '</div><p class="hide-after">[/hidecontent]</p></div>' + suffix);
        }

        function is_own(element) {
            return element.className === 'tinymce-hide'
        }

        editor.addButton('zib_hide', {
            text: '',
            icon: 'preview',
            tooltip: '隐藏内容',
            type: 'menubutton',
            stateSelector: '.tinymce-hide',
            menu: menu
        });

        editor.on('wptoolbar', function (event) {
            if (is_own(event.element) && editor.wp) {
                event.toolbar = editor.wp._createToolbar([
                    'zib_hide', 'dom_remove'
                ], true);
            }
        });
    });

    PluginManagerAdd('zib_quote', function (editor, url) {
        editor.addButton('zib_quote', {
            text: '',
            icon: 'blockquote',
            tooltip: '引言',
            type: 'menubutton',
            menu: [{
                text: '灰色',
                onclick: function () {
                    setContent('')
                }
            }, {
                text: '红色',
                onclick: function () {
                    setContent('qe_wzk_c-red')
                }
            }, {
                text: '蓝色',
                onclick: function () {
                    setContent('qe_wzk_lan')
                }
            }, {
                text: '绿色',
                onclick: function () {
                    setContent('qe_wzk_lv')
                }
            }, ]
        });

        function setContent(type) {
            var val = $.trim(editor.selection.getContent()) || '';
            var suffix = '<p></p>';
            val = '<p>' + val + '</p>';
            var getNode = editor.selection.getNode();
            if (is_own(getNode)) {
                suffix = '';
                val = $(getNode).find('[contenteditable="true"]').html();
            } else if (getNode.nodeName === 'P') {
                var outerHTML = $.trim(getNode.outerHTML);
                if (outerHTML) {
                    editor.dom.remove(getNode);
                    val = outerHTML;
                }
            }
            editor.insertContent('<div class="quote_q quote-mce ' + type + '" contenteditable="false"><div contenteditable="true">' + val + '</div></div>' + suffix);
        }

        function is_own(element) {
            return $(element).hasClass('quote_q')
        }

        editor.on('wptoolbar', function (event) {
            if (is_own(event.element) && editor.wp) {
                event.toolbar = editor.wp._createToolbar([
                    'zib_quote', 'dom_remove'
                ], true);
            }
        });
    });

    //添加编辑器上传按钮
    PluginManagerAdd('zib_img', function (editor, url) {
        var acceptExts = 'image/gif,image/jpeg,image/jpg,image/png';
        var upload_input_id = 'edit-zibimg-input';
        var modal_id = 'edit-zibimg-modal';
        var textarea_id = 'edit-zibimg-url';
        var textarea_btn_id = 'edit-zibimg-url-btn';
        var tab_id_prefix = 'edit-zibimg-tab-'; //前缀
        var my_box_id = 'edit-zibimg-mybox'; //前缀
        var upload_placeholder = 'upload-placeholder';
        var _body = $('body');
        var is_upload = mce.img_allow_upload; //允许上传
        var is_myimg_loaded = false;
        var upload_size_max = mce.img_max || 4;

        function init() {
            var tab_con;
            var input_html = '<div class="box-body"><p>请填写图片地址：</p><textarea id="' + textarea_id + '" rows="2" tabindex="1" class="form-control input-textarea" style="height:84px;" placeholder="http://..."></textarea></div><div class="modal-buts but-average"><a id="' + textarea_btn_id + '" class="but c-blue" href="javascript:;">确认插入</a></div>';
            if (is_upload) {
                var upload_input_html = '<div class="box-body"><div class="em09 muted-2-color mb6">上传图片支持jpg、png、gif格式，最大' + upload_size_max + 'M</div><label class="muted-box pointer block"><input id="' + upload_input_id + '" style="display: none;" class="" type="file" accept="' + acceptExts + '"><div style="padding: 40px 10px;" class="text-center opacity5 ' + upload_placeholder + '"><i aria-hidden="true" class="fa fa-camera mr6"></i>上传图片</div></label></div>';
                var my_html = '<div id="' + my_box_id + '"><div class="box-body"> <div class="my-lists mini-scrollbar scroll-y max-vh5"></div></div><div class="modal-buts but-average"><a style="display: none;" class="but c-blue my-submit" href="javascript:;">确认插入</a></div></div>';

                tab_con = '<button data-dismiss="modal" class="mr10 mt10 close"><svg class="ic-close" aria-hidden="true"><use xlink:href="#icon-close"></use></svg></button><ul class="mt20 text-center list-inline tab-nav-theme"><li class="active ml20"><a href="#' + tab_id_prefix + '-1" data-toggle="tab">输入地址</a></li><li><a href="#' + tab_id_prefix + '-2" data-toggle="tab">上传图片</a></li><li><a href="#' + tab_id_prefix + '-3" data-toggle="tab">我的图片</a></li></ul>';
                tab_con += '<div class="tab-content">\
                <div class="tab-pane fade in active" id="' + tab_id_prefix + '-1">' + input_html + '</div>\
                <div class="tab-pane fade" id="' + tab_id_prefix + '-2">' + upload_input_html + '</div>\
                <div class="tab-pane fade" id="' + tab_id_prefix + '-3">' + my_html + '</div>\
                </div>';
            } else {
                tab_con = input_html;
            }

            var modal_html = '<div class="modal flex jc fade" id="' + modal_id + '" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; z-index: 100101;">\
                            <div class="modal-mini modal-dialog" role="document">\
                            <div class="modal-content">' + tab_con + '</div>\
                            </div></div></div>';

            _body.append(modal_html);
        }
        init();

        //关闭弹窗
        function modalClose() {
            $('#' + modal_id).modal('hide');
            //    $('a[href="#' + tab_id_prefix + '-1"]').click();
            $('#' + textarea_id).val('');
            $('#' + modal_id + ' .preview-box').remove();
            $('#' + modal_id + ' .' + upload_placeholder).show(); //占位符
        }

        //打开弹窗
        function modalOpen() {
            $('#' + modal_id).modal('show');
        }

        //加载我的图片
        function myLoad(paged) {
            if (is_myimg_loaded && paged === 1) return;
            var _lists_box = $('#' + my_box_id + ' .my-lists');
            var lists_html = '';

            var data = {
                action: 'current_user_image',
                paged: paged || 1
            }

            if (!is_myimg_loaded && paged === 1) {
                _lists_box.html('<div class="mb6 theme-pagination" style="padding: 60px 0;"></div>');
            }

            $('#' + my_box_id + ' .theme-pagination').html('<div class="muted-2-color flex jc"><div class="loading mr10"></div>正在加载</div>');

            $.post(mce.ajax_url, data, function (n) {
                $('#' + my_box_id + ' .theme-pagination').remove();
                if (n.lists) {
                    $.each(n.lists, function (i, img_data) {
                        lists_html += '<div class="myimg-list"><div class="myimg-list-box"><img data-edit-file-id="' + img_data.id + '" src="' + img_data.thumbnail_url + '" data-large-src="' + img_data.large_url + '" alt="' + img_data.title + '" data-full-url="' + img_data.url + '"></div></div>';
                    })
                }

                if (lists_html && n.all_pages > paged) {
                    //加载下一页
                    lists_html += '<div class="text-center theme-pagination"><div class="ajax-next"><a href="" next-paged="' + (paged + 1) + '"><i class="fa fa-angle-right"></i>加载更多</a></div></div>';
                }

                if (paged === 1) {
                    if (lists_html) {
                        _lists_box.html(lists_html);
                    } else {
                        _lists_box.html('<div class="muted-2-color mb6 theme-pagination text-center" style="padding: 60px 0;">您暂无已上传的图片</div>');
                    }
                } else {
                    _lists_box.find('.myimg-list').last().after(lists_html);
                }

            }, "json");
        }

        //加载图片
        $('a[href="#' + tab_id_prefix + '-3"]').on('shown.bs.tab', function (e) {
            is_myimg_loaded || myLoad(1), is_myimg_loaded = true;
        });

        //下一页加载图片
        $('#' + my_box_id).on('click', 'a[next-paged]', function (e) {
            var next = ~~($(this).attr('next-paged'));
            myLoad(next);
            return false;
        });

        //我的图片->选择图片
        $('#' + my_box_id).on('click', '.myimg-list', function (e) {
            $(this).toggleClass('active');
            var _btn = $('#' + my_box_id + ' .my-submit');
            if ($('#' + my_box_id + ' .myimg-list.active').length) {
                _btn.show();
            } else {
                _btn.hide();
            }
        });

        //我的图片确认插入
        $('#' + my_box_id).on('click', '.my-submit', function (e) {
            var img_html = '';
            $('#' + my_box_id + ' .myimg-list.active').each(function () {
                var img = $(this).removeClass('active').find('img').clone();
                img.attr('src', img.attr('data-large-src'));
                img.removeAttr('data-large-src');
                img_html += '<p>' + img.prop('outerHTML') + '</p>';
            })

            $(this).hide();
            modalClose();
            editor.insertContent(img_html + '<p></p>')
        });

        //输入图片地址
        $('#' + textarea_btn_id).on('click', function (e) {
            var src = $('#' + textarea_id).val();
            if (!src) {
                return notyf("请输入图片地址", "warning"); //警告
            }

            editor.insertContent('<img src="' + src + '"><p></p>');
            modalClose();
        })

        function imgToB64(e, n) {
            if ("undefined" == typeof FileReader) return notyf("当前浏览器不支持图片上传，请更换浏览器", "danger");
            var r = new FileReader();
            r.readAsDataURL(e), r.onload = function (e) {
                n && n(e.target.result);
            };
        }

        //上传图片
        $('#' + upload_input_id).on('change', function (e) {
            var files = this.files || e.dataTransfer.files;
            var ing_key = 'upload_ing';
            var _this = $(this);
            var _parent = _this.parent();

            //没有文件退出
            if (!files[0] || _this.data(ing_key))  return false;

            var file = files[0];
            //文件大小判断
            if (file.size > upload_size_max * 1024000) {
                return notyf("文件[" + file.name + "]大小超过限制，最大" + upload_size_max + "M，请重新选择", "danger");
            }

            //图片预览
            imgToB64(file, function (e) {
                var preview_img = '<div class="preview-box img-preview-box"><img class="preview-img" alt="' + file.name + '" src="' + e + '"><badge class="progress-text b-black mr6 mt6"></badge></div>';
                _parent.find('.' + upload_placeholder).hide(); //占位符
                _parent.append(preview_img);
            });

            //执行上传
            upload('image', file, _this, function (n) {
                if (n.url) {
                    is_myimg_loaded = false;
                    modalClose();
                    var src = n.large_url || n.url;
                    editor.insertContent('<img data-edit-file-id="' + n.id + '" src="' + src + '" alt="' + n.title + '" data-full-url="' + n.url + '"><p></p>')
                }
            });
        })

        editor.addButton('zib_img', {
            text: '',
            icon: 'image',
            tooltip: '图片',
            onclick: function () {
                is_mobile() || modalOpen()
            },
            onTouchEnd: function () {
                is_mobile() && modalOpen()
            }
        })
    });

    //添加编辑器上传按钮
    PluginManagerAdd('zib_video', function (editor, url) {
        var acceptExts = 'video/*';
        var upload_input_id = 'edit-zibvideo-input';
        var modal_id = 'edit-zibvideo-modal';
        var textarea_id = 'edit-zibvideo-url';
        var textarea_btn_id = 'edit-zibvideo-url-btn';
        var tab_id_prefix = 'edit-zibvideo-tab-'; //前缀
        var my_box_id = 'edit-zibvideo-mybox'; //前缀+
        var upload_placeholder = 'upload-placeholder';
        var _body = $('body');
        var is_upload = mce.video_allow_upload; //允许上传
        var is_iframe = mce.video_allow_iframe; //允许上传
        var is_my_loaded = false;
        var upload_size_max = mce.video_max || 30;

        function init() {
            var tab_con = '';
            var tab_nav = '';
            var input_html = '<div class="box-body"><p>请输入视频地址：</p><textarea id="' + textarea_id + '" rows="2" tabindex="1" class="form-control input-textarea" style="height:84px;" placeholder="http://..."></textarea></div><div class="modal-buts but-average"><a id="' + textarea_btn_id + '" class="but c-blue" href="javascript:;">确认插入</a></div>';

            if (is_upload) {
                var upload_input_html = '<div class="box-body"><div class="em09 muted-2-color mb6">支持常见视频格式，最大' + upload_size_max + 'M</div><label class="muted-box pointer block"><input id="' + upload_input_id + '" style="display: none;" class="" type="file" accept="' + acceptExts + '"><div style="padding: 40px 10px;" class="text-center opacity5 ' + upload_placeholder + '"><i aria-hidden="true" class="fa fa-cloud-upload mr6"></i>上传视频</div></label></div>';
                var my_html = '<div id="' + my_box_id + '"><div class="box-body"><div class="my-lists mini-scrollbar scroll-y max-vh5"></div></div></div>';

                tab_nav += '<li><a href="#' + tab_id_prefix + '-2" data-toggle="tab">上传视频</a></li><li><a href="#' + tab_id_prefix + '-3" data-toggle="tab">我的视频</a></li>';
                tab_con += '<div class="tab-pane fade" id="' + tab_id_prefix + '-2">' + upload_input_html + '</div><div class="tab-pane fade" id="' + tab_id_prefix + '-3">' + my_html + '</div>';
            }

            if (is_iframe) {
                var iframe_input_html = '<div class="box-body"><p>请输入嵌入地址或者直接粘贴iframe嵌入代码：</p><textarea rows="2" tabindex="1" class="form-control input-textarea" style="height:84px;" placeholder="&lt;iframe src=&quot;https://....&quot;&gt;&lt;/iframe&gt;"></textarea></div><div class="modal-buts but-average"><a class="but c-blue iframe-submit" href="javascript:;">确认嵌入</a></div>';
                tab_nav += '<li><a href="#' + tab_id_prefix + '-4" data-toggle="tab">嵌入视频</a></li>';
                tab_con += '<div class="tab-pane fade" id="' + tab_id_prefix + '-4">' + iframe_input_html + '</div>';
            }

            if (tab_nav) {
                tab_nav = '<ul class="mt20 text-center list-inline tab-nav-theme"><li class="active ml20"><a href="#' + tab_id_prefix + '-1" data-toggle="tab">输入地址</a></li>' + tab_nav + '</ul>';
                tab_con = '<div class="tab-content"><div class="tab-pane fade in active" id="' + tab_id_prefix + '-1">' + input_html + '</div>' + tab_con + '</div>';
            } else {
                tab_con = input_html;
            }

            var modal_html = '<div class="modal flex jc fade" id="' + modal_id + '" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; z-index: 100101;">\
                            <div class="modal-mini modal-dialog" role="document">\
                            <div class="modal-content"><button data-dismiss="modal" class="mr10 mt10 close"><svg class="ic-close" aria-hidden="true"><use xlink:href="#icon-close"></use></svg></button>' + tab_nav + tab_con + '</div>\
                            </div></div></div>';

            _body.append(modal_html);
        }
        init();

        //关闭弹窗
        function modalClose() {
            //    $('a[href="#' + tab_id_prefix + '-1"]').click();
            $('#' + modal_id).modal('hide');
            $('#' + textarea_id).val('');
            $('#' + tab_id_prefix + '-4 textarea').val('');
            $('#' + modal_id + ' .preview-box').remove();
            $('#' + modal_id + ' .' + upload_placeholder).show(); //占位符
        }

        //打开弹窗
        function modalOpen() {
            $('#' + modal_id).modal('show');
        }

        //加载我的图片
        function myLoad(paged) {
            if (is_my_loaded && paged === 1) return;
            var _lists_box = $('#' + my_box_id + ' .my-lists');
            var lists_html = '';

            var data = {
                action: 'current_user_video',
                paged: paged || 1
            }

            if (!is_my_loaded && paged === 1) {
                _lists_box.html('<div class="mb6 theme-pagination" style="padding: 60px 0;"></div>');
            }

            $('#' + my_box_id + ' .theme-pagination').html('<div class="muted-2-color flex jc"><div class="loading mr10"></div>正在加载</div>');

            $.post(mce.ajax_url, data, function (n) {
                $('#' + my_box_id + ' .theme-pagination').remove();
                if (n.lists) {
                    $.each(n.lists, function (i, img_data) {
                        lists_html += '<div class="myimg-list pointer" data-video-name="' + img_data.filename + '" data-video-url="' + img_data.url + '" ><div class="myimg-list-box"><div class="px12 flex1 flex xx padding-6"><div class="px12 text-ellipsis-2"><i class="fa fa-file-video-o mr6"></i>' + img_data.filename + '</div><div class="flex1 flex ab muted-2-color"><span class=""><i class="fa fa-play-circle-o mr3"></i>' + img_data.fileLength + '</span></div></div></div></div>';
                    })
                }

                if (lists_html && n.all_pages > paged) {
                    //加载下一页
                    lists_html += '<div class="text-center theme-pagination"><div class="ajax-next"><a href="" next-paged="' + (paged + 1) + '"><i class="fa fa-angle-right"></i>加载更多</a></div></div>';
                }

                if (paged === 1) {
                    if (lists_html) {
                        _lists_box.html(lists_html);
                    } else {
                        _lists_box.html('<div class="muted-2-color mb6 theme-pagination text-center" style="padding: 60px 0;">您暂无已上传的视频</div>');
                    }
                } else {
                    _lists_box.find('.myimg-list').last().after(lists_html);
                }

            }, "json");
        }

        //加载图片
        $('a[href="#' + tab_id_prefix + '-3"]').on('shown.bs.tab', function (e) {
            is_my_loaded || myLoad(1), is_my_loaded = true;
        });

        //下一页加载图片
        $('#' + my_box_id).on('click', 'a[next-paged]', function (e) {
            var next = ~~($(this).attr('next-paged'));
            myLoad(next);
            return false;
        });

        //我的图片->选择图片
        $('#' + my_box_id).on('click', '.myimg-list', function (e) {
            var url = $(this).attr('data-video-url');
            var name = $(this).attr('data-video-name');

            modalClose();
            editor.insertContent('<div contenteditable="false" data-video-url="' + url + '" data-video-name="' + name + '" class="new-dplayer post-dplayer dplayer"></div><p></p>')
        });

        //嵌入视频确认插入
        $('#' + tab_id_prefix + '-4').on('click', '.iframe-submit', function (e) {
            var src = $('#' + tab_id_prefix + '-4 textarea').val();
            if (!src) {
                return notyf("请输入嵌入地址", "warning"); //警告
            }

            var html = $.parseHTML(src);
            src = $(html).attr('src') || tinymce.html.Entities.encodeAllRaw(src);

            modalClose();
            editor.insertContent('<div contenteditable="false" class="wp-block-embed is-type-video mb20"><div class="iframe-absbox" style="padding-bottom:65%;"><iframe src="' + src + '" allowfullscreen="allowfullscreen" framespacing="0" border="0" width="100%" frameborder="no"></iframe></div></div><p></p>')
        });


        //输入地址
        $('#' + textarea_btn_id).on('click', function (e) {
            var src = $('#' + textarea_id).val();
            if (!src) {
                return notyf("请输入视频地址", "warning"); //警告
            }
            modalClose();
            editor.insertContent('<div contenteditable="false" data-video-url="' + src + '" data-video-name="' + src + '" class="new-dplayer post-dplayer dplayer"></div><p></p>')
        })

        //上传
        $('#' + upload_input_id).on('change', function (e) {
            var files = this.files || e.dataTransfer.files;
            var ing_key = 'upload_ing';
            var _this = $(this);
            var _parent = _this.parent();

            //没有文件退出
            if (!files[0] || _this.data(ing_key)) return false;

            var file = files[0];
            //文件大小判断
            if (file.size > upload_size_max * 1024000) {
                return notyf("文件[" + file.name + "]大小超过限制，最大" + upload_size_max + "M，请重新选择", "danger");
            }

            var preview_html = '<div class="preview-box" style="padding:15px 0;"><div class="mb30"><i class="fa fa-file-video-o mr6"></i>' + file.name + '</div><div class="progress-text opacity5"></div></div>';
            _parent.find('.' + upload_placeholder).hide(); //占位符
            _parent.append(preview_html);

            //执行上传
            upload('video', file, _this, function (n) {
                if (n.url) {
                    is_my_loaded = false;
                    modalClose();
                    editor.insertContent('<div contenteditable="false" data-video-url="' + n.url + '" data-video-name="' + n.filename + '" data-edit-file-id="' + n.id + '" class="new-dplayer post-dplayer dplayer"></div><p></p>')
                }
            });
        })

        editor.addButton('zib_video', {
            text: '',
            tooltip: '视频',
            icon: 'media',
            onclick: function () {
                is_mobile() || modalOpen()
            },
            onTouchEnd: function () {
                is_mobile() && modalOpen()
            }
        })
    });

    //绑定上传进度
    function jqXhr(fun) {
        jqXhr.onprogress = fun;
        //使用闭包实现监听绑
        return function () {
            //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
            var xhr = $.ajaxSettings.xhr();
            //判断监听函数是否为函数
            if (typeof jqXhr.onprogress !== 'function')
                return xhr;
            //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
            if (jqXhr.onprogress && xhr.upload) {
                xhr.upload.onprogress = jqXhr.onprogress;
            }
            return xhr;
        }
    };

    function upload(type, file, _input, success_fun) {
        var formData = new FormData();
        var ing_key = 'upload_ing';
        formData.append('file', file, file.name);
        formData.append('action', 'edit_upload');
        formData.append('_wpnonce', mce.upload_nonce);
        formData.append('file_type', type);

        _input.data(ing_key, true);
        $.ajax({
            type: 'POST',
            url: mce.ajax_url,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            error: function (n) {
                var _msg = "操作失败 " + n.status + ' ' + n.statusText + '，请刷新页面后重试';
                if (n.responseText && n.responseText.indexOf("致命错误") > -1) {
                    _msg = '网站遇到致命错误，请检查插件冲突或通过错误日志排除错误';
                }
                notice('上传失败：' + _msg, 'danger');
                _input.data(ing_key, false);
            },
            xhr: jqXhr(function (e) {
                var percent = Math.round(e.loaded / e.total * 100);
                _input.parent().find('.progress-text').html('<i class="loading mr6"></i>' + (percent >= 100 ? '处理中 ...' : '正在上传 ' + percent + '%'))
            }),
            success: function (n) {
                if (n.msg) {
                    var ys = (n.ys ? n.ys : (n.error ? 'danger' : ""));
                    notice(n.msg, ys);
                }
                $.isFunction(success_fun) && success_fun(n, _input);
                _input.data(ing_key, false);
            }
        })
    }

    function is_mobile() {
        return /Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent);
    }

    //系统通知
    function notice(str, ys, time, id) {
        $('.notyn').length || $('body').append('<div class="notyn"></div>');
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

        function notyf_close(_e) {
            _e.addClass('notyn-out')
            setTimeout(function () {
                _e.remove()
            }, 1000);
        }
    }

})(jQuery);