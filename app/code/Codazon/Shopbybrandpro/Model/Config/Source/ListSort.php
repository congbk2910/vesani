<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Model\Config\Source;

class ListSort implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
    {
        return [
            ['value' => 'name',  'label' => __('Name')],
            ['value' => 'sort_order',   'label' => __('Position')]
        ];
    }	
	
}