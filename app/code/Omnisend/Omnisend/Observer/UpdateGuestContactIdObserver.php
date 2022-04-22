<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omnisend\Omnisend\Api\OmnisendGuestSubscriberRepositoryInterface;
use Omnisend\Omnisend\Helper\SearchCriteria\OmnisendGuestSubscriber as OmnisendGuestSubscriberSearchCriteria;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;
use Omnisend\Omnisend\Model\OmnisendContactIdCookieUpdater;

class UpdateGuestContactIdObserver implements ObserverInterface
{
    /**
     * @var OmnisendContactIdCookieUpdater
     */
    protected $omnisendContactIdCookieUpdater;

    /**
     * @var OmnisendGuestSubscriberSearchCriteria
     */
    protected $omnisendGuestSubscriberSearchCriteria;

    /**
     * @var OmnisendGuestSubscriberRepositoryInterface
     */
    protected $omnisendGuestSubscriberRepository;

    /**
     * @param OmnisendContactIdCookieUpdater $omnisendContactIdCookieUpdater
     * @param OmnisendGuestSubscriberSearchCriteria $omnisendGuestSubscriberSearchCriteria
     * @param OmnisendGuestSubscriberRepositoryInterface $omnisendGuestSubscriberRepository
     */
    public function __construct(
        OmnisendContactIdCookieUpdater $omnisendContactIdCookieUpdater,
        OmnisendGuestSubscriberSearchCriteria $omnisendGuestSubscriberSearchCriteria,
        OmnisendGuestSubscriberRepositoryInterface $omnisendGuestSubscriberRepository
    ) {
        $this->omnisendContactIdCookieUpdater = $omnisendContactIdCookieUpdater;
        $this->omnisendGuestSubscriberSearchCriteria = $omnisendGuestSubscriberSearchCriteria;
        $this->omnisendGuestSubscriberRepository = $omnisendGuestSubscriberRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $subscriberId = $observer->getEvent()->getData(OmnisendContactEventDispatcher::SUBSCRIBER_ID);
        $storeId = $observer->getEvent()->getData(OmnisendContactEventDispatcher::STORE_ID);

        if (!$subscriberId || !$storeId) {
            return;
        }

        $searchCriteria = $this->omnisendGuestSubscriberSearchCriteria->getOmnisendSubscriberInStoreBySubscriberIdSearchCriteria(
            $subscriberId,
            $storeId
        );

        $omnisendContact = $this->omnisendGuestSubscriberRepository->getList($searchCriteria)->getFirstItem();
        $contactId = $omnisendContact->getOmnisendId();

        if (!$contactId) {
            return;
        }

        $this->omnisendContactIdCookieUpdater->handleCookieUpdateRequest($contactId);
    }
}
