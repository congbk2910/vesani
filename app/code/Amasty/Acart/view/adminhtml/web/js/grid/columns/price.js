define([
    'Magento_Catalog/js/price-utils',
    'Amasty_Acart/js/grid/columns/number'
], function (priceUtils, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            imports: {
                basePriceFormat: '${ $.provider }:data.basePriceFormat'
            }
        },

        initObservable: function () {
            this._super()
                .track([
                    'priceFormat'
                ]);

            return this;
        },

        getLabel: function (row, index) {
            var price = this._super(row, index);

            return priceUtils.formatPrice(price, this.basePriceFormat);
        }
    });
});
