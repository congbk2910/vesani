<?php
/**
 * Copyright © 2017 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Codazon\AjaxLayeredNavPro\Plugin\Catalog;

class FilterList 
{
    protected $helper;
        
    public function __construct(
        \Codazon\AjaxLayeredNavPro\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    
    public function aroundGetFilters(
        \Magento\Catalog\Model\Layer\FilterList $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer $layer)
    {
        $filters = $proceed($layer);
        if ($this->helper->enableFilterByRating()) {
            $productCollection = $layer->getProductCollection();
            $this->helper->attachRatingAvgPercentFieldToCollection($productCollection);
            $filters[] = $this->helper->getRatingFilter($layer);
        }
        return $filters;
    }
    
}