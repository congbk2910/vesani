<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Model\ResourceModel\Page;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Codazon\GoogleAmpManager\Model\Page', 'Codazon\GoogleAmpManager\Model\ResourceModel\Page');
    }
}
