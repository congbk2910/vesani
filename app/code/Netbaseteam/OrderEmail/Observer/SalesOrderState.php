<?php

namespace Netbaseteam\OrderEmail\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Message\ManagerInterface;


class SalesOrderState implements ObserverInterface
{
    const TOPIC_NAME = 'vesani.ordermail.queue.topic';

    public $publisher;

    public function __construct(
        PublisherInterface $publisher,
        ManagerInterface $messageManager,
        \Netbaseteam\OrderEmail\Model\Queue\Consumer $consumer
    )
    {
        $this->publisher = $publisher;
        $this->messageManager = $messageManager;
        $this->consumer = $consumer;
    }

    /**
     * Sales Order Place Success event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderId = (int)$order->getId();

        if ($order instanceof \Magento\Framework\Model\AbstractModel) {
            if ($order->getState() == 'complete') {
                $this->publisher->publish(self::TOPIC_NAME, $orderId);
                $this->messageManager->addSuccessMessage(__('Message is added to queue!'));
            }
        }
        return $this;

    }
}