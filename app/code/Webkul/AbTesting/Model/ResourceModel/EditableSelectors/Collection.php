<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AbTesting\Model\ResourceModel\EditableSelectors;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\AbTesting\Model\EditableSelectors::class,
            \Webkul\AbTesting\Model\ResourceModel\EditableSelectors::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(
            \Magento\Framework\DB\Select::ORDER
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_COUNT
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_OFFSET
        );
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        );

        return $select;
    }
}
