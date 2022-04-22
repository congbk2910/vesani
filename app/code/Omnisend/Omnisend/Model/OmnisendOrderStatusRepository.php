<?php

namespace Omnisend\Omnisend\Model;

use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Api\OmnisendOrderStatusRepositoryInterface;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendOrderStatus as OmnisendOrderStatusResource;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendOrderStatus\CollectionFactory;

class OmnisendOrderStatusRepository implements OmnisendOrderStatusRepositoryInterface
{
    /**
     * @var OmnisendOrderStatusResource
     */
    private $omnisendOrderStatusResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var OmnisendOrderStatusFactory
     */
    private $omnisendOrderStatusFactory;

    /**
     * OmnisendOrderStatusRepository constructor.
     * @param OmnisendOrderStatusResource $omnisendOrderStatusResource
     * @param CollectionFactory $collectionFactory
     * @param OmnisendOrderStatusFactory $omnisendOrderStatusFactory
     */
    public function __construct(
        OmnisendOrderStatusResource $omnisendOrderStatusResource,
        CollectionFactory $collectionFactory,
        OmnisendOrderStatusFactory $omnisendOrderStatusFactory
    ) {
        $this->omnisendOrderStatusResource = $omnisendOrderStatusResource;
        $this->collectionFactory = $collectionFactory;
        $this->omnisendOrderStatusFactory = $omnisendOrderStatusFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function save(OmnisendOrderStatusInterface $omnisendOrderStatus)
    {
        $this->omnisendOrderStatusResource->save($omnisendOrderStatus);
    }

    /**
     * {@inheritDoc}
     */
    public function getById($statusId)
    {
        $omnisendOrderStatus = $this->omnisendOrderStatusFactory->create();
        $this->omnisendOrderStatusResource->load($omnisendOrderStatus, $statusId);

        return $omnisendOrderStatus;
    }

    /**
     * {@inheritDoc}
     */
    public function getList()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(OmnisendOrderStatusInterface $omnisendOrderStatus)
    {
        $this->omnisendOrderStatusResource->delete($omnisendOrderStatus);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($statusId)
    {
        $this->omnisendOrderStatusResource->delete($this->getById($statusId));
    }
}
