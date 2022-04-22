<?php

namespace Omnisend\Omnisend\Model;

use Magento\Newsletter\Model\Subscriber;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;

interface UnsubscriptionServiceInterface
{
    /**
     * @param Subscriber $subscription
     * @param OmnisendContactInterface[] $contacts
     * @param OmnisendGuestSubscriberInterface[] $guestSubscribers
     * @return bool
     */
    public function unsubscribeFromAllStores($subscription, $contacts, $guestSubscribers);
}
