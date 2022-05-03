<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Cleaner
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ResourceModel\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var ResourceModel\RuleQuote\CollectionFactory
     */
    private $ruleQuoteCollectionFactory;

    /**
     * @var string
     */
    private $expiredDate;

    public function __construct(
        \Amasty\Acart\Model\ConfigProvider $configProvider,
        \Amasty\Acart\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Amasty\Acart\Model\Date $date,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Acart\Model\ResourceModel\RuleQuote\CollectionFactory $ruleQuoteCollectionFactory
    ) {
        $this->configProvider = $configProvider;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->date = $date;
        $this->logger = $logger;
        $this->ruleQuoteCollectionFactory = $ruleQuoteCollectionFactory;
    }

    /**
     * @return $this
     */
    public function clearExpiredHistory()
    {
        $historyCollection = $this->getHistoryCollection()->addExpiredFilter($this->getExpiredFormattedDate());
        $this->clearCollection($historyCollection);

        return $this;
    }

    /**
     * @return $this
     */
    public function clearExpiredRuleQuotes()
    {
        $collection = $this->getRuleQuoteCollection()
            ->addFieldToFilter(RuleQuote::CREATED_AT, ['lt' => $this->getExpiredFormattedDate()])
            ->addFieldToFilter(RuleQuote::STATUS, RuleQuote::STATUS_COMPLETE);
        $this->clearCollection($collection);

        return $this;
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return $this
     */
    private function clearCollection(AbstractCollection $collection)
    {
        try {
            if ($collection->getSize()) {
                $collection->walk('delete');
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $this;
    }

    /**
     * @return bool|null|string
     */
    private function getExpiredFormattedDate()
    {
        if (!$this->expiredDate) {
            $historyCleanDays = $this->configProvider->getHistoryAutoCleanDays();
            $this->expiredDate = $historyCleanDays ? $this->date->getFormattedDate(
                $this->date->getCurrentTimestamp() - $this->date->convertDaysInSeconds($historyCleanDays)
            ) : false;
        }

        return $this->expiredDate;
    }

    /**
     * @return ResourceModel\History\Collection
     */
    private function getHistoryCollection()
    {
        return $this->historyCollectionFactory->create();
    }

    /**
     * @return ResourceModel\RuleQuote\Collection
     */
    private function getRuleQuoteCollection()
    {
        return $this->ruleQuoteCollectionFactory->create();
    }
}
