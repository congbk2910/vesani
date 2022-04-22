<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);


namespace Cminds\Salesrep\Block\Adminhtml\Order\View\Tab\Tabs\Commissions\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\DataObject;

/**
 * Class Form
 * @package Cminds\Salesrep\Block\Adminhtml\Order\View\Tab\Tabs\Commissions\Edit
 */
class Form extends Generic
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Cminds\Salesrep\Helper\Data
     */
    protected $salesrepHelper;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $adminUsers;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $adminSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var \Cminds\Salesrep\Api\SalesrepRepositoryInterface
     */
    protected $salesrepRepositoryInterface;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Cminds\Salesrep\Helper\Data $salesrepHelper
     * @param \Magento\User\Model\ResourceModel\User\Collection $adminUsers
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customer
     * @param \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Cminds\Salesrep\Helper\Data $salesrepHelper,
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customer,
        \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ) {
        parent::__construct($context, $registry, $formFactory);
        $this->registry = $registry;
        $this->salesrepHelper = $salesrepHelper;
        $this->adminUsers = $adminUsers;
        $this->adminSession = $adminSession;
        $this->customerRepositoryInterface = $customer;
        $this->salesrepRepositoryInterface = $salesrepRepositoryInterface;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'method' => 'post'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $orderId = $this->getRequest()->getParam('order_id');

        $salesrepModel = $this->salesrepRepositoryInterface->getByOrderId($orderId);
        if ($salesrepModel->getSalesrepId()) {
            $data = $salesrepModel->getData();
        } else {
            $data['order_id'] = $orderId;
            $data['rep_commission_status'] = $this->_scopeConfig->getValue(
                'cminds_salesrep_configuration/'
                . 'commissions/default_status',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $data['manager_commission_status'] = $this->_scopeConfig->getValue(
                'cminds_salesrep_configuration/'
                . 'commissions/default_status',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $data['coordinator_commission_status'] = $this->_scopeConfig->getValue(
                'cminds_salesrep_configuration/'
                . 'commissions/default_status',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        $data['salesrep_change_salesrep_url'] = $this->getUrl("salesrep/order/ChangeSalesrepRep");
        $data['salesrep_change_manager_url'] = $this->getUrl("salesrep/order/ChangeManager");
        $data['salesrep_change_coordinator_url'] = $this->getUrl("salesrep/order/ChangeCoordinator");
        $data['salesrep_change_status_url'] = $this->getUrl("salesrep/order/ChangePaymentStatus");
        $data['salesrep_change_selectsalesrepadmin_url'] = $this->getUrl("salesrep/order/Selectsalesrepadmin");

        $isAdmin = $this->_authorization->isAllowed(
            'Magento_Backend::all'
        );
        $changeRep = $this->_authorization->isAllowed(
            'Cminds_Salesrep::change_representative'
        );

        $changeRepComm = $this->_authorization->isAllowed(
            'Cminds_Salesrep::change_representative_commission_status'
        );

        $changeManager = $this->_authorization->isAllowed(
            'Cminds_Salesrep::change_manager'
        );

        $changeManagerComm = $this->_authorization->isAllowed(
            'Cminds_Salesrep::change_manager_commission_status'
        );


        $fieldset = $form->addFieldset(
            'order_assign_representative',
            ['legend' => __('Sales Representative')]
        );

        $fieldset->addField(
            'order_id',
            'hidden',
            [
                'name' => 'order_id',
                'id' => 'order_id',
                'value' => $this->getRequest()->getParam('order_id'),
                'data-form-part' => 'order_form',
            ]
        );

        $fieldset->addField(
            'salesrep_change_salesrep_url',
            'hidden',
            [
                'name' => 'salesrep_change_salesrep_url',
                'id' => 'salesrep_change_salesrep_url',
                'data-form-part' => 'order_form',
            ]
        );

        $fieldset->addField(
            'salesrep_change_manager_url',
            'hidden',
            [
                'name' => 'salesrep_change_manager_url',
                'id' => 'salesrep_change_manager_url',
                'data-form-part' => 'order_form',
            ]
        );

        $fieldset->addField(
            'salesrep_change_coordinator_url',
            'hidden',
            [
                'name' => 'salesrep_change_coordinator_url',
                'id' => 'salesrep_change_coordinator_url',
                'data-form-part' => 'order_form',
            ]
        );

        $fieldset->addField(
            'salesrep_change_status_url',
            'hidden',
            [
                'name' => 'salesrep_change_status_url',
                'id' => 'salesrep_change_status_url',
                'data-form-part' => 'order_form',
            ]
        );

        $fieldset->addField(
            'salesrep_change_selectsalesrepadmin_url',
            'hidden',
            [
                'name' => 'salesrep_change_selectsalesrepadmin_url',
                'id' => 'salesrep_change_selectsalesrepadmin_url',
                'data-form-part' => 'order_form',
            ]
        );

        if ($isAdmin || $changeRep || $changeRepComm) {
            if ($changeRep) {
                $fieldset->addField(
                    'rep_id',
                    'select',
                    [
                        'name' => 'rep_name',
                        'label' => __('Name:'),
                        'id' => 'rep_name',
                        'title' => __('Name'),
                        'required' => false,
                        'values' => $this->salesrepHelper->getAdmins(),
                        'data-form-part' => 'order_form',
                    ]
                );
            } else {
                $fieldset->addField(
                    'rep_id',
                    'note',
                    [
                        'name' => 'rep_name',
                        'label' => __('Name:'),
                        'id' => 'rep_name',
                        'title' => __('Name'),
                        'required' => false,
                        'text' => ($data['rep_name']) ? $data['rep_name'] : '',
                        'data-form-part' => 'order_form',
                    ]
                );
            }


            $fieldset->addField(
                'rep_commission_earned',
                'note',
                [
                    'name' => 'rep_commission_earned',
                    'label' => __('Commission:'),
                    'id' => 'rep_commission_earned',
                    'title' => __('Commission'),
                    'required' => false,
                    'text' => $this->isCommissionSet($data, false, false),
                    'data-form-part' => 'order_form',
                ]
            );


            if ($changeRepComm) {
                $fieldset->addField(
                    'rep_commission_status',
                    'select',
                    [
                        'name' => 'rep_commission_status',
                        'label' => __('Payment Status:'),
                        'id' => 'rep_commission_status',
                        'title' => __('Payment Status'),
                        'required' => false,
                        'values' => $this->salesrepHelper->getStatusList(),
                        'data-form-part' => 'order_form',
                    ]
                );
            } else {
                $fieldset->addField(
                    'rep_commission_status',
                    'note',
                    [
                        'name' => 'rep_commission_status',
                        'label' => __('Payment Status:'),
                        'id' => 'rep_commission_status',
                        'title' => __('Payment Status'),
                        'required' => false,
                        'text' => ($data['rep_commission_status']) ? $data['rep_commission_status'] : '',
                        'data-form-part' => 'order_form',
                    ]
                );
            }
        }

        // Manager
        if ($isAdmin || $changeManagerComm || $changeManager) {
            $fieldset = $form->addFieldset(
                'order_assign_manager',
                ['legend' => __('Manager')]
            );

            if ($changeManager) {
                $fieldset->addField(
                    'manager_id',
                    'select',
                    [
                        'name' => 'manager_name',
                        'label' => __('Name:'),
                        'id' => 'manager_name',
                        'title' => __('Name'),
                        'required' => false,
                        'values' => $this->salesrepHelper->getAdmins(),
                        'data-form-part' => 'order_form',
                    ]
                );
            } else {
                $fieldset->addField(
                    'manager_id',
                    'note',
                    [
                        'name' => 'manager_name',
                        'label' => __('Name:'),
                        'id' => 'manager_name',
                        'title' => __('Name'),
                        'required' => false,
                        'text' => ($data['manager_name']) ? $data['manager_name'] : '',
                        'data-form-part' => 'order_form',
                    ]
                );
            }

            $fieldset->addField(
                'manager_commission_earned',
                'note',
                [
                    'name' => 'manager_commission_earned',
                    'label' => __('Commission:'),
                    'id' => 'manager_commission_earned',
                    'title' => __('Commission'),
                    'required' => false,
                    'text' => $this->isCommissionSet($data, true, false),
                    'data-form-part' => 'order_form',
                ]
            );

            if ($changeManagerComm) {
                $fieldset->addField(
                    'manager_commission_status',
                    'select',
                    [
                        'name' => 'manager_commission_status',
                        'label' => __('Payment Status:'),
                        'id' => 'manager_commission_status',
                        'title' => __('Payment Status'),
                        'required' => false,
                        'values' => $this->salesrepHelper->getStatusList(),
                        'data-form-part' => 'order_form',
                    ]
                );
            } else {
                $fieldset->addField(
                    'manager_commission_status',
                    'note',
                    [
                        'name' => 'manager_commission_status',
                        'label' => __('Payment Status:'),
                        'id' => 'manager_commission_status',
                        'title' => __('Payment Status'),
                        'required' => false,
                        'values' => ($data['manager_commission_status']) ? $data['manager_commission_status'] : '',
                        'data-form-part' => 'order_form',
                    ]
                );
            }
        }

        // Coordinator
        if ($isAdmin || $changeManagerComm || $changeManager) {
            $fieldset = $form->addFieldset(
                'order_assign_coordinator',
                ['legend' => __('Coordinator')]
            );

            if ($changeManager) {
                $fieldset->addField(
                    'coordinator_id',
                    'select',
                    [
                        'name' => 'coordinator_name',
                        'label' => __('Name:'),
                        'id' => 'coordinator_name',
                        'title' => __('Name'),
                        'required' => false,
                        'values' => $this->salesrepHelper->getAdmins(),
                        'data-form-part' => 'order_form',
                    ]
                );
            } else {
                $fieldset->addField(
                    'coordinator_id',
                    'note',
                    [
                        'name' => 'coordinator_name',
                        'label' => __('Name:'),
                        'id' => 'coordinator_name',
                        'title' => __('Name'),
                        'required' => false,
                        'text' => ($data['coordinator_name']) ? $data['coordinator_name'] : '',
                        'data-form-part' => 'order_form',
                    ]
                );
            }

            $fieldset->addField(
                'coordinator_commission_earned',
                'note',
                [
                    'name' => 'coordinator_commission_earned',
                    'label' => __('Commission:'),
                    'id' => 'coordinator_commission_earned',
                    'title' => __('Commission'),
                    'required' => false,
                    'text' => $this->isCommissionSet($data, false, true),
                    'data-form-part' => 'order_form',
                ]
            );

            if ($changeManagerComm) {
                $fieldset->addField(
                    'coordinator_commission_status',
                    'select',
                    [
                        'name' => 'coordinator_commission_status',
                        'label' => __('Payment Status:'),
                        'id' => 'coordinator_commission_status',
                        'title' => __('Payment Status'),
                        'required' => false,
                        'values' => $this->salesrepHelper->getStatusList(),
                        'data-form-part' => 'order_form',
                    ]
                );
            } else {
                $fieldset->addField(
                    'coordinator_commission_status',
                    'note',
                    [
                        'name' => 'coordinator_commission_status',
                        'label' => __('Payment Status:'),
                        'id' => 'coordinator_commission_status',
                        'title' => __('Payment Status'),
                        'required' => false,
                        'values' => ($data['coordinator_commission_status']) ? $data['coordinator_commission_status'] : '',
                        'data-form-part' => 'order_form',
                    ]
                );
            }
        }

        $form->setValues($data);

        return parent::_prepareForm();
    }

    /**
     * @param $data
     * @param $isManager
     * @param $isCoordinator
     * @return float|string
     */
    public function isCommissionSet($data, $isManager, $isCoordinator)
    {
        $commission = $this->pricingHelper->currency(0, true, false);

        if ($isManager) {
            if (isset($data['manager_commission_earned'])
                && $data['manager_commission_earned'] != ''
            ) {
                $commission = $this->pricingHelper
                    ->currency($data['manager_commission_earned'], true, false);
            }
        } elseif ($isCoordinator) {
            if (isset($data['coordinator_commission_earned'])
                && $data['coordinator_commission_earned'] != ''
            ) {
                $commission = $this->pricingHelper
                    ->currency($data['coordinator_commission_earned'], true, false);
            }
        } else {
            if (isset($data['rep_commission_earned'])
                && $data['rep_commission_earned'] != ''
            ) {
                $commission = $this->pricingHelper
                    ->currency($data['rep_commission_earned'], true, false);
            }
        }

        return $commission;
    }
}
