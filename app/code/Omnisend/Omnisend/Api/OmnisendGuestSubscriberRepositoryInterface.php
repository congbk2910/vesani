<?php

namespace Omnisend\Omnisend\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;

interface OmnisendGuestSubscriberRepositoryInterface
{
    /**
     * @param OmnisendGuestSubscriberInterface $omnisendGuestSubscriber
     * @return void
     */
    public function save(OmnisendGuestSubscriberInterface $omnisendGuestSubscriber);

    /**
     * @param $id
     * @return OmnisendGuestSubscriberInterface
     */
    public function getById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return OmnisendGuestSubscriberInterface[]
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param OmnisendGuestSubscriberInterface $omnisendGuestSubscriber
     * @return void
     */
    public function delete(OmnisendGuestSubscriberInterface $omnisendGuestSubscriber);

    /**
     * @param $id
     * @return void
     */
    public function deleteById($id);
}
