define(
    [
        'ko',
        'jquery',
        'uiComponent'
    ],
    function (ko, $, Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Cminds_Salesrep/salesrep-step'
            },

            context: function () {
                return this;
            },

            isSalesrepEnabled: function () {
                if (!window.checkoutConfig.salesrep.isSalesrepEnabled) {
                    return;
                }

                if (!this.isAvailableByParam()) {
                    return true;
                }

                if (!this.getAvailableParam()) {
                    return false;
                }

                return this.isParamExist(this.getAvailableParam());

            },

            isParamExist: function(name) {
                var field = name;
                var url = window.location.href;
                if(url.indexOf('?' + field) != -1)
                    return true;
                else if(url.indexOf('&' + field) != -1)
                    return true;
                return false
            },

            getSalesrepList: function () {
                return window.checkoutConfig.salesrep.getSalesrepList;
            },

            getSalesrepLabel: function () {
                return window.checkoutConfig.salesrep.getSalesrepLabel;
            },

            getSalesrepNote: function () {
                return window.checkoutConfig.salesrep.getSalesrepNote;
            },

            isAvailableByParam: function() {
                return window.checkoutConfig.salesrep.isAvailableByParam;
            },

            getAvailableParam: function() {
                return window.checkoutConfig.salesrep.selectorVisibleParam;
            },

            saveSelectedSalesrep: function () {
                $('#salesrep-choose #co-salesrep-form #salesrep option').each(function () {
                    if ($(this).is(':selected')) {
                        if (this.value) {
                            var selectedSalesrep = this.value;
                            $.ajax({
                                url: window.checkoutConfig.salesrep.getCheckoutAjaxUrl,
                                type: "POST",
                                data: {selectedSalesrep: selectedSalesrep},
                                dataType: 'json',
                                success: function () {
                                }
                            });
                        }
                    }
                })
            }
        });
    }
);
