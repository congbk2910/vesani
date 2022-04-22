<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;

class OmnisendGuestSubscriber extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OmnisendGuestSubscriberInterface::TABLE_NAME, OmnisendGuestSubscriberInterface::ID);
    }

    /**
     * Truncate the table
     * @throws LocalizedException
     */
    public function clearTable()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }
}
