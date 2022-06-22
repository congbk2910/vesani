define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (modal) {

        modal.prototype.openModal = wrapper.wrap(modal.prototype.openModal, function(original) {
            var result = original();
            $('.' + this.options.overlayClass).appendTo('.modal-popup._show');
            $('.modal-inner-wrap').css('z-index', 902);
            return result;
        });

        return modal;
    };
});