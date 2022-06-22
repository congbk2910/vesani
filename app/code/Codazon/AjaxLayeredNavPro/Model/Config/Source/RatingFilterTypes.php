<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\AjaxLayeredNavPro\Model\Config\Source;

/**
 * Config category source
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class RatingFilterTypes implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray($addEmpty = true)
    {
        return [
            ['value' => 'link-up', 'label' => __('Link - Up (score_min ≤ filtered value)')],
            ['value' => 'link-interval', 'label' => __('Link - Interval (score_min < filtered value ≤ score_max)')],
            ['value' => 'slider-up', 'label' => __('Slider - Up (score_min ≤ filtered value)')],
            ['value' => 'slider-interval', 'label' => __('Slider - Interval (score_min < filtered value ≤ score_max)')],
        ];
    }
}
