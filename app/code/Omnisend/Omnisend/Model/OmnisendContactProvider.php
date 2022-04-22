<?php

namespace Omnisend\Omnisend\Model;

use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;
use Omnisend\Omnisend\Api\OmnisendContactRepositoryInterface;
use Omnisend\Omnisend\Api\OmnisendGuestSubscriberRepositoryInterface;
use Omnisend\Omnisend\Helper\SearchCriteria\OmnisendContact as OmnisendContactSearchCriteria;
use Omnisend\Omnisend\Helper\SearchCriteria\OmnisendGuestSubscriber as OmnisendGuestSubscriberSearchCriteria;

class OmnisendContactProvider implements OmnisendContactProviderInterface
{
    /**
     * @var OmnisendContactSearchCriteria
     */
    private $contactSearchCriteria;

    /**
     * @var OmnisendContactRepositoryInterface
     */
    private $contactRepository;

    /**
     * @var OmnisendGuestSubscriberSearchCriteria
     */
    private $guestSubscriberSearchCriteria;

    /**
     * @var OmnisendGuestSubscriberRepositoryInterface
     */
    private $guestSubscriberRepository;

    /**
     * @param OmnisendContactSearchCriteria $contactSearchCriteria
     * @param OmnisendContactRepositoryInterface $contactRepository
     * @param OmnisendGuestSubscriberSearchCriteria $guestSubscriberSearchCriteria
     * @param OmnisendGuestSubscriberRepositoryInterface $guestSubscriberRepository
     */
    public function __construct(
        OmnisendContactSearchCriteria $contactSearchCriteria,
        OmnisendContactRepositoryInterface $contactRepository,
        OmnisendGuestSubscriberSearchCriteria $guestSubscriberSearchCriteria,
        OmnisendGuestSubscriberRepositoryInterface $guestSubscriberRepository
    ) {
        $this->contactSearchCriteria = $contactSearchCriteria;
        $this->contactRepository = $contactRepository;
        $this->guestSubscriberSearchCriteria = $guestSubscriberSearchCriteria;
        $this->guestSubscriberRepository = $guestSubscriberRepository;
    }

    /**
     * @param int $customerId
     * @return OmnisendContactInterface[]
     */
    public function getOmnisendContactsByCustomerId($customerId)
    {
        if (!$customerId) {
            return [];
        }

        $searchCriteria = $this->contactSearchCriteria->getOmnisendContactByCustomerIdSearchCriteria($customerId);

        return $this->contactRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param int $subscriberId
     * @return OmnisendGuestSubscriberInterface[]
     */
    public function getOmnisendGuestSubscribersBySubscriberId($subscriberId)
    {
        if (!$subscriberId) {
            return [];
        }

        $searchCriteria = $this->guestSubscriberSearchCriteria->getOmnisendSubscriberBySubscriberIdSearchCriteria(
            $subscriberId
        );

        return $this->guestSubscriberRepository->getList($searchCriteria)->getItems();
    }
}
