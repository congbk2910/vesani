/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper, commentAssigner) {
    'use strict';

    return function (placeOrderService) {
        return wrapper.wrap(placeOrderService, function (originalAction, serviceUrl, payload, messageContainer) {
            if ($('#osc_order_comment').length) {
                payload['comments'] = $('#osc_order_comment').val();
            }
            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});
