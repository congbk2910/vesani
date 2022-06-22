<?php
/**
 *
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\Core\Model\Config\Source;

class List4Options implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('1')],
            ['value' => '2', 'label' => __('2')],
            ['value' => '3', 'label' => __('3')],
            ['value' => '4', 'label' => __('4')]
        ];
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
