<?php
 
namespace FME\ShareCart\Model\ResourceModel\Order\Grid;
 
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
 
class Collection extends OriginalCollection
{
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('sales_order');
        $this->getSelect()->joinLeft($joinTable, 'main_table.entity_id = sales_order.entity_id', ['sale_rep_id']);
        parent::_renderFiltersBefore();
    }
}