<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Omnisend\Omnisend\Api\Data\OmnisendRateLimitInterface;

class OmnisendRateLimit extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OmnisendRateLimitInterface::TABLE_NAME, OmnisendRateLimitInterface::ID);
        $this->_isPkAutoIncrement = false;
    }
}
