define([
    "jquery", "domReady!", "catalogAddToCart","Magento_Theme/js/fastest"
],function ($) {
    $.widget('codazon.firstLoad', {
        options: {
            trigger: '.cdz-ajax-trigger',
            itemsWrap: '.product-items',
            ajaxLoader: '.ajax-loader',
            ajaxUrl: null,
            jsonData: null,
            currentUrl: '',
            formKeyInputSelector: 'input[name="form_key"]'
        },
        _currentPage: 1,
        _checkVisible: function(){
            var $element = this.element;
            var cond1 = ($element.get(0).offsetWidth > 0) && ($element.get(0).offsetHeight > 0);
            var cond2 = ($element.is(':visible'));
            var winTop = $(window).scrollTop(),
            winBot = winTop + window.innerHeight,
            elTop = $element.offset().top,
            elHeight = $element.outerHeight(true),
            elBot = elTop + elHeight;
            var cond3 = (elTop <= winTop) && (elBot >= winTop);
            var cond4 = (elTop >= winTop) && (elTop <= winBot);
            var cond5 = (elTop >= winTop) && (elBot <= winBot);
            var cond6 = (elTop <= winBot) && (elBot >= winBot);
            var cond7 = true;
            if ($element.parents('md-tab-content').length > 0) {
                cond7 = $element.parents('md-tab-content').first().hasClass('md-active');
            }

            return cond1 && cond2 && (cond3 || cond4 || cond5 || cond6) && cond7;
        },
        _create: function() {
            var self = this;
            this.formKey = $(this.options.formKeyInputSelector).first().val();
            this._bindEvents();
        },
        _bindEvents: function() {
            var self = this;
            if(self._checkVisible()) {
                self._ajaxFirstLoad();
            } else {
                setTimeout(function(){
                    self._bindEvents();
                },500);
            }
        },
        _ajaxFirstLoad: function() {
            var self = this;
            var config = this.options;
            config.jsonData.current_url = config.currentUrl;
            /* if (typeof config.jsonData.cache_key_info == 'object') {
                config.jsonData.cache_key_info[0] = config.jsonData.cache_key_info[0] + '_' + $(config.formKeyInputSelector).val().substr(0,4);
            } */
            $.ajax({
                url: config.ajaxUrl,
                type: "POST",
                data: config.jsonData,
                cache: false,
                success: function(res){
                    if (typeof res.now !== 'undefined') {
                        window.codazon.now = res.now;
                    }
                    if (typeof res.html !== 'undefined') {
                        self.formKey = $(config.formKeyInputSelector).first().val();
                        self.element.hide().html(res.html).removeClass('no-loaded').fadeIn(500);
                        setTimeout(function() {
                            self.element.find('[name="form_key"]').each(function() {
                                var $field = $(this).val(self.formKey);
                            });
                        }, 500);
                        if (typeof window.angularCompileElement !== 'undefined') {
                            window.angularCompileElement(self.element);
                        }
                    }
                    require(['mage/apply/main'], function(mage) {
                        if (mage) {
                            mage.apply();
                        }
                        $('body').trigger('contentUpdated');
                        $.fn._buildSlider();
                        $.fn._tooltip();
                        $("[data-role=tocart-form], .form.map.checkout").catalogAddToCart({});
                        self.element.find("input[name*='form_key']").remove();
                        self.element.find("form").prepend('<input name="form_key" type="hidden" value="' + ($( "input[name*='form_key']" ).val()) + '">');
                        setTimeout(function() {
                            $('body').trigger('ajaxProductFirstTimeLoaded');
                        }, 100);
                    });
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    console.error(textStatus);
                }
            });
        }
    });
    return $.codazon.firstLoad;
});
