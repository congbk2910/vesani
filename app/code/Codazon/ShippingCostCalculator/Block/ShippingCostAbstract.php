<?php
/**
 * Copyright © 2018 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\ShippingCostCalculator\Block;

use Magento\Framework\View\Element\Template;

class ShippingCostAbstract extends Template
{
    public function __construct(
		Template\Context $context,
		\Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Registry $coreRegistry,
        \Codazon\ShippingCostCalculator\Helper\Data $helper,
        array $data = []
	){
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->context = $context;
        $this->storeManager = $context->getStoreManager();
        $this->mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->assetRepository = $context->getAssetRepository();
        $this->helper = $helper;
        $this->coreRegistry = $coreRegistry;
        $this->copeConfig = $context->getScopeConfig();
    }
 
 
}