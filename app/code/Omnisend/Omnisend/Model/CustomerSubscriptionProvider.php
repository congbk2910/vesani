<?php

namespace Omnisend\Omnisend\Model;

use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Omnisend\Omnisend\Model\ResourceModel\Subscriber as SubscriberResource;

class CustomerSubscriptionProvider implements SubscriptionProviderInterface
{
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var SubscriberResource
     */
    protected $subscriberResource;

    /**
     * @param SubscriberFactory $subscriberFactory
     * @param SubscriberResource $subscriberResource
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        SubscriberResource $subscriberResource
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResource = $subscriberResource;
    }

    /**
     * @param int $customerId
     * @param string $customerEmail
     * @param int $storeId
     * @return Subscriber
     */
    public function getSubscription($customerId, $customerEmail, $storeId)
    {
        $subscriber = $this->subscriberFactory->create();
        $subscriberData = $this->subscriberResource->getSubscriberData($customerId, $customerEmail, $storeId);
        $subscriber->setData($subscriberData);

        return $subscriber;
    }
}
