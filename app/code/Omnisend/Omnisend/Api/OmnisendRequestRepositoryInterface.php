<?php
declare(strict_types=1);

namespace Omnisend\Omnisend\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Omnisend\Omnisend\Api\Data\OmnisendRequestInterface;
use Omnisend\Omnisend\Api\Data\OmnisendRequestSearchResultsInterface;
use Omnisend\Omnisend\Model\OmnisendRequest;

interface OmnisendRequestRepositoryInterface
{

    /**
     * Save OmnisendRequestRecord
     * @param OmnisendRequestInterface | OmnisendRequest $omnisendRequestRecord
     * @return OmnisendRequestInterface
     * @throws LocalizedException
     */
    public function save(
        OmnisendRequestInterface $omnisendRequestRecord
    );

    /**
     * Retrieve OmnisendRequestRecord
     * @param string $entityId
     * @return OmnisendRequestInterface
     * @throws LocalizedException
     */
    public function getById($entityId);

    /**
     * Retrieve OmnisendRequestRecord matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return OmnisendRequestSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete OmnisendRequestRecord
     * @param OmnisendRequestInterface | OmnisendRequest $omnisendRequestRecord
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        OmnisendRequestInterface $omnisendRequestRecord
    );

    /**
     * Delete OmnisendRequestRecord by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($entityId);
}
