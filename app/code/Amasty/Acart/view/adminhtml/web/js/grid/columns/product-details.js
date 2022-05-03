define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            headerTmpl: 'ui/grid/columns/text',
            bodyTmpl: 'Amasty_Acart/grid/columns/product-details',
            subBodyTmpl: 'Amasty_Acart/grid/columns/bodyText',
            subHeaderTmpl: 'Amasty_Acart/grid/columns/text',
            disableAction: true,
            sortable: true,
            sorting: false,
            visible: true,
            draggable: true,
            imports: {
                productCols: '${ $.columnsControlsProvider }:elems',
                realVisibleItems: '${ $.historyListing }:realVisibleItems'
            },
            listens: {
                realVisibleItems: 'updateProductColumns'
            }
        },

        /**
         * @inheritDoc
         * @returns {Object}
         */
        initObservable: function () {
            return this._super()
                .observe([
                    'isUpdateVisible'
                ]);
        },

        updateProductColumns: function () {
            var visibleItems = this.filterVisible();

            this.visible = !!visibleItems.length;
        },

        filterVisible: function () {
            return this.productCols.filter(function (item) {
                return item.isProductDetails && item.visible;
            });
        },

        prepareColumns: function (columns) {
            return columns.map(function (item) {
                item.bodyTmpl = this.subBodyTmpl;
                item.headerTmpl = this.subHeaderTmpl;

                return item;
            }.bind(this));
        },

        getColumns: function () {
            this.draggable = false;

            return this.prepareColumns(this.filterVisible());
        }
    });
});
