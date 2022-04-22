<?php

namespace Omnisend\Omnisend\Helper\SearchCriteria;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;

class OmnisendGuestSubscriber
{
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * SearchCriteriaBuilderHelper constructor.
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param $subscriberId
     * @param $storeId
     * @return SearchCriteria
     */
    public function getOmnisendSubscriberInStoreBySubscriberIdSearchCriteria($subscriberId, $storeId)
    {
        $customerFilter = $this->filterBuilder
            ->create()
            ->setField(OmnisendGuestSubscriberInterface::SUBSCRIBER_ID)
            ->setConditionType('eq')
            ->setValue($subscriberId);

        $storeFilter = $this->filterBuilder
            ->create()
            ->setField(OmnisendGuestSubscriberInterface::STORE_ID)
            ->setConditionType('eq')
            ->setValue($storeId);

        $customerFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$customerFilter]);

        $storeFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$storeFilter]);

        $searchCriteria = $this->searchCriteriaBuilder
            ->create()
            ->setFilterGroups([$customerFilterGroup, $storeFilterGroup]);

        return $searchCriteria;
    }

    /**
     * @param int $subscriberId
     * @return SearchCriteria
     */
    public function getOmnisendSubscriberBySubscriberIdSearchCriteria($subscriberId)
    {
        $customerFilter = $this->filterBuilder
            ->create()
            ->setField(OmnisendGuestSubscriberInterface::SUBSCRIBER_ID)
            ->setConditionType('eq')
            ->setValue($subscriberId);

        $customerFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$customerFilter]);

        $searchCriteria = $this->searchCriteriaBuilder
            ->create()
            ->setFilterGroups([$customerFilterGroup]);

        return $searchCriteria;
    }
}
