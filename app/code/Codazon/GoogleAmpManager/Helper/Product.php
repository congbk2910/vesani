<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\GoogleAmpManager\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $registry;
    
    protected $_storeManager;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->registry = $registry;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function getProductPageCrumbs()
    {
		$evercrumbs = [];
		
		$evercrumbs[] = [
			'label' => __('Home'),
			'title' => __('Go to Home Page'),
			'link' => $this->_storeManager->getStore()->getBaseUrl()
		];

		$product = $this->registry->registry('current_product');
		$categoryCollection = clone $product->getCategoryCollection();
		$categoryCollection->clear();
		$categoryCollection->addAttributeToSort('level', $categoryCollection::SORT_ORDER_DESC)
            ->addAttributeToFilter('path', array('like' => "1/" . $this->_storeManager->getStore()->getRootCategoryId() . "/%"));
		$categoryCollection->setPageSize(1);
		$breadcrumbCategories = $categoryCollection->getFirstItem()->getParentCategories();
		foreach ($breadcrumbCategories as $category) {
			$evercrumbs[] = [
				'label' => $category->getName(),
				'title' => $category->getName(),
				'link' => $category->getUrl()
			];
		}
	
		
		$evercrumbs[] = [
            'label' => $product->getName(),
            'title' => $product->getName(),
            'link' => '',
            'last' => true
        ];
				
		return $evercrumbs;
    }
}