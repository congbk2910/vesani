define([
    'jquery',
    'uiCollection',
    'mageUtils'
], function ($, Collection, utils) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'ui/grid/filters/filters',
            applied: {
                placeholder: true
            },
            filters: {
                placeholder: true
            },
            exports: {
                applied: '${ $.provider }:params.filters'
            }
        },

        /**
         * Sets filters data to the applied state.
         *
         * @returns {Filters} Chainable.
         */
        apply: function () {
            $('body').notification('clear');
            this.set('applied', utils.copy(this.filters));

            return this;
        }
    });
});
