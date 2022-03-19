/**
 * Copyright Â© 2018 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
define(['jquery','jquery-ui-modules/slider'], function($) {

   !function(o){if(o.support.touch="ontouchend"in document,o.support.touch){var t,e=o.ui.mouse.prototype,u=e._mouseInit,n=e._mouseDestroy;function c(o,t){if(!(o.originalEvent.touches.length>1)){o.preventDefault();var e=o.originalEvent.changedTouches[0],u=document.createEvent("MouseEvents");u.initMouseEvent(t,!0,!0,window,1,e.screenX,e.screenY,e.clientX,e.clientY,!1,!1,!1,!1,0,null),o.target.dispatchEvent(u)}}e._touchStart=function(o){!t&&this._mouseCapture(o.originalEvent.changedTouches[0])&&(t=!0,this._touchMoved=!1,c(o,"mouseover"),c(o,"mousemove"),c(o,"mousedown"))},e._touchMove=function(o){t&&(this._touchMoved=!0,c(o,"mousemove"))},e._touchEnd=function(o){t&&(c(o,"mouseup"),c(o,"mouseout"),this._touchMoved||c(o,"click"),t=!1)},e._mouseInit=function(){var t=this;t.element.bind({touchstart:o.proxy(t,"_touchStart"),touchmove:o.proxy(t,"_touchMove"),touchend:o.proxy(t,"_touchEnd")}),u.call(t)},e._mouseDestroy=function(){var t=this;t.element.unbind({touchstart:o.proxy(t,"_touchStart"),touchmove:o.proxy(t,"_touchMove"),touchend:o.proxy(t,"_touchEnd")}),n.call(t)}}}($);

    $.widget('codazon.ajaxlayerednavpro', {
        options: {
            ajaxSelector: '.swatch-option-link-layered, .block-content.filter-content a.action.remove, .filter-options-content a, a.action.clear.filter-clear, .toolbar-products .pages-items a, .sidebar #layered-filter-block .options .items .item a'
        },
        _create: function() {
            var self = this, conf = this.options;
            this.isProcessing = false;
            this.initRange = {};
            this._prepareHtml();
            this._attacheEvents();
            setTimeout(function() {
                self._modifyFunction();
            }, 500);
        },
        _prepareHtml: function() {
            var self = this, conf = this.options;
            $('[data-role=filter-slider-container]').each(function() {
                var $container = $(this), $slider = $container.find('[data-role=filter-slider]');
                var data = $container.data('filter');
                var $min = $container.find('[data-role=min-value]'), $max = $container.find('[data-role=max-value]');
                var min = 0, max = (data.valuesRange.length - 1), curMin, curMax;
                var step = 1;
                var sliderOptions = {
                    range: data.range,  //true
                    min: min*step,
                    max: max*step,
                    //step: step,
                    values: [data.min*step, data.max*step],
                    slide: function(event, ui) {
                        curMin = Math.round(ui.values[0]/step);
                        curMax = Math.round(ui.values[1]/step);
                        $min.text(data.valuesRange[curMin].label);
                        $max.text(data.valuesRange[curMax].label);
                    },
                    stop: function(event, ui) {
                        curMin = Math.round(ui.values[0]/step);
                        curMax = Math.round(ui.values[1]/step);
                        var ajaxUrl = data.action;
                        var value = [];
                        for (var i=curMin; i <= curMax; i++) {
                            value.push(data.valuesRange[i].value);
                        }
                        if (value.length) {
                            value = value.join(',');
                            ajaxUrl += (ajaxUrl.search(/\?/) != -1) ? '&' : '?';
                            ajaxUrl += data.code + '=' + value;
                        }
                        self.activeCode = data.code;
                        self._ajaxLoad(ajaxUrl);
                    }
                }
                $($slider).slider(sliderOptions);;
            });
            $('[data-role=filter-dropdown]').on('change', function() {
                var $select = $(this);
                var ajaxUrl = $select.val();
                self.activeCode = $select.data('code');
                self._ajaxLoad(ajaxUrl);
            });
            $('[data-role=filter-checkbox-container] [type=checkbox]').on('change', function() {
                var $checkbox = $(this), $container = $checkbox.parents('[data-role=filter-checkbox-container]').first();
                var data = $container.data('filter');
                var value = [];
                var ajaxUrl = data.action;
                if (data.multiSelect) {
                    $container.find('[type=checkbox]:checked').each(function() {
                        value.push($(this).val());
                    });
                } else if ($checkbox.is(':checked')) {
                    value.push($checkbox.val());
                    $container.find('[type=checkbox]:checked').each(function() {
                        var $this = $(this);
                        if (!$this.is($checkbox)) {
                            $this.prop('checked', false);
                        }
                    });
                }
                if (value.length) {
                    value = value.join(',');
                    ajaxUrl += (ajaxUrl.search(/\?/) != -1) ? '&' : '?';
                    ajaxUrl += data.code + '=' + value;
                }
                self.activeCode = data.code;
                self._ajaxLoad(ajaxUrl);
            });
            $('[data-role=rating-slider-container]').each(function() {
                var $container = $(this), $slider = $container.find('[data-role=rating-slider]'),
                $form = $container.find('[data-role=rating-form]').first(), code = $form.data('code'), $input = $form.find('[name=' + code + ']'), $text = $container.find('[data-role=text]').show(), $count = $container.find('[data-role=count]'),
                $handle = $container.find('[data-role=slider-handle]').first(), $bar = $container.find('[data-role=rating-bar]').first(), value = parseInt($input.val());
                var data = $container.data('filter');
                var filterData = {};
                $.each(data.itemsData, function(i, el) {
                    filterData[el.value] = el;
                });
                var makeupSlider = function(value) {
                     var handleText = (value > 0) ? value : data.allText;
                     var width = 100*parseInt(value)/5;
                     $handle.text(handleText);
                     $input.val(value);
                     $bar.css({width: width + '%'});
                     if (filterData[value]) {
                         $text.text(filterData[value].label);
                         $count.show().text(filterData[value].count);
                     } else {
                         $text.text(data.allText);
                         $count.hide().text('');
                     }
                }
                var sliderOptions = {
                    min: data.min,
                    max: data.max,
                    step: 1,
                    range: false,
                    value: value,
                    create: function() {
                       makeupSlider(value);
                    },
                    slide: function(event, ui) {
                        makeupSlider(ui.value);
                    },
                    stop: function(event, ui) {
                        makeupSlider(ui.value);
                        if (parseInt(ui.value) != value) {
                            $form.submit();
                        }
                    }
                }
                $form.on('submit', function(e) {
                    e.preventDefault();
                    if ($form.valid()) {
                        value = parseInt($input.val());
                        var ajaxUrl = $form.attr('action');
                        if (value > 0) {
                            ajaxUrl += (ajaxUrl.search(/\?/) != -1) ? '&' : '?';
                            ajaxUrl += code + '=' + value;
                        }
                        $form.validation();
                        self.activeCode = code;
                        self._ajaxLoad(ajaxUrl);
                    }
                });
                $($slider).slider(sliderOptions);;
            });
            $('[data-role=price-slider-container]').each(function() {
                var $container = $(this), $slider = $container.find('[data-role=price-slider]'),
                $min = $container.find('[data-role=min_price]'), $max = $container.find('[data-role=max_price]'),
                $form = $container.find('[data-role=price-form]').first(), code = $form.data('code'), $priceInput = $form.find('[name=' + code + ']'),
                min = parseFloat($min.val()), max = parseFloat($max.val()), curMin, curMax;
                var data = $container.data('filter');
                if (!self.initRange[code]) {
                    self.initRange[code] = {min: data.minValue, max: data.maxValue};
                }
                if (self.initRange[code].max < max) {
                    self.initRange[code].max = max;
                }
                //var step = 1;
                var step = 1;
                var sliderOptions = {
                    //range: true,
                    min: self.initRange[code].min * step,
                    max: self.initRange[code].max * step,
                    values: [min * step, max * step],
                    // step: step,
                    slide: function(event, ui) {
                        curMin = (ui.values[0] / step);
                        curMax = (ui.values[1] / step);
                        $min.val(curMin);
                        $max.val(curMax);
                        $priceInput.val(curMin + '-' + curMax);
                    },
                    stop: function(event, ui) {
                        curMin = (ui.values[0] / step);
                        curMax = (ui.values[1] / step);
                        $min.val(curMin);
                        $max.val(curMax);
                        $priceInput.val(curMin + '-' + curMax);
                        $form.submit();
                    }
                };
                $form.on('submit', function(e) {
                    e.preventDefault();
                    if ($form.valid()) {
                        curMin = $min.val();
                        curMax = $max.val();
                        $priceInput.val(curMin + '-' + curMax);
                        var ajaxUrl = $form.attr('action');
                        ajaxUrl += (ajaxUrl.search(/\?/) != -1) ? '&' : '?';
                        ajaxUrl += code + '=' + $priceInput.val();
                        $form.validation();
                        self.activeCode = code;
                        self._ajaxLoad(ajaxUrl);
                    }
                });
                $($slider).slider(sliderOptions);;
            });
        },
        _modifyFunction: function() {
            var self = this, conf = this.options;
            $('.toolbar.toolbar-products').each(function() {
                var $toolbar = $(this);
                var toolbarForm = $toolbar.data('mageProductListToolbarForm');
                if (toolbarForm) {
                    toolbarForm.changeUrl = function (paramName, paramValue, defaultValue) {
                        if (self.isProcessing) {
                            return true;
                        }
                        var decode = window.decodeURIComponent,
                        urlPaths = this.options.url.split('?'),
                        baseUrl = urlPaths[0],
                        urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                        paramData = {},
                        parameters, i;

                        for (i = 0; i < urlParams.length; i++) {
                            parameters = urlParams[i].split('=');
                            paramData[decode(parameters[0])] = parameters[1] !== undefined ?
                            decode(parameters[1].replace(/\+/g, '%20')) :
                            '';
                        }
                        paramData[paramName] = paramValue;

                        if (paramValue == defaultValue) { //eslint-disable-line eqeqeq
                            delete paramData[paramName];
                        }
                        paramData = $.param(paramData);

                        var ajaxUrl = baseUrl + (paramData.length ? '?' + paramData : '');
                        self._ajaxLoad(ajaxUrl, true);
                    }
                }
            });
            setTimeout(function() {
                $('body').trigger('layeredNavLoaded');
            }, 500);
        },
        _attacheEvents: function() {
            var self = this, conf = this.options;
            $(conf.ajaxSelector).on('click', function(e) {
                e.preventDefault();
                var $a = $(this);
                var ajaxUrl = $a.attr('href');
                if ($a.parents('.toolbar-products').length) {
                    self._ajaxLoad(ajaxUrl, true);
                } else {
                    self._ajaxLoad(ajaxUrl, false);
                }

            });
        },
        getUrlVars: function(url) {
            var vars = {};
            var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        },
        _ajaxLoad: function(ajaxUrl, needSrollTop) {
            var self = this, conf = this.options;
            self.isProcessing = true;
            if ((!ajaxUrl) || (ajaxUrl.search('javascript:') == 0) || (ajaxUrl.search('#') == 0)) {
                return;
            }
            if (!needSrollTop) {
                needSrollTop = false;
            }

            if(self.getUrlVars(ajaxUrl)["p"] == self.getUrlVars(window.location.href)["p"]){
                var tmp = "p="+self.getUrlVars(ajaxUrl)["p"];
                ajaxUrl = ajaxUrl.replace(tmp,"");
            }

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {ajax_nav: 1},
                showLoader: true,
                success: function(res) {
                    if (res.catalog_leftnav) {
                        $('.block.filter').first().replaceWith(res.catalog_leftnav);
                    }
                    if (res.category_products) {
                        res.category_products = decodeURIComponent(res.category_products);
                        var $listContainer = $('#product-list-container');
                        $listContainer.html(res.category_products);
                        if (needSrollTop) {
                            $(window).scrollTop($listContainer.offset().top - 60);
                        }
                    }
                    if (res.page_main_title) {
                        $('.page-title-wrapper').first().replaceWith(res.page_main_title);
                    }
                    if (res.updated_url) {
                        window.history.pushState(res.updated_url, document.title, res.updated_url);
                    } else {
                        window.history.pushState(ajaxUrl, document.title, ajaxUrl);
                    }
                    $('body').trigger('contentUpdated');
                    self._prepareHtml();
                    self._attacheEvents();
                    setTimeout(function() {
                        self._modifyFunction();
                    }, 100);
                    if (window.innerWidth >= 768) {
                        setTimeout(function() {
                            if (self.activeCode) {
                                $('.filter-options-item').each(function(i, el) {
                                    var $collapsible = $(this);
                                    if ($collapsible.hasClass(self.activeCode)) {
                                        if($('#narrow-by-list').data('mageAccordion')){
                                            $('#narrow-by-list').data('mageAccordion').activate(i);
                                        }
                                        return false;
                                    }
                                });
                            }
                        }, 100);
                    }
                }
            }).always(function() {
                self.isProcessing = false;
            });
        }
    });
    return $.codazon.ajaxlayerednavpro;
});
