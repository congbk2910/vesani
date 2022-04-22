<?php
declare(strict_types=1);

namespace Omnisend\Omnisend\Api\Data;

interface OmnisendRequestSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get OmnisendRequestRecord list.
     * @return \Omnisend\Omnisend\Api\Data\OmnisendRequestInterface[]
     */
    public function getItems();

    /**
     * Set title list.
     * @param \Omnisend\Omnisend\Api\Data\OmnisendRequestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
