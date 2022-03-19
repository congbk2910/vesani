/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    return function (Component) {
        return Component.extend({
            selectAddress: function () {
                this._super();
                $(window).trigger('refreshShippingInfomation');
            }
        });
    }
});