<?php

namespace Svea\Checkout\Api;

interface PushRepositoryInterface
{
    /**
     * Create or update a push request
     *
     * @param \Svea\Checkout\Api\Data\PushInterface $push
     * @return \Svea\Checkout\Api\Data\PushInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Svea\Checkout\Api\Data\PushInterface $push);

    /**
     * Retrieve push.
     *
     * @param int $sveaOrderId
     * @return \Svea\Checkout\Api\Data\PushInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified email does not exist.
     */
    public function get($sveaOrderId);

    /**
     * Get push by Entity ID.
     *
     * @param int $entityId
     * @return \Svea\Checkout\Api\Data\PushInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     */
    public function getById($entityId);
}
