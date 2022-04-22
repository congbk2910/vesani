<?php namespace Cminds\Salesrep\Model\ResourceModel\CommissionGroup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Cminds\Salesrep\Model\CommissionGroup::class,
            \Cminds\Salesrep\Model\ResourceModel\CommissionGroup::class
        );
    }
}
