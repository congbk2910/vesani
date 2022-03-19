<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Model\ResourceModel;

class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
	{
		$this->_init('cdz_amp_cms_block', 'entity_id');
	}
}
