<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel;

use Amasty\Acart\Model\History as HistoryModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class History extends AbstractDb
{
    public const TABLE_NAME = 'amasty_acart_history';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, HistoryModel::HISTORY_ID);
    }
}
