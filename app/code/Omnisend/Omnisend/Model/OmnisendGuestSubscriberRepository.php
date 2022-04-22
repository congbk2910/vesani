<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;
use Omnisend\Omnisend\Api\OmnisendGuestSubscriberRepositoryInterface;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendGuestSubscriber as OmnisendGuestSubscriberResource;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendGuestSubscriber\CollectionFactory;

class OmnisendGuestSubscriberRepository implements OmnisendGuestSubscriberRepositoryInterface
{
    /**
     * @var OmnisendGuestSubscriberResource
     */
    private $omnisendGuestSubscriberResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var OmnisendGuestSubscriberFactory
     */
    private $omnisendGuestSubscriberFactory;

    /**
     * OmnisendContactRepository constructor.
     * @param OmnisendGuestSubscriberResource $omnisendGuestSubscriberResource
     * @param CollectionFactory $collectionFactory
     * @param OmnisendGuestSubscriberFactory $omnisendGuestSubscriberFactory
     */
    public function __construct(
        OmnisendGuestSubscriberResource $omnisendGuestSubscriberResource,
        CollectionFactory $collectionFactory,
        OmnisendGuestSubscriberFactory $omnisendGuestSubscriberFactory
    ) {
        $this->omnisendGuestSubscriberResource = $omnisendGuestSubscriberResource;
        $this->collectionFactory = $collectionFactory;
        $this->omnisendGuestSubscriberFactory = $omnisendGuestSubscriberFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function save(OmnisendGuestSubscriberInterface $omnisendGuestSubscriber)
    {
        $this->omnisendGuestSubscriberResource->save($omnisendGuestSubscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function getById($id)
    {
        $omnisendGuestSubscriber = $this->omnisendGuestSubscriberFactory->create();
        $this->omnisendGuestSubscriberResource->load($omnisendGuestSubscriber, $id);

        if (!$omnisendGuestSubscriber->getSubscriberId()) {
            return null;
        }

        return $omnisendGuestSubscriber;
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
    public function delete(OmnisendGuestSubscriberInterface $omnisendGuestSubscriber)
    {
        $this->omnisendGuestSubscriberResource->delete($omnisendGuestSubscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($id)
    {
        $this->omnisendGuestSubscriberResource->delete($this->getById($id));
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param OmnisendGuestSubscriberInterface[] $collection
     * @return OmnisendGuestSubscriberInterface[]
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
     * @param OmnisendGuestSubscriberInterface[] $collection
     * @return OmnisendGuestSubscriberInterface[]
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
