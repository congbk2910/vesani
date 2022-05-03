<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel;

use Amasty\Acart\Model\RuleQuote as RuleQuoteModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RuleQuote extends AbstractDb
{
    public const MAIN_TABLE = 'amasty_acart_rule_quote';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, RuleQuoteModel::RULE_QUOTE_ID);
    }

    /**
     * Delete previous rule_quote entities
     *
     * @since 1.6.1 moved from RuleQuote\Collection
     */
    public function deleteNotUnique()
    {
        $activeIds = $this->getActiveRuleQuoteIds();

        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where('`status` != ?', RuleQuoteModel::STATUS_COMPLETE)
            ->where('`rule_quote_id` NOT IN (?)', $activeIds);

        $deleteQuery = $this->getConnection()->deleteFromSelect($select, $this->getMainTable());
        $this->getConnection()->query($deleteQuery);
    }

    /**
     * @return array
     */
    public function getActiveRuleQuoteIds()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['MAX(`rule_quote_id`)'])
            ->where('status != ?', RuleQuoteModel::STATUS_COMPLETE)
            ->group('quote_id');

        return $this->getConnection()->fetchCol($select);
    }
}
