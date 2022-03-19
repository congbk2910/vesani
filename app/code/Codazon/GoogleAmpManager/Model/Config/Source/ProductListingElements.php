<?php
/**
 *
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Model\Config\Source;

class ProductListingElements implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => 'description', 'label' => __('Description')],
            ['value' => 'rating', 'label' => __('Rating')],
            ['value' => 'price', 'label' => __('Price')],
            ['value' => 'addtocart', 'label' => __('Add to cart')],
            ['value' => 'thumbnail', 'label' => __('Thumbnail')],
        ];
    }
    
    public function toArray()
    {
        return $this->toOptionArray();
    }
}
