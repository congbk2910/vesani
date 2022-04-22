<?php

namespace Omnisend\Omnisend\Helper\SearchCriteria;

use Magento\Framework\Api\SearchCriteria;

interface EntityInterface
{
    /**
     * @param $isImported
     * @param $storeId
     * @return SearchCriteria
     */
    public function getEntityInStoreByImportStatusSearchCriteria($isImported, $storeId);
}
