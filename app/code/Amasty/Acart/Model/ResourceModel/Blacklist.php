<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel;

use Amasty\Acart\Model\Blacklist as BlacklistModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Blacklist extends AbstractDb
{
    public const TABLE_NAME = 'amasty_acart_blacklist';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, BlacklistModel::BLACKLIST_ID);
    }

    /**
     * @param $emails
     */
    public function saveImportData($emails)
    {
        if ($emails) {
            $this->getConnection()->insertOnDuplicate(
                $this->getMainTable(),
                $emails,
                [BlacklistModel::CUSTOMER_EMAIL]
            );
        }
    }
}
