/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function ($,Component,url) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Svea_Checkout/payment/checkout'
            },
            continueToSvea: function () {
                $.mage.redirect(url.build('sveacheckout'));
                return false;
            }
        });
    }
);
