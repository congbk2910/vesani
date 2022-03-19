/**
/**
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
define(['jquery','jquery-ui-modules/widget'], function($) {        
    $.widget('codazon.alert', {
        _create: function() {
            var self = this;
            console.log(self.element.html());
        }
    });
    return $.codazon.alert;
});