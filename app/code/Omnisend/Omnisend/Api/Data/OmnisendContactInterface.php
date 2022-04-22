<?php

namespace Omnisend\Omnisend\Api\Data;

interface OmnisendContactInterface
{
    const TABLE_NAME = 'customer_omnisend';

    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const OMNISEND_ID = 'omnisend_id';
    const STORE_ID = 'store_id';

    const LABEL_ID = 'Id';
    const LABEL_CUSTOMER_ID = 'Customer Id';
    const LABEL_OMNISEND_ID = 'Omnisend Id';
    const LABEL_STORE_ID = 'Store Id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @return string|null
     */
    public function getOmnisendId();

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param $id
     * @return void
     */
    public function setId($id);

    /**
     * @param $customerId
     * @return void
     */
    public function setCustomerId($customerId);

    /**
     * @param $omnisendId
     * @return void
     */
    public function setOmnisendId($omnisendId);

    /**
     * @param $storeId
     * @return int
     */
    public function setStoreId($storeId);
}
