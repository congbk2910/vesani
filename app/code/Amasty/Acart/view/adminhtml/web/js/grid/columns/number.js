define([
    'underscore',
    'mage/utils/strings',
    'Magento_Ui/js/grid/columns/column'
], function (_, stringUtils, Column) {
    'use strict';

    return Column.extend({

        getLabel: function (row, index) {
            var number;

            if (_.isUndefined(index)) {
                number = this._super(row);
            } else {
                number = row[index];
            }

            if (stringUtils.isEmpty(number)) {
                return '0';
            } else if (Math.floor(number) == number) {
                return String(number * 1);
            }

            return String(Number(number * 1).toFixed(2));
        }
    });
});
