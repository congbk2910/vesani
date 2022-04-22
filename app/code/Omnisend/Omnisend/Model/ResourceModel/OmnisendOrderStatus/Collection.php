<?php

namespace Omnisend\Omnisend\Model\ResourceModel\OmnisendOrderStatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;

class Collection extends AbstractCollection
{
    protected $_idFieldName = OmnisendOrderStatusInterface::STATUS;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Omnisend\Omnisend\Model\OmnisendOrderStatus',
            'Omnisend\Omnisend\Model\ResourceModel\OmnisendOrderStatus'
        );
    }
}
