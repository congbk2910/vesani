<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\ProductLabel\Block;

class ProductLabel extends \Magento\Framework\View\Element\Template
{
	protected $objectManager;
    protected $_labels = null;

    protected $_template = 'productlabel.phtml';

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\SalesRule\Model\Rule $saleRuleModel,
        \Codazon\ProductLabel\Model\ProductLabel $labelModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		array $data = []
    ) {
		$this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_saleRuleModel = $saleRuleModel;
        $this->_labelModel = $labelModel;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    protected function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 3600;
    }

    public function getCacheKeyInfo()
    {
        return [
            'PRODUCT_LABEL',
            $this->getProduct()->getId()
        ];
    }

    public function getLabels(){
        if($this->_labels){
            return $this->_labels;
        }
        $this->_labels = $this->_labelModel->getCollection()->setStoreId($this->_storeManager->getStore(true)->getId())
            ->addFieldToFilter('is_active',array('eq' => 1))
            ->addAttributeToSelect('content')
            ->addAttributeToSelect('label_image')
            ->addAttributeToSelect('label_background')
            ->addAttributeToSelect('custom_class')
            ->addAttributeToSelect('custom_css');            
        return $this->_labels;
    }

    public function getObject(){
        $labels = $this->getLabels();
        $_product = $this->getProduct();
        $validLabels = [];
        foreach ($labels as $label) {
            $conditionsArr = $label->getConditions();
            $this->_labelModel->getConditions()->setConditions([])->loadArray($conditionsArr);
            if ($validate = (bool)$this->_labelModel->validate($_product)) {
                $validLabels[] = $label;
            } else {
                if ($_product->getTypeId() == 'configurable') {
                    $_children = $_product->getTypeInstance()->getUsedProducts($_product);
                    if(count($_children) > 0){
                        foreach($_children as $_child){
                            if($validate = $this->_labelModel->validate($_child)){
                                $validLabels[] = $label; break;
                            }
                        }
                    }
                }
            }
        }
        return ['labels'=>$validLabels, 'product' => $_product];
    }
    
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
