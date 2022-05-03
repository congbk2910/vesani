define([
    'mageUtils',
    'uiLayout'
], function (utils, layout) {
    'use strict';

    return function (name, label, formElement, dataType, additionalProps) {
        var component = 'Magento_Ui/js/form/element/abstract',
            field;

        if (typeof additionalProps !== 'object') {
            additionalProps = {};
        }

        if (formElement === 'select') {
            component = 'Magento_Ui/js/form/element/select';
        }

        if (additionalProps.component) {
            component = additionalProps.component;
        }

        field = utils.extend(additionalProps, {
            'name': this.name + '.' + name,
            'component': component,
            'provider': this.provider,
            'formElement': formElement,
            'dataType': dataType,
            'dataScope': this.dataScope + '.' + name,
            'template': additionalProps.customTemplate || 'ui/form/field',
            'variables': this.variables,
            'label': label
        });

        layout([field]);
        this.insertChild(field.name);
    };
});
