<?php namespace Cminds\Salesrep\Model\ResourceModel\Salesrep;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Cminds\Salesrep\Model\Salesrep',
            'Cminds\Salesrep\Model\ResourceModel\Salesrep'
        );
    }

    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return $this
     */
    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }
}
