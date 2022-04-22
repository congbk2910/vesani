require([
    "jquery",
    'Magento_Ui/js/modal/modal'
    ], function($,modal){
        $(document).ready(function() {
        });
        $(document).on('change',"[id='tracks']",function () {
            var optionId = $(this).val();
            var optionVal = $('.tracks option:selected').text();
            if(optionVal == 'Track Pages Visit on') {
                $('.wktrackurl').removeClass('no-display');
                $('.wktrackcond').removeClass('no-display');
            } else if(optionVal == 'Click on element') {
                $('.wkcssselector').removeClass('no-display');
                $('.wktrackurl').addClass('no-display');
                $('.wktrackcond').addClass('no-display');
            } else {
                $('.wktrackurl').addClass('no-display');
                $('.wktrackcond').addClass('no-display');
                $('.wkcssselector').addClass('no-display');
            }
       });
       
    });