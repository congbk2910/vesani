/**
 * Amasty Acart schedule component
 */

define([
    'jquery',
    'uiCollection',
    'uiLayout',
    'mageUtils',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'underscore'
], function ($, Collection, layout, utils, confirm, $t, _) {
    'use strict';

    return Collection.extend({
        defaults: {
            htmlId: '',
            htmlName: '',
            dataScope: 'schedules',
            schedules: [],
            elemIndex: 0
        },

        initialize: function () {
            this._super();

            this.initFields();

            return this;
        },

        /**
         * Validates can sales rules select be shown
         * @returns {Boolean}
         */
        isShowSalesRuleSelect: function () {
            return typeof this.salesRuleData !== undefined;
        },

        /**
         * Get numeric options
         * @param {Number} lastNum
         * @returns {Array}
         */
        getNumericOptions: function (lastNum) {
            var array = Array.apply(null, { length: lastNum }).map(function (item, index) {
                return {
                    label: index + 1,
                    value: index + 1
                };
            });

            array.unshift({ label: '-' });

            return array;
        },

        /**
         * Create new record
         * @returns {void}
         */
        addRecord: function () {
            this.initField({});
        },

        /**
         * Initialize new record
         * @returns {void}
         */
        initFields: function () {
            if (this.schedules.length) {
                this.schedules.forEach(function (item) {
                    this.initField(item);
                }.bind(this));
            } else {
                this.addRecord();
            }
        },

        /**
         * Initialize child component
         * @param {Object} item
         * @returns {void}
         */
        initField: function (item) {
            var schedule = this.createField(item, this.elemIndex, this.dataScope);

            layout([schedule]);
            this.insertChild(schedule.name, this.elemIndex);
            this.elemIndex += 1;
        },

        /**
         * Create component
         * @param {Object} data
         * @param {Number} index
         * @param {String} dataScope
         * @returns {Object}
         */
        createField: function (data, index, dataScope) {
            const UseShoppingCartRule = data.use_shopping_cart_rule,
                useCampaignUtm = data.use_campaign_utm,
                sendSameCoupon = data.send_same_coupon;

            data.use_shopping_cart_rule = !_.isUndefined(UseShoppingCartRule) ? +UseShoppingCartRule : 0;
            data.use_campaign_utm = !_.isUndefined(useCampaignUtm) ? +useCampaignUtm : 1;
            data.send_same_coupon = !_.isUndefined(sendSameCoupon) ? +sendSameCoupon : 0;

            return utils.extend(data, {
                'name': this.name + '.' + index,
                'component': 'Amasty_Acart/js/scheduleItem',
                'provider': this.provider,
                'dataScope': dataScope + '.' + index,
                'sortOrder': index,
                'template': 'Amasty_Acart/form/schedule',
                'baseEmailTemplates': this.baseEmailTemplates,
                'loadTemplateUrl': this.loadTemplateUrl,
                'variables': this.variables,
                'imports': {
                    prevSameCoupon: this.name + '.' + (index - 1) + ':send_same_coupon',
                    parentCouponType: this.name + '.' + (index - 1) + ':simple_action',
                    prevUseCartRule: this.name + '.' + (index - 1) + ':use_shopping_cart_rule'
                }
            });
        },

        /**
         * Set same coupon value
         * @param {Object} child
         * @param {Number} index
         * @returns {void}
         */
        setSameCouponValue: function (child, index) {
            var nextChild = this.elems()[index + 1];

            if ((!child.simple_action() || !child.send_same_coupon()) && nextChild) {
                nextChild.send_same_coupon(false);
            }
        },

        /**
         * Delete Item
         * @returns {void}
         */
        deleteItem: function (data) {
            confirm({
                content: $t('Are you sure?'),
                actions: {
                    confirm: function () {
                        data.source.remove(data.dataScope);
                        data.destroy();
                    }
                }
            });
        }
    });
});
