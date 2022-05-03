define([
    'uiComponent',
    'underscore',
    'uiLayout',
    'mageUtils',
    'Magento_Ui/js/lib/validation/utils'
], function (Collection, _, layout, utils, validationUtils) {
    'use strict';

    return Collection.extend({
        defaults: {
            isShowExtra: false,
            prevSameCoupon: false,
            prevUseCartRule: false,
            parentCouponType: '',
            send_same_coupon: '',
            baseEmailTemplates: [],
            exports: {
                send_same_coupon: '${ $.provider }:${ $.dataScope }.send_same_coupon',
                use_campaign_utm: '${ $.provider }:${ $.dataScope }.use_campaign_utm',
                utm_source: '${ $.provider }:${ $.dataScope }.utm_source',
                utm_medium: '${ $.provider }:${ $.dataScope }.utm_medium',
                utm_campaign: '${ $.provider }:${ $.dataScope }.utm_campaign',
                use_shopping_cart_rule: '${ $.provider }:${ $.dataScope }.use_shopping_cart_rule'
            },
            links: {
                days: '${ $.provider }:${ $.dataScope }.days',
                hours: '${ $.provider }:${ $.dataScope }.hours',
                minutes: '${ $.provider }:${ $.dataScope }.minutes',
                schedule_id: '${ $.provider }:${ $.dataScope }.schedule_id',
                template_id: '${ $.provider }:${ $.dataScope }.template_id',
                discount_amount: '${ $.provider }:${ $.dataScope }.discount_amount',
                expired_in_days: '${ $.provider }:${ $.dataScope }.expired_in_days',
                discount_qty: '${ $.provider }:${ $.dataScope }.discount_qty',
                discount_step: '${ $.provider }:${ $.dataScope }.discount_step',
                use_rule: '${ $.provider }:${ $.dataScope }.use_rule',
                sales_rule_id: '${ $.provider }:${ $.dataScope }.sales_rule_id',
                simple_action: '${ $.provider }:${ $.dataScope }.simple_action'
            },
            listens: {
                template_id: 'toggleCustomTemplateForm',
                discount_amount: 'setDiscountAmount'
            },
            modules: {
                parent: '${ $.parentName }',
                gaFieldset: 'amasty_acart_rule_form.areas.analytics_fieldset'
            },
            childTemplate: 'Amasty_Acart/form/custom-template',
            utmTemplate: 'Amasty_Acart/form/utm'
        },

        initialize: function () {
            this._super();

            this.toggleCustomTemplateForm();
            this.setDiscountAmount(this.discount_amount());

            return this;
        },

        initObservable: function () {
            this._super().observe([
                'isShowExtra',
                'prevSameCoupon',
                'prevUseCartRule',
                'parentCouponType',
                'days',
                'hours',
                'minutes',
                'schedule_id',
                'template_id',
                'discount_amount',
                'expired_in_days',
                'discount_qty',
                'discount_step',
                'send_same_coupon',
                'use_campaign_utm',
                'utm_source',
                'utm_medium',
                'utm_campaign',
                'use_shopping_cart_rule',
                'use_rule',
                'sales_rule_id',
                'simple_action'
            ]);

            return this;
        },

        toggleMoreInfo: function () {
            this.isShowExtra(!this.isShowExtra());
        },

        onCheckedChanged: function (value, index) {
            value(Number(value()));
            this.parent().setSameCouponValue(this, index);
            this.simple_action('');
        },

        toggleCustomTemplateForm: function () {
            if (!this.template_id() && !_.isUndefined(this.template_id())) {
                this.initField();

                return this;
            }

            this.destroyChildren();
        },

        setDiscountAmount: function (value) {
            const preparedValue = validationUtils.parseNumber(value);

            if (!isNaN(preparedValue) && this.discount_amount() !== preparedValue) {
                this.discount_amount(preparedValue);
            }
        },

        /**
         * Initialize child component
         * @returns {void}
         */
        initField: function () {
            var schedule = utils.extend({}, {
                'name': this.name + '.custom_template',
                'component': 'Amasty_Acart/js/custom-template',
                'provider': this.provider,
                'dataScope': this.dataScope + '.custom_template',
                'template': this.childTemplate,
                'variables': this.variables,
                'baseEmailTemplates': this.baseEmailTemplates,
                'validation': {'required-entry': true}
            });

            layout([schedule]);
            this.insertChild(schedule.name);
        },

        openGaTab: function () {
            this.gaFieldset().activate();
        }
    });
});
