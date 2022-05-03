<?php

namespace Amasty\CronScheduleList\Plugin;

class ScheduleCollectionPlugin
{
    public function afterGetIdFieldName($subject, $result)
    {
        if ($result === null) {
            $result = 'schedule_id';
        }

        return $result;
    }
}
