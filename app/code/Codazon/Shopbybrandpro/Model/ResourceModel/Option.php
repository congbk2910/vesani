<?php
/**
 * Copyright © 2021 Codazon. All rights reserved.
 * See COPYING.txt for license details.
*/

namespace Codazon\Shopbybrandpro\Model\ResourceModel;

class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
	{
		$this->_init('eav_attribute_option', 'option_id');
	}
}
