<?php
/**
 * Copyright © 2017 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Model\ResourceModel\BrandEntity;

class Collection extends AbstractCollection
{
	protected function _construct()
    {
		$this->_init('Codazon\Shopbybrandpro\Model\Brand', 'Codazon\Shopbybrandpro\Model\ResourceModel\BrandEntity');
    }
    
    public function addAttributeJoin($attributeCode, $joinType = 'inner')
    {
        return parent::_addAttributeJoin($attributeCode, $joinType);
    }    
}
