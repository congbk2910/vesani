<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Magento\Sales\Api\Data\OrderInterface;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;

use Psr\Log\LoggerInterface;

class Order implements EntityDataSenderInterface
{
    /**
     * @var RequestInterface
     */
    protected $orderRequest;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param RequestInterface $orderRequest
     * @param ImportStatus $importStatus
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $orderRequest,
        ImportStatus $importStatus,
        LoggerInterface $logger
    ) {
        $this->orderRequest = $orderRequest;
        $this->importStatus = $importStatus;
        $this->logger = $logger;
    }

    /**
     * @param OrderInterface $order
     * @return null|string
     */
    public function send($order)
    {
        try {
            $postStatus = $order->getOmnisendPostStatus();

            if ($postStatus) {
                return $this->putFirst($order);
            }

            return $this->postFirst($order);
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }

    }

    /**
     * @param OrderInterface $order
     * @return null|string
     */
    protected function postFirst($order)
    {
        $response = $this->orderRequest->post($order, $order->getStoreId());

        if (!$this->importStatus->getImportStatus($response)) {
            return $this->orderRequest->put($order->getEntityId(), $order, $order->getStoreId());
        }

        return $response;
    }

    /**
     * @param OrderInterface $order
     * @return null|string
     */
    protected function putFirst($order)
    {
        $response = $this->orderRequest->put($order->getEntityId(), $order, $order->getStoreId());

        if (!$this->importStatus->getImportStatus($response)) {
            return $this->orderRequest->post($order, $order->getStoreId());
        }

        return $response;
    }
}
