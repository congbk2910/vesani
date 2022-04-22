<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omnisend\Omnisend\Api\OmnisendContactRepositoryInterface;
use Omnisend\Omnisend\Helper\SearchCriteria\OmnisendContact as OmnisendContactSearchCriteria;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;
use Omnisend\Omnisend\Model\OmnisendContactIdCookieUpdater;

class UpdateCustomerContactIdObserver implements ObserverInterface
{
    /**
     * @var OmnisendContactIdCookieUpdater
     */
    protected $omnisendContactIdCookieUpdater;

    /**
     * @var OmnisendContactSearchCriteria
     */
    protected $omnisendContactSearchCriteria;

    /**
     * @var OmnisendContactRepositoryInterface
     */
    protected $omnisendContactRepository;

    /**
     * @param OmnisendContactIdCookieUpdater $omnisendContactIdCookieUpdater
     * @param OmnisendContactSearchCriteria $omnisendContactSearchCriteria
     * @param OmnisendContactRepositoryInterface $omnisendContactRepository
     */
    public function __construct(
        OmnisendContactIdCookieUpdater $omnisendContactIdCookieUpdater,
        OmnisendContactSearchCriteria $omnisendContactSearchCriteria,
        OmnisendContactRepositoryInterface $omnisendContactRepository
    ) {
        $this->omnisendContactIdCookieUpdater = $omnisendContactIdCookieUpdater;
        $this->omnisendContactSearchCriteria = $omnisendContactSearchCriteria;
        $this->omnisendContactRepository = $omnisendContactRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customerId = $observer->getEvent()->getData(OmnisendContactEventDispatcher::CUSTOMER_ID);
        $storeId = $observer->getEvent()->getData(OmnisendContactEventDispatcher::STORE_ID);

        if (!$customerId || !$storeId) {
            return;
        }

        $searchCriteria = $this->omnisendContactSearchCriteria->getOmnisendContactInStoreByCustomerIdSearchCriteria(
            $customerId,
            $storeId
        );

        $omnisendContact = $this->omnisendContactRepository->getList($searchCriteria)->getFirstItem();
        $contactId = $omnisendContact->getOmnisendId();

        if (!$contactId) {
            return;
        }

        $this->omnisendContactIdCookieUpdater->handleCookieUpdateRequest($contactId);
    }
}
