<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;

class OmnisendContact extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OmnisendContactInterface::TABLE_NAME, OmnisendContactInterface::ID);
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
