<?php

namespace Omnisend\Omnisend\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;

interface OmnisendContactRepositoryInterface
{
    /**
     * @param OmnisendContactInterface $omnisendContact
     * @return void
     */
    public function save(OmnisendContactInterface $omnisendContact);

    /**
     * @param $id
     * @return OmnisendContactInterface
     */
    public function getById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return OmnisendContactInterface[]
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param OmnisendContactInterface $omnisendContact
     * @return void
     */
    public function delete(OmnisendContactInterface $omnisendContact);

    /**
     * @param $id
     * @return void
     */
    public function deleteById($id);
}
