<?php

namespace Svea\Checkout\Api\Data;

interface PushInterface
{
    const ENTITY_ID = 'entity_id';
    const SVEA_ORDER_ID = 'sid';
    const MAGENTO_ORDER_ID = 'order_id';
    const CREATED_AT = 'created_at';

    public function getId();
    public function setId($id);

    public function getSid();
    public function setSid($sid);

    public function getCreatedAt();
    public function setCreatedAt($createdAt);

    public function getOrderId();
    public function setOrderId($orderId);
}
