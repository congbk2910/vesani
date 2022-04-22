<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Magento\Catalog\Api\Data\ProductInterface;
use Omnisend\Omnisend\Helper\ProductPostStatusHelper;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\RequestService;
use Omnisend\Omnisend\Setup\UpgradeSchema;
use Psr\Log\LoggerInterface;

class Product implements EntityDataSenderInterface
{
    /**
     * @var RequestInterface
     */
    protected $productRequest;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var ProductPostStatusHelper
     */
    protected $productPostStatusHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param RequestInterface $productRequest
     * @param ImportStatus $importStatus
     * @param ProductPostStatusHelper $productPostStatusHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $productRequest,
        ImportStatus $importStatus,
        ProductPostStatusHelper $productPostStatusHelper,
        LoggerInterface $logger
    ) {
        $this->productRequest = $productRequest;
        $this->importStatus = $importStatus;
        $this->productPostStatusHelper = $productPostStatusHelper;
        $this->logger = $logger;
    }

    /**
     * @param ProductInterface $product
     * @return null|string
     */
    public function send($product)
    {
        try {
            $postStatus = $product->getCustomAttribute(UpgradeSchema::OMNISEND_POST_STATUS);

            if ($this->productPostStatusHelper->isPosted($postStatus)) {
                return $this->putFirst($product);
            }

            return $this->postFirst($product);
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param int $productId
     * @param int $storeId
     */
    public function delete($productId, $storeId)
    {
        try {
            if (!$productId || !$storeId) {
                return;
            }

            $response = $this->productRequest->get($productId, $storeId);

            if ($response === null || $response == RequestService::HTTP_RESPONSE_NOT_FOUND) {
                return;
            }
            $this->productRequest->delete($productId, $storeId);
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return;
        }
    }

    /**
     * @param ProductInterface $product
     * @return null|string
     */
    protected function postFirst($product)
    {
        $postResponse = $this->productRequest->post($product, $product->getStoreId());
        if (!$this->importStatus->getImportStatus($postResponse)) {
            return $this->productRequest->put($product->getId(), $product, $product->getStoreId());
        }

        return $postResponse;
    }

    /**
     * @param ProductInterface $product
     * @return null|string
     */
    protected function putFirst($product)
    {
        $response = $this->productRequest->put($product->getId(), $product, $product->getStoreId());

        if (!$this->importStatus->getImportStatus($response)) {
            return $this->productRequest->post($product, $product->getStoreId());
        }

        return $response;
    }
}
