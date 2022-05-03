<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\ResourceModel\RuleQuote;

use Amasty\Acart\Model\History;
use Amasty\Acart\Model\ResourceModel\RuleQuote as RuleQuoteResource;
use Amasty\Acart\Model\ResourceModel\History as HistoryResource;
use Amasty\Acart\Model\ResourceModel\GuestCustomerQuotes as GuestCustomerQuotesResource;
use Amasty\Acart\Model\RuleQuote;
use Amasty\Acart\Model\StatisticsManagement;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(RuleQuote::class, RuleQuoteResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function addCompleteFilter(): Collection
    {
        $this->getSelect()
            ->joinLeft(
                ['history' => $this->getTable(HistoryResource::TABLE_NAME)],
                'main_table.' . RuleQuote::RULE_QUOTE_ID . ' = history.' . History::RULE_QUOTE_ID
                . ' AND history.status <> "' . History::STATUS_SENT . '"',
                []
            )
            ->where('main_table.' . RuleQuote::STATUS . ' = ? ', RuleQuote::STATUS_PROCESSING)
            ->group('main_table.' . RuleQuote::RULE_QUOTE_ID . '')
            ->having('count(history.' . History::RULE_QUOTE_ID . ') = 0');

        return $this;
    }

    public function addFilterByAbandonedStatus(string $status): Collection
    {
        $this->addFieldToFilter(RuleQuote::ABANDONED_STATUS, $status)
            ->groupByQuoteId();

        return $this;
    }

    public function groupByQuoteId()
    {
        $this->getSelect()->group('quote_id');
    }

    public function getTotalAbandonedMoney(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        array $campaignIds
    ): float {
        $select = $this->getSelect();
        $select2 = clone $select;

        if ($dateFrom && $dateTo) {
            $select->where(
                'main_table.' . RuleQuote::CREATED_AT . ' BETWEEN \'' . $dateFrom . '\' AND \'' . $dateTo . '\''
            );
        }

        $select2->reset();

        $select2->from(['quote' => $this->getTable('quote')], StatisticsManagement::SUM_GRAND_TOTAL . ' as total')
            ->where('quote.is_active = 1')
            ->where(
                'quote.entity_id IN (?)',
                $select->reset('columns')
                    ->where('main_table.' . RuleQuote::STORE_ID . ' IN (?)', $storeIds)
                    ->where('main_table.' . RuleQuote::RULE_ID . ' IN (?)', $campaignIds)
                    ->columns('quote_id')
                    ->group('quote_id')
            );

        return (float)$this->getConnection()->fetchOne($select2);
    }

    public function getRestoredOrdersValue(
        array $storeIds,
        ?string $dateTo,
        ?string $dateFrom,
        string $param,
        array $campaignIds
    ): float {
        $selectQuoteIds = $this->getSelect();
        $selectOrdersInfo = clone $selectQuoteIds;

        if ($dateFrom && $dateTo) {
            $selectQuoteIds->where(
                'main_table.' . RuleQuote::CREATED_AT . ' BETWEEN \'' . $dateFrom . '\' AND \'' . $dateTo . '\''
            );
        }

        $selectQuoteIds->reset('columns')
            ->columns(
                [
                    'res_quote_id' => $this->getConnection()->getIfNullSql(
                        'guest_quotes.quote_id',
                        'main_table.' . RuleQuote::QUOTE_ID
                    )
                ]
            )->joinLeft(
                ['guest_quotes' => $this->getTable(GuestCustomerQuotesResource::TABLE_NAME)],
                'main_table.' . RuleQuote::QUOTE_ID . ' = guest_quotes.orig_quote_id',
                []
            )->where('main_table.' . RuleQuote::STORE_ID . ' IN (?)', $storeIds)
            ->where('main_table.' . RuleQuote::RULE_ID . ' IN (?)', $campaignIds)
            ->where(
                'main_table.' . RuleQuote::ABANDONED_STATUS . ' = (?)',
                RuleQuote::ABANDONED_RESTORED_STATUS
            )->group('res_quote_id');

        $selectOrdersInfo->reset();

        $selectOrdersInfo->from(['order' => $this->getTable('sales_order')], $param . ' as total')
            ->where('order.quote_id IN (?)', $selectQuoteIds);

        return (float)$this->getConnection()->fetchOne($selectOrdersInfo);
    }

    public function addFilterByStoreIds(array $storeIds): Collection
    {
        return $this->addFieldToFilter('main_table.' . RuleQuote::STORE_ID, ['in' => $storeIds]);
    }

    public function addFilterByCampaignIds(array $campaignIds): Collection
    {
        return $this->addFieldToFilter('main_table.' . RuleQuote::RULE_ID, ['in' => $campaignIds]);
    }

    public function addFilterByDate(?string $dateTo, ?string $dateFrom): Collection
    {
        if ($dateTo && $dateFrom) {
            $this->addFieldToFilter('main_table.' . RuleQuote::CREATED_AT, ['lteq' => $dateTo])
                ->addFieldToFilter('main_table.' . RuleQuote::CREATED_AT, ['gteq' => $dateFrom]);
        }

        return $this;
    }

    public function addFilterByTop5ForgetProducts(): Collection
    {
        $this
            ->getSelect()
            ->joinLeft(
                ['quote_item' => $this->getTable('quote_item')],
                'main_table.' . RuleQuote::QUOTE_ID . ' = quote_item.quote_id',
                []
            )
            ->reset(Select::COLUMNS)
            ->reset(Select::GROUP)
            ->columns(
                [
                    'product_name' => 'quote_item.name',
                    'count' => new \Zend_Db_Expr('FLOOR(sum(quote_item.qty))')
                ]
            )->where('parent_item_id is null')
            ->where('quote_item.name is not null')
            ->group('quote_item.sku')
            ->order('count DESC')
            ->limit(5);

        return $this;
    }
}
