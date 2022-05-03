<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\OptionSource;

use Amasty\Acart\Model\Rule as Rule;
use Magento\Framework\Data\OptionSourceInterface;

class CancelCondition implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $optionArray = [];

        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $optionArray;
    }

    public function toArray(): array
    {
        return [
            Rule::CANCEL_CONDITION_CLICKED => __('Link from Email Clicked'),
            Rule::CANCEL_CONDITION_ANY_PRODUCT_WENT_OUT_OF_STOCK => __('Any product went out of stock'),
            Rule::CANCEL_CONDITION_ALL_PRODUCTS_WENT_OUT_OF_STOCK => __('All products went out of stock'),
            Rule::CANCEL_CONDITION_ALL_PRODUCTS_WERE_DISABLED => __('All products were disabled')
        ];
    }
}
