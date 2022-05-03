<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Rule extends AbstractDb
{
    public const RULE_TABLE = 'amasty_acart_rule';
    public const RULE_STORE_TABLE = 'amasty_acart_rule_store';
    public const RULE_CUSTOMER_GROUP_TABLE = 'amasty_acart_rule_customer_group';

    protected function _construct()
    {
        $this->_init(self::RULE_TABLE, 'rule_id');
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $rule)
    {
        $this->loadStoreIds($rule);
        $this->loadCustomerGroupIds($rule);

        return parent::_afterLoad($rule);
    }

    public function loadCustomerGroupIds(\Magento\Framework\Model\AbstractModel $rule)
    {
        $select = $this->getConnection()->select()->from(['main_table' => $this->getMainTable()]);
        $select->joinInner(
            ['rule_customer_group' => $this->getTable(self::RULE_CUSTOMER_GROUP_TABLE)],
            'main_table.rule_id = rule_customer_group.rule_id',
            ['customer_group_id']
        );
        $select->reset(Select::COLUMNS)
            ->where('main_table.rule_id = ?', (int)$rule->getRuleId())
            ->columns(['customer_group_ids' => 'rule_customer_group.customer_group_id']);

        $customerGroupIds = $select->getConnection()->fetchCol($select);
        $rule->setData(\Amasty\Acart\Model\Rule::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    public function loadStoreIds(\Magento\Framework\Model\AbstractModel $rule)
    {
        $select = $this->getConnection()->select()->from(['main_table' => $this->getMainTable()]);
        $select->joinInner(
            ['rule_store' => $this->getTable(self::RULE_STORE_TABLE)],
            'main_table.rule_id = rule_store.rule_id',
            ['store_id']
        );
        $select->reset(Select::COLUMNS)
            ->where('main_table.rule_id = ?', (int)$rule->getRuleId())
            ->columns(['store_ids' => 'rule_store.store_id']);

        $storeIds = $select->getConnection()->fetchCol($select);
        $rule->setData(\Amasty\Acart\Model\Rule::STORE_IDS, $storeIds);
    }

    /**
     * return all attribute codes used in Acart rules
     *
     * @return array
     */
    public function getAttributes()
    {
        $db = $this->getConnection();

        $select = $db->select()->from($this->getTable('amasty_acart_attribute'), ['code'])
            ->distinct(true);

        return $db->fetchCol($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of the rule
     *
     * @param int $id
     * @param array $attributes
     *
     * @return $this
     */
    public function saveAttributes($id, $attributes)
    {
        $db = $this->getConnection();
        $tbl = $this->getTable('amasty_acart_attribute');

        $db->delete($tbl, ['rule_id=?' => $id]);

        $data = [];
        foreach ($attributes as $code) {
            $data[] = [
                'rule_id' => $id,
                'code' => $code,
            ];
        }
        $db->insertMultiple($tbl, $data);

        return $this;
    }
}
