<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omnisend\Omnisend\Helper\ProductPostStatusHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Product as ProductDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Omnisend\Omnisend\Setup\UpgradeSchema;

class ProductUpdateObserverBefore implements ObserverInterface
{
    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

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
     * @var ProductPostStatusHelper
     */
    protected $productPostStatusHelper;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param ProductDataSender $productDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param ProductPostStatusHelper $productPostStatusHelper
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        ProductDataSender $productDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        ProductPostStatusHelper $productPostStatusHelper
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->productDataSender = $productDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->productPostStatusHelper = $productPostStatusHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if (!$this->responseRateManager->check($product->getStoreId()) ||
            $product->getStoreId() == 0 ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled()
        ) {
            $product->setCustomAttribute('is_imported', 0);

            return;
        }

        $response = $this->productDataSender->send($product);
        $isImported = $this->importStatus->getImportStatus($response);
        $product->setCustomAttribute('is_imported', $isImported);

        $postStatus = $product->getCustomAttribute(UpgradeSchema::OMNISEND_POST_STATUS);

        if (!$this->productPostStatusHelper->isPosted($postStatus) && $isImported) {
            $product->setCustomAttribute(UpgradeSchema::OMNISEND_POST_STATUS, 1);
        }
    }
}
