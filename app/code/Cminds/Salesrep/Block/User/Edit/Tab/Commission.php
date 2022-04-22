<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Block\User\Edit\Tab;

/**
 * Class Commission
 * @package Cminds\Salesrep\Block\User\Edit\Tab
 */
class Commission extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    private $adminUsers;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;

    /**
     * @var \Cminds\Salesrep\Model\Source\CalculationType
     */
    private $calculationTypeSource;

    /**
     * Commission constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\User\Model\ResourceModel\User\Collection $adminUsers
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Cminds\Salesrep\Model\Source\CalculationType $calculationTypeSource
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cminds\Salesrep\Model\Source\CalculationType $calculationTypeSource
    ) {
        $this->adminUsers = $adminUsers;
        $this->authSession = $authSession;
        parent::__construct($context, $registry, $formFactory);
        $this->calculationTypeSource = $calculationTypeSource;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $canEditSalesRepCommission = $this->authSession->isAllowed(
            'Cminds_Salesrep::edit_sales_rep_commission_rates'
        );
        $canEditManagerCommission = $this->authSession->isAllowed(
            'Cminds_Salesrep::edit_manager_commission_rates'
        );

        $model = $this->_coreRegistry->registry('permissions_user');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        if ($canEditManagerCommission || $canEditSalesRepCommission) {
            $commissionRatesFieldset = $form->addFieldset(
                'commission_rates',
                ['legend' => __('Commission Rates')]
            );

            if ($canEditSalesRepCommission) {
                $commissionRatesFieldset->addField(
                    'salesrep_rep_commission_calculation_type',
                    'select',
                    [
                        'name' => 'salesrep_rep_commission_calculation_type',
                        'label' => __('Rep Commission Calculation Type'),
                        'id' => 'salesrep_rep_commission_calculation_type',
                        'title' => __('Rep Commission Calculation Type'),
                        'values' => $this->getCommissionCalculationType(),
                        'required' => false,
                        'note' => __('Specify salesrep commission calculation type. "Subtotal" will calculate commission based on order subtotal. "Margin" - will calculate commission based on product cost.')
                    ]
                );

                $commissionRatesFieldset->addField(
                    'salesrep_rep_commission_rate',
                    'text',
                    [
                        'name' => 'salesrep_rep_commission_rate',
                        'label' => __('Commission Rate as Sales Representative'),
                        'id' => 'salesrep_rep_commission_rate',
                        'title' => __('Commission Rate as Sales Representative'),
                        'required' => false,
                        'note' => __('Specify the commission rate that this user should earn on orders for which they are the *primary* sales representative. If left blank, the default commission rate will be used (specified under System -> Config -> Sales Representative).')
                    ]
                );

                $commissionRatesFieldset->addField(
                    'salesrep_manager_commission_rate',
                    'text',
                    [
                        'name' => 'salesrep_manager_commission_rate',
                        'label' => __('Commission Rate as Manager	'),
                        'id' => 'salesrep_manager_commission_rate',
                        'title' => __('Commission Rate as Manager'),
                        'required' => false,
                        'note' => __('Specify the commission rate that this user should earn on orders submitted by any sales representatives they manage. If this user does not manage any other users, values will be ignored.')
                    ]
                );

                $commissionRatesFieldset->addField(
                    'salesrep_coordinator_commission_rate',
                    'text',
                    [
                        'name' => 'salesrep_coordinator_commission_rate',
                        'label' => __('Commission Rate as Coordinator'),
                        'id' => 'salesrep_coordinator_commission_rate',
                        'title' => __('Commission Rate as Coordinator'),
                        'required' => false,
                        'note' => __('Specify the commission rate that this coordinator should earn on orders submitted by any sales representatives with their managers they manage. If this coordinator does not coordinate any other managers, values will be ignored.')
                    ]
                );
            }
        }

        if ($canEditManagerCommission) {
            $managersFieldset = $form->addFieldset(
                'sales_teams_and_managers',
                ['legend' => __('Sales Teams (Managers and Coordinators)')]
            );

            $managersFieldset->addField(
                'salesrep_manager_id',
                'select',
                [
                    'name' => 'salesrep_manager_id',
                    'label' => __('Manager'),
                    'id' => 'salesrep_manager_id',
                    'title' => __('Manager'),
                    'values' => $this->getAdmins(1),
                    'required' => false,
                    'note' => __('Specify a manager who should be credited for this user\'s orders. If this user does not have a manager or is himself a top-level manager, you should select "No Manager".')
                ]
            );

            $dependField = $managersFieldset->addField(
                'salesrep_coordinator_id',
                'select',
                [
                    'name' => 'salesrep_coordinator_id',
                    'label' => __('Coordinator'),
                    'id' => 'salesrep_coordinator_id',
                    'title' => __('Coordinator'),
                    'values' => $this->getAdmins(0),
                    'required' => false,
                    'note' => __('Specify a coordinator who should be credited for this user\'s orders. If this user does not have a manager (No Manager) you should select "No Coordinator".')
                ]
            );
        }

        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param int $level
     * @return array
     */
    public function getAdmins($level = 1)
    {
        $current_user = $this->_coreRegistry->registry('permissions_user');
        $collection = $this->adminUsers->setOrder('firstname', 'asc')->load();

        $result = [];
        if ($level == 0) {
            $result[] = ['value' => "", 'label' => __("No Coordinator")];
        }
        if ($level == 1) {
            $result[] = ['value' => "", 'label' => __("No Manager")];
        }

        foreach ($collection as $admin) {
            if ($current_user->getId() != $admin->getId()) {
                $result[] = [
                    'value' => $admin->getId(),
                    'label' => $admin->getFirstname() . ' ' . $admin->getLastname() . ' (' . $admin->getUsername() . ')'
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCommissionCalculationType()
    {
        return $this->calculationTypeSource->toOptionArray();
    }
}
