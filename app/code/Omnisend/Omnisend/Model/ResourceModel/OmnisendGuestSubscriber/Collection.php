<?php

namespace Omnisend\Omnisend\Model\ResourceModel\OmnisendGuestSubscriber;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;
use Omnisend\Omnisend\Model\OmnisendGuestSubscriber;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendGuestSubscriber as OmnisendGuestSubscriberResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = OmnisendGuestSubscriberInterface::ID;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OmnisendGuestSubscriber::class, OmnisendGuestSubscriberResource::class);
    }
}
