<?php

namespace Omnisend\Omnisend\Cron;

use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Helper\SubscriberFilter;
use Omnisend\Omnisend\Helper\SubscriberStatusHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\AttributeUpdaterInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Subscriber as SubscriberDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;

class UpdateGuestSubscribers
{
    const IMPORTED_ATTRIBUTE_VALUE = 1;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SubscriberCollectionFactory
     */
    protected $subscriberCollectionFactory;

    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var SubscriberDataSender
     */
    protected $subscriberDataSender;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var SubscriberFilter
     */
    protected $subscriberFilter;

    /**
     * @var AttributeUpdaterInterface
     */
    protected $subscriberAttributeUpdater;

    /**
     * @param GeneralConfig $generalConfig
     * @param StoreManagerInterface $storeManager
     * @param SubscriberCollectionFactory $subscriberCollectionFactory
     * @param ResponseRateManagerInterface $responseRateManager
     * @param SubscriberDataSender $productDataSender
     * @param ImportStatus $importStatus
     * @param SubscriberFilter $subscriberFilter
     * @param AttributeUpdaterInterface $subscriberAttributeUpdater
     */
    public function __construct(
        GeneralConfig $generalConfig,
        StoreManagerInterface $storeManager,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        ResponseRateManagerInterface $responseRateManager,
        SubscriberDataSender $productDataSender,
        ImportStatus $importStatus,
        SubscriberFilter $subscriberFilter,
        AttributeUpdaterInterface $subscriberAttributeUpdater
    ) {
        $this->generalConfig = $generalConfig;
        $this->storeManager = $storeManager;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->responseRateManager = $responseRateManager;
        $this->subscriberDataSender = $productDataSender;
        $this->importStatus = $importStatus;
        $this->subscriberFilter = $subscriberFilter;
        $this->subscriberAttributeUpdater = $subscriberAttributeUpdater;
    }

    public function execute()
    {
        if (!$this->generalConfig->getIsCronSynchronizationEnabled()) {
            return;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();

            $collection = $this->subscriberFilter->setCollectionFilters(
                $this->subscriberCollectionFactory->create(),
                $storeId
            );

            $subscribers = $collection->getItems();

            if (!$this->sendSubscribers($subscribers, $storeId)) {
                return;
            }
        }
    }

    /**
     * @param Subscriber[] $subscribers
     * @param $storeId
     * @return bool
     */
    public function sendSubscribers($subscribers, $storeId)
    {
        foreach ($subscribers as $subscriber) {
            if (!$this->responseRateManager->check($storeId)) {
                return false;
            }

            $this->processSubscriber($subscriber);
        }

        return true;
    }

    /**
     * @param Subscriber $subscriber
     */
    public function processSubscriber(Subscriber $subscriber)
    {
        if (!SubscriberStatusHelper::subscriberStatusChanged($subscriber)) {
            $this->subscriberAttributeUpdater->setIsImported($subscriber->getId(), self::IMPORTED_ATTRIBUTE_VALUE);

            return;
        }

        $response = $this->subscriberDataSender->send($subscriber);
        $isImported = $this->importStatus->getImportStatus($response);

        if ($isImported) {
            $this->subscriberAttributeUpdater->setIsImported($subscriber->getId(), $isImported);
        }
    }
}
