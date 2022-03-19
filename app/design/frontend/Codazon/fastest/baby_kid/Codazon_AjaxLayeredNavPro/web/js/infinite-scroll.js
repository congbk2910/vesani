/**
 * Copyright © 2018 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
define(['jquery', 'Codazon_AjaxLayeredNavPro/js/layout'], function($) {
    $.widget('codazon.productListingInfinitScroll', {
        options: {
            nextLink: '.pages-item-next a.next',
            itemsWrap: '.items.product-items',
            item: '.item.product-item',
            toolbar: '.toolbar.toolbar-products',
            pager: '.toolbar.toolbar-products .pages'
        },
        _create: function() {
            this._assignVariables();
            this._initHtml();
            this._bindEvents();
        },
        _assignVariables: function() {
            this.interval = false;
            this.processing = false;
            this.$pool = $('<div class="infinite-scroll-pool">').appendTo('body').hide();
            this.$loader = this.element.find('[data-role="loader"]').first();
        },
        _initHtml: function() {

        },
        _bindEvents: function() {
            var self = this, conf = this.options;
            this.interval = setInterval(function() {
                if (self._checkVisible(self.element)) {
                    if (!self.processing) {
                        self._ajaxload();
                    }
                }
                if (typeof self.element == 'undefined') {
                    clearInterval(self.interval);
                }
            }, 500);
        },
        _ajaxload: function() {
            var self = this, conf = this.options;
            this.processing = true;
            var $nextLink = $(conf.pager).last().find(conf.nextLink);
            if ($nextLink.length) {
                var ajaxUrl = $nextLink.attr('href');
                if (ajaxUrl) {
                    self.$loader.show();
                    $.ajax({
                        url: ajaxUrl,
                        data: {ajax_nav: 1},
                        type: 'POST',
                        success: function(res) {
                            if (res.category_products) {
                                res.category_products = decodeURIComponent(res.category_products);
                                var $itemsWrap = $(conf.itemsWrap);
                                var $toolbarTop = $(conf.toolbar).first();
                                var $toolbarBottom = $(conf.toolbar).last();

                                self.$pool.html(res.category_products);
                                $newWrap = self.$pool.find(conf.itemsWrap);
                                $newToolbarTop = self.$pool.find(conf.toolbar).first();
                                $newToolbarBottom = self.$pool.find(conf.toolbar).last();
                                if ($newWrap.length) {
                                    $newWrap.children().appendTo($itemsWrap);
                                }
                                if ($newToolbarTop.length) {
                                    //$toolbarTop.replaceWith($newToolbarTop);
                                }
                                if ($newToolbarBottom.length) {
                                    $toolbarBottom.replaceWith($newToolbarBottom.removeAttr('data-mage-init'));
                                }
                                self.$pool.empty();
                            }
                            /* if (res.updated_url) {
                                window.history.pushState(res.updated_url, document.title, res.updated_url);
                            } else {
                                window.history.pushState(ajaxUrl, document.title, ajaxUrl);
                            } */
                            setTimeout(function() {

                            }, 100);
                        }
                    }).always(function() {
                        setTimeout(function() {
                            self.$loader.hide();
                            self.processing = false;
                            $.codazon.categoryLayout({},$('.items.product-items'));
                            $('body').trigger('contentUpdated');
                        }, 100);
                    });
                } else {
                    this.processing = false;
                }
            } else {
                this.processing = false;
            }
        },
        _checkVisible: function($element){
            var cond1 = ($element.get(0).offsetWidth > 0) && ($element.get(0).offsetHeight > 0);
            var cond2 = ($element.is(':visible'));
            var winTop = $(window).scrollTop(),
            winBot = winTop + window.innerHeight,
            elTop = $element.offset().top,
            elHeight = $element.outerHeight(true),
            elBot = elTop + elHeight;

            var delta = 100;

            var cond3 = (elTop <= winTop) && (elBot >= winTop);
            var cond4 = (elTop >= winTop) && (elTop <= winBot);
            var cond5 = (elTop >= winTop) && (elBot <= winBot);
            var cond6 = (elTop <= winBot) && (elBot >= winBot);

            return cond1 && cond2 && (cond3 || cond4 || cond5 || cond6);
        },
    });
    return $.codazon.productListingInfinitScroll;
});
