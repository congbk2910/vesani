<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\Config\Source;

class DataRange implements \Magento\Framework\Data\OptionSourceInterface
{
    public const LAST_DAY = 1;
    public const LAST_WEEK = 7;
    public const LAST_MONTH = 30;
    public const OVERALL = 'Overall';
    public const CUSTOM = 0;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::LAST_DAY,
                'label' => __('Today')
            ],
            [
                'value' => self::LAST_WEEK,
                'label' => __('Last 7 days')
            ],
            [
                'value' => self::LAST_MONTH,
                'label' => __('Last 30 days')
            ],
            [
                'value' => self::OVERALL,
                'label' => __('Overall')
            ],
            [
                'value' => self::CUSTOM,
                'label' => __('Custom')
            ],
        ];
    }
}
