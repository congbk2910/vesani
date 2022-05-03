<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Ui\DataProvider\History\Filter;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

class AddStatusFilterToCollection implements AddFilterToCollectionInterface
{
    public function addFilter(Collection $collection, $field, $condition = null)
    {
        $collection->addFieldToFilter('main_table.' . $field, $condition);
    }
}
