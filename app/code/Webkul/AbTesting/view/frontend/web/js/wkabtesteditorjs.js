define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "jquery/ui",
], function ($, alert) {
    'use strict';
    $.widget('mage.wkabtesteditor', {

        options: {
            confirmMsg: ('')
        },
        _create: function () {

            var self = this;
            var currentVariantId = self.options.currentVariantId;
            var baseUrl = self.options.baseUrl;
            var mainControlUrl = self.options.mainControlUrl;
            var checkAdminStatusUrl = self.options.checkAdminStatusUrl;
            var variantToken = self.options.variantToken;
            var previewUrl = mainControlUrl+'?previewId='+currentVariantId;
            var pageMainClass = $("body").attr('class');
            $( document ).ready(function(){
                if (typeof(baseUrl) != "undefined" && typeof(mainControlUrl) != "undefined") {
                    var showLoader=true;
                    self._checkIsAdminLoggedIn(baseUrl,mainControlUrl,variantToken);
                }
                if (typeof(pageMainClass) != "undefined" && typeof(baseUrl) != "undefined")  {
                    var showLoader=true;
                    self._getAllParentClass(baseUrl,pageMainClass);
                }
                 // load localstorage data if exist
                var variantData = JSON.parse(localStorage.getItem(currentVariantId));
                if (localStorage.getItem(currentVariantId) !== null) {
                        $.each(variantData, function( index, value ) {
                            if(typeof(index) != undefined) {
                                $(index).html(value);
                            }
                        });
                } else {
                    self._getVariantDataFromDbIfExists(currentVariantId,baseUrl);
                }
            });

          //on saving edits into database
          $("#save-local-storage").on('click',function(){
            $("#textBox").html("");
            $("#editor").hide();
            var variantData = JSON.parse(localStorage.getItem(currentVariantId));
            if (variantData && variantData instanceof Array && !variantData.length) {
                alert({
                   title: 'No Data to save',
                   content: 'No changes available to be saved',
                   actions: {
                       always: function(){}
                   }
               });
            } else {
                if(variantData != null) {
                    self._saveEditsToDb(baseUrl,currentVariantId,variantData);
                } else {
                    alert({
                       title: 'No Data to save',
                       content: 'No changes available to be saved',
                       actions: {
                           always: function(){}
                       }
                    });
                }
            }
        });
        // preview of variants
        $("#preview-page").on('click',function(){
            if (typeof(currentVariantId) != "undefined" && typeof(baseUrl) != "undefined") {
                var showLoader=true;
                self._getVariantDataFromDb(currentVariantId,baseUrl,previewUrl);
            }
        });

          //close editor block
          $("#close-editor").on('click',function(event) {
            $('#editor').addClass('no-display');
          });
          // save variants info to local storage
          $("#submithtml").on('click',function(event){
            var data = $('#textBox').html();
            $('.widget-selected').html(data);
            var uniqueClassId = $('.widget-selected').closest("[wk-data-id^=wk-ab-]").attr("wk-data-id");
            if(typeof(uniqueClassId) != undefined) {
                self._saveEditsToLocalStorage(baseUrl,uniqueClassId,currentVariantId);
                event.preventDefault();
            }
          });
        },

        _getVariantDataFromDbIfExists: function (variantId,baseUrl) {
            $.ajax({
                showLoader: true,
                url: baseUrl+'abtesting/selectors/getvariantdata',
                data: 'variantId='+variantId,
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
        _saveEditsToLocalStorage: function (baseUrl,uniqueClassId,currentVariantId) {

            var parentClass = "";
            $.ajax({
                showLoader: true,
                url: baseUrl+'abtesting/selectors/getClassFromId',
                type: 'get',
                data: 'uniqueId='+uniqueClassId,
                dataType: 'json'
                }).done(function (data) {
                    parentClass  = data.value;
                    $(parentClass).html(function (i, html) {
                        var storageValue =  html.replace(/&nbsp;/g, " ");
                        var storageValue =  storageValue.replace('widget-selected', '');
                        var storageValue =  storageValue.replace('editable-border', '');
                        if(storageValue == undefined) {
                            return;
                        }
                        if (localStorage.getItem(currentVariantId) !== null) {
                      var variantData = JSON.parse(localStorage.getItem(currentVariantId));
                      if(variantData) {
                        if($.inArray(parentClass, variantData )) {
                          variantData[parentClass] = storageValue;
                        } else {
                          variantData[parentClass] = storageValue;
                        }
                      }
                    localStorage.setItem(currentVariantId, JSON.stringify(variantData));
                    } else {
                      var variantData = {};
                      variantData[parentClass] = storageValue;

                        localStorage.setItem(currentVariantId, JSON.stringify(variantData));
                    }
                    });
                    
        });
        },
        _checkIsAdminLoggedIn: function (baseUrl,mainControlUrl,variantToken) {
            var errorContent = "Invalid Admin Session. Please log in to make edits";
                var variantTokenData = 'variantToken='+variantToken;
                $.ajax({
                    showLoader: true,
                    url: baseUrl+'abtesting/selectors/checkTokenStatus',
                    type: "GET",
                    data: variantTokenData,
                    }).done(function (data) {
                    if(data.success == false) {
                     alert({
                       title: 'Invalid Session',
                       content: errorContent,
                       actions: {
                           always: function(){}
                       }
                    });
                    window.location.href = mainControlUrl;
                    }
                });
        },
        _getAllParentClass: function (baseUrl,pageMainClass) {
                $.ajax({
                    showLoader: true,
                    url: baseUrl+'abtesting/selectors/getparentajax',
                    data: 'bodyClass='+pageMainClass,
                    type: "POST",
                    dataType: 'json'
                    }).done(function (data) {
                    if(data.value != null) {
                        $.each(data.value, function( index, value ) {
                            $(index).addClass('editable-border');
                            $(index).attr('wk-data-id',value);
                        });
                    }
            });
        },
        _saveEditsToDb: function (baseUrl,currentVariantId,variantData) {
            $.ajax({
                showLoader: true,
                url: baseUrl+'abtesting/selectors/savelocalstoragetodb',
                data: 'variantId='+currentVariantId+'&variantData='+JSON.stringify(variantData),
                type: "POST",
                dataType: 'json'
                }).done(function (data) {
                if(data.success == true) {
                 alert({
                           title: 'Success',
                           content: data.value,
                           actions: {
                               always: function(){}
                           }
                });
                localStorage.removeItem(currentVariantId);
                }
        });
    },
    _getVariantDataFromDb: function (currentVariantId,baseUrl,previewUrl) {
        $.ajax({
            showLoader: true,
            url: baseUrl+'abtesting/selectors/getvariantdata',
            data: 'variantId='+currentVariantId,
            type: "POST",
            dataType: 'json'
            }).done(function (data) {
            if(data.success == true) {
                 var redirectWin = window.open(previewUrl, "_blank");
                 redirectWin.focus();
            } else {
                alert({
                       title: 'Preview',
                       content: 'You have not saved any data for preview yet.',
                       actions: {
                           always: function(){}
                       }
                });
            }
    });
    }
    });
    return $.mage.wkabtesteditor;
});
