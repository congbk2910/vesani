<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Magento\Quote\Api\Data\CartInterface;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Psr\Log\LoggerInterface;

class Quote implements EntityDataSenderInterface
{
    /**
     * @var RequestInterface
     */
    protected $quoteRequest;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param RequestInterface $quoteRequest
     * @param ImportStatus $importStatus
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $quoteRequest,
        ImportStatus $importStatus,
        LoggerInterface $logger
    ) {
        $this->quoteRequest = $quoteRequest;
        $this->importStatus = $importStatus;
        $this->logger = $logger;
    }

    /**
     * @param CartInterface $quote
     * @return null|string
     */
    public function send($quote)
    {
        try {
            $postStatus = $quote->getOmnisendPostStatus();

            if ($postStatus) {
                return $this->putFirst($quote);
            }
            return $this->postFirst($quote);
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }

    }

    /**
     * @param int $quoteId
     * @param int $storeId
     * @return int|null
     */
    public function delete($quoteId, $storeId)
    {
        try {
            if (!$quoteId || !$storeId) {
                return null;
            }

            $response = $this->quoteRequest->delete($quoteId, $storeId);

            if ($response === null) {
                return null;
            }

            return 1;
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }

    }

    /**
     * @param CartInterface $quote
     * @return null|string
     */
    protected function postFirst($quote)
    {
        $response = $this->quoteRequest->post($quote, $quote->getStoreId());

        if (!$this->importStatus->getImportStatus($response)) {
            return $this->quoteRequest->put($quote->getId(), $quote, $quote->getStoreId());
        }

        return $response;
    }

    /**
     * @param CartInterface $quote
     * @return null|string
     */
    protected function putFirst($quote)
    {
        $response = $this->quoteRequest->put($quote->getId(), $quote, $quote->getStoreId());
        if (!$this->importStatus->getImportStatus($response, true)) {
            return $this->quoteRequest->post($quote, $quote->getStoreId());
        }

        return $response;
    }
}
