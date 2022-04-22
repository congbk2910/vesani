<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;
use Omnisend\Omnisend\Api\OmnisendContactRepositoryInterface;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendContact as OmnisendContactResource;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendContact\CollectionFactory;

class OmnisendContactRepository implements OmnisendContactRepositoryInterface
{
    /**
     * @var OmnisendContactResource
     */
    private $omnisendContactResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var OmnisendContactFactory
     */
    private $omnisendContactFactory;

    /**
     * OmnisendContactRepository constructor.
     * @param OmnisendContactResource $omnisendContactResource
     * @param CollectionFactory $collectionFactory
     * @param OmnisendContactFactory $omnisendContactFactory
     */
    public function __construct(
        OmnisendContactResource $omnisendContactResource,
        CollectionFactory $collectionFactory,
        OmnisendContactFactory $omnisendContactFactory
    ) {
        $this->omnisendContactResource = $omnisendContactResource;
        $this->collectionFactory = $collectionFactory;
        $this->omnisendContactFactory = $omnisendContactFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function save(OmnisendContactInterface $omnisendContact)
    {
        $this->omnisendContactResource->save($omnisendContact);
    }

    /**
     * {@inheritDoc}
     */
    public function getById($id)
    {
        $omnisendContact = $this->omnisendContactFactory->create();
        $this->omnisendContactResource->load($omnisendContact, $id);

        if (!$omnisendContact->getCustomerId()) {
            return null;
        }

        return $omnisendContact;
    }

    /**
     * {@inheritDoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->applyFilterGroups($searchCriteria, $collection);

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(OmnisendContactInterface $omnisendContact)
    {
        $this->omnisendContactResource->delete($omnisendContact);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($id)
    {
        $this->omnisendContactResource->delete($this->getById($id));
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OmnisendContactInterface[] $collection
     * @return OmnisendContactInterface[]
     */
    protected function applyFilterGroups(SearchCriteriaInterface $searchCriteria, $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $collection = $this->applyFilters($filterGroup, $collection);
        }

        return $collection;
    }

    /**
     * @param $filterGroup
     * @param OmnisendContactInterface[] $collection
     * @return OmnisendContactInterface[]
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
