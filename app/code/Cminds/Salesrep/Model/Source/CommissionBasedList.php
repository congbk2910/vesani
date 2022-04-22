<?php

namespace Cminds\Salesrep\Model\Source;

class CommissionBasedList
{

    /**
     * Returns manager commission based array
     *
     * @return array
     */
    public function toOptionArray()
    {

        $result = [];
        $result[] = ['value' => '1', 'label' => 'Order Subtotal'];
        $result[] = ['value' => '2', 'label' => 'Employee Commission'];

        return $result;
    }
}
