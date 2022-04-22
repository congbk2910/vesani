<?php

namespace Omnisend\Omnisend\Helper;

use function floatval;
use function is_string;
use function round;
use function str_replace;
use function strpos;

class PriceHelper
{
    /**
     * @param string|float $price
     * @return int
     */
    public function getPriceInCents($price)
    {
        $priceInCents = $this->toFloat($price) * 100;

        return round($priceInCents);
    }

    /**
     * @param string|float $price
     * @return float
     */
    protected function toFloat($price)
    {
        if (is_string($price)) {
            $price = $this->normalizePriceString($price);
        }

        $floatPrice = floatval($price);

        return round($floatPrice, 2);
    }

    /**
     * @param string $price
     * @return string
     */
    protected function normalizePriceString($price)
    {
        if (strpos($price, ',') !== false && strpos($price, '.') !== false) {
            return str_replace(',', '', $price);
        }

        return str_replace(',', '.', $price);
    }
}
