<?php

namespace Omnisend\Omnisend\Helper\SearchCriteria;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;

class OmnisendContact
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
     * @param $customerId
     * @param $storeId
     * @return SearchCriteria
     */
    public function getOmnisendContactInStoreByCustomerIdSearchCriteria($customerId, $storeId)
    {
        $customerFilter = $this->filterBuilder
            ->create()
            ->setField(OmnisendContactInterface::CUSTOMER_ID)
            ->setConditionType('eq')
            ->setValue($customerId);

        $storeFilter = $this->filterBuilder
            ->create()
            ->setField(OmnisendContactInterface::STORE_ID)
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
     * @param int $customerId
     * @return SearchCriteria
     */
    public function getOmnisendContactByCustomerIdSearchCriteria($customerId)
    {
        $customerFilter = $this->filterBuilder
            ->create()
            ->setField(OmnisendContactInterface::CUSTOMER_ID)
            ->setConditionType('eq')
            ->setValue($customerId);

        $customerFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$customerFilter]);

        $searchCriteria = $this->searchCriteriaBuilder
            ->create()
            ->setFilterGroups([$customerFilterGroup]);

        return $searchCriteria;
    }
}
