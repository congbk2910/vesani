<?php

namespace Omnisend\Omnisend\Model\ResourceModel\OmnisendContact;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;
use Omnisend\Omnisend\Model\OmnisendContact;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendContact as OmnisendContactResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = OmnisendContactInterface::ID;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OmnisendContact::class, OmnisendContactResource::class);
    }
}
