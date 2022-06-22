/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/action/create-billing-address',
    'underscore',
    'mage/utils/wrapper'
], function ($,
    addressList,
    quote,
    checkoutData,
    createShippingAddress,
    selectShippingAddress,
    selectShippingMethodAction,
    paymentService,
    selectPaymentMethodAction,
    addressConverter,
    selectBillingAddress,
    createBillingAddress,
    _,
    wrapper) {
    'use strict';
    var firstTime = true;
    return function(widget) {
        widget.oldResolveShippingRates = widget.resolveShippingRates;
        widget.resolveShippingRates = function(ratesData) {
            var selectedShippingRate = checkoutData.getSelectedShippingRate();
            if (firstTime && ratesData.length) {
                firstTime = false;
                var defaultRate = null;
                $.each(ratesData, function(i, rate) {
                    let method = rate.carrier_code + '_' + rate.method_code;
                    if (method === cdzOscConfig.defaultShippingMethod) {
                        defaultRate = rate;
                    }
                });
                if (!defaultRate) {
                    defaultRate = ratesData[0];
                }
                if (!selectedShippingRate && !quote.shippingMethod()) {
                    window.noValidateShippingAddress = true;
                    selectShippingMethodAction(defaultRate);
                    setTimeout(function() {
                        window.noValidateShippingAddress = false;
                    }, 2000);
                    return;
                }
            }
            return widget.oldResolveShippingRates(ratesData);
        }
        return widget;
    }
});
