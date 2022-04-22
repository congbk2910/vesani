<?php

namespace Omnisend\Omnisend\Block\Adminhtml\OrderStatus;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Model\OmnisendOrderStatus;

class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry, array $data = [])
    {
        $this->coreRegistry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = OmnisendOrderStatusInterface::STATUS;
        $this->_blockGroup = 'Omnisend_Omnisend';
        $this->_controller = 'adminhtml_orderStatus';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Details'));

        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                        'mage-init' => [
                                'button' => [
                                        'event' => 'saveAndContinueEdit',
                                        'target' => '#edit_form'
                                    ],
                            ],
                    ]
            ],
            -100
        );

        $this->buttonList->update('delete', 'label', __('Delete'));
    }

    /**
     * @return Phrase
     */
    public function getHeaderText()
    {
        if ($this->getOrderStatusFromRegistry()->getId()) {
            return __("Edit Order Status");
        }

        return __('New Order Status');
    }

    /**
     * @return string
     */
    protected function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'omnisend/*/save',
            [
                '_current' => true,
                'back' => 'edit',
                'active_tab' => ''
            ]
        );
    }

    /**
     * @return OmnisendOrderStatus
     */
    protected function getOrderStatusFromRegistry()
    {
        return $this->coreRegistry->registry('order_status');
    }
}
