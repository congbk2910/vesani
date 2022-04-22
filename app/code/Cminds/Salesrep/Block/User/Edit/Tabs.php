<?php
namespace Cminds\Salesrep\Block\User\Edit;

class Tabs extends \Magento\User\Block\User\Edit\Tabs
{

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }


    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $isModuleEnabled = $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/module_status/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $canEditSalesRepCommission = $this->_authSession->isAllowed(
            'Cminds_Salesrep::edit_sales_rep_commission_rates'
        );
        $canEditManagerCommission = $this->_authSession->isAllowed(
            'Cminds_Salesrep::edit_manager_commission_rates'
        );
        $canEditAssignedManager = $this->_authSession->isAllowed(
            'Cminds_Salesrep::edit_assigned_manager'
        );
        $isAdmin = $this->_authSession->isAllowed(
            'Magento_Backend::all'
        );

        if ($isModuleEnabled) {
            if ($isAdmin
                || $canEditSalesRepCommission
                || $canEditManagerCommission
                || $canEditAssignedManager
            ) {
                $this->addTabAfter(
                    'commision',
                    [
                        'label' => __('Commission'),
                        'title' => __('Commission'),
                        'content' => $this->getLayout()
                            ->createBlock(
                                'Cminds\Salesrep\Block\User\Edit\Tab\Commission'
                            )->toHtml(),
                        'active' => true
                    ],
                    'roles_section'
                );
            }
        }

        return parent::_beforeToHtml();
    }
}
