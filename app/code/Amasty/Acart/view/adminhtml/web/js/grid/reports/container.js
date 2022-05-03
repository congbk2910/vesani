define([
    'uiComponent',
    'Magento_Ui/js/lib/spinner'
], function (Component, loader) {
    'use strict';

    return Component.extend({
        defaults: {
            listens: {
                '${ $.provider }:reload': 'beforeReload',
                '${ $.provider }:reloaded': 'onDataReloaded'
            }
        },

        initObservable: function () {
            return this._super()
                .observe({

                });
        },

        beforeReload: function () {
            loader.get(this.name).show();
        },

        onDataReloaded: function () {
            loader.get(this.name).hide();
        }
    });
});
