<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\Blacklist;

use Amasty\Acart\Api\BlacklistRepositoryInterface;
use Amasty\Acart\Api\Data\BlacklistInterface;
use Amasty\Acart\Api\Data\BlacklistInterfaceFactory;
use Amasty\Acart\Api\Data\BlacklistSearchResultsInterface;
use Amasty\Acart\Api\Data\BlacklistSearchResultsInterfaceFactory;
use Amasty\Acart\Model\AbstractCachedRepository;
use Amasty\Acart\Model\Blacklist as BlacklistModel;
use Amasty\Acart\Model\ResourceModel\Blacklist as BlacklistResource;
use Amasty\Acart\Model\ResourceModel\Blacklist\Collection;
use Amasty\Acart\Model\ResourceModel\Blacklist\CollectionFactory as BlacklistCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

class Repository extends AbstractCachedRepository implements BlacklistRepositoryInterface
{
    /**
     * @var BlacklistInterfaceFactory
     */
    private $blacklistFactory;

    /**
     * @var BlacklistResource
     */
    private $blacklistResource;

    /**
     * @var BlacklistSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var BlacklistCollectionFactory
     */
    private $blacklistCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        BlacklistInterfaceFactory $blacklistFactory,
        BlacklistResource $blacklistResource,
        BlacklistSearchResultsInterfaceFactory $searchResultsFactory,
        BlacklistCollectionFactory $blacklistCollectionFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->blacklistFactory = $blacklistFactory;
        $this->blacklistResource = $blacklistResource;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->blacklistCollectionFactory = $blacklistCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Get data by field value
     *
     * @param mixed $value
     * @param string $field
     *
     * @return BlacklistInterface
     * @throws NotFoundException
     */
    private function getBy($value, $field = BlacklistModel::BLACKLIST_ID): BlacklistInterface
    {
        if (($result = $this->getFromCache($field, $value)) !== null) {
            return $result;
        }

        /** @var BlacklistInterface $blacklist */
        $blacklist = $this->blacklistFactory->create();
        $this->blacklistResource->load($blacklist, $value, $field);
        if (!$blacklist->getBlacklistId()) {
            throw new NotFoundException(
                __('Black list with with specified %1 "%2" not found.', $field, $value)
            );
        }

        return $this->addToCache($field, $value, $blacklist);
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): BlacklistInterface
    {
        return $this->getBy($id, BlacklistModel::BLACKLIST_ID);
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerEmail(string $customerEmail): BlacklistInterface
    {
        return $this->getBy($customerEmail, BlacklistModel::CUSTOMER_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function save(BlacklistInterface $blacklist): BlacklistInterface
    {
        try {
            $this->blacklistResource->save($blacklist);
            $this->invalidateCache($blacklist);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save the black list. Error: %1', $e->getMessage())
            );
        }

        return $blacklist;
    }

    /**
     * @inheritdoc
     */
    public function delete(BlacklistInterface $blacklist): bool
    {
        try {
            $this->blacklistResource->delete($blacklist);
            $this->invalidateCache($blacklist);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to delete the blacklist email. Error: %1', $e->getMessage())
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): bool
    {
        $this->delete($this->getById($id));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): BlacklistSearchResultsInterface
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $blacklistCollection */
        $blacklistCollection = $this->blacklistCollectionFactory->create();
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $blacklistCollection);
        }
        $searchResults->setTotalCount($blacklistCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $blacklistCollection);
        }
        $blacklistCollection->setCurPage($searchCriteria->getCurrentPage());
        $blacklistCollection->setPageSize($searchCriteria->getPageSize());
        $blacklists = [];
        /** @var Blacklist $blacklistModel */
        foreach ($blacklistCollection as $blacklistModel) {
            /** @var BlacklistInterface $blacklist */
            $blacklist = $this->blacklistFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $blacklist,
                $blacklistModel->getData(),
                BlacklistInterface::class
            );
            $blacklists[] = $blacklist;
        }
        $searchResults->setItems($blacklists);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $withConsentCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $blacklistCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $blacklistCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $blacklistCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $blacklistCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $blacklistCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }
}
