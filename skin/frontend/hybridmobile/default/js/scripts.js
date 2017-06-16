jQuery.noConflict();
jQuery(function($) {
    var $dataajax = '.switchtodesktop, #logo a, li.home a, a.product-link, .product-name a, .product-shop .add-to-cart a, button #idsend2, button #idsend, .advancedsearch, a.top-link-checkout';
    // page-back
    // $('.super-attribute-select').removeAttr("disabled");
    // $('.add-to-cart .link-wishlist, button.btn-checkout').attr("data-role", "button");
    // $('.page-title')
    //     .addClass('ui-header ui-bar-inherit')
    //     .find('h1')
    //     .addClass('ui-title');

    var isRetina = (
        window.devicePixelRatio > 1 ||
        (window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches)
    );
    if (isRetina) {
        $('img[data-srcX2]').each(function(){
            $(this).attr('src', $(this).attr('data-srcX2'));
        });
    }

    // Handle configurable products
    var $configSelects = $('select', '#product-options-wrapper');
    $configSelects.on('change', function() {
        $configSelects.each(function() {
            $(this).is(':disabled') ? $(this).selectmenu('disable') : $(this).selectmenu('enable');
        });
    });

    var $searchWrapper = $('.searchbar');
    $('#open-search').on('click', function(e) {
        e.preventDefault();
        if ($searchWrapper.is('.animate')) {
            $searchWrapper.removeClass('animate').find('input').blur();
        } else {
            $searchWrapper.addClass('animate');
        }
    });

    $('.category-products-grid .product-name').ellipsis({
        lines: 2,
        ellipClass: 'ellip'
    });

    $('.header').on('touchmove', function(e) {
        e.preventDefault();
    });

    FastClick.attach(document.body);

    var CategoryMenu = function($el) {
        var $categoryMenu = $el,
            $back = $('#go-back'),
            $menuOverlay = $el.find('.menu-overlay'),
            $menuLayers = $categoryMenu.find('.swiper-root .menu-layer'),
            $subSwipers = $categoryMenu.find('.swiper-root .swiper-container'),
            $swiperRoot;

        $back.data('visible', ($back.is('.off') ? false : true)).data('submenus', []);
        return {
            init: function() {
                var that = this;

                var fixLayerPosition = function(s) {
                    $(s.container[0]).find('> .swiper-wrapper').children('.menu-layer').css('top', -s.translate);
                };

                var swiperOptions = {
                    direction: 'vertical',
                    slidesPerView: 'auto',
                    freeMode: true,
                    freeModeSticky: true,
                    touchAngle: 90,
                    onTouchEnd: fixLayerPosition,
                    onTransitionEnd: fixLayerPosition
                };

                $swiperRoot = $categoryMenu.find('.swiper-root').swiper(swiperOptions);

                $subSwipers.swiper(swiperOptions);

                $categoryMenu.on('click', '.menu-item', function(e) {
                    e.preventDefault();
                    that.openSubMenu($(this).parent());
                });

                $menuLayers.on('transitionend webkitTransitionEnd', function() {
                    if (!$(this).is('.active')) {
                        $(this).find('> .swiper-container')[0].swiper.slideTo(0);
                    }
                });

                $menuOverlay.on({
                    'click': function() {
                        that.closeSubMenu();
                    },
                    'touchmove': function(e) {
                        e.preventDefault();
                    },
                    'transitionend webkitTransitionEnd': function() {
                        if (!$menuOverlay.is('.active')) {
                            $($swiperRoot.container[0]).find('> .swiper-wrapper > .active').removeClass('active');
                        }
                    }
                });

                $back.on('click', function(e) {
                    e.preventDefault();
                    var state = $back.data('state');
                    if (state == 'page-back') {
                        window.history.go(-1);
                    } else if (state == 'close-menu') {
                        that.toggleMenu();
                    } else if (state == 'close-submenu') {
                        that.closeSubMenu();
                    }
                });
            },
            fixHeight: function() {
                $swiperRoot.onResize();
                $subSwipers.each(function() {
                    this.swiper.onResize();
                });
            },
            toggleMenu: function() {
                $categoryMenu.toggleClass('active');
                if ($categoryMenu.is('.active')) {
                    $back.removeClass('off').data('state', 'close-menu');
                    this.fixHeight();
                    $.mobile.silentScroll(0);
                } else {
                    if ($back.data('visible')) {
                        $back.data('state', 'page-back');
                    } else {
                        $back.addClass('off');
                    }
                    this.closeSubMenu(true);
                }
            },
            openSubMenu: function($item) {
                var $submenu = $item.next();
                if ($submenu.is('.menu-layer') && !$item.is('.active')) {
                    $back.data('state', 'close-submenu');
                    $back.data('submenus').push($submenu);
                    setTimeout(function() {
                        $item.addClass('active');
                        $submenu.addClass('active');
                        $menuOverlay.addClass('active');
                    });
                    $swiperRoot.detachEvents();
                } else {
                    window.location = $item.find('a').attr('href');
                }
            },
            closeSubMenu: function(closeAll) {
                var $subMenus = $back.data('submenus');
                if (closeAll) {
                    $back.data('submenus', []);
                    $categoryMenu
                        .find('.menu-layer.active')
                        .removeClass('active')
                        .prev()
                        .removeClass('active');
                    $menuOverlay.removeClass('active');
                    $swiperRoot.attachEvents();
                    $swiperRoot.slideTo(0);
                } else if ($subMenus.length == 1) {
                    // Item class 'active' is removed on menuoverlay transitionend
                    $subMenus.pop().removeClass('active');
                    $back.data('state', 'close-menu');
                    $menuOverlay.removeClass('active');
                    $swiperRoot.attachEvents();
                } else {
                    var $menu = $subMenus.pop();
                    if ($menu) {
                        $menu.removeClass('active').prev().removeClass('active');
                    }
                }
            }
        }
    };

    var CatMenu = new CategoryMenu($('.category-menu'));
    CatMenu.init();

    $('#open-category-menu').on('click', function() {
        CatMenu.toggleMenu();
    });

    function pageChangeUpdate() {
        if (window.navigator.standalone){
            // $('.header').css('padding-top', 15);
            // $.mobile.ajaxEnabled = true;
        }
        // $('.swiper-container .swiper-slide').imagesLoaded()
        //     .progress(function(instance, image) {
        //         $(image.img).parent().addClass('va-open');
        //     });
        $('.category-products-grid .product-link').imagesLoaded()
            .progress(function(instance, image) {
                $(image.img).fadeTo('slow', 1);
                //.parent().removeClass('s1 s2 s3 s4 s5');
            });

        $($dataajax).attr("data-ajax", "false");
        $('ul.checkout-types li > div.ui-btn').addClass("ui-btn-up-b").removeClass("ui-btn-up-c");
        $('#shopping-cart-table thead tr').addClass("ui-bar-a");
    }
    pageChangeUpdate();

    $(document).on({
        'pagebeforeshow': function() {
            // console.log('pagebeforeshow')
            pageChangeUpdate();
        },
        'pagebeforeload': function() {
            // console.log('pagebeforeload')
            if (snapper)
                snapper.close();
        }
    });

    $('body').on('tap', 'a.tab-reviews', function(e) {
        e.preventDefault();
        if ($('#product-tabs .product-tab:last').hasClass('ui-collapsible-collapsed'))
            $('#product-tabs .product-tab:last a').attr('href', 'javascript: void(0)').trigger('click');
        if (!$(this).hasClass('add')) {
            $.mobile.silentScroll($('#product-tabs .product-tab:last').offset().top);
        } else {
            $.mobile.silentScroll($('#review-form').offset().top);
        }
        return false;
    });

    if ($('#va-addtocart').length) {
        var $vaaddcart = $('#va-addtocart');
        $vaaddcart.find('#va-qty').on('tap', function() {
            if (!$(this).hasClass('va-open'))
                $(this).addClass('va-open');
            else
                $(this).removeClass('va-open');
            return false;
        }).find('.va-qty-drop-down li:not(.va-more)').on('tap', function() {
            $(this).addClass('active').siblings().removeClass('active');
            var val = $(this).text();
            $('input#qty').val(val);
            $vaaddcart.find('#va-qty').find('span').text(val);
            var p = parseFloat($vaaddcart.find('.va-price').data('original'));
            val = parseFloat(val) * p;
            $vaaddcart.find('.va-price').text(val.toFixed(2));
            $vaaddcart.find('#va-qty').removeClass('va-open');
            return false;
        });
        $vaaddcart.find('#va-qty .va-more input').on({
            'focus': function(e) {
                // $vaaddcart.css('bottom', -$vaaddcart.height());
            },
            'blur': function(e) {
                var val = $(this).val();
                if (val.length) {
                    $(this).parent().parent().addClass('active').siblings().removeClass('active');
                    $('input#qty').val(val);
                    $vaaddcart.find('#va-qty').find('span').text(val);
                    $vaaddcart.find('#va-qty').removeClass('va-open');
                    var p = parseFloat($vaaddcart.find('.va-price').data('original'));
                    val = parseFloat(val) * p;
                    $vaaddcart.find('.va-price').text(val.toFixed(2));
                }
                // $vaaddcart.css('bottom', 0);
                return false;
            },
            'tap': function(e) {
                e.stopImmediatePropagation();
            }
        });
        $(document).on({
            'tap': function(e) {
                if (!$(e.target).hasClass('va-qty-drop-down') || !$(e.target).parents().hasClass('va-qty-drop-down'))
                    if ($vaaddcart.find('#va-qty').hasClass('va-open')) {
                        $vaaddcart.find('#va-qty li.va-more input').trigger('blur');
                        $vaaddcart.find('#va-qty').removeClass('va-open');
                    }
            },
            'scrollstart': function() {
                if ($vaaddcart.find('#va-qty').hasClass('va-open')  && !$vaaddcart.find('#va-qty .va-more input').is(':focus'))
                    $vaaddcart.find('#va-qty').removeClass('va-open');
            }
        });
    }

    // Modal controller
    // var nwgModal = function() {
    //     var that = this;
    //     var show_modal = 'nwg-show-modal';
    //     var show_overlay = 'nwg-show-overlay';
    //     var $body = $('body');
    //     var $overlay = $('<div class="nwg-overlay" />').appendTo($body);
    //     var $modalContainer = $('#nwg-modal-container');
    //     if (!$modalContainer.length)
    //         $modalContainer = $('<div id="nwg-modal-container" />').appendTo($body);

    //     this.setModal = function(html) {
    //         $modalContainer.html(html);
    //         return this;
    //     }
    //     this.showModal = function(c) {
    //         setTimeout(function() {
    //             $overlay.addClass(show_overlay);
    //             $modalContainer.addClass(show_modal);
    //             if (c)
    //                 $modalContainer.find('.' + c).addClass(show_modal + ' nwg-no-close');
    //             else
    //                 $modalContainer.find('.nwg-modal').first().addClass(show_modal + ' nwg-no-close');
    //         }, 1);
    //         return this;
    //     }
    //     this.hideModal = function() {
    //         setTimeout(function() {
    //             $overlay.removeClass(show_overlay);
    //             $modalContainer.removeClass(show_modal);
    //             $modalContainer.children().removeClass(show_modal + ' nwg-no-close');
    //         }, 1);
    //         return this;
    //     }

    //     $modalContainer.on('click', '.nwg-modal-close', function(e) {
    //         e.preventDefault();
    //         e.stopImmediatePropagation();
    //         that.hideModal();
    //     });
    //     $modalContainer.on('click', function(e) {
    //         if (!$(e.target).closest('.nwg-no-close').length)
    //             that.hideModal();
    //     });

    //     $modalContainer.on('click', '.nwg-button', function(e) {
    //         e.preventDefault();
    //         var $modal = $(this).parents('.nwg-modal');
    //         if ($modal.hasClass('newsletter')) {
    //             var $email_field = $modal.find('#newsletter-affiliate-email'),
    //                 post = $email_field.data('post'),
    //                 value = $email_field.val(),
    //                 re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    //             // post = 'http://localhost/mtto-enterprise/index.php' + post;
    //             if (re.test(value)) {
    //                 $.post(post, {email: value}, function(res) {
    //                     that.hideModal();
    //                 });
    //             } else {
    //                 $email_field.addClass('error');
    //             }
    //         } else if ($modal.hasClass('affiliate')) {
    //             that.hideModal();
    //         }
    //     });
    // }

    // var TheModal = new nwgModal();

    // $.ajax({
    //     // url: 'http://localhost/mtto-enterprise/index.php/nanowebg_popupmanager/request',
    //     url: '/nanowebg_popupmanager/request',
    //     success: function(res) {
    //         res = JSON.parse(res);
    //         if (res.success) {
    //             TheModal.setModal(res.html).showModal();
    //         }
    //     }
    // });

});
