<?php

namespace Omnisend\Omnisend\Model\ResourceModel\OmnisendRateLimit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Omnisend\Omnisend\Api\Data\OmnisendRateLimitInterface;

class Collection extends AbstractCollection
{
    protected $_idFieldName = OmnisendRateLimitInterface::ID;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Omnisend\Omnisend\Model\OmnisendRateLimit',
            'Omnisend\Omnisend\Model\ResourceModel\OmnisendRateLimit'
        );
    }
}
