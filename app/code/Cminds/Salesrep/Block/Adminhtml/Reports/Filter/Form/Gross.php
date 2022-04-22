<?php

namespace Cminds\Salesrep\Block\Adminhtml\Reports\Filter\Form;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Gross extends \Magento\Reports\Block\Adminhtml\Filter\Form
{

    protected $orderConfig;

    protected $salesrepHelper;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Sales\Model\Order\ConfigFactory $orderConfig,
        \Cminds\Salesrep\Helper\Data $salesrepHelper,
        array $data
    ) {
        $this->orderConfig = $orderConfig;
        $this->salesrepHelper = $salesrepHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Add fieldset with general report fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/gross');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'filter_form',
                    'action' => $actionUrl,
                    'method' => 'get'
                ]
            ]
        );

        $data = [];

        $salesrepConfig = $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/report_defaults/sales_rep',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        if ($salesrepConfig) {
            $data['sales_rep'] = $salesrepConfig;
        }
        $orderStatusesConfig = $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/report_defaults/order_statuses',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        if ($orderStatusesConfig) {
            $data['order_statuses'] = $orderStatusesConfig;
        }

        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Filter')]
        );

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $fieldset->addField('store_ids', 'hidden', ['name' => 'store_ids']);

        $fieldset->addField(
            'period_type',
            'select',
            [
                'name' => 'period_type',
                'options' => [
                    'day' => __('Day'),
                    'week' => __('Week'),
                    'month' => __('Month'),
                    'year' => __('Year')
                ],
                'label' => __('Breakdown'),
                'title' => __('Breakdown')
            ]
        );

        $fieldset->addField(
            'from',
            'date',
            [
                'name' => 'from',
                'date_format' => $dateFormat,
                'label' => __('From'),
                'title' => __('From'),
                'required' => true,
                'class' => 'admin__control-text'
            ]
        );

        $fieldset->addField(
            'to',
            'date',
            [
                'name' => 'to',
                'date_format' => $dateFormat,
                'label' => __('To'),
                'title' => __('To'),
                'required' => true,
                'class' => 'admin__control-text'
            ]
        );

        $fieldset->addField(
            'include_commission_status',
            'select',
            [
                'name' => 'include_commission_status',
                'label' => __('Include Commission Status'),
                'options' => ['0' => __('Any'), '1' => __('Specified')],
                'note' => __('Applies to Any of the Specified Commission Status')
            ],
            'to'
        );

        $fieldset->addField(
            'commission_statuses',
            'select',
            [
                'name' => 'commission_statuses',
                'label' => '',
                'options' => [
                    'Paid' => __('Paid'),
                    'Unpaid' => __('Unpaid')
                ],
                'display' => 'none'
            ],
            'commission_statuses'
        );

        // define field dependencies
        if ($this->getFieldVisibility('include_commission_status')
            && $this->getFieldVisibility('commission_statuses')
        ) {
            $this->setChild(
                'form_after',
                $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Widget\Form\Element\Dependence'
                )->addFieldMap(
                    "{$htmlIdPrefix}include_commission_status",
                    'include_commission_status'
                )->addFieldMap(
                    "{$htmlIdPrefix}commission_statuses",
                    'commission_statuses'
                )->addFieldDependence(
                    'commission_statuses',
                    'include_commission_status',
                    '1'
                )
            );
        }

        $statuses = $this->orderConfig->create()->getStatuses();
        $values = [];
        foreach ($statuses as $code => $label) {
            if (false === strpos($code, 'pending')) {
                $values[] = ['label' => __($label), 'value' => $code];
            }
        }

        $fieldset->addField(
            'order_statuses',
            'multiselect',
            [
                'name' => 'order_statuses',
                'label' => 'Order Status',
                'values' => $values,
            ]
        );

//        $fieldset->addField(
//            'sales_rep',
//            'multiselect',
//            [
//                'name' => 'sales_rep',
//                'label' => __('Sales Representative'),
//                'title' => __('Sales Representative'),
//                'values' => $this->salesrepHelper->getAdminsForReport(),
//            ]
//        );

        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);
    }
}
