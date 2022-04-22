<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;

class CustomerLoginObserver implements ObserverInterface
{
    /**
     * @var OmnisendContactEventDispatcher
     */
    protected $omnisendContactEventDispatcher;

    /**
     * @param OmnisendContactEventDispatcher $omnisendContactEventDispatcher
     */
    public function __construct(OmnisendContactEventDispatcher $omnisendContactEventDispatcher)
    {
        $this->omnisendContactEventDispatcher = $omnisendContactEventDispatcher;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        if (!$customer instanceof Customer) {
            return;
        }

        $customerId = $customer->getId();
        $storeId = $customer->getStoreId();

        $this->omnisendContactEventDispatcher->dispatchCustomerContactIdUpdateEvent($customerId, $storeId);
    }
}
