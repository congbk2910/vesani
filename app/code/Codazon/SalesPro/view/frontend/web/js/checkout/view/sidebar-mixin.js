/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'mage/validation',
    'mage/translate',
    'Magento_Ui/js/modal/alert'
], function ($, ko, quote, validation, $t, alert) {
     var checkoutConfig = window.checkoutConfig,
        agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {},
        agreementsInputPath = '#opc-place-order-form div.checkout-agreements input';
    return function (Component) {
        return Component.extend({
            placeOrder: function() {
                if (this.validate()) {
                    var msg = '';
                    if (!quote.paymentMethod()) {
                        msg += '<p>' + $t('No payment method selected.')+'</p>';
                    }
                    if (!quote.shippingMethod() && !quote.isVirtual()) {
                        msg += '<p>' + $t('No shipping method selected.')+'</p>';
                    }
                    if (msg != '') {
                        alert({
                            modalClass: 'cdz-alert-popup',
                            title: $t('Warning'),
                            content: msg
                        });
                        return;
                    }
                    var $activePayment = $('#checkout-payment-method-load .payment-method._active').first();
                    if ($activePayment.length) {
                        $('.checkout-agreements input', $activePayment).prop('checked', 'checked');
                        var $placeOrder = $('.action.primary.checkout', $activePayment);
                        if ($placeOrder.length) {
                            if ($placeOrder.hasClass('disabled')) {
                                var $updateAddressBtn = $('.checkout-billing-address .actions-toolbar .action.action-update', $activePayment);
                                if ($updateAddressBtn.is(':visible')) {
                                    $updateAddressBtn.focus();
                                }
                            } else {
                                $placeOrder.trigger('click');
                                if (window.innerWidth < 768) {
                                    setTimeout(function() {
                                        var $error = $('#checkout-step-shipping div.mage-error').first();
                                        if ($error.length && $error.is(':visible')) {
                                            $('html,body').animate({scrollTop: $error.offset().top - 200}, 300);
                                        } else {
                                            $error = $('[data-role="checkout-messages"]', $activePayment).first();
                                            if ($error.length && $error.is(':visible')) {
                                                $('html,body').animate({scrollTop: $error.offset().top - 200}, 300);
                                            }
                                        }
                                    }, 300);
                                }
                            }
                        } else {
                            $('html,body').animate({scrollTop: $activePayment.offset().top - 50}, 300);
                        }
                    }
                }
            },
            validate: function (hideError) {
                var isValid = true;
                if (!agreementsConfig.isEnabled || $(agreementsInputPath).length === 0) {
                    return true;
                }
                $(agreementsInputPath).each(function (index, element) {
                    if (!$.validator.validateSingleElement(element, {
                        errorElement: 'div',
                        hideError: hideError || false
                    })) {
                        isValid = false;
                    }
                });
                return isValid;
            },
            getPlaceOrderLabel: function () {
                return window.cdzOscConfig.customPlaceOrderLabel ? window.cdzOscConfig.customPlaceOrderLabel : $t('Place Order');
            },
            enableOrderComment: function () {
                return window.cdzOscConfig.enableOrderComment;
            }
        });
    }
});