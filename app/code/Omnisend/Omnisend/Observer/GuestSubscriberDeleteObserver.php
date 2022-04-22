<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\Subscriber;
use Omnisend\Omnisend\Model\EntityDataSender\Subscriber as SubscriberEntityDataSender;
use Omnisend\Omnisend\Model\OmnisendContactProviderInterface;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Psr\Log\LoggerInterface;

class GuestSubscriberDeleteObserver implements ObserverInterface
{
    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var OmnisendContactProviderInterface
     */
    protected $omnisendContactProvider;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SubscriberEntityDataSender
     */
    protected $subscriberEntityDataSender;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param OmnisendContactProviderInterface $omnisendContactProvider
     * @param LoggerInterface $logger
     * @param SubscriberEntityDataSender $subscriberEntityDataSender
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        OmnisendContactProviderInterface $omnisendContactProvider,
        LoggerInterface $logger,
        SubscriberEntityDataSender $subscriberEntityDataSender
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->omnisendContactProvider = $omnisendContactProvider;
        $this->logger = $logger;
        $this->subscriberEntityDataSender = $subscriberEntityDataSender;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();

        if (!$subscriber instanceof Subscriber) {
            return;
        }

        $subscriberId = $subscriber->getId();
        $guestSubscribers = $this->omnisendContactProvider->getOmnisendGuestSubscribersBySubscriberId($subscriberId);

        if (!count($guestSubscribers)) {
            return;
        }

        if (!$this->responseRateManager->check($subscriber->getStoreId())) {
            $this->logger->critical(
                'Request limit reached during guest unsubscription from Omnisend, for subscriber: ' . $subscriberId
            );

            return;
        }

        $this->subscriberEntityDataSender->unsubscribe($subscriber, $guestSubscribers);
    }
}
