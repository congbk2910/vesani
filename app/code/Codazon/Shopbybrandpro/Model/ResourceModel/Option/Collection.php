<?php
/**
 * Copyright © 2021 Codazon. All rights reserved.
 * See COPYING.txt for license details.
*/

namespace Codazon\Shopbybrandpro\Model\ResourceModel\Option;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Codazon\Shopbybrandpro\Model\Option::class, \Codazon\Shopbybrandpro\Model\ResourceModel\Option::class);
    }    
}
