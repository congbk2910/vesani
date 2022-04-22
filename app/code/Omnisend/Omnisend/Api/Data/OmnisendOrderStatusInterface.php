<?php

namespace Omnisend\Omnisend\Api\Data;

interface OmnisendOrderStatusInterface
{
    const TABLE_NAME = 'sales_order_status_omnisend';

    const STATUS = 'status';
    const PAYMENT_STATUS = 'payment_status';
    const FULFILLMENT_STATUS = 'fulfillment_status';

    const LABEL_STATUS = 'Status';
    const LABEL_PAYMENT_STATUS = 'Payment Status';
    const LABEL_FULFILLMENT_STATUS = 'Fulfillment Status';

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getPaymentStatus();

    /**
     * @return string
     */
    public function getFulfillmentStatus();

    /**
     * @param $status
     * @return void
     */
    public function setStatus($status);

    /**
     * @param $paymentStatus
     * @return void
     */
    public function setPaymentStatus($paymentStatus);

    /**
     * @param $fulfillmentStatus
     * @return void
     */
    public function setFulfillmentStatus($fulfillmentStatus);
}
