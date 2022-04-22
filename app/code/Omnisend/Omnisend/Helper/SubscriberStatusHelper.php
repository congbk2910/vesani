<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Newsletter\Model\Subscriber;
use Omnisend\Omnisend\Model\ResourceModel\Subscriber as SubscriberResource;

class SubscriberStatusHelper
{
    /**
     * @param Subscriber $subscriber
     * @return bool
     */
    public static function subscriberStatusChanged($subscriber)
    {
        if (!$subscriber instanceof Subscriber) {
            return false;
        }

        $subscriptionStatus = $subscriber->getStatus();
        $previousSubscriptionStatus = $subscriber->getData(SubscriberResource::OMNISEND_PREVIOUS_SUBSCRIBER_STATUS);

        return $subscriptionStatus != $previousSubscriptionStatus;
    }
}
