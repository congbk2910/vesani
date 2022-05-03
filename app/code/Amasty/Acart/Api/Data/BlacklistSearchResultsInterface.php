<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BlacklistSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blacklist list
     *
     * @return \Amasty\Acart\Api\Data\BlacklistInterface[]
     */
    public function getItems();

    /**
     * Set blacklist list
     *
     * @param \Amasty\Acart\Api\Data\BlacklistInterface[] $items
     * @return \Amasty\Acart\Api\Data\BlacklistSearchResultsInterface
     */
    public function setItems(array $items);
}
