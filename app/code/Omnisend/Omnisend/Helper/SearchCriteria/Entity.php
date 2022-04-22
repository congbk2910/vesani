<?php

namespace Omnisend\Omnisend\Helper\SearchCriteria;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Setup\InstallData;

class Entity implements EntityInterface
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
     * @var GeneralConfig
     */
    private $generalConfig;

    /**
     * SearchCriteriaBuilderHelper constructor.
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GeneralConfig $generalConfig
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GeneralConfig $generalConfig
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->generalConfig = $generalConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityInStoreByImportStatusSearchCriteria($isImported, $storeId)
    {
        $isImportedFilter = $this->filterBuilder
            ->create()
            ->setField(InstallData::IS_IMPORTED)
            ->setConditionType('eq')
            ->setValue($isImported);

        $storeFilter = $this->filterBuilder
            ->create()
            ->setField(CustomerInterface::STORE_ID)
            ->setConditionType('eq')
            ->setValue($storeId);

        $isImportedFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$isImportedFilter]);

        $storeFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$storeFilter]);

        $searchCriteria = $this->searchCriteriaBuilder
            ->create()
            ->setFilterGroups([$isImportedFilterGroup, $storeFilterGroup]);

        $searchCriteria->setPageSize($this->generalConfig->getMaximumEntitiesPerCron());

        return $searchCriteria;
    }
}
