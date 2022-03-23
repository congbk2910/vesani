define([
    "jquery","jquery-ui-modules/tooltip", "cdz_slider", 'domReady!', "toggleAdvanced", "matchMedia", 'mage/tabs','Codazon_ProductFilter/js/productfilter'
], function($) {
    if (typeof window.cdzUtilities == 'undefined') {
        window.cdzUtilities = {};
    }
    $.fn._buildToggle = function() {
        $("[data-cdz-toggle]").each(function() {
            $(this).toggleAdvanced({
                selectorsToggleClass: "active",
                baseToggleClass: "expanded",
                toggleContainers: $(this).data('cdz-toggle'),
            });
        });

    };
    $.fn._buildTabs = function() {
        if ($('.cdz-tabs').length > 0) {
            $('.cdz-tabs').each(function() {
                var $tab = $(this);
                mediaCheck({
                    media: '(min-width: 768px)',
                    // Switch to Desktop Version
                    entry: function() {
                        $tab.tabs({
                            openedState: "active",
                            openOnFocus: true,
                            collapsible: false,
                        });
                    },
                    // Switch to Mobile Version
                    exit: function() {
                        $tab.tabs({
                            openedState: "active",
                            openOnFocus: false,
                            collapsible: true
                        });
                    }
                });
            });
        }
    };

    $.fn._buildSlider = function() {
        if ($('.cdz-slider').length > 0) {
            $('.cdz-slider').each(function() {
                var $owl = $(this);
                if ((typeof $owl.data('no_slider') == 'undefined') || (!$owl.data('noslider'))) {
                    $owl.addClass('owl-carousel');
                    var sliderItem = typeof($owl.data('items')) !== 'undefined' ? $owl.data('items') : 5;
                    $owl.owlCarousel({
                        loop: typeof($owl.data('loop')) !== 'undefined' ? $owl.data('loop') : true,
                        margin: typeof($owl.data('margin')) !== 'undefined' ? $owl.data('margin') : 0,
                        responsiveClass: true,
                        nav: typeof($owl.data('nav')) !== 'undefined' ? $owl.data('nav') : true,
                        dots: typeof($owl.data('dots')) !== 'undefined' ? $owl.data('dots') : false,
                        autoplay: typeof($owl.data('autoplay')) !== 'undefined' ? $owl.data('autoplay') : false,
                        autoplayTimeout: typeof($owl.data('autoplayTimeout')) !== 'undefined' ? $owl.data('autoplayTimeout') : 1000,
                        autoplayHoverPause: typeof($owl.data('autoplayHoverPause')) !== 'undefined' ? $owl.data('autoplayHoverPause') : false,
                        items: sliderItem,
                        center: typeof($owl.data('center')) !== 'undefined' ? $owl.data('center') : null,
                        autoWidth: typeof($owl.data('autoWidth')) !== 'undefined' ? $owl.data('autoWidth') : false,
                        rtl: ThemeOptions.rtl_layout == 1 ? true : false,
                        responsive: {
                            0: {
                                items: typeof($owl.data('items-0')) !== 'undefined' ? $owl.data('items-0') : sliderItem
                            },
                            480: {
                                items: typeof($owl.data('items-480')) !== 'undefined' ? $owl.data('items-480') : sliderItem
                            },
                            768: {
                                items: typeof($owl.data('items-768')) !== 'undefined' ? $owl.data('items-768') : sliderItem
                            },
                            1024: {
                                items: typeof($owl.data('items-1024')) !== 'undefined' ? $owl.data('items-1024') : sliderItem
                            },
                            1280: {
                                items: typeof($owl.data('items-1280')) !== 'undefined' ? $owl.data('items-1280') : sliderItem
                            },
                            1440: {
                                items: typeof($owl.data('items-1440')) !== 'undefined' ? $owl.data('items-1440') : sliderItem
                            }
                        }
                    });
                    
                }
                $owl.removeClass("cdz-slider");
            });
        }
    };

    $.fn._tooltip = function() {
        var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if(iOS == false){
            $('.show-tooltip').each(function() {
                $(this).tooltip({
                    position: {
                        my: "center top-80%",
                        at: "center top",
                        using: function(position, feedback) {
                            $(this).css(position);
                            $(this).addClass("cdz-tooltip");
                        }
                    }
                });
            })
        }
    };

    $.fn._fixBlogSearch = function(){
        $("#blog_search_mini_form button.search").prop("disabled", false);
    }

    $.fn._fixHoverIos = function(){
        $('a.product-item-photo').on('click touchend', function(e) {
            var el = $(this);
            var link = el.attr('href');
            window.location = link;
        });
    }

    $.fn._buildMobileDropdown = function(){
        if ($('.cdz-mobiledropdown').length > 0) {
            $('.cdz-mobiledropdown').each(function() {
                var $drop = $(this);
                $drop.mobiledropdown();
            });
        }
    }

    cdzUtilities.popup = function() {
        var $popupContainer, $ppContainerInner, $openedPopup, $backface;
        function _prepare() {
            $popupContainer = $('#cdz-popup-area');
            if ($popupContainer.length == 0) {
                $popupContainer = $('<div class="cdz-popup-area" id="cdz-popup-area">');
                $popupContainer.appendTo('body');
                $ppContainerInner = $('<div class="cdz-popup-area-inner" >').appendTo($popupContainer);
                $backface = $('<div class="cdz-backface" data-role="close-cdzpopup">').appendTo($ppContainerInner);
            }
        }
        function _buildPopup() {
            $('[data-cdzpopup]').each(function() {
                var $popup = $(this);
                var $wrap = $('<div class="cdz-popup">').appendTo($ppContainerInner);
                $wrap.addClass('popup-' + $popup.attr('id'));
                var $inner = $('<div class="cdz-popup-inner">').appendTo($wrap);
                var $content = $('<div class="cdz-popup-content">').appendTo($inner);
                var $closeBtn = $('<button type="button" class="close-cdzpopup" data-role="close-cdzpopup"><span></span></button>').appendTo($wrap);
                $popup.removeAttr('data-cdzpopup');
                $popup.appendTo($content);
                if (!$popup.hasClass('no-nice-scroll')) {
                    $content.addClass('nice-scroll');
                }
                if ($popup.hasClass('hidden-overflow')) {
                    $content.css({overflow: 'hidden'});
                }
                if ($popup.data('parentclass')) {
                    $wrap.addClass($popup.data('parentclass'));
                }
                $popup.on('triggerPopup', function() {
                    cdzUtilities.triggerPopup($popup.attr('id'));
                });
            });
        }
        this.triggerPopup = function(popupId, $trigger) {
            var $popup = $('#' + popupId);
            if ($popup.length) {
                if ($popup.parents('.cdz-popup').length) {
                    $popup.parents('.cdz-popup').first().addClass('opened').siblings().removeClass('opened');
                    $('body').css({overflow: 'hidden'});
                    $('.js-sticky-menu.active').css({
                        right: 'auto',
                        width: 'calc(100% - ' + cdzUtilities.scrollBarWidth +'px)'
                    });
                    $('body').addClass('cdz-popup-opened');
                    setTimeout(function() {
                        $popup.trigger('cdz_popup_opened');
                        if ($trigger) {
                            if (typeof $trigger.data('event') === 'string') {
                                $popup.trigger($trigger.data('event'));
                            }
                        }
                    }, 300);
                }
            }
        }
        function _bindEvents() {
            $('body').on('click', '[data-cdzpopuptrigger]', function(e) {
                e.preventDefault();
                var $trigger = $(this);
                var popupId = $trigger.data('cdzpopuptrigger');
                cdzUtilities.triggerPopup(popupId, $trigger);
            });
            function closePopup() {
                $('.cdz-popup.opened').removeClass('opened');
                $('body').removeClass('cdz-popup-opened');
                $('body').css({overflow: ''});
                $('.js-sticky-menu').css({right: '', width: ''});
            }
            function modifyButton($button, it) {
                $button.attr('id', 'btn-minicart-close-popup');
                if (!$button.data('popup_bind_event')) {
                    $button.data('popup_bind_event', true);
                    $button.on('click', closePopup);
                    $popupContainer.find('#top-cart-btn-checkout').on('click', closePopup);
                    if (it) clearInterval(it);
                }
            }
            if ($popupContainer.find('div.block.block-minicart').length) {
                var it = setInterval(function() {
                    var $button = $popupContainer.find('#btn-minicart-close');
                    if ($button.length) {
                        modifyButton($button, it);
                    }
                }, 2000);
                require(['Magento_Customer/js/customer-data'], function(customerData) {
                    var cartData = customerData.get('cart');
                    cartData.subscribe(function (updatedCart) {
                        var $button = $popupContainer.find('#btn-minicart-close');
                        if ($button.length) {
                            setTimeout(function() {
                                modifyButton($button, false);
                            }, 1000);
                        }
                    });
                });
            }
            $popupContainer.on('click', '[data-role=close-cdzpopup]', closePopup);
        }
        _prepare();
        _buildPopup();
        _bindEvents();
        $('body').on('cdzBuildPopup', _buildPopup);
    };
    cdzUtilities.sidebar = function() {
        var $backface = $('#cdz-sidebar-backface');
        if ($backface.length == 0) {
            $backface = $('<div data-role="cdz-close-sidebar" id="cdz-sidebar-backface" class="cdz-sidebar-backface" >');
            $backface.appendTo('body');
        }
        var side, $sidebar, section, openedEvent, interval;
        function closeSidebar() {
            $('html').removeClass('cdz-panel-open-left cdz-panel-open-right');
            $('html').addClass('cdz-panel-close-' + side);
            openedEvent = false;
            if (interval) clearInterval(interval);
            setTimeout(function() {
                $sidebar.css('top', '');
                $('html').removeClass('cdz-panel-close-' + side);
                $('#' + section).hide();
                $body.css({paddingLeft: '', paddingRight: ''});
            }, 200);
        }
        function openSidebar() {
            $sidebar.css('top', $(window).scrollTop());
            if (interval) clearInterval(interval);
            interval = setInterval(function() {
                $sidebar.css('top', $(window).scrollTop());
            }, 100);
            $('html').removeClass('cdz-panel-open-left cdz-panel-open-right')
                    .addClass('cdz-panel-open-' + side);
            $('#' + section).show().siblings().hide();
            (side == checkedSide)?$body.css({paddingLeft: cdzUtilities.scrollBarWidth}):$body.css({paddingRight: cdzUtilities.scrollBarWidth});
            setTimeout(function() {
                if (openedEvent) {
                    $('#' + section).trigger(openedEvent);
                }
            },300);
        }
        
        $('body').on('click', '[data-sidebartrigger]', function(e) {
            e.preventDefault();
            var $trigger = $(this);
            var data = $trigger.data('sidebartrigger');
            
            section = data.section ? data.section : 'utilities-main';
            side = data.side ? data.side : 'right';
            $sidebar = $('[data-sidebarid=' + side + ']').first();
            openedEvent = data.event;
            
            if ($('html').hasClass('cdz-panel-open-' + side)) {
                closeSidebar();
            } else {
                openSidebar();
            }
            $sidebar.find('[data-action=close]').off('click').on('click', function() {
                closeSidebar();
            });
            
        });
        $('body').on('click touchend', '[data-role=cdz-close-sidebar]', function(e) {
            setTimeout(function() {
                closeSidebar();
            }, 50);
        });
    };

    $.fn._buildSlider();
    $.fn._buildTabs();
    $.fn._tooltip();
    $.fn._buildToggle();
    $.fn._fixBlogSearch();
    $.fn._buildMobileDropdown();
    
    //$.fn._fixHoverIos();
    setTimeout($.fn._fixBlogSearch,500);
    cdzUtilities.init = function() {
        this.sidebar();
        this.popup();
    };
    if (document.readyState == 'complete') {
        cdzUtilities.init();
    } else {
        $(document).ready(function() {
            cdzUtilities.init();
        });
    }

});
