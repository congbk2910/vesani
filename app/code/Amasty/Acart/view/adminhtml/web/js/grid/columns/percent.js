define([
    'Amasty_Acart/js/grid/columns/number'
], function (Column) {
    'use strict';

    return Column.extend({

        getLabel: function (row, index) {
            return this._super(row, index) + '%';
        }
    });
});
