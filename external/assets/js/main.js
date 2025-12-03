(function (window, document, $, undefined) {
	'use strict';

	var NEWS = {
		i: function (e) {
			NEWS.s();
			NEWS.methods();
		},

		s: function (e) {
			this._window = $(window),
				this._document = $(document),
				this._body = $('body'),
				this._html = $('html'),
				this.sideNav = $('.side-nav'),
				this.zoomGallery = $('.zoom-gallery'),
				this.niceScrollContainer = $('.nicescroll-container'),
				this.yScrollContainer = $('.y-scroll-container'),
				this._bannerSidebarMediaWrapper = $(".axil-banner-sidebar-media-wrapper"),
				this._navbarSearch = $(".navbar-search"),
				this._subscribePopUp = $('.subscribe-popup'),
				this.videoPopup = $('.video-popup');
		},

		methods: function (e) {
			NEWS.w();
			NEWS._clickDoc();
			NEWS._click();
			NEWS.menuItemAnim();
			NEWS.owl();
			NEWS.counterUp();
			NEWS.magnificPopup();
			NEWS.niceScrollInit();
			NEWS.yScrollInit();
			NEWS.slickSync();
			NEWS.isIE();
			NEWS.initContactForm();
			NEWS.initWebstories();
		},

		w: function (e) {
			this._window.on('load', NEWS.l).on('scroll', NEWS.scrl).on('resize', NEWS.res)
		},

		l: function (e) {
			NEWS.perfectSquare();
			NEWS.shapeLoaded();
			NEWS.animPostBannerContent();
			NEWS._masonryInit();
			NEWS._isotopInit();
			NEWS.loadSubscribePopup();
		},

		scrl: function () {

		},

		res: function () {
			NEWS.perfectSquare();
		},

		loadSubscribePopup: function () {
			setTimeout(function () {
				NEWS._subscribePopUp.addClass('show-popup');
			}, 3000);
		},

		_clickDoc: function (e) {
			var smoothScroll, openMainNav, openSubmenu, closeSubmenu, closeSideNav, openSideNav, inputFocus, inputblur, toggleShares, scrollPostUp, scrollPostDown, navSearchShow, navSearchHide, sideNavHover, removeHoverEffect, subscribePopupHide;

			//SMOOTHSCROLL
			smoothScroll = function (e) {

				if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
					var $f = target.offset().top,
						$g = $f - 40,
						$y;

					if (target.length) {
						$('#section-featured_work').is(target) ? $y = $g : $y = $f;
						$('html, body').animate({
							scrollTop: $y
						}, 1000, 'easeInOutExpo');
						return !1;
					}
				}
			};

			openMainNav = function () {

				if (NEWS._html.hasClass('main-menu-opened')) {
					NEWS._html.removeClass('main-menu-opened');
					$(this).removeClass('expanded');
					$('.main-navigation').children('li').removeAttr('style');
				} else {
					$(this).addClass('expanded');

					setTimeout(function () {
						NEWS._html.addClass('main-menu-opened');
						$('.main-menu-opened .main-navigation').children('li').each(function (index) {
							$(this).delay(80 * index)
								.animate({
									opacity: "1",
									top: '0'
								}, 300);
						});
					}, 800);
				}
			};

			openSubmenu = function (e) {
				if (NEWS._window.width() < 992) {
					$(this).siblings('.submenu').slideToggle(500, "easeInOutQuint").parent('li').toggleClass('active');
					$(this).parents('li').siblings().removeClass('active').find('.submenu').slideUp(500, "easeInOutQuint");
				} else {
					$(this).siblings('.submenu').toggleClass('opened').parent('li').toggleClass('active');
					$(this).parents('li').siblings('.has-dropdown').removeClass('active').find('.submenu').removeClass('opened');
				}
			};

			closeSubmenu = function (e) {
				if (!$('.main-navigation li, .main-navigation li a').is(e.target)) {
					$('.submenu').removeClass('opened').parent('li').removeClass('active');
				}
			};

			openSideNav = function () {
				NEWS.sideNav.addClass('opened');
				NEWS._html.addClass('side-nav-opened');

				setTimeout(function () {
					$('.side-nav-opened .side-navigation').children('li').each(function (index) {
						$(this).delay(100 * index)
							.animate({
								opacity: "1",
								left: '0'
							}, 100);
					});
				}, 500);
			};

			closeSideNav = function (e) {
				if (!$('.side-nav, .side-nav *:not(".close-sidenav")').is(e.target) && !$('.side-nav-toggler, .side-nav-toggler *').is(e.target)) {
					NEWS.sideNav.removeClass('opened');
					NEWS._html.removeClass('side-nav-opened');
					$('.side-navigation').children('li').removeAttr('style');
				}
			};

			inputFocus = function (e) {
				$(this).parents(".form-group").addClass('focused');
			};

			inputblur = function (e) {
				if (!$(this).val()) {
					$(this).parent(".form-group").removeClass('focused');
				}
			};

			toggleShares = function () {
				$(this).siblings('.social-share-wrapper').toggleClass('show-shares');
			};

			scrollPostDown = function (e) {
				e.preventDefault();
				NEWS._bannerSidebarMediaWrapper.animate({
					scrollTop: '+=200'
				}, 800, "easeInOutExpo");
			};

			scrollPostUp = function (e) {
				e.preventDefault();
				NEWS._bannerSidebarMediaWrapper.animate({
					scrollTop: '-=200'
				}, 800, "easeInOutExpo");
			};

			navSearchShow = function (e) {
				e.preventDefault();
				NEWS._navbarSearch.addClass('show-nav-search');
			};

			navSearchHide = function (e) {
				e.preventDefault();
				NEWS._navbarSearch.removeClass('show-nav-search');
			};

			sideNavHover = function (e) {
				e.preventDefault();
				$(this).removeClass('hover-removed').addClass('hovered').siblings('li').addClass('hover-removed').removeClass('hovered');
			};

			removeHoverEffect = function (e) {
				e.preventDefault();
				$(this).find('li').removeClass('hover-removed hovered');
			};

			subscribePopupHide = function (e) {
				if (!$('.subscribe-popup-inner, .subscribe-popup-inner *:not(.close-popup,.close-popup i)').is(e.target)) {
					NEWS._subscribePopUp.fadeOut("300");
				}
			};

			NEWS._document
				.on('click', '.page-scroll', smoothScroll)
				.on('click', '.main-navigation .has-dropdown > a', openSubmenu)
				.on('click', closeSubmenu)
				.on('click', '#close-sidenav', closeSideNav)
				.on('click', closeSideNav)
				.on('click', '.close-popup', subscribePopupHide)
				.on('click', subscribePopupHide)
				.on('click', '#side-nav-toggler', openSideNav)
				.on('focus', 'input:not([type="radio"]),input:not([type="checkbox"]),textarea,select', inputFocus)
				.on('blur', 'input,textarea,select', inputblur)
				.on('click', '.toggle-shares', toggleShares)
				.on('click', '.axil-post-scroll-down', scrollPostDown)
				.on('click', '.axil-post-scroll-up', scrollPostUp)
				.on('click', '.nav-search-field-toggler', navSearchShow)
				.on('click', '.navbar-search-close', navSearchHide)
				.on('mouseenter', '.side-navigation li', sideNavHover)
				.on('mouseout', '.side-navigation', removeHoverEffect)
				.on('click', '#main-nav-toggler', openMainNav);
		},

		_click: function (e) {
			NEWS.b();
			NEWS.c();
		},

		b: function (e) {},
		c: function (e) {},
		isIE: function () {
			var ua = navigator.userAgent;
			var is_ie = ua.indexOf("MSIE ") > -1 || ua.indexOf("Trident/") > -1;

			return is_ie;
		},
		menuItemAnim: function (e) {
			$('.rotate-txt li').each(function () {
				var txt = $(this).find('> a').text();

				$(this).find(">a").attr('data-txt', txt);
			});
		},

		owl: function () {
			var _owlContainer = $('.owl-carousel:not(.owl-synced)'),
				_owlCatContainer = $('.category-carousel .owl-carousel'),
				_customPrevBtn = '.custom-owl-prev',
				_customNextBtn = '.custom-owl-next';

			if (_owlContainer.length) {
				_owlContainer.each(function () {

					var _defaults = {
						loop: true,
						margin: 10,
						responsiveClass: true,
						items: 1,
						autoplay: true,
						nav: false,
						dots: false,
						animateIn: false,
						animateOut: false
					}
					//vars
					var _loop = $(this)[0].hasAttribute("data-owl-loop") ? $(this).data('owl-loop') : _defaults.loop,
						_items = $(this)[0].hasAttribute("data-owl-items") ? $(this).data('owl-items') : _defaults.items,
						_dots = $(this)[0].hasAttribute("data-owl-dots") ? $(this).data('owl-dots') : _defaults.dots,
						_nav = $(this)[0].hasAttribute("data-owl-nav") ? $(this).data('owl-nav') : _defaults.nav,
						_margin = $(this)[0].hasAttribute("data-owl-margin") ? $(this).data('owl-margin') : _defaults.margin,
						_animateIn = $(this)[0].hasAttribute("data-owl-animate-in") ? $(this).data('owl-animate-in') : _defaults.animateIn,
						_animateOut = $(this)[0].hasAttribute("data-owl-animate-out") ? $(this).data('owl-animate-out') : _defaults.animateOut,
						_autoplay = $(this)[0].hasAttribute("data-owl-autoplay") ? $(this).data('owl-autoplay') : _defaults.autoplay;

					if (NEWS.isIE()) {
						_animateIn = false;
						_animateOut = false;
					}

					//init
					_owlContainer.owlCarousel({
						loop: _loop,
						margin: _margin,
						responsiveClass: true,
						items: _items,
						autoplay: _autoplay,
						nav: _nav,
						dots: _dots,
						animateIn: _animateIn,
						animateOut: _animateOut,
						navText: ['<i class="feather icon-chevron-left"></i>', '<i class="feather icon-chevron-right"></i>']
					});

					// Trigger counter when slide changes
					if (_owlCatContainer.length) {

						// Go to the previous item
						NEWS._document.on('click', _customPrevBtn, function () {
							_owlCatContainer.trigger('prev.owl.carousel', [300]);
						});

						// Go to the next item
						NEWS._document.on('click', _customNextBtn, function () {
							_owlCatContainer.trigger('next.owl.carousel', [300]);
						});
					}
				});
			}
		},

		slickSync: function () {
			var _slickCont = $('.slick-slider'),
				_slickFor = $('.slick-slider-nav');

			if (_slickCont.length) {

				var _prevArrow = '<button type="button" class="slick-prev"><i class="feather icon-chevron-left"></i></button>',
					_nextArrow = '<button type="button" class="slick-next"><i class="feather icon-chevron-right"></i></button>';

				var _defaults = {
					items: 1,
					dots: false,
					arrows: false,
					infinite: true,
					centerMode: false,
					variableWidth: false
				}

				//vars
				var _items = _slickFor[0].hasAttribute("data-slick-items") ? _slickFor.data('slick-items') : _defaults.items,
					_dots = _slickFor[0].hasAttribute("data-slick-dots") ? _slickFor.data('slick-dots') : _defaults.dots,
					_loop = _slickFor[0].hasAttribute("data-slick-loop") ? _slickFor.data('slick-loop') : _defaults.infinite,
					_center = _slickFor[0].hasAttribute("data-slick-center") ? _slickFor.data('slick-center') : _defaults.centerMode,
					_autoWidth = _slickFor[0].hasAttribute("data-slick-autowidth") ? _slickFor.data('slick-autowidth') : _defaults.variableWidth,
					_arrows = _slickFor[0].hasAttribute("data-slick-arrows") ? _slickFor.data('slick-arrows') : _defaults.arrows;


				$('.slick-slider-for').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: false,
					fade: false,
					dots: true,
					asNavFor: '.slick-slider-nav,.banner-share-slider',
					adaptiveHeight: true,
					autoplaySpeed: 2000
				});
				$('.slick-slider-nav').slick({
					slidesToShow: _items,
					slidesToScroll: 1,
					asNavFor: '.slick-slider-for,.banner-share-slider',
					prevArrow: _prevArrow,
					nextArrow: _nextArrow,
					arrows: true,
					dots: false,
					infinite: _loop,
					centerMode: _center,
					centerPadding: "0",
					autoplaySpeed: 2000,
					responsive: [{
							breakpoint: 1024,
							settings: {
								slidesToShow: _items
							}
						},
						{
							breakpoint: 991,
							settings: {
								slidesToShow: 1,
								centerMode: false
							}
						},
						{
							breakpoint: 767,
							settings: {
								slidesToShow: 1,
								centerMode: false
							}
						}
						// You can unslick at a given breakpoint now by adding:
						// settings: "unslick"
						// instead of a settings object
					]
				});

				$('.banner-share-slider').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: false,
					dots: false,
					vertical: true,
					autoplaySpeed: 2000
				});

			}
		},

		counterUp: function () {
			var _counter = $('.counter');
			if (_counter.length) {
				_counter.counterUp({
					delay: 10,
					time: 1000,
					triggerOnce: true
				});
			}
		},

		perfectSquare: function () {
			var _square = $('.perfect-square');

			_square.each(function () {
				var squareWidth = $(this).width();
				$(this).height(squareWidth);
			});
		},

		_masonryInit: function () {

			var _masonryGrid = $('.masonry-grid');

			if (_masonryGrid.length) {
				_masonryGrid.isotope({
					itemSelector: '.grid-item',
					percentPosition: true,
					masonry: {
						columnWidth: '.grid-item'
					}
				});
			}

		},

		//isotope initialization
		_isotopInit: function () {
			var _axilIsoGrid = $('.axil-iso-grid'),
				_filterBtnGrp = $('.axil-filter-button-group');

			if (_axilIsoGrid.length) {
				_axilIsoGrid.each(function () {});
				var $grid = _axilIsoGrid.isotope({
					// options
					itemSelector: '.iso-grid-item',
					layoutMode: 'masonry'
				});
			}

			// filter items on button click

			_filterBtnGrp.each(function () {
				$(this).on('click', '.filter-btn', function () {
					var filterValue = $(this).attr('data-filter');
					$grid.isotope({
						filter: filterValue
					});
				});
			});


			_filterBtnGrp.each(function (i, buttonGroup) {
				var $buttonGroup = $(buttonGroup);
				$buttonGroup.on('click', '.filter-btn', function () {
					$buttonGroup.find('.is-checked').removeClass('is-checked');
					$(this).addClass('is-checked');
				});
			});
		},

		magnificPopup: function () {
			if (NEWS.videoPopup.length) {
				NEWS.videoPopup.magnificPopup({
					type: 'iframe',
					mainClass: 'mfp-fade',
					iframe: {
						markup: '<div class="mfp-iframe-scaler">' + '<div class="mfp-close"></div>' + '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' + '</div>',
						patterns: {
							youtube: {
								index: 'youtube.com/',
								id: 'v=',
								src: '//www.youtube.com/embed/%id%?autoplay=1'
							}
						},
						srcAction: 'iframe_src'
					},
					gallery: {
						enabled: true
					},
					zoom: {
						enabled: true,
						duration: 300, // don't foget to change the duration also in CSS
						opener: function (element) {
							return element.find('img');
						}
					}
				});
			}

			if (NEWS.zoomGallery.length) {
				NEWS.zoomGallery.magnificPopup({
					delegate: 'a:not(.gallery-lightbox)',
					type: 'image',
					closeOnContentClick: false,
					closeBtnInside: false,
					mainClass: 'mfp-with-zoom mfp-img-mobile',
					image: {
						verticalFit: true
					},
					gallery: {
						enabled: true
					},
					zoom: {
						enabled: true,
						duration: 300, // don't foget to change the duration also in CSS
						opener: function (element) {
							return element.find('img');
						}
					}

				});
			}
		},

		shapeLoaded: function () {
			var _shape1 = $('.axil-shape-circle'),
				_shape2 = $('.axil-shape-circle__two');

			if (_shape1.length && _shape2.length) {
				_shape1.addClass('shape-loaded');
				_shape2.addClass('shape-loaded');
			}
		},
		niceScrollInit: function () {
			NEWS.niceScrollContainer.niceScroll({
				cursorcolor: "#D3D7DA",
				cursorborderradius: "0"
			});
		},

		yScrollInit: function () {
			if (NEWS._window.width() > 991) {
				NEWS.yScrollContainer.niceScroll({
					cursorcolor: "#D3D7DA",
					cursorborderradius: "0",
					horizrailenabled: false
				});
			}
		},
		animPostBannerContent: function () {
			var _loadAnimWrapper = $('.load-anim-wrapper');
			_loadAnimWrapper.each(function () {
				$(this).find('.load-anim').each(function (index) {
					$(this).delay(200 * index)
						.animate({
							opacity: "1",
							top: '0'
						}, 800);
				});
			});
		},

		initContactForm: function () {
			$('.axil-contact-form').on('submit', function (e) {
				e.preventDefault();
				var _self = $(this);
				var __selector = _self.closest('input,textarea');
				_self.closest('div').find('input,textarea').removeAttr('style');
				_self.find('.error-msg').remove();
				_self.closest('div').find('button[type="submit"]').attr('disabled', 'disabled');
				var data = $(this).serialize();
				$.ajax({
					url: 'assets/scripts/email.php',
					type: "post",
					dataType: 'json',
					data: data,
					success: function (data) {
						_self.closest('div').find('button[type="submit"]').removeAttr('disabled');
						if (data.code == false) {
							_self.closest('div').find('[name="' + data.field + '"]');
							_self.find('.btn-primary').after('<div class="error-msg"><p>*' + data.err + '</p></div>');
						} else {
							$('.error-msg').hide();
							$('.form-group').removeClass('focused');
							_self.find('.btn-primary').after('<div class="success-msg"><p>' + data.success + '</p></div>');
							_self.closest('div').find('input,textarea').val('');

							setTimeout(function () {
								$('.success-msg').fadeOut('slow');
							}, 5000);
						}
					}
				});
			});
		},

		initWebstories: function () {
			var STORY_DURATION = 6000;
			var currentStoryId = '';
			var currentSlideIndex = 0;
			var totalSlides = 0;
			var autoplayTimer = null;

			var modalSelector = '#webstoryModal';
			var $modal = $();
			var $slidesContainer = $();
			var $caption = $();
			var $progress = $();

			function isVisible() {
				return bindModalElements() && $modal.hasClass('is-visible');
			}

			function bindModalElements() {
				$modal = $(modalSelector);

				if (!$modal.length) {
					$slidesContainer = $();
					$caption = $();
					$progress = $();
					return false;
				}

				$slidesContainer = $modal.find('.webstory-slides');
				$caption = $modal.find('.webstory-caption-text');
				$progress = $modal.find('.webstory-progress');
				return true;
			}

			function clearAutoplay() {
				if (autoplayTimer) {
					clearTimeout(autoplayTimer);
					autoplayTimer = null;
				}
			}

			function startAutoplay() {
				clearAutoplay();

				if (totalSlides <= 1) {
					return;
				}

				autoplayTimer = setTimeout(function () {
					showSlide(currentSlideIndex + 1);
				}, STORY_DURATION);
			}

			function buildProgress(count) {
				if (!bindModalElements()) {
					return;
				}

				$progress.empty();

				for (var i = 0; i < count; i++) {
					var $segment = $('<div/>', { 'class': 'webstory-progress-segment' });
					$segment.append($('<span/>', { 'class': 'webstory-progress-fill' }));
					$progress.append($segment);
				}
			}

			function animateProgress(index) {
				if (!bindModalElements()) {
					return;
				}

				var $segments = $progress.find('.webstory-progress-segment');

				$segments.each(function (i) {
					var $fill = $(this).find('.webstory-progress-fill');
					if (!$fill.length) {
						return;
					}

					var widthValue = i < index ? '100%' : '0%';
					$fill.css({ transition: 'none', width: widthValue });
				});

				var $currentFill = $segments.eq(index).find('.webstory-progress-fill');
				if ($currentFill.length) {
					$currentFill.css({ transition: 'none', width: '0%' });
					void $currentFill[0].offsetWidth;
					$currentFill.css({ transition: 'width ' + STORY_DURATION + 'ms linear', width: '100%' });
				}
			}

			function updateCaption($slide) {
				if (!bindModalElements()) {
					return;
				}

				var caption = $slide.data('caption') || '';
				$caption.text(caption);
			}

			function showSlide(index) {
				if (totalSlides === 0) {
					return;
				}

				if (index >= totalSlides) {
					closeWebstory();
					return;
				}

				if (index < 0) {
					index = 0;
				}

				currentSlideIndex = index;

				var $slides = $slidesContainer.children('.webstory-slide');
				$slides.removeClass('active');
				var $activeSlide = $slides.eq(index).addClass('active');

				updateCaption($activeSlide);
				animateProgress(index);
				startAutoplay();
			}

			function populateSlides(storyId, fallbackData) {
				if (!bindModalElements()) {
					return;
				}

				var $collection = $('.webstory-slide-collection[data-story="' + storyId + '"]');
				if (!$collection.length) {
					console.warn('[Webstories] No slide collection found for story:', storyId);
					totalSlides = 0;
					return;
				}

				var $sourceSlides = $collection.children('.webstory-slide');
				$slidesContainer.empty();

				$sourceSlides.each(function () {
					var $clone = $(this).clone();
					$slidesContainer.append($clone);
				});

				totalSlides = $slidesContainer.children('.webstory-slide').length;

				if (totalSlides === 0) {
					console.warn('[Webstories] Slide collection empty for story:', storyId);

					if (fallbackData && fallbackData.cover) {
						var $fallbackSlide = $('<div/>', {
							'class': 'webstory-slide active',
							'data-story': storyId,
							'data-caption': fallbackData.title || ''
						});

						$('<img/>', {
							'class': 'webstory-slide-image',
							'src': fallbackData.cover,
							'alt': fallbackData.title || 'Web Story'
						}).appendTo($fallbackSlide);

						if (fallbackData.title) {
							$('<div/>', {
								'class': 'webstory-slide-caption',
								'text': fallbackData.title
							}).appendTo($fallbackSlide);
						}

						$slidesContainer.append($fallbackSlide);
						totalSlides = 1;
					}
				}
			}

			function openWebstory(storyId, fallbackData) {
				if (!bindModalElements()) {
					return;
				}

				console.log('[Webstories] openWebstory', storyId, fallbackData);

				clearAutoplay();
				populateSlides(storyId, fallbackData);

				if (totalSlides === 0) {
					console.warn('[Webstories] openWebstory aborted – no slides for', storyId);
					return;
				}

				currentStoryId = storyId;
				currentSlideIndex = 0;

				buildProgress(totalSlides);
				$modal.addClass('is-visible').attr('aria-hidden', 'false');
				$('body').addClass('modal-open');

				showSlide(0);
			}

			function closeWebstory() {
				if (!bindModalElements()) {
					return;
				}

				clearAutoplay();
				currentStoryId = '';
				currentSlideIndex = 0;
				totalSlides = 0;

				$modal.removeClass('is-visible').attr('aria-hidden', 'true');
				$('body').removeClass('modal-open');
				$slidesContainer.empty();
				$progress.empty();
				$caption.text('');
			}

			function nextSlide() {
				if (totalSlides === 0) {
					return;
				}

				showSlide(currentSlideIndex + 1);
			}

			function prevSlide() {
				if (totalSlides === 0) {
					return;
				}

				var targetIndex = currentSlideIndex - 1;
				if (targetIndex < 0) {
					targetIndex = 0;
				}

				showSlide(targetIndex);
			}

			$(document).on('click', '.webstory-trigger', function (e) {
				e.preventDefault();
				var $trigger = $(this);
				var $card = $trigger.closest('.webstory-card');
				var storyId = $trigger.attr('data-story') || $card.attr('data-story');
				var fallbackCover = $trigger.attr('data-cover') || $card.attr('data-cover') || '';
				var fallbackTitle = $trigger.attr('data-title') || $card.attr('data-title') || '';

				console.log('[Webstories] Trigger click', {
					storyId: storyId,
					fallbackCover: fallbackCover,
					fallbackTitle: fallbackTitle
				});

				if (!storyId) {
					console.warn('[Webstories] Story trigger missing data-story attribute.', this);
					return;
				}

				openWebstory(storyId, {
					cover: fallbackCover,
					title: fallbackTitle
				});
			});

			$(document).on('click', '#webstoryModal', function (e) {
				if (e.target === this) {
					closeWebstory();
				}
			});

			$(document).on('click', '#webstoryModal .webstory-close', function () {
				closeWebstory();
			});

			$(document).on('click', '#webstoryModal .tap-next', function (e) {
				e.preventDefault();
				nextSlide();
			});

			$(document).on('click', '#webstoryModal .tap-prev', function (e) {
				e.preventDefault();
				prevSlide();
			});

			$(document).on('click', '#webstoryModal .webstory-slides', function (e) {
				var bounds = this.getBoundingClientRect();
				var clickX = e.clientX - bounds.left;

				if (clickX > bounds.width / 2) {
					nextSlide();
				} else {
					prevSlide();
				}
			});

			$(document).on('keydown', function (e) {
				if (e.keyCode === 27 && NEWS._navbarSearch.hasClass('show-nav-search')) {
					NEWS._navbarSearch.removeClass('show-nav-search');
				}

				if (!isVisible()) {
					return;
				}

				if (e.keyCode === 27) {
					closeWebstory();
				} else if (e.keyCode === 37) {
					prevSlide();
				} else if (e.keyCode === 39) {
					nextSlide();
				}
			});

			$(document).on('mouseenter mouseleave', '#webstoryModal', function (e) {
				if (totalSlides === 0) {
					return;
				}

				if (e.type === 'mouseenter') {
					clearAutoplay();
				} else {
					startAutoplay();
				}
			});

			document.addEventListener('visibilitychange', function () {
				if (!isVisible()) {
					return;
				}

				if (document.hidden) {
					clearAutoplay();
				} else {
					startAutoplay();
				}
			});

			window.openWebstory = function(storyId, fallbackData) {
				console.log('[Webstories] window.openWebstory called', storyId, fallbackData);
				openWebstory(storyId, fallbackData || {});
			};
			window.closeWebstory = closeWebstory;
			window.openGallery = function(storyId, fallbackData) {
				openWebstory(storyId, fallbackData || {});
			};
			window.closeGallery = closeWebstory;
		}
	};

	NEWS.i();
	
})(window, document, jQuery);

