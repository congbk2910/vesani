<?php
namespace Cminds\Salesrep\Block\Adminhtml\Order\View\Tab;

class Commissions extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getTabLabel()
    {
        return __('Commissions');
    }

    public function getTabTitle()
    {
        return __('Commissions');
    }

    public function canShowTab()
    {
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

        if ($isAdmin) {
            return true;
        }

        if (!$changeRep
            && !$changeRepComm
            && !$changeManager
            && !$changeManagerComm
        ) {
            return false;
        }

        return true;
    }

    public function isHidden()
    {
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

        if ($isAdmin) {
            return false;
        }

        if (!$changeRep
            && !$changeRepComm
            && !$changeManager
            && !$changeManagerComm
        ) {
            return true;
        }

        return false;
    }

    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/*/*', ['order_id' => $orderId]);
    }
}
