<?php
/**
 * Copyright © 2021 Codazon. All rights reserved.
 * See COPYING.txt for license details.
*/

namespace Codazon\Shopbybrandpro\Model;

class Option extends \Magento\Framework\Model\AbstractModel
{
    const ENTITY = 'habi_brand';
    
    protected function _construct()
    {
        $this->_init('Codazon\Shopbybrandpro\Model\ResourceModel\Option');
    }
}
