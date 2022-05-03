define([
    'Magento_Ui/js/form/components/fieldset',
    'jquery',
    'Amasty_Acart/js/action/create-field',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'Magento_Email/js/variables'
], function (Fieldset, $, createField, $t, alertMessage) {
    'use strict';

    return Fieldset.extend({
        defaults: {
            template_id: null,
            loadTemplateUrl: '',
            custom_template_id: '',
            baseEmailTemplates: [],
            templateContentId: '',
            variables: null,
            links: {
                origTemplateVariables: '${ $.provider }:${ $.dataScope }.orig_template_variables',
                templateVariables: '${ $.provider }:${ $.dataScope }.template_variables'
            },
            imports: {
                loadTemplateUrl: '${ $.parentName }:loadTemplateUrl',
                template_id: '${ $.provider }:${ $.dataScope }.template_id'
            },
            elements: [{
                name: 'custom_template_id',
                label: 'Template',
                formElement: 'select',
                dataType: 'select',
                additionalProps: {
                    additionalClasses: 'amacart-row -template',
                    firstContainer: true,
                    validation: {'required-entry': true},
                    exports: {value: '${ $.name }:custom_template_id'}
                }
            }, {
                name: 'load_button',
                label: false,
                formElement: 'button',
                dataType: 'button',
                additionalProps: {
                    component: 'Magento_Ui/js/form/components/button',
                    title: 'Load Template',
                    customTemplate: 'ui/form/components/button/simple',
                    firstContainer: true,
                    buttonClasses: 'amacart-button',
                    actions: [{ actionName: 'loadTemplate', targetName: '${ $.name }' }]
                }
            }, {
                name: 'template_subject',
                label: 'Template Subject',
                formElement: 'input',
                dataType: 'text',
                additionalProps: {
                    validation: { 'required-entry': true },
                    additionalClasses: 'amacart-row'
                }
            }, {
                name: 'insert_variable',
                label: false,
                formElement: 'button',
                dataType: 'button',
                additionalProps: {
                    component: 'Magento_Ui/js/form/components/button',
                    title: 'Insert Variable',
                    customTemplate: 'ui/form/components/button/simple',
                    buttonClasses: 'amacart-button',
                    actions: [{ actionName: 'insertVariable', targetName: '${ $.name }' }]
                }
            }, {
                name: 'template_text',
                label: 'Template Content',
                formElement: 'textarea',
                dataType: 'text',
                additionalProps: {
                    component: 'Magento_Ui/js/form/element/textarea',
                    validation: { 'required-entry': true },
                    additionalClasses: 'amacart-row',
                    exports: { uid: '${ $.name }:templateContentId' }
                }
            }, {
                name: 'template_styles',
                label: 'Template Styles',
                formElement: 'textarea',
                dataType: 'text',
                additionalProps: {
                    component: 'Magento_Ui/js/form/element/textarea',
                    additionalClasses: 'amacart-row'
                }
            }]
        },

        initialize: function () {
            this._super();

            this.elements.forEach(function (elem) {
                if (elem.name === 'custom_template_id') {
                    elem.additionalProps.options = this.baseEmailTemplates;
                }
                createField.call(this, elem.name, elem.label, elem.formElement, elem.dataType, elem.additionalProps);
            }.bind(this));

            return this;
        },

        initObservable: function () {
            this._super().observe([
                'custom_template_id',
                'templateVariables',
                'origTemplateVariables'
            ]);

            return this;
        },

        loadTemplate: function () {
            $.ajax({
                showLoader: true,
                url: this.loadTemplateUrl,
                dataType: 'JSON',
                data: { code: this.custom_template_id, form_key: FORM_KEY },
                type: 'POST',
                success: function (response) {
                    this.setLoadedData(response);
                }.bind(this),
                error: function () {
                    alertMessage({
                        content: $t('The template did not load. Please review the log for details.')
                    });
                }
            });
        },

        setLoadedData: function (data) {
            this.elems.each(function (item) {
                var key = item.name.split('.').pop();

                if (data[key] && key !== 'template_id') {
                    item.value(data[key]);
                }
            });

            this.origTemplateVariables(data.orig_template_variables);
            this.templateVariables(data.template_variables);
        },

        insertVariable: function (index) {
            this.openVariableChooser(index);
        },

        openVariableChooser: function () {
            window.Variables.init(this.templateContentId);

            if (this.variables && this.templateVariables()) {
                this.variables.push(JSON.parse(this.templateVariables()));
            }

            if (this.variables) {
                window.Variables.openVariableChooser(this.variables);
            }
        }
    });
});
