<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Model\EntityDataSender\Product as ProductEntityDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Psr\Log\LoggerInterface;

class ProductDeleteObserver implements ObserverInterface
{
    const DELETION_ERROR_MESSAGE = 'Product %s deletion is impossible, because request limit for store %s is reached.';

    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductEntityDataSender
     */
    protected $productEntityDataSender;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param ProductEntityDataSender $productEntityDataSender
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        ProductEntityDataSender $productEntityDataSender
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->productEntityDataSender = $productEntityDataSender;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if (!$product || !$product instanceof ProductInterface || !$productId = $product->getId()) {
            return;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $this->deleteStoreProduct($productId, $store->getId());
        }
    }

    /**
     * @param int $productId
     * @param int $storeId
     */
    protected function deleteStoreProduct($productId, $storeId)
    {
        if (!$this->responseRateManager->check($storeId)) {
            $this->logger->critical(sprintf(self::DELETION_ERROR_MESSAGE, $productId, $storeId));
            return;
        }

        $this->productEntityDataSender->delete($productId, $storeId);
    }
}
