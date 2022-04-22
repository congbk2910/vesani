<?php

namespace Omnisend\Omnisend\Model;

use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;

interface OmnisendContactProviderInterface
{
    /**
     * @param int $customerId
     * @return OmnisendContactInterface[]
     */
    public function getOmnisendContactsByCustomerId($customerId);

    /**
     * @param int $subscriberId
     * @return OmnisendGuestSubscriberInterface[]
     */
    public function getOmnisendGuestSubscribersBySubscriberId($subscriberId);
}
