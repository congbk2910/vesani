<?php
/**
 *
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\Core\Model\Config\Source;

class PositionHorizontal implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => 'left', 'label' => __('Left')],
            ['value' => 'center', 'label' => __('Center')],
            ['value' => 'right', 'label' => __('Right')]
        ];
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
