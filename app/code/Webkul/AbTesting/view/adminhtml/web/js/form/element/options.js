define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';    
    return select.extend({  
        initialize: function (){
            var specUrl = uiRegistry.get('index = specific_url');
            var defaultId = uiRegistry.get('index = default_type_id');
            var categoryType = uiRegistry.get('index = category_type');
            var productType = uiRegistry.get('index = product_type');
            var cmsType = uiRegistry.get('index = cms_type');
            var status = this._super().initialValue; 
            if (status == 0) {
                specUrl.show();
                defaultId.hide();
                categoryType.hide();
            } else if(status == 1){
                specUrl.hide();
                defaultId.show();
            } 
            return this;

        },      

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var specUrl = uiRegistry.get('index = specific_url');
            var defaultId = uiRegistry.get('index = default_type_id');
            var categoryType = uiRegistry.get('index = category_type');
            var productType = uiRegistry.get('index = product_type');
            var cmsType = uiRegistry.get('index = cms_type');
            if (value == 0) {
                specUrl.show();
                defaultId.hide();
            } else if(value == 1) {
                specUrl.hide();
                defaultId.show();
            }      
            return this._super();
        },
    });
});