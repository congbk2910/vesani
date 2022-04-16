<?php

namespace Svea\Checkout\Model\Svea\Data\PresetValues;

use Svea\Checkout\Model\Client\DTO\Order\PresetValue;

class PresetValueFactory
{
    /**
     * @param $type
     * @param $value
     *
     * @return PresetValue
     */
    public function create($type = null, $value = null) : PresetValue
    {
        $presetValue = new PresetValue();
        if ($type && $value) {
            $presetValue->setTypeName($type);
            $presetValue->setValue($value);
        }

        return $presetValue;
    }
}
