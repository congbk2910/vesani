/**
 * @copyright  Codazon. All rights reserved.
 * @author     Nicolas
 */
define([
    'jquery',
    'Magento_Checkout/js/action/get-payment-information',
    'uiRegistry'
], function ($, getPaymentInformation, registry) {
    'use strict';

    return function (Component) {
        return Component.extend({
            initialize: function () {
                this._super();
                this._prepareData();
                return this;
            },
            isVisible: function() {
                return true;
            },
            _prepareData: function() {
                var self = this;
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    getPaymentInformation().done(function (rs) {
                        self.isVisible(true);
                    });
                });
            }
        });
    };
});