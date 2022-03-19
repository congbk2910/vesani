<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Codazon\Shopbybrandpro\Controller\Index;

use Magento\Framework\View\Result\LayoutFactory;

class SearchBrands extends \Magento\Framework\App\Action\Action
{
    
    protected $_brandObject;
    
    protected $_context;
    
    protected $_attributeCode;
    
    protected $_viewRoute;
    
    protected $_helper;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        LayoutFactory $resultLayoutFactory,
        \Codazon\Shopbybrandpro\Model\BrandFactory $brandFactory,
        \Codazon\Shopbybrandpro\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_urlManager = $context->getUrl();
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_brandFactory = $brandFactory;
        $this->_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->_imageHelper = $helper->getImageHelper();
        $this->_attributeCode = $helper->getStoreBrandCode();
        $this->_viewRoute = $helper->getViewRoute();
        $this->_helper = $helper;
    }
    
    public function getUrl($urlKey, $params = null)
    {
        return $this->_urlManager->getUrl($urlKey, $params);
    }
    
    public function getBrandCollection($query = false, $orderBy = 'name', $orderDir = 'asc')
    {
        if (!$this->_brandObject) {
            $collection = $this->_helper->getBrandCollection(null, null, ['brand_url_key', 'brand_thumbnail']);
            $collection->setOrder($orderBy, $orderDir);
            if ($query) {
                $collection->addFieldToFilter('name', ['like' => "%{$query}%"]);
            }
            $this->_brandObject = $collection;
		}
		return $this->_brandObject;
    }
    
    public function execute()
    {
        $brandLabels = [];
        $query = $this->getRequest()->getParam('term', false);
        $brandData = $this->getBrandCollection($query);
        if (count($brandData)) {
            foreach ($brandData as $brand) {
                $brandLabels[] = [
                    'label' => $brand->getData('name'),
                    'value' => $brand->getData('name'),
                    'url'   => $this->_helper->getBrandUrl($brand),
                    'img'   => $this->getThumbnailImage($brand, ['width' => 50, 'height' => 50])
                ];
            }
        }
        echo json_encode($brandLabels); die();
    }
    
    public function getThumbnailImage($brand, array $options = []) {
		if (!($brandThumb = $brand->getBrandThumbnail())) {
			$brandThumb = 'codazon/brand/placeholder_thumbnail.jpg';
        }
        if (isset($options['width']) || isset($options['height'])) {
            if(!isset($options['width']))
                $options['width'] = null;
            if(!isset($options['height']))
                $options['height'] = null;
            return $this->_imageHelper->init($brandThumb)->resize($options['width'], $options['height'])->__toString();
        } else {
            return $this->_mediaUrl.$brandThumb;
        }
	}
    
}