<?php

namespace Omnisend\Omnisend\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Delta
 * @package Omnisend\Omnisend\Model\Config\Source
 */
class Delta implements OptionSourceInterface
{

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => '5 minutes', 'label' => __('Only Current Data')],
            ['value' => '1 hours', 'label' => __('1 Hour')],
            ['value' => '2 hours', 'label' => __('2 Hours')],
            ['value' => '3 hours', 'label' => __('3 Hours')],
            ['value' => '4 hours', 'label' => __('4 Hours')],
            ['value' => '5 hours', 'label' => __('5 Hours')],
            ['value' => '6 hours', 'label' => __('6 Hours')],
            ['value' => '7 hours', 'label' => __('7 Hour')],
            ['value' => '8 hours', 'label' => __('8 Hours')],
            ['value' => '9 hours', 'label' => __('9 Hours')],
            ['value' => '10 hours', 'label' => __('10 Hours')],
            ['value' => '11 hours', 'label' => __('11 Hours')],
            ['value' => '12 hours', 'label' => __('12 Hours')],
            ['value' => '24 hours', 'label' => __('24 Hours')],
        ];
    }
}
