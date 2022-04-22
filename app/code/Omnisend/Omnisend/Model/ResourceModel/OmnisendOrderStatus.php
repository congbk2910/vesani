<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;

class OmnisendOrderStatus extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OmnisendOrderStatusInterface::TABLE_NAME, OmnisendOrderStatusInterface::STATUS);
        $this->_isPkAutoIncrement = false;
    }
}
