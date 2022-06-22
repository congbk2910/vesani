<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\AjaxLayeredNavPro\Plugin\Catalog\Model;

use Magento\Store\Model\StoreManagerInterface;

class Config
{
    protected $helper;

    public function __construct(
        \Codazon\AjaxLayeredNavPro\Helper\Data $helper
    ) {
        $this->helper = $helper;

    }

    /**
     * Adding custom options and changing labels
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param [] $options
     * @return []
     */
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        if ($this->helper->enableSortByRating()) {
            $options[$this->helper->getRatingCode()] = $this->helper->getRatingSortLabel();
        }
        return $options;
    }
}


