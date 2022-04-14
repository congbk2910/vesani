<?php
/**
 * @component  Goivvy_DecimalPlaces
 * @copyright  Copyright (c) 2017 Goivvy.com. (https://www.goivvy.com)
 * @license    https://www.goivvy.com/license Commercial License
 * @author     Goivvy.com developer team <team@goivvy.com>
 */

namespace Goivvy\DecimalPlaces\Model\Config\Source;

class Place implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('No decimal places')]
              , ['value' => 1, 'label' => __('1 place after dot')]
              , ['value' => 2, 'label' => __('2 places after dot (magento default)')]
              , ['value' => 3, 'label' => __('3 places after dot')]
              , ['value' => 4, 'label' => __('4 places after dot')]];
    }

    public function toArray()
    {
        return [0 => __('No decimal places')
              , 1 => __('1 place after dot')
              , 2 => __('2 places after dot (magento default)')
              , 3 => __('3 places after dot')
              , 4 => __('4 places after dot')];
    }
}
