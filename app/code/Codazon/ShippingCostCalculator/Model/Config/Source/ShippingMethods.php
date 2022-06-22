<?php
/**
 * Copyright © 2018 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\ShippingCostCalculator\Model\Config\Source;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote as Quote;
use Magento\Framework\Reflection\DataObjectProcessor;

class ShippingMethods implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options;
    
    public function toOptionArray()
    {
        if ($this->_options === null) {
            $this->_options = $this->_getOptions();
        }
        return $this->_options;
    }
    
    protected function _getOptions()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $shippingConfig = $objectManager->get('Magento\Shipping\Model\Config');
        $activeCarriers = $shippingConfig->getActiveCarriers();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $methods = [];
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = [];
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode.'_'.$methodCode;
                    $options[] = ['value' => $code,'label' => $method];
                }
                $carrierTitle =$scopeConfig->getValue('carriers/'.$carrierCode.'/title');
            }
            $methods[] = ['value' => $options, 'label' => $carrierTitle];
        }
        return $methods;
    }
}