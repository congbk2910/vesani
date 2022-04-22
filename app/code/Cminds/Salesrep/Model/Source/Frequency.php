<?php

namespace Cminds\Salesrep\Model\Source;

class Frequency
{
    const EVERY_DAY = 1;
    const EVERY_WEEKDAY = 2;
    const EVERY_FRIDAY = 3;
    const EVERY_TWO_WEEKS = 4;
    const EVERY_MONTH = 5;

    /**
     * Fetch options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        $result[] = [

            'label' => __('Every Day'),
            'value' => self::EVERY_DAY
        ];
        $result[] = [
            'label' => __('Every Weekday'),
            'value' => self::EVERY_WEEKDAY
        ];
        $result[] = [
            'label' => __('Every Friday'),
            'value' => self::EVERY_FRIDAY
        ];
        $result[] = [
            'label' => __('15th & Months End'),
            'value' => self::EVERY_TWO_WEEKS
        ];
        $result[] = [
            'label' => __('Months End'),
            'value' => self::EVERY_MONTH
        ];
        return $result;
    }
}
