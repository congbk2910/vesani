<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Framework\Json\Helper\Data;
use Magento\Newsletter\Model\Subscriber as NewsletterSubscriber;
use Omnisend\Omnisend\Model\SubscriptionStatusManagerInterface;
use Omnisend\Omnisend\Observer\CustomerUpdateObserver;

class Subscriber extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    /**
     * @var SubscriptionStatusManagerInterface
     */
    private $subscriptionStatusManager;

    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @param SubscriptionStatusManagerInterface $subscriptionStatusManager
     * @param Data $jsonHelper
     */
    public function __construct(
        SubscriptionStatusManagerInterface $subscriptionStatusManager,
        Data $jsonHelper
    ) {
        $this->subscriptionStatusManager = $subscriptionStatusManager;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @param NewsletterSubscriber $subscriber
     * @return string
     */
    public function build($subscriber)
    {
        $subscriptionData = $this->subscriptionStatusManager->handleGuestSubscriptionStatus($subscriber);

        if ($subscriber->getData(CustomerUpdateObserver::ARRAY_INDEX_CHANGE_EMAIL)) {
            $subscriptionData[SubscriptionStatusManagerInterface::SUBSCRIPTION_STATUS] = SubscriptionStatusManagerInterface::STATUS_UNSUBSCRIBED;
        }

        $this->addData(Contact::EMAIL, $subscriber->getSubscriberEmail());
        $this->addData(Contact::STATUS, $subscriptionData[SubscriptionStatusManagerInterface::SUBSCRIPTION_STATUS]);
        $this->addData(Contact::STATUS_DATE, $subscriptionData[SubscriptionStatusManagerInterface::STATUS_DATE]);
        $this->addData(Contact::SEND_WELCOME_EMAIL, true);

        return $this->jsonHelper->jsonEncode($this->getData());
    }
}
