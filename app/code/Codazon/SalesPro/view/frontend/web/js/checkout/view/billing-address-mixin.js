/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/set-billing-address',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'Magento_Checkout/js/model/billing-address-postcode-validator',
    'uiRegistry'
],
function (
    ko,
    _,
    Component,
    customer,
    addressList,
    quote,
    createBillingAddress,
    selectBillingAddress,
    checkoutData,
    checkoutDataResolver,
    customerData,
    setBillingAddressAction,
    globalMessageList,
    $t,
    billingAddressPostcodeValidator,
    registry
) {
     var lastSelectedBillingAddress = {},
        addressUpadated = false,
        addressEdited = false,
        countryData = customerData.get('directory-data'),
        addressOptions = addressList().filter(function (address) {
            return address.getType() === 'customer-address';
        });
    return function (Component) {
        return Component.extend({
            defaults: {
                detailsTemplate: 'Codazon_SalesPro/checkout/billing-address/details',
            },
            initObservable: function () {
                this.observe({
                    elems: [],
                    selectedAddress: null,
                    isAddressDetailsVisible: quote.billingAddress() != null,
                    isAddressFormVisible: !customer.isLoggedIn() || !addressOptions.length,
                    isAddressSameAsShipping: false,
                    saveInAddressBook: 1
                });
                quote.billingAddress.subscribe(function (newAddress) {
                    if (quote.isVirtual() || !quote.shippingAddress()) {
                        this.isAddressSameAsShipping(false);
                    } else {
                        this.isAddressSameAsShipping(
                            newAddress != null &&
                            newAddress.getCacheKey() == quote.shippingAddress().getCacheKey() //eslint-disable-line eqeqeq
                        );
                    }
                    if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                        this.saveInAddressBook(newAddress.saveInAddressBook);
                    } else {
                        this.saveInAddressBook(1);
                    }
                    this.isAddressDetailsVisible(true);
                }, this);
                return this;
            }
        });
    }
});