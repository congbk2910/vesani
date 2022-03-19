<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Block\Brand;

class AllBrands extends \Magento\Framework\View\Element\Template implements \Magento\Framework\DataObject\IdentityInterface
{
    protected $_coreRegistry = null;
    
    protected $_scopeConfig = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }
    
    public function getIdentities()
    {
        return ['codazon_all_brands_page'];
    }
    
    public function getPageInfo()
    {
         if (!$this->_coreRegistry->registry('all_brands_info')) {
             $brands = new \Magento\Framework\DataObject([
                'title'                     => $this->_scopeConfig->getValue('codazon_shopbybrand/all_brand_page/title', 'store')?:__('Our Brands'),
                'description'               => $this->_scopeConfig->getValue('codazon_shopbybrand/all_brand_page/description', 'store')?:'',
                'display_featured_brands'   => $this->_scopeConfig->getValue('codazon_shopbybrand/all_brand_page/display_featured_brands', 'store'),
                'display_brand_search'      => $this->_scopeConfig->getValue('codazon_shopbybrand/all_brand_page/display_brand_search', 'store'),
                'meta_title'                => $this->_scopeConfig->getValue('codazon_shopbybrand/all_brand_page/meta_title', 'store'),
                'meta_keywords'             => $this->_scopeConfig->getValue('codazon_shopbybrand/all_brand_page/meta_keywords', 'store'),
                'meta_description'          => $this->_scopeConfig->getValue('codazon_shopbybrand/all_brand_page/meta_description', 'store'),
                'featured_brand_title'      => $this->_scopeConfig->getValue('codazon_shopbybrand/featured_brands/title', 'store')
            ]);
            $this->_coreRegistry->register('all_brands_info', $brands);
         }
         return $this->_coreRegistry->registry('all_brands_info');
    }
    
}