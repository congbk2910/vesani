<?php

namespace Omnisend\Omnisend\Helper\SearchCriteria;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Quote\Api\Data\CartInterface;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Setup\InstallData;
use Psr\Log\LoggerInterface;

class Quote implements EntityInterface
{
    const QUOTE_CUSTOMER_EMAIL = 'customer_email';

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
    protected $generalConfig;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * SearchCriteriaBuilderHelper constructor.
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GeneralConfig $generalConfig
     * @param DateTime $dateTime
     * @param LoggerInterface $logger

     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GeneralConfig $generalConfig,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->generalConfig = $generalConfig;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntityInStoreByImportStatusSearchCriteria($isImported, $storeId)
    {
        $customerEmailFilter = $this->filterBuilder
            ->create()
            ->setField(self::QUOTE_CUSTOMER_EMAIL)
            ->setConditionType('neq')
            ->setValue(null);

        $isImportedFilter = $this->filterBuilder
            ->create()
            ->setField(InstallData::IS_IMPORTED)
            ->setConditionType('eq')
            ->setValue($isImported);

        $storeFilter = $this->filterBuilder
            ->create()
            ->setField(CartInterface::KEY_STORE_ID)
            ->setConditionType('eq')
            ->setValue($storeId);

        $toDate = $this->dateTime->date();
        $fromDate = strtotime('-' . $this->generalConfig->getQuoteCronDelta(), strtotime($toDate));
        $fromDate = $this->dateTime->date(null, $fromDate);

        $fromDateFilter = $this->filterBuilder
            ->create()
            ->setField(CartInterface::KEY_UPDATED_AT)
            ->setConditionType('from')
            ->setValue($fromDate);

        $customerEmailFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$customerEmailFilter]);

        $isImportedFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$isImportedFilter]);

        $storeFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$storeFilter]);

        $fromDateFilterGroup = $this->filterGroupBuilder
            ->create()
            ->setData('filters', [$fromDateFilter]);


        $searchCriteria = $this->searchCriteriaBuilder
            ->create()
            ->setFilterGroups([
                $customerEmailFilterGroup,
                $isImportedFilterGroup,
                $storeFilterGroup,

                $fromDateFilterGroup

            ]);

        $searchCriteria->setPageSize($this->generalConfig->getMaximumEntitiesPerCron());

        return $searchCriteria;
    }
}
