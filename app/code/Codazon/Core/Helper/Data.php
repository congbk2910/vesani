<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\Core\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    
    protected $coreRegistry;
    
    protected $storeManager;
    
    protected $context;
    
    protected $scopeConfig;
    
    protected $storeId;
    
    protected $layout;
    
    protected $pageConfig;
    
    protected $request;
    
    protected $blockFilter;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->scopeConfig = $context->getScopeConfig();
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->storeId = $storeManager->getStore()->getId();
        $this->layout = $layout;
        $this->pageConfig = $pageConfig;
    }
    
    public function getObjectManager()
    {
        return $this->objectManager;
    }
    
    public function getStoreManager()
    {
        return $this->storeManager;
    }
    
    public function getCurrentStoreId()
    {
        return $this->storeId;
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
    
    public function getPageConfig()
    {
        return $this->pageConfig;
    }
    
    public function getUrl($path = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($path, $params);
    }
    
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }
    
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = $this->objectManager->get(\Magento\Framework\App\RequestInterface::class);
        }
        return $this->request;
    }
    
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->storeId);
    }
    
    public function getMediaUrl($path = '')
    {
        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;   
    }
    
    public function getBlockFilter()
    {
        if ($this->blockFilter === null) {
            $this->blockFilter = $this->objectManager->get(\Magento\Cms\Model\Template\FilterProvider::class)->getBlockFilter();
        }
        return $this->blockFilter;
    }
    
    public function htmlFilter($content)
    {
        return $this->getBlockFilter()->filter($content);
    }
    
    public function getCoreRegistry()
    {
        return $this->coreRegistry;
    }
}
