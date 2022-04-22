<?php

namespace Cminds\Salesrep\Model\Source;

class MinutesList
{

    public function toOptionArray()
    {

        $result = [];
        $result[] = ['value' => '0', 'label' => __(':00 top of the hour')];
        $result[] = ['value' => '15', 'label' => __(':15 quarter past')];
        $result[] = ['value' => '30', 'label' => __(':30 half past')];
        $result[] = ['value' => '45', 'label' => __(':45 quarter til')];

        return $result;
    }
}
