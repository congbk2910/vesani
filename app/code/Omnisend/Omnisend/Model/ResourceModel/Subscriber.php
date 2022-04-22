<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Omnisend\Omnisend\Setup\InstallData;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Subscriber extends AbstractDb
{
    const OMNISEND_PREVIOUS_SUBSCRIBER_STATUS = 'omnisend_previous_subscriber_status';
    const TABLE_NEWSLETTER_SUBSCRIBER = 'newsletter_subscriber';
    const SUBSCRIBER_ID = 'subscriber_id';
    const CUSTOMER_ID = 'customer_id';
    const STORE_ID = 'store_id';
    const SUBSCRIBER_EMAIL = 'subscriber_email';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NEWSLETTER_SUBSCRIBER, self::SUBSCRIBER_ID);
    }

    /**
     * @param $subscriberId
     * @param $isImported
     * @throws LocalizedException
     */
    public function updateIsImported($subscriberId, $isImported)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            [InstallData::IS_IMPORTED => $isImported],
            self::SUBSCRIBER_ID . ' = ' . $subscriberId
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
     * @param int $subscriberId
     * @param int $status
     * @return int
     * @throws LocalizedException
     */
    public function updatePreviousSubscriberStatus($subscriberId, $status)
    {
        return $this->getConnection()->update(
            $this->getMainTable(),
            [self::OMNISEND_PREVIOUS_SUBSCRIBER_STATUS => $status],
            self::SUBSCRIBER_ID . ' = ' . $subscriberId
        );
    }

    /**
     * @param int $customerId
     * @param string $customerEmail
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     */
    public function getSubscriberData($customerId, $customerEmail, $storeId)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getMainTable())
            ->where('store_id=:store_id')
            ->where('customer_id=:customer_id or subscriber_email=:subscriber_email');

        $result = $this->getConnection()
            ->fetchRow(
                $select,
                [
                    self::CUSTOMER_ID => $customerId,
                    self::SUBSCRIBER_EMAIL => $customerEmail,
                    self::STORE_ID => $storeId
                ]
            );

        if ($result) {
            return $result;
        }

        return [];
    }
}
