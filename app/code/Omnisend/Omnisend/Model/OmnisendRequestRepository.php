<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Omnisend\Omnisend\Api\Data;
use Omnisend\Omnisend\Api\OmnisendRequestRepositoryInterface;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest as ResourceOmnisendRequest;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest\Collection as OmnisendRequestCollection;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest\CollectionFactory as OmnisendRequestCollectionFactory;

/**
 * Class OmnisendRequestRepository
 * @package Omnisend\Omnisend\Model
 */
class OmnisendRequestRepository implements OmnisendRequestRepositoryInterface
{
    /**
     * @var ResourceOmnisendRequest
     */
    protected $resource;

    /**
     * @var OmnisendRequestFactory
     */
    protected $omnisendRequestFactory;

    /**
     * @var OmnisendRequestCollectionFactory
     */
    protected $omnisendRequestCollectionFactory;

    /**
     * @var Data\OmnisendRequestSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * OmnisendRequestRepository constructor.
     * @param ResourceOmnisendRequest $resource
     * @param OmnisendRequestFactory $omnisendRequestFactory
     * @param OmnisendRequestCollectionFactory $omnisendRequestCollectionFactory
     * @param Data\OmnisendRequestSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceOmnisendRequest $resource,
        OmnisendRequestFactory $omnisendRequestFactory,
        OmnisendRequestCollectionFactory $omnisendRequestCollectionFactory,
        Data\OmnisendRequestSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->omnisendRequestFactory = $omnisendRequestFactory;
        $this->omnisendRequestCollectionFactory = $omnisendRequestCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(Data\OmnisendRequestInterface $omnisendRequest)
    {
        try {
            $this->resource->save($omnisendRequest);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the page: %1', $exception->getMessage()),
                $exception
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getById($entityId)
    {
        /** @var OmnisendRequest $omnisendRequest */
        $omnisendRequest = $this->omnisendRequestFactory->create();
        $omnisendRequest = $this->resource->load($omnisendRequest, $entityId, OmnisendRequest::RECORD_ID);
        if (!$omnisendRequest->getId()) {
            throw new NoSuchEntityException(__('The OmnisendRequest with the "%1" ID doesn\'t exist.', $entityId));
        }
        return $omnisendRequest;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var  OmnisendRequestCollection $collection */
        $collection = $this->omnisendRequestCollectionFactory->create();
        $collection = $this->applyFilterGroups($searchCriteria, $collection);

        /** @var Data\OmnisendRequestSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(Data\OmnisendRequestInterface $omnisendRequest)
    {
        try {
            $this->resource->delete($omnisendRequest);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the omnisendRequest: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId)
    {
        try {
            $this->resource->delete($this->getById($entityId));
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the omnisendRequest: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function deleteAll()
    {
        try {
            $connection = $this->resource->getConnection();
            $table = $this->resource->getMainTable();
            $connection->truncateTable($table);
        } catch (LocalizedException $exception) {
            throw new CouldNotDeleteException(
                __('Could not truncate the table: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OmnisendRequestCollection $collection
     * @return OmnisendRequestCollection
     */
    protected function applyFilterGroups(SearchCriteriaInterface $searchCriteria, $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $collection = $this->applyFilters($filterGroup, $collection);
        }

        return $collection;
    }

    /**
     * @param FilterGroup $filterGroup
     * @param OmnisendRequestCollection $collection
     * @return OmnisendRequestCollection
     */
    protected function applyFilters($filterGroup, $collection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType();
            $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }

        return $collection;
    }
}
