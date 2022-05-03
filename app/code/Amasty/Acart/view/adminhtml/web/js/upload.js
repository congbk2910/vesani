define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('amacart.upload', {
        options: {
            selectors: {
                filename: '[data-amacart-js="filename"]',
                fileInput: '[data-amacart-js="file"]'
            }
        },

        _create: function () {
            this._on(this.options.selectors.fileInput, {
                change: function (event) {
                    this.element.find(this.options.selectors.filename).html(event.target.files[0].name);
                }
            });
        }
    });

    return $.amacart.upload;
});
