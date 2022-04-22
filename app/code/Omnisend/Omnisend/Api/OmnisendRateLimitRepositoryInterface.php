<?php

namespace Omnisend\Omnisend\Api;

use Omnisend\Omnisend\Api\Data\OmnisendRateLimitInterface;

interface OmnisendRateLimitRepositoryInterface
{
    /**
     * @param OmnisendRateLimitInterface $omnisendRateLimit
     * @return void
     */
    public function save(OmnisendRateLimitInterface $omnisendRateLimit);

    /**
     * @param $id
     * @return OmnisendRateLimitInterface
     */
    public function getById($id);

    /**
     * @return OmnisendRateLimitInterface[]
     */
    public function getList();

    /**
     * @param OmnisendRateLimitInterface $omnisendRateLimit
     * @return void
     */
    public function delete(OmnisendRateLimitInterface $omnisendRateLimit);

    /**
     * @param $id
     * @return void
     */
    public function deleteById($id);
}
