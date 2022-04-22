<?php

namespace Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'record_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'omnisend_request_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'request_collection';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Omnisend\Omnisend\Model\OmnisendRequest::class,
            \Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest::class
        );
    }
}
