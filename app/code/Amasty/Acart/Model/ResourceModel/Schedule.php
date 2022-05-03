<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel;

use Amasty\Acart\Model\Schedule as ScheduleModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class Schedule extends AbstractDb
{
    public const TABLE_NAME = 'amasty_acart_schedule';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ScheduleModel::SCHEDULE_ID);
    }
}
