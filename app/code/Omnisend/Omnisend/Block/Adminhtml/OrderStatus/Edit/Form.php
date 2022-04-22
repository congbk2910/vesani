<?php

namespace Omnisend\Omnisend\Block\Adminhtml\OrderStatus\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Helper\OrderStatusHelper;

class Form extends Generic
{
    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var OrderStatusHelper
     */
    private $orderStatusHelper;

    /**
     * Form constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param OrderStatusHelper $orderStatusHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        OrderStatusHelper $orderStatusHelper,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->orderStatusHelper = $orderStatusHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('orderstatus_form');
        $this->setTitle(__('Order Status Information'));
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $omnisendOrderStatus = $this->_coreRegistry->registry('order_status');

        $form = $this->_formFactory->create(
            [
                'data' => [
                        'id' => 'edit_form',
                        'action' => $this->getData('action'),
                        'method' => 'post'
                    ]
            ]
        );

        $form->setHtmlIdPrefix('orderstatus_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            OmnisendOrderStatusInterface::STATUS,
            'select',
            [
                'name' => OmnisendOrderStatusInterface::STATUS,
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->orderStatusHelper->getOrderStatusOptions(),
                'required' => true
            ]
        );

        $fieldset->addField(
            OmnisendOrderStatusInterface::PAYMENT_STATUS,
            'select',
            [
                'name' => OmnisendOrderStatusInterface::PAYMENT_STATUS,
                'label' => __('Payment Status'),
                'title' => __('Payment Status'),
                'options' => $this->orderStatusHelper->getOrderPaymentStatusOptions(),
                'required' => true
            ]
        );

        $fieldset->addField(
            OmnisendOrderStatusInterface::FULFILLMENT_STATUS,
            'select',
            [
                'name' => OmnisendOrderStatusInterface::FULFILLMENT_STATUS,
                'label' => __('Fulfillment Status'),
                'title' => __('Fulfillment Status'),
                'options' => $this->orderStatusHelper->getOrderFulfillmentStatusOptions(),
                'required' => true
            ]
        );

        $form->setValues($omnisendOrderStatus->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
