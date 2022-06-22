/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['jquery', 'owlslider'], function($, owlCarousel, domReady) {
    $.widget('codazon.brandListing', {
        options: {
            ajaxLinks: '.char-list a, .toolbar .pages a'
        },
        _create: function() {
            this._prepareAll();
            this._prepareAjaxFilter();
        },
        _prepareAll: function() {
            this._alphabetTable();
            this._modifyToolbar();
        },
        _prepareAjaxFilter: function() {
            var self = this, conf = this.options;
            this.element.on('click', conf.ajaxLinks, function(e) {
                e.preventDefault();
                var $link = $(this), url = $link.attr('href');
                self._ajaxFilter(url);
            });
        },
        _alphabetTable: function() {
            var self = this, conf = this.options;
            var activeChar = $('[data-activechar]', this.element).data('activechar');
            $('[data-char]', this.element).each(function() {
                let $a = $(this), si = (conf.charUrl.indexOf('?') === -1) ? '?' : '&', fchr = $a.data('char');
                let href = (fchr === 'all') ? conf.charUrl : conf.charUrl + si + 'first_char=' + fchr;
                $a.addClass('available').attr('href', href);
                if (activeChar == fchr) {
                    $a.addClass('active');
                } else if ((fchr === 'all') && !activeChar) {
                    $a.addClass('active');
                } else {
                    $a.removeClass('active');
                }
            });
        },
        _modifyToolbar: function() {
            var self = this, conf = this.options;
            $('.toolbar', this.element).each(function() {
                var $toolbar = $(this), tbrData,
                it = setInterval(function() {
                    if (tbrData = $toolbar.data('mageProductListToolbarForm')) {
                        clearInterval(it);
                        tbrData.oldChangeUrl = tbrData.changeUrl;
                        tbrData.changeUrl = function(paramName, paramValue, defaultValue) {
                            if (this.options.post) {
                                this.oldChangeUrl(paramName, paramValue, defaultValue);
                            } else {
                                var urlPaths = this.options.url.split('?'),
                                baseUrl = urlPaths[0], paramData = this.getUrlParams(), currentPage = this.getCurrentPage(), newPage;
                                if (currentPage > 1 && paramName === this.options.limit) {
                                    newPage = Math.floor(this.getCurrentLimit() * (currentPage - 1) / paramValue) + 1;
                                    if (newPage > 1) {
                                        paramData[this.options.page] = newPage;
                                    } else {
                                        delete paramData[this.options.page];
                                    }
                                }
                                paramData[paramName] = paramValue;
                                if (paramValue == defaultValue) {
                                    delete paramData[paramName];
                                }
                                paramData = $.param(paramData);
                                baseUrl = baseUrl + (paramData.length ? '?' + paramData : '');
                                self._ajaxFilter(baseUrl);
                            }
                        }
                    }
                }, 50);
            });
        },
        _ajaxFilter: function(url) {
            var self = this, conf = this.options;
            $.ajax({
                url: url,
                data: {ajax_load: 1},
                showLoader: true,
                cache: true,
                type: 'get',
                success: function(rs) {
                    if (rs.list) {
                        $('[data-role="brand_list"]', self.element).replaceWith(rs.list);
                        self._prepareAll();
                        $('body').trigger('contentUpdated');
                        $('html,body').animate({scrollTop: self.element.offset().top - 100}, 600);
                        window.history.pushState({}, '', url);
                    }
                }
            });
        }
    });
    return $.codazon.brandListing;
});