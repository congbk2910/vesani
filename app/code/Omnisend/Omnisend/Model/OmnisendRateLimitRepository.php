<?php

namespace Omnisend\Omnisend\Model;

use Omnisend\Omnisend\Api\Data\OmnisendRateLimitInterface;
use Omnisend\Omnisend\Api\OmnisendRateLimitRepositoryInterface;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendRateLimit as OmnisendRateLimitResource;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendRateLimit\CollectionFactory;

class OmnisendRateLimitRepository implements OmnisendRateLimitRepositoryInterface
{
    /**
     * @var OmnisendRateLimitResource
     */
    private $omnisendRateLimitResource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var OmnisendRateLimitFactory
     */
    private $omnisendRateLimitFactory;

    /**
     * OmnisendRateLimitRepository constructor.
     * @param OmnisendRateLimitResource $omnisendRateLimitResource
     * @param CollectionFactory $collectionFactory
     * @param OmnisendRateLimitFactory $omnisendRateLimitFactory
     */
    public function __construct(
        OmnisendRateLimitResource $omnisendRateLimitResource,
        CollectionFactory $collectionFactory,
        OmnisendRateLimitFactory $omnisendRateLimitFactory
    ) {
        $this->omnisendRateLimitResource = $omnisendRateLimitResource;
        $this->collectionFactory = $collectionFactory;
        $this->omnisendRateLimitFactory = $omnisendRateLimitFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function save(OmnisendRateLimitInterface $omnisendRateLimit)
    {
        $this->omnisendRateLimitResource->save($omnisendRateLimit);
    }

    /**
     * {@inheritDoc}
     */
    public function getById($id)
    {
        $omnisendRateLimit = $this->omnisendRateLimitFactory->create();
        $this->omnisendRateLimitResource->load($omnisendRateLimit, $id);

        return $omnisendRateLimit;
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
    public function delete(OmnisendRateLimitInterface $omnisendRateLimit)
    {
        $this->omnisendRateLimitResource->delete($omnisendRateLimit);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($id)
    {
        $this->omnisendRateLimitResource->delete($this->getById($id));
    }
}
