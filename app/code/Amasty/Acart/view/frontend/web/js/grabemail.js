define([
    'jquery',
    'uiElement',
    'underscore',
    'rjsResolver',
], function ($, Element, _, resolver) {
    'use strict';

    return Element.extend({
        defaults: {
            isNeedLogEmail: false,
            grabEmailUrl: '',
            grabEmailDelay: 500,
            emailInputSelector: '#customer-email'
        },

        initialize: function () {
            this._super();
            resolver(function () {
                this.initEventListeners()
            }.bind(this));

            return this;
        },

        initEventListeners: function () {
            $(this.emailInputSelector).on('keyup', _.debounce(this.grabEmail.bind(this), this.grabEmailDelay));
        },

        grabEmail: function () {
            var emailInputElement = $(this.emailInputSelector),
                grabbedEmail = emailInputElement.length ? emailInputElement.val() : false,
                isEmailValid = $.validator.methods['validate-email'](grabbedEmail);

            if (isEmailValid && this.isNeedLogEmail) {
                $.ajax({
                    url: this.grabEmailUrl,
                    method: 'POST',
                    global: false,
                    data: { email: grabbedEmail }
                });
            }
        }
    });
});
