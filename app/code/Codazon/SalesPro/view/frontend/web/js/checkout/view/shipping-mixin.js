/**
 * @copyright  Codazon. All rights reserved.
 * @author     Nicolas
 */
define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'ko',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/model/address-list',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, quote, registry, stepNavigator, $t, ko, checkoutDataResolver, addressConverter, getPaymentInformation, checkoutData, addressList) {
    'use strict';
    var widget;
    return function (Component) {
        return Component.extend({
            defaults: {
                template: 'Codazon_SalesPro/checkout/shipping'
            },
            initialize: function () {
                var self = this;
                this.ingoreValidationMessage = true;
                this._adjustFunctions();
                this._super();
                widget = this;
                this._prepareData();
                return this;
            },
            _prepareData: function() {
                var self = this;
                $(window).on('refreshShippingInfomation', function () {
                    widget.setShippingInformation();
                });
                this.prepareFormEvents();
                
            },
            _adjustFunctions: function () {
                stepNavigator.setHash = function (hash) {
                    window.location.hash = '';
                };
                stepNavigator.oldIsProcessed = stepNavigator.isProcessed;
                stepNavigator.isProcessed = function (code) {
                    if (code == 'shipping') {
                        return true;
                    } else {
                        stepNavigator.oldIsProcessed(code);
                    }
                }
            },
            /* visible: function() {
                return (!quote.isVirtual());
            }, */
            canDisplayed: function() {
                return (!quote.isVirtual());
            },
            selectShippingMethod: function (shippingMethod) {
                this._super(shippingMethod);
                widget.setShippingInformation();
                return true;
            },
            hasShippingMethod: function () {
                return window.checkoutConfig.selectedShippingMethod !== null;
            },
            saveNewAddress: function() {
                this._super();
                widget.setShippingInformation();
            },
            prepareFormEvents: function() {
                var self = this;
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    if (self.visible()) {
                        var addressData = self.source.get('shippingAddress');
                        var it = setInterval(function() {
                            var field, $shippingForm = $('#co-shipping-form');
                            if ($shippingForm.length) {
                                clearInterval(it);
                                $('form[data-role=email-with-possible-login] input[name=username]').on('change', function() {
                                    $(window).trigger('refreshShippingInfomation');
                                });
                                $shippingForm.on('change', 'input,select', function () {
                                    $(window).trigger('refreshShippingInfomation');
                                });
                            }
                        }, 100);
                    }
                });
            },
            validateShippingInformation: function() {
                if (window.noValidateShippingAddress) {
                    return true;
                } else {
                    return this._super();
                }
            }
        });
    };
});