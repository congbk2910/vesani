<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Setup\InstallData;

class SubscriberFilter
{
    /**
     * @var GeneralConfig
     */
    private $generalConfig;

    /**
     * @param GeneralConfig $generalConfig
     */
    public function __construct(GeneralConfig $generalConfig)
    {
        $this->generalConfig = $generalConfig;
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function setCollectionFilters($collection, $storeId)
    {
        $collection->addFieldToFilter('customer_id', 0);
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->addFieldToFilter(InstallData::IS_IMPORTED, 0);
        $collection->setPageSize($this->generalConfig->getMaximumEntitiesPerCron());

        return $collection;
    }
}
