/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:40
 * @LastEditTime: 2022-09-30 13:49:17
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

'use strict';
window.Miniimgbox = (function (window) {
	$("link#swiper").length || $("head").append('<link type="text/css" id="swiper" rel="stylesheet" href="' + _win.uri + '/css/swiper.min.css?ver=' + _win.ver + '">');
	var _body = _win.bd;
	var beijin = '<div class="modal-backdrop imgbox-bg"></div>';
	var data_tag_id = 'imgbox-id';
	var data_tag_index = 'imgbox-index';
	window.imgbox = {};

	var box_option = {
		show_thumbs: _win.imgbox_thumbs || false,
		down: _win.imgbox_down || false,
		play: _win.imgbox_play || false,
		zoom: _win.imgbox_zoom || false,
		full: _win.imgbox_full || false,
	}

	var buttons = {
		thumbs: '<a href="javascript:;" class="toggle-thumbs toggle-radius mr6" title="查看更多图片"><i class="fa fa-th-large"></i></a>',
		close: '<a href="javascript:;" class="icon-close toggle-radius" title="关闭"><i data-svg="close" data-class="ic-close icon em12" data-viewbox="0 0 1024 1024"></i></a>',
		down: '<a href="javascript:;" download class="img-down toggle-radius mr6" title="下载图片"><i class="fa fa-download"></i></a>',
		play: '<a href="javascript:;" class="toggle-palay toggle-radius mr6" title="播放图片"><i class="fa fa-play"></i></a>',
		zoom: '<a href="javascript:;" class="toggle-zoom toggle-radius mr6" title="切换图片缩放"><i class="fa fa-search"></i></a>',
		full: '<a href="javascript:;" class="hide-sm toggle-full toggle-radius mr6" title="切换全屏"><i class="fa fa-expand"></i></a>',
	}

	_body.on('click', '.img-close,.imgbox-bg,.imgbox-an .icon-close', function () {
		close();
	});

	_body.on('click', '.imgbox-an .toggle-thumbs,.hide-but', function () {
		$(this).parents('.group-box').toggleClass('show-thumb');
	});
	_body.on('click', '.imgbox-an .img-down', function () {
		var _this = $(this);
		var _box = _this.parents('.imgbox');
		var img = _box.find('.swiper-slide-active img');
		var src = img.attr("data-src") || img.attr("src");
		var down_name = src.substring(src.lastIndexOf('/') + 1) || src;
		_this.attr('href', src).attr('download', down_name);
	});
	_body.on('click', '.imgbox-an .toggle-palay', function () {
		var _this = $(this);
		var get_swiper = get_swiper_el(_this);
		_this.hasClass('is-play') ? get_swiper.autoplay.stop() : get_swiper.autoplay.start();
		_this.toggleClass('is-play');
	});
	_body.on('click', '.imgbox-an .toggle-zoom', function () {
		var _this = $(this);
		var get_swiper = get_swiper_el(_this);
		get_swiper.zoom.toggle();
		_this.toggleClass('is-zoom');
	});
	_body.on('click', '.imgbox-an .toggle-full', function () {
		if (!document.fullscreenElement) {
			//进入页面全屏
			full_in();
		} else {
			full_out();
		}
	});

	function full_in() {

		var docElm = document.documentElement;
		if (docElm.requestFullscreen) {
			docElm.requestFullscreen();
		} else if (docElm.msRequestFullscreen) {
			docElm.msRequestFullscreen();
		} else if (docElm.mozRequestFullScreen) {
			docElm.mozRequestFullScreen();
		} else if (docElm.webkitRequestFullScreen) {
			docElm.webkitRequestFullScreen();
		}
		$('.imgbox-an .toggle-full .fa').removeClass('fa-expand').addClass('fa-compress');
		$(".imgbox.show").addClass('full-screen'); //全屏
	}

	function full_out() {
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.msExitFullscreen) {
			document.msExitFullscreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.webkitCancelFullScreen) {
			document.webkitCancelFullScreen();
		}
		$('.imgbox-an .toggle-full .fa').removeClass('fa-compress').addClass('fa-expand');
		$(".imgbox.show").removeClass('full-screen'); //全屏
	}

	function get_swiper_el(_this) {
		var _box = _this.parents('.imgbox');
		var swiper_id = _box.attr('id');
		return window.imgbox[swiper_id][0] || window.imgbox[swiper_id];
	}

	function append(_class, _html, _id, is_group) {
		_class = _class && ' ' + _class;
		var _id_tag = _id && ' id="' + _id + '"';
		var button = '';
		button += box_option.down ? buttons.down : '';
		if (is_group) {
			button += box_option.play ? buttons.play : '';
			button += box_option.show_thumbs ? buttons.thumbs : '';
		}
		button += box_option.zoom ? buttons.zoom : '';
		button += box_option.full ? buttons.full : '';
		button += buttons.close;
		var button_html = '<div class="imgbox-an">' + button + '</div>';
		var imgbox = '<div class="imgbox' + _class + '"' + _id_tag + '>' + beijin + button_html + _html + '</div>';
		_body.append(imgbox);
		touch_close(_id);
		auto_fun();
	}

	function link_replace(link) {
		return link.replace(/(.*\/)(.*)(-\d+x\d+\.)(.*)/g, "$1$2.$4").replace(/\??x-oss-process(.*)/, "");
	}

	function show(e, _this) {
		$("body").addClass('imgbox-show');
		//$(".modal").modal("hide");
		show_style(e, _this);
		$(e).addClass("show").removeClass("hide");
	}

	var show_style = function (e, _this) {
		var offset = _this.offset();
		var offset_top = offset.top;
		var offset_left = offset.left;
		var scrollTop = $(document).scrollTop();
		var height = _this.height();
		var width = _this.width();

		var s_top = offset_top - scrollTop + (height / 2);
		var s_left = offset_left + (width / 2);

		$(e).attr('style', '--imgbox-origin-top:' + s_top + 'px;--imgbox-origin-left:' + s_left + 'px;');
	}

	function close() {
		$("body").removeClass('imgbox-show');
		var _show = $(".imgbox.show");
		_show.addClass('hideing').removeClass("show");
		full_out();
		setTimeout(function () {
			_show.removeClass("hideing").addClass("hide").attr('style', '');
			$('.toggle-palay.is-play').removeClass("is-play");
			$.each(window.imgbox, function (name, value) {
				try {
					value = value[0] || value;
					value.zoom.out();
				} catch (e) {}
			});
			$('.swiper-close').css({
				'opacity': "",
				'transform': "",
			});
		}, 400);
	}

	function touch_close(_id) {
		$("#" + _id).on('touchmove pointermove MSPointerMove', function (e) {
			e.preventDefault ? e.preventDefault() : e.returnValue = !1;
		});
		$("#" + _id).minitouch({
			direction: 'bottom',
			selector: '.swiper-close',
			depreciation: 100,
			onStart: false,
			stop: function (_e, _this, distanceX, distanceY) {
				var scale = _this.find('img').css("transform").replace(/[^0-9\-,]/g, '').split(',')[0];
				return (scale && scale > 1);
			},
			onIng: function (_e, _this, distanceX, distanceY) {
				var set_opacity = (200 - distanceY) / 100;
				set_opacity = set_opacity < 1 && set_opacity;
				_this.css('opacity', set_opacity);
			},
			inEnd: function (_e, _this, distanceX, distanceY) {
				(distanceY <= 50) && _this.css({
					'opacity': "",
				});
			},
			onEnd: function (_e, _this, distanceX, distanceY) {
				close();
				_this.css({
					'transform': "translateY(" + (distanceY + 200) + "px)",
				});
			}
		});
	}


	function get_slide(each_this) {
		var thumbs_src = each_this.attr("src");
		var data_src = each_this.attr("data-src");
		if (!thumbs_src && !data_src) return '';
		var full_src = each_this.attr("data-full-url") || link_replace((data_src || thumbs_src));
		var main_img = (thumbs_src == full_src) ? '<img src="' + full_src + '" class="lazyloaded">' : '<img src="' + thumbs_src + '" data-src="' + full_src + '"  class="lazyload"><div class="swiper-lazy-preloader"></div>';
		var main_img_html = '<div class="swiper-slide"><div class="swiper-close"><div class="swiper-zoom-container"><div class="absolute img-close"></div>' + main_img + '</div></div></div>';
		return main_img_html;
	}

	function get_thumbs(each_this) {
		var thumbs_src_b = each_this.attr("data-src") || each_this.attr("src");
		if (!thumbs_src_b) return '';
		var thumbs_img = '<img data-src="' + thumbs_src_b + '"  class="lazyload fit-cover">';
		var thumbs_img_html = '<div class="swiper-slide">' + thumbs_img + '</div>';
		return thumbs_img_html;
	}

	function new_swiper(id, is_group, show_thumbs) {
		tbquire(['swiper'], function () {
			var selector = '.' + id;
			var speed = ~~(($(window).width() + 800) / 310);
			var option = {};
			option['init'] = false;
			option['speed'] = speed * 100;
			option['zoom'] = {
				maxRatio: ($(window).width() < 768 ? 3 : 2)
			};

			if (is_group) {
				option['autoplay'] = {
					disableOnInteraction: false
				};
				option['grabCursor'] = true;
				option['navigation'] = {
					nextEl: selector + " .swiper-button-next",
					prevEl: selector + " .swiper-button-prev"
				};
				option['keyboard'] = {
					enabled: !0,
					onlyInViewport: !1
				};
				option['on'] = {
					slideChange: function () {
						$(selector + " .counter-con").html('<badge class="b-black counter">' + (this.realIndex + 1) + '/' + this.slides.length + '</badge>')
					}
				}
			}
			if (show_thumbs) {
				option['thumbs'] = {
					swiper: window.imgbox[id + '_thumbs'],
					autoScrollOffset: ~~(speed / 1.6),
				};
			}
			window.imgbox[id] = new Swiper(selector, option);
		})
	}

	function new_thumbs_swiper(id) {
		tbquire(['swiper'], function () {
			var selector = '.swiper-thumbsbox.' + id;
			var option = {};
			option['init'] = false;
			option['watchSlidesVisibility'] = true; //防止不可点击
			option['navigation'] = {
				nextEl: selector + " .swiper-button-next",
				prevEl: selector + " .swiper-button-prev"
			};
			option.slidesPerView = 'auto';
			option.freeMode = true;
			option.freeModeSticky = true;
			window.imgbox[id + '_thumbs'] = new Swiper(selector, option);
		})
	}

	function is_no(_this) {
		//判断上级是否有链接，且不是链接到原图
		var src = _this.attr("data-src") || _this.attr("src");
		if (!src) return false;

		src = link_replace(src);
		var parent = _this.parent('a');
		if (!parent.length) return false;
		var href = parent.attr('box-img') || parent.attr('href');
		href = link_replace(href);
		return (href && (href.indexOf(src) == -1 || src.indexOf(href) == -1));
	}

	var group_open = function (e) {
		var _this = $(e);
		if (!_this.length) return;
		//必须为图片<img>

		var show_thumbs = box_option.show_thumbs;
		var tupian = '';
		var thumbs = '';
		var rand_id = 'swiper-imgbox-' + (parseInt((Math.random() + 1) * Math.pow(10, 4)));

		_this.each(function (index) {
			var each_this = $(this);
			if (!is_no(each_this)) {
				each_this.attr({
					'imgbox-id': rand_id,
					'imgbox-index': index
				});
				tupian += get_slide(each_this);
				//缩略图
				if (show_thumbs) thumbs += get_thumbs(each_this);
			}
		});

		var wrapper = '<div class="swiper-wrapper">' + tupian + '</div>';
		var swiper = '<div class="swiper-imgbox ' + rand_id + '">' + wrapper + '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div><div class="abs-left counter-con"></div></div>';

		var thumbs_wrapper = '<div class="swiper-wrapper">' + thumbs + '</div>';
		var thumbs_swiper = '<div class="swiper-thumbsbox ' + rand_id + '">' + thumbs_wrapper + '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div><div class="abs-center right-top"><i class="hide-but fa fa-angle-down em12"></i></div></div>';

		if (show_thumbs) swiper += thumbs_swiper;

		var _class = 'group-box' + (show_thumbs ? ' thumbs-box' : '');
		append(_class, swiper, rand_id, true);

		if (show_thumbs) new_thumbs_swiper(rand_id);
		new_swiper(rand_id, true, show_thumbs);

		_body.OnlyOn('click', e, 'imgbox_group_open', function () {
			var _id = $(this).attr(data_tag_id);
			if (!_id) return;
			var _index = $(this).attr(data_tag_index);
			var swiper = window.imgbox[_id];
			swiper = swiper[0] || swiper;
			var thumbs = window.imgbox[_id + '_thumbs'];
			show('#' + _id, $(this));
			thumbs && thumbs.init();
			swiper.init();
			swiper.slideToLoop(_index, 10);
			swiper.autoplay.stop();
			return !1;
		});
	}

	var alone_open = function (e) {
		if (!$(e).length) return;

		//不一定为图片，可以是链接，链接内有图片则退出
		var rand_id = 'swiper-imgbox-alone';
		var _id = '#' + rand_id;
		if (!$(_id).length) {
			var tupian = '<div class="swiper-slide"><div class="swiper-close"><div class="swiper-zoom-container alone-box-container"><div class="absolute img-close"></div><img src=""><div class="swiper-lazy-preloader"></div></div></div></div>';
			var wrapper = '<div class="swiper-wrapper">' + tupian + '</div>';
			var swiper = '<div class="swiper-imgbox ' + rand_id + '">' + wrapper + '</div>';
			append('alone-box', swiper, rand_id);
			new_swiper(rand_id);
		}
		var _box = $(_id);

		_body.OnlyOn('click', e, 'imgbox_alone_open', function () {

			var _this = $(this);
			//如果内部还有图片则停止，或者上级有链接且不是连接到原图
			if (_this.find('img').attr(data_tag_id) || is_no(_this)) return;

			var _href = _this.attr("box-img") || _this.attr("href");
			var thumbs_src = _href || _this.attr("src");
			var full = _href ? link_replace(_href) : _this.attr("data-full-url") || link_replace((_this.attr("data-src") || thumbs_src));

			var img_html = (thumbs_src == full) ? '<img src="' + full + '" class="lazyloaded">' : '<img src="' + thumbs_src + '" data-src="' + full + '" class="lazyload">';

			var is_img = _box.find('.alone-box-container>img');
			is_img.attr("src") != full && is_img.prop("outerHTML", img_html);

			window.imgbox[rand_id].init();
			window.imgbox[rand_id].update();

			show(_id, _this);
			return !1;
		});
	}

	return {
		group: group_open,
		alone: alone_open
	};
})(window);

if (_win.imgbox_type == 'group') {
	//此函数同一个选择器不能重复执行
	Miniimgbox.group('.wp-posts-content img:not(.no-imgbox,.avatar,.img-icon,.avatar-badge)');
	Miniimgbox.group('[data-imgbox="payimg"] img');
} else {
	Miniimgbox.alone('.wp-posts-content img:not(.no-imgbox,.avatar,.img-icon,.avatar-badge)');
}

Miniimgbox.alone('.comment-content .box-img,.alone-imgbox-img,a[data-imgbox]');