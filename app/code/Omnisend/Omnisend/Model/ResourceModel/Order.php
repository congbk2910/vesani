<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Api\Data\OrderInterface;
use Omnisend\Omnisend\Setup\InstallData;
use Omnisend\Omnisend\Setup\UpgradeSchema;

class Order extends AbstractDb
{
    const TABLE_ORDER = 'sales_order';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_ORDER, OrderInterface::ENTITY_ID);
    }

    /**
     * @param $orderId
     * @param $isImported
     * @throws LocalizedException
     */
    public function updateIsImported($orderId, $isImported)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            [InstallData::IS_IMPORTED => $isImported],
            OrderInterface::ENTITY_ID . ' = ' . $orderId
        );
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function resetIsImportedValues()
    {
        return $this->getConnection()->update(
            $this->getMainTable(),
            [InstallData::IS_IMPORTED => InstallData::DEFAULT_IS_IMPORTED_VALUE],
            InstallData::IS_IMPORTED . ' = ' . InstallData::IMPORTED_ATTRIBUTE_VALUE
        );
    }

    /**
     * @param int $orderId
     * @param int $postStatus
     * @throws LocalizedException
     */
    public function updatePostStatus($orderId, $postStatus)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            [UpgradeSchema::OMNISEND_POST_STATUS => $postStatus],
            OrderInterface::ENTITY_ID . ' = ' . $orderId
        );
    }
}
