<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Codazon\Shopbybrandpro\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_scopeConfig;
    
    protected $_helper;
    
    protected $_attributeCode;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Codazon\Shopbybrandpro\Model\BrandFactory $brandFactory,
        \Codazon\Shopbybrandpro\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_scopeConfig = $helper->getScopeConfig();
        $this->_storeManager = $helper->getStoreManager();
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->_brandFactory = $brandFactory;
        $this->_attributeCode = $helper->getStoreBrandCode();
    }
    
    public function initBrands()
    {
        if (!$this->_coreRegistry->registry('brand_collection')) {
            $this->_coreRegistry->register('brand_collection', $this->_helper->getBrandCollection(
                null,
                null,
                $this->_helper->getAttributesInList()
            ));
        }
        return $this->_coreRegistry->registry('brand_collection');
    }
    
    protected function _initBrandPage()
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
    
    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $brand = $this->_initBrandPage();
        $this->initBrands();
        $pageConfig = $page->getConfig();
        $pageConfig->addRemotePageAsset($this->_storeManager->getStore()->getCurrentUrl(false),
            'canonical', 
            ['attributes' => ['rel' => 'canonical']]
        );
        $pageConfig->addBodyClass('cdz-all-brands');
        
        $title = $brand->getData('title');
        
        $pageConfig->getTitle()->set($brand->getData('meta_title')?:$title);
        $pageConfig->setKeywords($brand->getData('meta_keywords'));
        $pageConfig->setDescription($brand->getData('meta_description'));
        
        $pageMainTitle = $page->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($title);
        }
        return $page;
    }
    
}