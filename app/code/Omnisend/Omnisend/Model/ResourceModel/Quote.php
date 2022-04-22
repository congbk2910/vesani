<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Omnisend\Omnisend\Setup\InstallData;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Omnisend\Omnisend\Setup\UpgradeSchema;

class Quote extends AbstractDb
{
    const TABLE_QUOTE = 'quote';
    const KEY_ENTITY_ID = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_QUOTE, self::KEY_ENTITY_ID);
    }

    /**
     * @param $quoteId
     * @param $isImported
     * @throws LocalizedException
     */
    public function updateIsImported($quoteId, $isImported)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            [InstallData::IS_IMPORTED => $isImported],
            self::KEY_ENTITY_ID . ' = ' . $quoteId
        );
    }

    /**
     * @param int $quoteId
     * @param int $postStatus
     * @throws LocalizedException
     */
    public function updatePostStatus($quoteId, $postStatus)
    {
        $this->getConnection()->update(
            $this->getMainTable(),
            [UpgradeSchema::OMNISEND_POST_STATUS => $postStatus],
            self::KEY_ENTITY_ID . ' = ' . $quoteId
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
     * @return int
     * @throws LocalizedException
     */
    public function setIsImportedValues()
    {
        return $this->getConnection()->update(
            $this->getMainTable(),
            [InstallData::IS_IMPORTED => InstallData::IMPORTED_ATTRIBUTE_VALUE],
            InstallData::IS_IMPORTED . ' = ' . InstallData::DEFAULT_IS_IMPORTED_VALUE
        );
    }
}
