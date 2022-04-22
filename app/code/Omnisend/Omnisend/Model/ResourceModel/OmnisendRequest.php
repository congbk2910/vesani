<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class OmnisendRequestRecord
 * @package Omnisend\Omnisend\Model\ResourceModel
 */
class OmnisendRequest extends AbstractDb
{
    const TABLE_NAME = 'omnisend_request';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            $this->getConnection()->getTableName(self::TABLE_NAME),
            'record_id'
        );
    }
}
