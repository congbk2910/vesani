<?php

namespace Svea\Checkout\Model\Svea\Data\PresetValues;

use Svea\Checkout\Model\Client\DTO\Order\PresetValue;

interface PresetValuesProviderInterface
{
    /**
     * @return mixed
     */
    public function getEmailAddress() : PresetValue;

    /**
     * @return mixed
     */
    public function getPhoneNumber() : PresetValue;

    /**
     * @return mixed
     */
    public function getPostalCode() : PresetValue;

    /**
     * @return mixed
     */
    public function getIsCompany() : PresetValue;

    /**
     * @return array
     */
    public function getData() : array;
}
