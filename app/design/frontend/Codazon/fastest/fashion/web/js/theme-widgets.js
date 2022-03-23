/**
/**
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
define(['jquery'], function($) {        
    $.widget('codazon.customValidation', {
        _create: function() {
            var self = this;
            require(['validation', 'domReady'], function() {
                self.element.validation();
            });
        }
    });

    $.widget('codazon.ajaxcontent', {
        options: {
            cache: true,
            method: 'GET',
            handle: 'replaceWith'
        },
        _create: function(){
            var self = this, conf = this.options;
            $.ajax({
                url: conf.ajaxUrl,
                method: conf.method,
                cache: conf.cache,
                success: function(rs) {
                    (self.element[conf.handle])(rs);
                    if (typeof conf.afterLoaded == 'function') {
                        conf.afterLoaded();
                    };
                    $('body').trigger('contentUpdated');
                }
            })
        }
    });
    $.widget('codazon.themewidgets', {
        _create: function(){
            var self = this;
            $.each(this.options, function(fn, options){
                var namespace = fn.split(".")[0];
                var name = fn.split(".")[1];
                if (typeof $[namespace] !== 'undefined') {
                    if ((namespace == 'codazon') && (name == 'slider')) {
                        name = 'flexibleSlider'; /* avoid conflicting with  jquery ui sliders */
                    }
                    if(typeof $[namespace][name] !== 'undefined') {
                        $[namespace][name](options, self.element);
                    }
                }
            });
        }
    });
    return $.codazon.themewidgets;
});