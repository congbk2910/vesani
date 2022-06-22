<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\SalesPro\Helper;

class Data extends \Codazon\Core\Helper\Data
{
    public function enableOneStepCheckout()
    {
        return $this->getConfig('codazon_osc/general/enable');
    }
    
    public function getCustomOptionsJson()
    {
        $options = [];
        $options['customPlaceOrderLabel'] = $this->getConfig('codazon_osc/customization/place_order_label');
        $options['enableOrderComment'] = (bool)$this->getConfig('codazon_osc/customization/enable_order_comment');
        $options['defaultShippingMethod'] = $this->getConfig('codazon_osc/customization/default_shipping_method');
        return json_encode($options);
    }
}
