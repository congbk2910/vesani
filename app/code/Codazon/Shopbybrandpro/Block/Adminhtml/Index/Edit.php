<?php

namespace Codazon\Shopbybrandpro\Block\Adminhtml\Index;

use Magento\Framework\App\ObjectManager;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
	protected $_coreRegistry;
    
    protected $_objectManager;
    
	public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_objectManager = ObjectManager::getInstance();
        parent::__construct($context, $data);
    }
	
	protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Codazon_Shopbybrandpro';
        $this->_controller = 'adminhtml_index';
		
        parent::_construct();

        if ($this->_isAllowedAction('Codazon_Shopbybrandpro::save')) {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }
		
		$this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }
        ";
        $this->buttonList->remove('delete');
    }
    
	public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('brand')->getId()) {
            return __("Edit '%1'", $this->escapeHtml($this->_coreRegistry->registry('brand')->getTitle()));
        } else {
            return __('Edit Brand');
        }
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
    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
	protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('shopbybrandpro/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
    
    public function getBackUrl()
    {
        if ($attrModel = $this->getAttributeModel()) {
            $attributeId = $attrModel->getAttributeId();
        } else {
            $attributeId = $this->getRequest()->getParam('attribute_id', false);
        }
        return $this->getUrl('*/*/', ['attribute_id' => $attributeId]);
    }
}