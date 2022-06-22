<?php
/**
 * Copyright © 2017 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Codazon\DynamicRow\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
    }

    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
