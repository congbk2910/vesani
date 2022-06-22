<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Controller\Adminhtml\Index;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
	public function __construct(Context $context, PageFactory $resultPageFactory)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}
	public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $attributeCode = $this->_objectManager->get(\Magento\Framework\App\Config\ScopeConfigInterface::class)
                ->getValue('codazon_shopbybrand/general/attribute_code', 'store', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
                
        if ($attributeId = $this->getRequest()->getParam('attribute_id')) {
             $attr = $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class)->load($attributeId); 
        } else {
             $attr = $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class)->load($attributeCode, 'attribute_code'); 
        }
        $label = $attr->getFrontendLabel();
        if ($attributeCode == $attr->getAttributeCode()) {
            $title = __("Brand options ({$label})");
        } else {
            $title = __("Attribute options ({$label})");
        }
        
        
        $resultPage->setActiveMenu('Codazon_Shopbybrandpro::shopbybrandpro');
        $resultPage->addBreadcrumb($title, $title);
        $resultPage->addBreadcrumb($title, $title);
        $resultPage->getConfig()->getTitle()->prepend($title);
        
        return $resultPage;
    }
	/**
     * Is the user allowed to view the menu grid.
     *
     * @return bool
     */
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Codazon_Shopbybrandpro::index');
    }
}