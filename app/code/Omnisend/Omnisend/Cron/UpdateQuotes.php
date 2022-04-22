<?php

namespace Omnisend\Omnisend\Cron;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Helper\SearchCriteria\EntityInterface as EntitySearchCriteriaInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\AttributeUpdaterInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Quote as QuoteDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;

class UpdateQuotes
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var GeneralConfig
     */
    private $generalConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EntitySearchCriteriaInterface
     */
    private $quoteSearchCriteria;

    /**
     * @var ResponseRateManagerInterface
     */
    private $responseRateManager;

    /**
     * @var QuoteDataSender
     */
    private $quoteDataSender;

    /**
     * @var AttributeUpdaterInterface
     */
    private $quoteAttributeUpdater;

    /**
     * @var ImportStatus
     */
    private $importStatus;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param GeneralConfig $generalConfig
     * @param StoreManagerInterface $storeManager
     * @param EntitySearchCriteriaInterface $quoteSearchCriteria
     * @param ResponseRateManagerInterface $responseRateManager
     * @param QuoteDataSender $quoteDataSender
     * @param AttributeUpdaterInterface $quoteAttributeUpdater
     * @param ImportStatus $importStatus
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        GeneralConfig $generalConfig,
        StoreManagerInterface $storeManager,
        EntitySearchCriteriaInterface $quoteSearchCriteria,
        ResponseRateManagerInterface $responseRateManager,
        QuoteDataSender $quoteDataSender,
        AttributeUpdaterInterface $quoteAttributeUpdater,
        ImportStatus $importStatus
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->generalConfig = $generalConfig;
        $this->storeManager = $storeManager;
        $this->quoteSearchCriteria = $quoteSearchCriteria;
        $this->responseRateManager = $responseRateManager;
        $this->quoteDataSender = $quoteDataSender;
        $this->quoteAttributeUpdater = $quoteAttributeUpdater;
        $this->importStatus = $importStatus;
    }

    public function execute()
    {
        if (!$this->generalConfig->getIsCronSynchronizationEnabled()) {
            return;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();
            $isImported = 0;

            $searchCriteria = $this->quoteSearchCriteria->getEntityInStoreByImportStatusSearchCriteria(
                $isImported,
                $storeId
            );

            $quotes = $this->quoteRepository
                ->getList($searchCriteria)
                ->getItems();

            if (!$this->sendQuotes($quotes, $storeId)) {
                return;
            }
        }
    }

    /**
     * @param CartInterface[] $quotes
     * @param $storeId
     * @return bool
     */
    public function sendQuotes($quotes, $storeId)
    {
        foreach ($quotes as $quote) {
            if (!$this->responseRateManager->check($storeId)) {
                return false;
            }

            $this->processQuote($quote);
        }

        return true;
    }

    /**
     * @param CartInterface $quote
     */
    public function processQuote(CartInterface $quote)
    {
        $quoteItemCount = $quote->getItemsCount();
        $isQuotePosted = $quote->getOmnisendPostStatus();

        if (!$quoteItemCount && !$isQuotePosted) {
            return;
        }

        $postStatusOnSuccess = 1;

        if ($quoteItemCount) {
            $response = $this->quoteDataSender->send($quote);
        } else {
            $response = $this->quoteDataSender->delete($quote->getId(), $quote->getStoreId());
            $postStatusOnSuccess = 0;
        }

        $isImported = $this->importStatus->getImportStatus($response);
        $this->quoteAttributeUpdater->setIsImported($quote->getId(), $isImported);

        if ($isQuotePosted != $postStatusOnSuccess && $isImported) {
            $this->quoteAttributeUpdater->setPostStatus($quote->getId(), $postStatusOnSuccess);
        }
    }
}
