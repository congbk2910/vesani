<?php

namespace Cminds\Salesrep\Model\Source;

class DefaultStatusList
{

    /**
     * Returns manager commission based array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        $result[] = ['value' => 'Unpaid', 'label' => __('Unpaid')];
        $result[] = ['value' => 'Ineligible', 'label' => __('Ineligible')];

        return $result;
    }
}
