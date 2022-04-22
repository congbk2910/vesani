<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\Subscriber as BaseSubscriber;
use Omnisend\Omnisend\Helper\SubscriberStatusHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\CustomerAttributeUpdater;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Attribute\IsImported\SubscriberAttributeUpdater;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Subscriber as SubscriberDataSender;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Psr\Log\LoggerInterface;

class GuestSubscriberUpdateObserver implements ObserverInterface
{
    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var SubscriberDataSender
     */
    protected $subscriberDataSender;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var CustomerAttributeUpdater
     */
    protected $customerAttributeUpdater;

    /**
     * @var SubscriberAttributeUpdater
     */
    protected $subscriberAttributeUpdater;

    /**
     * @var OmnisendContactEventDispatcher
     */
    protected $omnisendContactEventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param SubscriberDataSender $subscriberDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param CustomerAttributeUpdater $customerAttributeUpdater
     * @param SubscriberAttributeUpdater $subscriberAttributeUpdater
     * @param OmnisendContactEventDispatcher $omnisendContactEventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        SubscriberDataSender $subscriberDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        CustomerAttributeUpdater $customerAttributeUpdater,
        SubscriberAttributeUpdater $subscriberAttributeUpdater,
        OmnisendContactEventDispatcher $omnisendContactEventDispatcher,
        LoggerInterface $logger
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->subscriberDataSender = $subscriberDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->customerAttributeUpdater = $customerAttributeUpdater;
        $this->subscriberAttributeUpdater = $subscriberAttributeUpdater;
        $this->omnisendContactEventDispatcher = $omnisendContactEventDispatcher;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        $subscriberId = $subscriber->getSubscriberId();
        $storeId = $subscriber->getStoreId();

        if (!SubscriberStatusHelper::subscriberStatusChanged($subscriber)) {
            return;
        }

        if ($subscriber->getCustomerId()) {
            return $this->handleCustomerSubscriptionUpdate($subscriber);
        }

        if (!$this->responseRateManager->check($storeId) ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled()
        ) {
            $this->omnisendContactEventDispatcher->dispatchGuestContactIdUpdateEvent($subscriberId, $storeId);
            $this->subscriberAttributeUpdater->setIsImported($subscriberId, 0);

            return;
        }

        $response = $this->subscriberDataSender->send($subscriber);
        $isImported = $this->importStatus->getImportStatus($response);
        $this->subscriberAttributeUpdater->setIsImported($subscriberId, $isImported);
    }

    /**
     * @param BaseSubscriber $subscriber
     */
    protected function handleCustomerSubscriptionUpdate($subscriber)
    {
        if (!$subscriber instanceof BaseSubscriber) {
            return;
        }

        $customerId = $subscriber->getCustomerId();

        if (!$this->responseRateManager->check($subscriber->getStoreId()) ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled() ||
            !$subscriber->getSubscriberId()
        ) {
            $this->customerAttributeUpdater->setIsImported($customerId, 0);

            return;
        }

        $response = $this->subscriberDataSender->send($subscriber);
        $isImported = $this->importStatus->getImportStatus($response);
        $this->customerAttributeUpdater->setIsImported($customerId, $isImported);
    }
}
