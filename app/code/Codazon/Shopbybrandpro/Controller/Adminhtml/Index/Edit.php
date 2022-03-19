<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
	
    public function __construct(
		Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\Registry $registry
	) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_coreRegistry = $registry;
		parent::__construct($context);
	}
    
    protected function getAttributeModel()
    {
        if ($model = $this->_coreRegistry->registry('attribute_model')) {
            return $model;
        }
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $model = $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
        if (!$attributeId) {
            $optionId = $this->getRequest()->getParam('option_id');
            $connection = $model->getResource()->getConnection();
            $select = $connection->select()->from(
                $model->getResource()->getTable('eav_attribute_option'), 'attribute_id'
            )->where('option_id = '. $optionId)->limit(1);
            $attributeId = $connection->fetchOne($select);
        }
        $this->_coreRegistry->register('attribute_model', $model);
        return $model->load($attributeId);
    }
    
	public function execute()
    {
        $optionId = $this->getRequest()->getParam('option_id');
		$entityId = $this->getRequest()->getParam('entity_id');
		$model = $this->_objectManager->create(\Codazon\Shopbybrandpro\Model\Brand::class);
		$storeId = (int)$this->getRequest()->getParam('store');
		if ($optionId) {
			$model->setStore($storeId);
			$model->setStoreId($storeId);
			$model->setOptionId($optionId);	
			$model->load($entityId);
		}
	
		$data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}
	
		$this->_coreRegistry->register('brand', $model);
	
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->_initAction();
        
        $attrModel = $this->getAttributeModel();
        
        $attrLabel = $attrModel->getFrontendLabel();
        
        if ($model->getBrandLabel()) {
            $title = __('%1 [Attribute: %2]', $model->getBrandLabel(), $attrLabel);
        } else {
            $title = __('Edit Brand');
        }

		$resultPage->getConfig()->getTitle()->prepend($title);
	
		return $resultPage;
    }
	
	protected function _initAction()
	{
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Codazon_Shopbybrandpro::shopbybrandpro');
        $resultPage->addBreadcrumb(__('Edit Option'), __('Edit Option'));
		return $resultPage;
	}
	
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Codazon_Shopbybrandpro::edit');
    }
}