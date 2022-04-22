<?php

namespace Omnisend\Omnisend\Api\Data;

interface OmnisendGuestSubscriberInterface
{
    const TABLE_NAME = 'guest_subscriber_omnisend';

    const ID = 'id';
    const SUBSCRIBER_ID = 'subscriber_id';
    const OMNISEND_ID = 'omnisend_id';
    const STORE_ID = 'store_id';

    const LABEL_ID = 'Id';
    const LABEL_SUBSCRIBER_ID = 'Subscriber Id';
    const LABEL_OMNISEND_ID = 'Omnisend Id';
    const LABEL_STORE_ID = 'Store Id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getSubscriberId();

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
     * @param $subscriberId
     * @return void
     */
    public function setSubscriberId($subscriberId);

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
