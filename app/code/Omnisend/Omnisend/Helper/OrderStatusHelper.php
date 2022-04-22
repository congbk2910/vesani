<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

class OrderStatusHelper
{
    const PAYMENT_STATUS_VALUE_AWAITING_PAYMENT = 'awaitingPayment';
    const PAYMENT_STATUS_VALUE_PARTIALLY_PAID = 'partiallyPaid';
    const PAYMENT_STATUS_VALUE_PAID = 'paid';
    const PAYMENT_STATUS_VALUE_PARTIALLY_REFUNDED = 'partiallyRefunded';
    const PAYMENT_STATUS_VALUE_REFUNDED = 'refunded';
    const PAYMENT_STATUS_VALUE_CANCELED = 'voided';
    const PAYMENT_STATUS_VALUE_DO_NOT_CHANGE = 'doNotChange';

    const PAYMENT_STATUS_LABEL_AWAITING_PAYMENT = 'Awaiting Payment';
    const PAYMENT_STATUS_LABEL_PARTIALLY_PAID = 'Partially Paid';
    const PAYMENT_STATUS_LABEL_PAID = 'Paid';
    const PAYMENT_STATUS_LABEL_PARTIALLY_REFUNDED = 'Partially Refunded';
    const PAYMENT_STATUS_LABEL_REFUNDED = 'Refunded';
    const PAYMENT_STATUS_LABEL_CANCELED = 'Canceled';
    const PAYMENT_STATUS_LABEL_DO_NOT_CHANGE = 'Do Not Change';

    const FULFILLMENT_STATUS_VALUE_UNFULFILLED = 'unfulfilled';
    const FULFILLMENT_STATUS_VALUE_IN_PROGRESS = 'inProgress';
    const FULFILLMENT_STATUS_VALUE_FULFILLED = 'fulfilled';
    const FULFILLMENT_STATUS_VALUE_DELIVERED = 'delivered';
    const FULFILLMENT_STATUS_VALUE_RESTOCKED = 'restocked';
    const FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE = 'doNotChange';

    const FULFILLMENT_STATUS_LABEL_UNFULFILLED = 'Unfulfilled';
    const FULFILLMENT_STATUS_LABEL_IN_PROGRESS = 'In Progress';
    const FULFILLMENT_STATUS_LABEL_FULFILLED = 'Fulfilled';
    const FULFILLMENT_STATUS_LABEL_DELIVERED = 'Delivered';
    const FULFILLMENT_STATUS_LABEL_RESTOCKED = 'Restocked';
    const FULFILLMENT_STATUS_LABEL_DO_NOT_CHANGE = 'Do Not Change';

    /**
     * @var CollectionFactory
     */
    private $statusCollectionFactory;

    /**
     * OrderStatusHelper constructor.
     * @param CollectionFactory $statusCollectionFactory
     */
    public function __construct(CollectionFactory $statusCollectionFactory)
    {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * @return array
     */
    public function getOrderStatusOptions()
    {
        $orderStatuses = $this->statusCollectionFactory->create()->toOptionArray();
        $selectOptions = [];

        foreach ($orderStatuses as $orderStatus) {
            $selectOptions[$orderStatus['value']] = __($orderStatus['label']);
        }

        return $selectOptions;
    }

    /**
     * @return array
     */
    public function getOrderPaymentStatusOptions()
    {
        return [
            self::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT => __(self::PAYMENT_STATUS_LABEL_AWAITING_PAYMENT),
            self::PAYMENT_STATUS_VALUE_PARTIALLY_PAID => __(self::PAYMENT_STATUS_LABEL_PARTIALLY_PAID),
            self::PAYMENT_STATUS_VALUE_PAID => __(self::PAYMENT_STATUS_LABEL_PAID),
            self::PAYMENT_STATUS_VALUE_PARTIALLY_REFUNDED => __(self::PAYMENT_STATUS_LABEL_PARTIALLY_REFUNDED),
            self::PAYMENT_STATUS_VALUE_REFUNDED => __(self::PAYMENT_STATUS_LABEL_REFUNDED),
            self::PAYMENT_STATUS_VALUE_CANCELED => __(self::PAYMENT_STATUS_LABEL_CANCELED),
            self::PAYMENT_STATUS_VALUE_DO_NOT_CHANGE => __(self::PAYMENT_STATUS_LABEL_DO_NOT_CHANGE)
        ];
    }

    /**
     * @return array
     */
    public function getOrderFulfillmentStatusOptions()
    {
        return [
            self::FULFILLMENT_STATUS_VALUE_UNFULFILLED => __(self::FULFILLMENT_STATUS_LABEL_UNFULFILLED),
            self::FULFILLMENT_STATUS_VALUE_IN_PROGRESS => __(self::FULFILLMENT_STATUS_LABEL_IN_PROGRESS),
            self::FULFILLMENT_STATUS_VALUE_FULFILLED => __(self::FULFILLMENT_STATUS_LABEL_FULFILLED),
            self::FULFILLMENT_STATUS_VALUE_DELIVERED => __(self::FULFILLMENT_STATUS_LABEL_DELIVERED),
            self::FULFILLMENT_STATUS_VALUE_RESTOCKED => __(self::FULFILLMENT_STATUS_LABEL_RESTOCKED),
            self::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE => __(self::FULFILLMENT_STATUS_LABEL_DO_NOT_CHANGE)
        ];
    }
}
