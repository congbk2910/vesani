<?php

namespace FME\ShareCart\Plugin\Api;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;


class OrderRepository
{

    const SALE_REP_ID = 'sale_rep_id';

    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Add "sale_rep_id" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $saleRepId = $order->getData(self::SALE_REP_ID);
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes->setSaleRepId($saleRepId);
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

    /**
     * Add "sale_rep_id" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $saleRepId = $order->getData(self::SALE_REP_ID);
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes->setSaleRepId($saleRepId);
            $order->setExtensionAttributes($extensionAttributes);
        }

        return $searchResult;
    }
}