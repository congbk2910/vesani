/**
 * Acart test message script
 */
define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, Column, alert, $t) {
    'use strict';

    return Column.extend({
        defaults: {
            update_url: null,
            ruleId: null,
            successMessage: '',
            message: {
                sent: $t('Test email(s) sent'),
                added: $t('Email(s) was added to the queue'),
                send: $t('Send'),
                add: $t('Add to Queue')
            }
        },

        getSuccessMessage: function () {
            return this.test ? this.message.sent : this.message.added;
        },

        getButtonText: function () {
            return this.test ? this.message.send : this.message.add;
        },

        send: function (id) {
            var self = this;

            $.ajax({
                url: self.update_url,
                method: 'post',
                showLoader: true,
                data: {
                    quote_id: id,
                    rule_id: self.ruleId
                },
                success: function (response) {
                    var message = response.error ?
                        response.errorMsg :
                        self.getSuccessMessage();

                    alert({ content: message });
                }
            });
        }
    });
});
