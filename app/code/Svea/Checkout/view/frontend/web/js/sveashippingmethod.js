define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";

    function main(config, element) {
        $(document).on('change', '.qty input' ,function(){
	 setTimeout(function(){
                $.ajax({
                    context: '#shipping-method-form',
                    url: '/sveacheckout/order/ReloadShippingMethods',
                    type: 'GET',
                    data: '',
                }).done(function (data) {
                    $('#shipping-method-form').html(data.output);
                    return true;
                });
            },2000);
        });
    };
    return main;
});
