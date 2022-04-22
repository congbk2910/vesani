<?php

namespace Cminds\Salesrep\Model\Source;

/**
 * Class CalculationType
 *
 * @package Cminds\Salesrep\Model\Source
 */
class CalculationType
{
    const SUBTOTAL_CALCULATION_TYPE = 0;

    const MARGIN_CALCULATION_TYPE = 1;

    const CALCULATION_TYPE_OPTION_KEY = 'salesrep_rep_commission_calculation_type';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SUBTOTAL_CALCULATION_TYPE,
                'label' => __('Subtotal')
            ],
            [
                'value' => self::MARGIN_CALCULATION_TYPE,
                'label' => __('Margin')
            ],
        ];
    }
}
