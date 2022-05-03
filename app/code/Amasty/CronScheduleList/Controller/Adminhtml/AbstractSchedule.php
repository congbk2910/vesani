<?php

namespace Amasty\CronScheduleList\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class AbstractSchedule extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_CronScheduleList::schedule_list';
}
