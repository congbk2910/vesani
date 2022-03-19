<?php
/**
 *
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\Core\Model\Config\Source;

class PositionVertical implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => 'top', 'label' => __('Top')],
            ['value' => 'middle', 'label' => __('Middle')],
            ['value' => 'bottom', 'label' => __('Bottom')]
        ];
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
