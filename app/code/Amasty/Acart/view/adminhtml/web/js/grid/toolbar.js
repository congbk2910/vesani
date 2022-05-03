define([
    'Magento_Ui/js/grid/toolbar',
    'jquery'
], function (Toolbar, $) {
    'use strict';

    return Toolbar.extend({
        defaults: {
            tableSelector: 'table.data-grid'
        },

        /**
         * Checks if sticky toolbar covers original elements.
         *
         * @returns {Boolean}
         */
        isCovered: function () {
            var pageMainActionsIndent = 77,
                stickyTop;

            if ($('.page-main-actions').length > 0) {
                pageMainActionsIndent = 0;
            }

            stickyTop = this._stickyTableTop - pageMainActionsIndent + this._wScrollTop;

            return stickyTop > this._tableTop;
        }
    });
});
