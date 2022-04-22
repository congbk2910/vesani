<?php

namespace Cminds\Salesrep\Model\Source;

class SendReportsTo
{
    const EMPLOYEE_ONLY = 1;
    const EMPLOYEE_AND_ADMIN = 2;

    public function toOptionArray()
    {

        $result = [];

        $result[] = [
            'label' => __('Sales Rep & Manager'),
            'value' => self::EMPLOYEE_ONLY
        ];
        $result[] = [
            'label' => __('Sales Rep, Manager, & Admin'),
            'value' => self::EMPLOYEE_AND_ADMIN
        ];

        return $result;
    }
}
