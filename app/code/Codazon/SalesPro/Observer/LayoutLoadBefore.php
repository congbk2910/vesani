<?php
/**
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\SalesPro\Observer;

class LayoutLoadBefore implements \Magento\Framework\Event\ObserverInterface
{    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $update = $observer->getLayout()->getUpdate();
        $handles = $update->getHandles();        
        if (in_array('checkout_index_index', $handles)) {
            if (\Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Codazon\SalesPro\Helper\Data::class)->enableOneStepCheckout()) {
                    $update->addPageHandles(['codazon_onestepcheckout']);
            }
        }
    }
}
