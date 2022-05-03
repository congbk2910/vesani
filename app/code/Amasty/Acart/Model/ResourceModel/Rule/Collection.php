<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel\Rule;

use Amasty\Acart\Model\Rule;
use Amasty\Acart\Model\RuleQuote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var bool
     */
    private $isRuleQuoteJoined = false;

    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Acart\Model\Rule::class, \Amasty\Acart\Model\ResourceModel\Rule::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function addRuleQuoteFilter(int $ruleQuoteId)
    {
        $this->joinRuleQuoteTable();
        $this->addFieldToFilter('rule_quote.' . RuleQuote::RULE_QUOTE_ID, $ruleQuoteId);

        return $this;
    }

    public function joinRuleQuoteTable()
    {
        if (!$this->isRuleQuoteJoined) {
            $this->join(
                ['rule_quote' => $this->getTable('amasty_acart_rule_quote')],
                'main_table.' . Rule::RULE_ID . ' = rule_quote.' . RuleQuote::RULE_ID
            );
            $this->isRuleQuoteJoined = true;
        }

        return $this;
    }

    protected function beforeAddLoadedItem(\Magento\Framework\DataObject $item)
    {
        /** @var \Amasty\Acart\Model\ResourceModel\Rule $ruleResource */
        $ruleResource = $this->getResource();
        $ruleResource->loadStoreIds($item);
        $ruleResource->loadCustomerGroupIds($item);

        return parent::beforeAddLoadedItem($item);
    }
}
