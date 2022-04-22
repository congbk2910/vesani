define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "jquery/ui",
], function ($, alert) {
    'use strict';
    $.widget('mage.wkabtestgoalsjs', {
        _create: function () {

            var self = this;
            var cookieVariant = self.options.cookieVariant;
            var goalBaseUrl = self.options.goalBaseUrl;
            var goalsDataArray = self.options.goalsDataArray;
            var currentUrl = self.options.currentUrl;
            $( document ).ready(function(){
                self._getVariantDataForPages(cookieVariant,goalBaseUrl);
            });
            if (typeof(cookieVariant) != "undefined" && typeof(goalBaseUrl) != "undefined")  {
                $(goalsDataArray).each(function(i, item){
                    if (typeof(item.goal_id) != "undefined" && typeof(item.selector) != "undefined")  {

                        if(item.type == "selectors" ) {
                            $(item.selector).attr('data-wk-gl-id',item.goal_id+"-"+cookieVariant);
                        } else {
                            self._checkAndVerifyUrlData(item.goal_id,cookieVariant,currentUrl,
                                item.selector,goalBaseUrl);
                        }

                    }
                });
            }
            $("[data-wk-gl-id]").on('click',function(event){
                var elementVal =  $(this).data('wk-gl-id');
                if(typeof(elementVal) != undefined) {
                    self.insertGoalDatatoDb(elementVal,goalBaseUrl);
                }
            });
        },
        _getVariantDataForPages: function (cookieVariant,goalBaseUrl) {
            $.ajax({
                showLoader: false,
                url: goalBaseUrl+'abtesting/selectors/getvariantdata',
                data: 'variantId='+cookieVariant,
                type: "POST",
                dataType: 'json'
                }).done(function (data) {
                if(data.success == true) {
                  $.each(data.value, function( index, selectorArray ) {
                    $(selectorArray.selector).html(selectorArray.html);
                   });
                }
        });
    },
    insertGoalDatatoDb: function (elementVal,goalBaseUrl) {
        $.ajax({
            showLoader: false,
            url: goalBaseUrl+'abtesting/selectors/insertGoalsData',
            data: 'elementVal='+elementVal,
            type: "POST",
            dataType: 'json'
            }).done(function (data) {
            if(data.success == true) {
              $.each(data.value, function( index, selectorArray ) {
                $(selectorArray.selector).html(selectorArray.html);
               });
            }
    });
},
_checkAndVerifyUrlData: function (goalId,variantId,currentUrl,goalUrl,goalBaseUrl) {
    var urlData = 'goalId='+goalId+'&variantId='+variantId+'&url='+goalUrl+'&currentUrl='+currentUrl;
    $.ajax({
        showLoader: false,
        url: goalBaseUrl+'abtesting/selectors/insertGoalsDataUrl',
        data: urlData,
        type: "POST",
        dataType: 'json'
        }).done(function (data) {
        if(data.success == true) {
          $.each(data.value, function( index, selectorArray ) {
            $(selectorArray.selector).html(selectorArray.html);
           });
        }
});
}
    });
    return $.mage.wkabtestgoalsjs;
});
