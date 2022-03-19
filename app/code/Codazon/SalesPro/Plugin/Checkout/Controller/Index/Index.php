<?php
/**
 * Copyright © 2020 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Codazon\SalesPro\Plugin\Checkout\Controller\Index;

class Index
{
    protected $helper;
    
    public function __construct(
        \Codazon\SalesPro\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    
    /* public function aroundExecute(
        \Magento\Checkout\Controller\Index\Index $subject,
        \Closure $proceed
    ) {
        $resultPage = $proceed();
        $resultPage->getLayout()->getUpdate()->addPageHandles(['codazon_onestepcheckout']);
        return $resultPage;
    } */
    
    /* public function afterExecute(\Magento\Checkout\Controller\Index\Index $controller, $resultPage)
    {
        if ($this->helper->enableOneStepCheckout()) {
            $resultPage->getLayout()->getUpdate()->addPageHandles(['codazon_onestepcheckout']);
        }
        return $resultPage;
    } */
}