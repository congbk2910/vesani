<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\ResourceModel\History;

use Amasty\Acart\Model\History as HistoryModel;
use Amasty\Acart\Model\History\ProductDetails\Detail as DetailModel;
use Amasty\Acart\Model\History\ProductDetails\ResourceModel\Detail;
use Amasty\Acart\Model\ResourceModel\History as HistoryResource;
use Amasty\Acart\Model\Rule;
use Amasty\Acart\Model\ResourceModel\Rule as RuleResource;
use Amasty\Acart\Model\RuleQuote as RuleQuoteModel;
use Amasty\Acart\Model\ResourceModel\RuleQuote as RuleQuoteResource;
use Amasty\Acart\Model\ResourceModel\Schedule as ScheduleResource;
use Amasty\Acart\Model\Schedule;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(HistoryModel::class, HistoryResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function addTimeFilter(string $currentExecution, string $lastExecution): Collection
    {
        $this->addFieldToFilter(
            'main_table.' . HistoryModel::SCHEDULED_AT,
            [
                'gteq' => $lastExecution
            ]
        )->addFieldToFilter(
            'main_table.' . HistoryModel::SCHEDULED_AT,
            [
                'lt' => $currentExecution
            ]
        )->getSelect()
            ->where(
                'main_table.' . HistoryModel::STATUS . ' = ?',
                HistoryModel::STATUS_PROCESSING
            );

        return $this;
    }

    public function addExpiredFilter(string $expiredDate): Collection
    {
        $this->addFieldToFilter(
            'main_table.' . HistoryModel::FINISHED_AT,
            [
                'lt' => $expiredDate
            ]
        )->addFieldToFilter(
            'main_table.' . HistoryModel::STATUS,
            [
                'eq' => HistoryModel::STATUS_SENT
            ]
        );

        return $this;
    }

    public function addRuleQuoteData(): Collection
    {
        $this->getSelect()
            ->joinLeft(
                ['ruleQuote' => $this->getTable(RuleQuoteResource::MAIN_TABLE)],
                'main_table.' . HistoryModel::RULE_QUOTE_ID . '= ruleQuote.' . RuleQuoteModel::RULE_QUOTE_ID,
                [
                    RuleQuoteModel::STORE_ID,
                    RuleQuoteModel::CUSTOMER_ID,
                    RuleQuoteModel::CUSTOMER_EMAIL,
                    RuleQuoteModel::CUSTOMER_FIRSTNAME,
                    RuleQuoteModel::CUSTOMER_LASTNAME,
                    RuleQuoteModel::QUOTE_ID,
                    RuleQuoteModel::CUSTOMER_PHONE
                ]
            );

        return $this;
    }

    public function addDetailsData(): Collection
    {
        $this->getSelect()->joinLeft(
            ['details' => $this->getTable(Detail::TABLE_NAME)],
            'main_table.' . HistoryModel::HISTORY_ID . ' = details.' . DetailModel::HISTORY_ID,
            []
        );
        $this->getSelect()->group('main_table.' . HistoryModel::HISTORY_ID);
        $columns = [
            DetailModel::PRODUCT_NAME,
            DetailModel::PRODUCT_SKU,
            DetailModel::PRODUCT_PRICE,
            DetailModel::PRODUCT_QTY
        ];
        foreach ($columns as $productColumn) {
            $this->addFilterToMap($productColumn, 'details.' . $productColumn);
        }

        return $this;
    }

    public function addRuleData(): Collection
    {
        $this->getSelect()
            ->joinLeft(
                ['rule' => $this->getTable(RuleResource::RULE_TABLE)],
                'ruleQuote.' . RuleQuoteModel::RULE_ID . ' = rule.' . Rule::RULE_ID,
                [Rule::NAME, Rule::IS_ACTIVE, Rule::CANCEL_CONDITION]
            );

        return $this;
    }

    public function addFilterByStoreIds(array $storeIds): Collection
    {
        return $this->addFieldToFilter('ruleQuote.' . RuleQuoteModel::STORE_ID, ['in' => $storeIds]);
    }

    public function addFilterByCampaignIds(array $campaignIds): Collection
    {
        return $this->addFieldToFilter('ruleQuote.' . RuleQuoteModel::RULE_ID, ['in' => $campaignIds]);
    }

    public function addFilterByDate(?string $dateTo, ?string $dateFrom): Collection
    {
        if ($dateTo && $dateFrom) {
            $this->addFieldToFilter('main_table.' . HistoryModel::EXECUTED_AT, ['lteq' => $dateTo])
                ->addFieldToFilter('main_table.' . HistoryModel::EXECUTED_AT, ['gteq' => $dateFrom]);
        }

        return $this;
    }

    public function addFilterByStatus(string $status): Collection
    {
        return $this->addFieldToFilter('main_table.' . HistoryModel::STATUS, $status);
    }

    public function getEmailStatistics(
        bool $grouped,
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): array {
        $uniqueHistorySelect = $this->getConnection()->select()
            ->from($this->getTable(HistoryResource::TABLE_NAME))
            ->group(HistoryModel::RULE_QUOTE_ID);
        $this
            ->addFilterByStoreIds($storeIds)
            ->addFilterByDate($dateTo, $dateFrom)
            ->addFilterByStatus(HistoryModel::STATUS_SENT)
            ->addFilterByCampaignIds($campaignIds)
            ->getSelect()
            ->joinLeft(
                ['schedule' => $this->getTable(ScheduleResource::TABLE_NAME)],
                'main_table.' . HistoryModel::SCHEDULE_ID . ' = schedule.' . Schedule::SCHEDULE_ID,
                []
            )->joinLeft(
                ['ruleQuote' => $this->getTable(RuleQuoteResource::MAIN_TABLE)],
                'main_table.' . HistoryModel::RULE_QUOTE_ID . ' = ruleQuote.' . RuleQuoteModel::RULE_QUOTE_ID,
            )->join(
                ['unique_hist' => new \Zend_Db_Expr('(' . $uniqueHistorySelect . ')')],
                'main_table.' . HistoryModel::HISTORY_ID . ' = unique_hist.' . HistoryModel::HISTORY_ID,
            )->reset(Select::COLUMNS)
            ->columns([
                'sent' => new \Zend_Db_Expr('COUNT(*)'),
                'opened' => new \Zend_Db_Expr(
                    'SUM(CASE WHEN main_table.' . HistoryModel::OPENED_COUNT . ' > 0 THEN 1 ELSE 0 END)'
                )
            ]);

        if ($grouped) {
            $this
                ->addRuleData()
                ->getSelect()
                ->columns(['rule_id' => 'ruleQuote.' . RuleQuoteModel::RULE_ID])
                ->group('ruleQuote.' . RuleQuoteModel::RULE_ID)
                ->order('ruleQuote.' . RuleQuoteModel::RULE_ID . ' ASC');
        }

        return $this->getData();
    }

    public function getOrderDetails(
        bool $grouped,
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): array {
        $uniqueHistorySelect = $this->getConnection()->select()
            ->from($this->getTable(HistoryResource::TABLE_NAME))
            ->group(HistoryModel::RULE_QUOTE_ID);
        $this
            ->addFilterByStoreIds($storeIds)
            ->addFilterByDate($dateTo, $dateFrom)
            ->addFilterByStatus(HistoryModel::STATUS_SENT)
            ->addFilterByCampaignIds($campaignIds)
            ->getSelect()
            ->joinLeft(
                ['ruleQuote' => $this->getTable(RuleQuoteResource::MAIN_TABLE)],
                'main_table.' . HistoryModel::RULE_QUOTE_ID . ' = ruleQuote.' . RuleQuoteModel::RULE_QUOTE_ID,
            )->join(
                ['order' => $this->getTable('sales_order')],
                'ruleQuote.' . RuleQuoteModel::QUOTE_ID . ' = order.quote_id',
                []
            )->join(
                ['unique_hist' => new \Zend_Db_Expr('(' . $uniqueHistorySelect . ')')],
                'main_table.' . HistoryModel::HISTORY_ID . ' = unique_hist.' . HistoryModel::HISTORY_ID,
            )->reset(Select::COLUMNS)
            ->columns([
                'placed_order_count' => new \Zend_Db_Expr('COUNT(*)'),
                'revenue' => new \Zend_Db_Expr('SUM(base_grand_total)')
            ])->where('ruleQuote.' . RuleQuoteModel::STATUS . ' = ?', RuleQuoteModel::STATUS_COMPLETE)
            ->where(
                'ruleQuote.' . RuleQuoteModel::ABANDONED_STATUS . ' = ?',
                RuleQuoteModel::ABANDONED_RESTORED_STATUS
            );

        if ($grouped) {
            $this
                ->addRuleData()
                ->getSelect()
                ->columns(['rule_id' => 'ruleQuote.' . RuleQuoteModel::RULE_ID])
                ->group('ruleQuote.' . RuleQuoteModel::RULE_ID)
                ->order('ruleQuote.' . RuleQuoteModel::RULE_ID . ' ASC');
        }

        return $this->getData();
    }

    public function getClickStatistics(
        bool $grouped,
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): array {
        $uniqueHistorySelect = $this->getConnection()->select()
            ->from($this->getTable(HistoryResource::TABLE_NAME))
            ->group(HistoryModel::RULE_QUOTE_ID);
        $this
            ->addFilterByStoreIds($storeIds)
            ->addFilterByDate($dateTo, $dateFrom)
            ->addFilterByStatus(HistoryModel::STATUS_SENT)
            ->addFilterByCampaignIds($campaignIds)
            ->getSelect()
            ->joinLeft(
                ['ruleQuote' => $this->getTable(RuleQuoteResource::MAIN_TABLE)],
                'main_table.' . HistoryModel::RULE_QUOTE_ID . ' = ruleQuote.' . RuleQuoteModel::RULE_QUOTE_ID,
            )->join(
                ['unique_hist' => new \Zend_Db_Expr('(' . $uniqueHistorySelect . ')')],
                'main_table.' . HistoryModel::HISTORY_ID . ' = unique_hist.' . HistoryModel::HISTORY_ID,
            )->reset(Select::COLUMNS)
            ->columns([
                'clicked' => new \Zend_Db_Expr('COUNT(*)')
            ])->where('ruleQuote.' . RuleQuoteModel::STATUS . ' = ?', RuleQuoteModel::STATUS_COMPLETE)
            ->where(
                'ruleQuote.' . RuleQuoteModel::ABANDONED_STATUS . ' = ?',
                RuleQuoteModel::ABANDONED_RESTORED_STATUS
            );

        if ($grouped) {
            $this
                ->addRuleData()
                ->getSelect()
                ->columns(['rule_id' => 'ruleQuote.' . RuleQuoteModel::RULE_ID])
                ->group('ruleQuote.' . RuleQuoteModel::RULE_ID)
                ->order('ruleQuote.' . RuleQuoteModel::RULE_ID . ' ASC');
        }

        return $this->getData();
    }
}
