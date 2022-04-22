<?php

namespace Omnisend\Omnisend\Observer;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omnisend\Omnisend\Helper\ProductPostStatusHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Attribute\IsImported\ProductAttributeUpdater;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Product as ProductDataSender;
use Omnisend\Omnisend\Setup\UpgradeSchema;

class ProductUpdateObserverAfter implements ObserverInterface
{
    const STORE_ID_FROM_AFTER = 'store_id_from_after';

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductDataSender
     */
    protected $productDataSender;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var ProductAttributeUpdater
     */
    protected $productAttributeUpdater;

    /**
     * @var ProductPostStatusHelper
     */
    protected $productPostStatusHelper;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductDataSender $productDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param ProductAttributeUpdater $productAttributeUpdater
     * @param ProductPostStatusHelper $productPostStatusHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductDataSender $productDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        ProductAttributeUpdater $productAttributeUpdater,
        ProductPostStatusHelper $productPostStatusHelper
    ) {
        $this->productRepository = $productRepository;
        $this->productDataSender = $productDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->productAttributeUpdater = $productAttributeUpdater;
        $this->productPostStatusHelper = $productPostStatusHelper;
    }

    /**
     * @param Observer $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product->getStoreId() != 0 || !$this->generalConfig->getIsRealTimeSynchronizationEnabled()) {
            return;
        }

        $storeIds = $product->getStoreIds();

        if (!is_array($storeIds)) {
            return;
        }

        foreach ($storeIds as $storeId) {
            $storeProduct = $this->productRepository->getById($product->getId(), false, $storeId);
            $this->processProduct($storeProduct);
        }
    }

    /**
     * @param ProductInterface $product
     * @throws Exception
     */
    protected function processProduct(ProductInterface $product)
    {
        $response = $this->productDataSender->send($product);
        $isImported = $this->importStatus->getImportStatus($response);
        $this->productAttributeUpdater->setIsImported($product->getId(), $isImported, $product->getStoreId());

        $postStatus = $product->getCustomAttribute(UpgradeSchema::OMNISEND_POST_STATUS);

        if (!$this->productPostStatusHelper->isPosted($postStatus) && $isImported) {
            $this->productAttributeUpdater->setPostStatus($product->getId(), 1, $product->getStoreId());
        }
    }
}
