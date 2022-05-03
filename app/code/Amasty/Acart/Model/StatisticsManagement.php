<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model;

use Amasty\Acart\Model\ResourceModel\History\CollectionFactory as HistoryCollectionFactory;
use Amasty\Acart\Model\ResourceModel\RuleQuote\Collection as RuleQuoteCollection;
use Amasty\Acart\Model\ResourceModel\RuleQuote\CollectionFactory as RuleQuoteCollectionFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;

class StatisticsManagement
{
    /**#@+*/
    public const SUM_GRAND_TOTAL = 'SUM(base_grand_total)';
    public const COUNT_OF_ORDERS = 'count(order.entity_id)';
    public const PERCENT = 100;
    /**#@-*/

    /**
     * @var RuleQuoteCollectionFactory
     */
    private $ruleQuoteCollectionFactory;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var HistoryCollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var int
     */
    private $restoredQuoteQty;

    /**
     * @var int
     */
    private $orderPlacedQty;

    public function __construct(
        RuleQuoteCollectionFactory $ruleQuoteCollection,
        QuoteCollectionFactory $quoteCollection,
        HistoryCollectionFactory $historyCollectionFactory
    ) {
        $this->ruleQuoteCollectionFactory = $ruleQuoteCollection;
        $this->quoteCollectionFactory = $quoteCollection;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    private function createRuleQuoteCollection(): RuleQuoteCollection
    {
        return $this->ruleQuoteCollectionFactory->create();
    }

    public function getAbandonmentRate(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): float {
        $result = 0;

        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $quoteCollection */
        $quoteCollection = $this->quoteCollectionFactory->create();

        if ($dateTo && $dateFrom) {
            $quoteCollection->addFieldToFilter(Quote::KEY_CREATED_AT, ['lteq' => $dateTo])
                ->addFieldToFilter(Quote::KEY_CREATED_AT, ['gteq' => $dateFrom]);
        }

        $quoteCollection->addFieldToFilter(Quote::KEY_STORE_ID, ['in' => $storeIds])
            ->addFieldToFilter(Quote::KEY_IS_ACTIVE, 0);

        $ordersQty = $quoteCollection->getSize();

        /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->createRuleQuoteCollection();
        $abandonedQuoteQty = $ruleQuoteCollection
            ->addFilterByAbandonedStatus(\Amasty\Acart\Model\RuleQuote::ABANDONED_NOT_RESTORED_STATUS)
            ->addFilterByStoreIds($storeIds)
            ->addFilterByDate($dateTo, $dateFrom)
            ->addFilterByCampaignIds($campaignIds)
            ->getSize();

        $totalQty = $abandonedQuoteQty + $ordersQty;

        if ($totalQty) {
            $result = $abandonedQuoteQty / $totalQty * self::PERCENT;
            $result = round($result);
        }

        return (float)$result;
    }

    public function getTotalSend(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): int {
        /** @var \Amasty\Acart\Model\ResourceModel\History\Collection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();

        $sentEmails = $historyCollection
            ->addRuleQuoteData()
            ->addFilterByStoreIds($storeIds)
            ->addFilterByDate($dateTo, $dateFrom)
            ->addFilterByStatus(History::STATUS_SENT)
            ->addFilterByCampaignIds($campaignIds)
            ->getSize();

        return $sentEmails;
    }

    public function getTotalRestoredCarts(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): int {
        if (!$this->restoredQuoteQty) {
            /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
            $ruleQuoteCollection = $this->createRuleQuoteCollection();

            $this->restoredQuoteQty = $ruleQuoteCollection
                ->addFilterByAbandonedStatus(\Amasty\Acart\Model\RuleQuote::ABANDONED_RESTORED_STATUS)
                ->addFilterByStoreIds($storeIds)
                ->addFilterByDate($dateTo, $dateFrom)
                ->addFilterByCampaignIds($campaignIds)
                ->getSize();
        }

        return $this->restoredQuoteQty;
    }

    public function getTotalAbandonedMoney(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): float {
        /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->createRuleQuoteCollection();

        $result = $ruleQuoteCollection->getTotalAbandonedMoney($storeIds, $dateTo, $dateFrom, $campaignIds);

        return round($result);
    }

    public function getAbandonmentRevenue(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): float {
        /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->createRuleQuoteCollection();

        $result = $ruleQuoteCollection->getRestoredOrdersValue(
            $storeIds,
            $dateTo,
            $dateFrom,
            self::SUM_GRAND_TOTAL,
            $campaignIds
        );

        return round($result);
    }

    public function getOrdersViaRestoredCarts(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): int {
        /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->createRuleQuoteCollection();
        if (!$this->orderPlacedQty) {
            $this->orderPlacedQty = (int)$ruleQuoteCollection->getRestoredOrdersValue(
                $storeIds,
                $dateTo,
                $dateFrom,
                self::COUNT_OF_ORDERS,
                $campaignIds
            );
        }

        return $this->orderPlacedQty;
    }

    public function getRecoveredCartsRate(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): float {
        $result = 0;

        $totalRestoredCarts = $this->getOrdersViaRestoredCarts($storeIds, $dateTo, $dateFrom, $campaignIds);
        /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->createRuleQuoteCollection();
        $totalAbandonedCarts = $ruleQuoteCollection
            ->addFilterByStoreIds($storeIds)
            ->addFilterByDate($dateTo, $dateFrom)
            ->addFilterByCampaignIds($campaignIds)
            ->getSize();

        if ($totalAbandonedCarts) {
            $result = round($totalRestoredCarts / $totalAbandonedCarts * self::PERCENT);
        }

        return $result;
    }

    public function getTop5ForgetProducts(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): array {
        /** @var \Amasty\Acart\Model\ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->createRuleQuoteCollection();
        $products = $ruleQuoteCollection
            ->addFilterByTop5ForgetProducts()
            ->addFilterByStoreIds($storeIds)
            ->addFilterByDate($dateTo, $dateFrom)
            ->addFilterByCampaignIds($campaignIds)
            ->getData();

        return $products;
    }

    public function getEmailStatistics(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): array {
        $columnNames = [
            'sent',
            'opened',
            'efficiency',
            'open_rate',
            'click_rate'
        ];

        /** @var \Amasty\Acart\Model\ResourceModel\History\Collection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();

        $ordersPlaced = $this->getOrdersViaRestoredCarts($storeIds, $dateTo, $dateFrom, $campaignIds);
        $emailStatistics = $historyCollection
            ->getEmailStatistics(false, $storeIds, $dateTo, $dateFrom, $campaignIds)[0];

        /** @var \Amasty\Acart\Model\ResourceModel\History\Collection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();
        $clickStatistics = $historyCollection
            ->getClickStatistics(false, $storeIds, $dateTo, $dateFrom, $campaignIds)[0];

        $statistics = array_merge($emailStatistics, $clickStatistics);
        foreach ($columnNames as $columnName) {
            if (!isset($statistics[$columnName])) {
                $statistics[$columnName] = 0;
            }
        }
        if ($statistics['sent'] > 0) {
            $statistics['open_rate'] = round($statistics['opened'] / $statistics['sent'] * self::PERCENT);
            $statistics['click_rate'] = round($statistics['clicked'] / $statistics['sent'] * self::PERCENT);
            $statistics['efficiency'] = round($ordersPlaced / $statistics['sent'] * self::PERCENT);
        }

        return $statistics;
    }

    public function getCampaignStatistics(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): array {
        $columnNames = [
            'rule_id',
            'name',
            'is_active',
            'sent',
            'opened',
            'open_rate',
            'clicked',
            'click_rate',
            'converted',
            'revenue',
            'placed_order_count'
        ];

        /** @var \Amasty\Acart\Model\ResourceModel\History\Collection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();
        $emailStatistics = $historyCollection
            ->getEmailStatistics(true, $storeIds, $dateTo, $dateFrom, $campaignIds);

        /** @var \Amasty\Acart\Model\ResourceModel\History\Collection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();
        $orderDetails = $historyCollection
            ->getOrderDetails(true, $storeIds, $dateTo, $dateFrom, $campaignIds);

        /** @var \Amasty\Acart\Model\ResourceModel\History\Collection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();
        $clickStatistics = $historyCollection
            ->getClickStatistics(true, $storeIds, $dateTo, $dateFrom, $campaignIds);

        $statistics = array_replace_recursive(
            array_column($emailStatistics, null, 'rule_id'),
            array_column($orderDetails, null, 'rule_id'),
            array_column($clickStatistics, null, 'rule_id')
        );

        foreach ($statistics as &$row) {
            foreach ($columnNames as $columnName) {
                if (!isset($row[$columnName])) {
                    $row[$columnName] = 0;
                }
            }
            if ($row['sent'] > 0) {
                $row['click_rate'] = $row['clicked'] / $row['sent'] * self::PERCENT;
                $row['converted'] = $row['placed_order_count'] / $row['sent'] * self::PERCENT;
                $row['open_rate'] =  $row['opened'] / $row['sent'] * self::PERCENT;
            }
        }

        return array_values($statistics);
    }
}
