define([
    'jquery',
    'Magento_Ui/js/grid/listing'
], function ($, Listing) {
    'use strict';

    return Listing.extend({
        defaults: {
            realVisibleItems: false
        },

        /**
         * @inheritDoc
         * @returns {Object}
         */
        initObservable: function () {
            return this._super()
                .observe([
                    'realVisibleItems'
                ]);
        },

        updateVisible: function () {
            this.visibleColumns = this.elems().filter(function (item) {
                return item.visible && !item.isProductDetails;
            });

            this.realVisibleItems(this.elems.filter('visible'));

            return this;
        }
    });
});
