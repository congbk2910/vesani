<?php

namespace Omnisend\Omnisend\Cron;

use Exception;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Helper\CategoryPostStatusHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\CategoryAttributeUpdater;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Category as CategoryDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Omnisend\Omnisend\Setup\InstallData;
use Omnisend\Omnisend\Setup\UpgradeSchema;

/**
 * Class UpdateCategories
 * @package Omnisend\Omnisend\Cron
 */
class UpdateCategories
{
    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var CategoryDataSender
     */
    protected $categoryDataSender;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var CategoryPostStatusHelper
     */
    protected $categoryPostStatusHelper;

    /**
     * @var CategoryAttributeUpdater
     */
    protected $categoryAttributeUpdater;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * UpdateCategories constructor.
     * @param GeneralConfig $generalConfig
     * @param StoreManagerInterface $storeManager
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $collectionFactory
     * @param ResponseRateManagerInterface $responseRateManager
     * @param CategoryDataSender $categoryDataSender
     * @param ImportStatus $importStatus
     * @param CategoryAttributeUpdater $categoryAttributeUpdater
     * @param CategoryPostStatusHelper $categoryPostStatusHelper
     */
    public function __construct(
        GeneralConfig $generalConfig,
        StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        CollectionFactory $collectionFactory,
        ResponseRateManagerInterface $responseRateManager,
        CategoryDataSender $categoryDataSender,
        ImportStatus $importStatus,
        CategoryAttributeUpdater $categoryAttributeUpdater,
        CategoryPostStatusHelper $categoryPostStatusHelper
    ) {
        $this->generalConfig = $generalConfig;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->responseRateManager = $responseRateManager;
        $this->categoryDataSender = $categoryDataSender;
        $this->importStatus = $importStatus;
        $this->categoryPostStatusHelper = $categoryPostStatusHelper;
        $this->categoryAttributeUpdater = $categoryAttributeUpdater;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        if (!$this->generalConfig->getIsCronSynchronizationEnabled()) {
            return;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();

            $collection = $this->collectionFactory->create()
                ->addAttributeToSelect('*')
                ->setStore($storeId)
                ->addAttributeToFilter(InstallData::IS_IMPORTED, 0)
                ->setPageSize($this->generalConfig->getMaximumEntitiesPerCron());

            /** @var CategoryInterface[] $categories */
            $categories = $collection->getItems();

            if (!$this->sendCategories($categories, $storeId)) {
                return;
            }
        }
    }

    /**
     * @param CategoryInterface[] $categories
     * @param $storeId
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendCategories($categories, $storeId)
    {
        foreach ($categories as $category) {
            if (!$this->responseRateManager->check($storeId)) {
                return false;
            }

            $this->processCategory($category, $storeId);
        }
        return true;
    }

    /**
     * @param CategoryInterface $category
     * @param $storeId
     * @throws NoSuchEntityException
     */
    public function processCategory($category, $storeId)
    {
        $category->setData('store_id', $storeId);
        $response = $this->categoryDataSender->send($category);
        $isImported = $this->importStatus->getImportStatus($response);
        $this->categoryAttributeUpdater->setIsImported($category->getId(), $isImported, $storeId);

        $postStatus = $category->getCustomAttribute(UpgradeSchema::OMNISEND_POST_STATUS);

        if (!$this->categoryPostStatusHelper->isPosted($postStatus) && $isImported) {
            $this->categoryAttributeUpdater->setPostStatus($category->getId(), 1, $storeId);
        }
    }
}
