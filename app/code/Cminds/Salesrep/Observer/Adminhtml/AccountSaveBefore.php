<?php
namespace Cminds\Salesrep\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class AccountSaveBefore implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getRequest();
        $params = $request->getPostValue();

        if (isset($params['salesrep_rep_commission_rate'])) {
            if ($params['salesrep_rep_commission_rate'] === '') {
                $request->setPostValue("salesrep_rep_commission_rate", null);
            }

            if ($params['salesrep_manager_commission_rate'] === '') {
                $request->setPostValue("salesrep_manager_commission_rate", null);
            }

            if ($params['salesrep_manager_id'] === '') {
                $request->setPostValue("salesrep_manager_id", null);
            }
        }

    }
}
