define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "jquery/ui",
], function ($, alert) {
    'use strict';
    $.widget('mage.wkabtestpreview', {

        options: {
            confirmMsg: ('divElement is removed.')
        },
        _create: function () {

            var self = this;
            var previewVariantId = self.options.previewVariantId;
            var previewDataUrl = self.options.previewDataUrl;
            if (typeof(previewVariantId) != "undefined" && typeof(previewDataUrl) != "undefined")  {
                var showLoader=true;
                self._getVariantDataFromDb(previewVariantId,previewDataUrl);
            }
        },
        _getVariantDataFromDb: function (previewVariantId,previewDataUrl) {
            $.ajax({
                showLoader: true,
                url: previewDataUrl+'abtesting/selectors/getvariantdata',
                data: 'variantId='+previewVariantId,
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
    return $.mage.wkabtestpreview;
});
