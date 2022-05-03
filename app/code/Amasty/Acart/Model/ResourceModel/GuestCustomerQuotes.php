<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GuestCustomerQuotes extends AbstractDb
{
    public const TABLE_NAME = 'amasty_acart_guest_customer_quotes';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'customer_id');
    }

    public function addCustomerQuote(int $customerId, int $quoteId, int $origQuoteId): void
    {
        $this->getConnection()
            ->insertOnDuplicate(
                $this->getMainTable(),
                [
                    'customer_id' => $customerId,
                    'quote_id' => $quoteId,
                    'orig_quote_id' => $origQuoteId
                ],
                ['customer_id', 'quote_id', 'orig_quote_id']
            );
    }

    public function getActiveQuoteId(int $customerId, int $origQuoteId): ?int
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['main_table' => $this->getMainTable()], 'main_table.quote_id')
            ->joinInner(
                ['quote' => $this->getTable('quote')],
                'quote.entity_id = main_table.quote_id',
                []
            )->where(
                'quote.is_active = 1 AND main_table.orig_quote_id = ' . $origQuoteId
                    . ' AND main_table.customer_id = ' . $customerId
            )->order('main_table.quote_id desc')
            ->limit(1);
        $quoteId = $connection->fetchOne($select);

        return $quoteId ? (int)$quoteId : null;
    }
}
