<?php

namespace Omnisend\Omnisend\Api;

use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;

interface OmnisendOrderStatusRepositoryInterface
{
    /**
     * @param OmnisendOrderStatusInterface $omnisendOrderStatus
     * @return void
     */
    public function save(OmnisendOrderStatusInterface $omnisendOrderStatus);

    /**
     * @param $statusId
     * @return OmnisendOrderStatusInterface
     */
    public function getById($statusId);

    /**
     * @return OmnisendOrderStatusInterface[]
     */
    public function getList();

    /**
     * @param OmnisendOrderStatusInterface $omnisendOrderStatus
     * @return void
     */
    public function delete(OmnisendOrderStatusInterface $omnisendOrderStatus);

    /**
     * @param $statusId
     * @return void
     */
    public function deleteById($statusId);
}
