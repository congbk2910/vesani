<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\History\ProductDetails\ResourceModel\Detail;

use Amasty\Acart\Model\History\ProductDetails;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ProductDetails\Detail::class, ProductDetails\ResourceModel\Detail::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
