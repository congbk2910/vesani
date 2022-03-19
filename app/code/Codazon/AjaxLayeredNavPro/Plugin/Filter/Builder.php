<?php
/**
 * Copyright © 2017 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Codazon\AjaxLayeredNavPro\Plugin\Filter;

class Builder
{
    protected $helper;
        
    public function __construct(
        \Codazon\AjaxLayeredNavPro\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    
    public function aroundBuild(
        \Magento\Framework\Search\Adapter\Mysql\Filter\Builder $subject,
        \Closure $proceed,
        \Magento\Framework\Search\Request\FilterInterface $filter,
        $condition
    )
    {
        return $proceed($filter, $condition);
    }
    
}