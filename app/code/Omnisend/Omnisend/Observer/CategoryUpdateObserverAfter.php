<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Omnisend\Omnisend\Helper\CategoryPostStatusHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\CategoryAttributeUpdater;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Category as CategoryDataSender;
use Omnisend\Omnisend\Setup\UpgradeSchema;

/**
 * Class CategoryPrepareSaveObserver
 * @package Omnisend\Omnisend\Observer
 */
class CategoryUpdateObserverAfter implements ObserverInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryPostStatusHelper
     */
    protected $categoryPostStatusHelper;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var CategoryAttributeUpdater
     */
    protected $categoryAttributeUpdater;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var CategoryDataSender
     */
    protected $categoryDataSender;

    /**
     * CategoryPrepareSaveObserver constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryPostStatusHelper $categoryPostStatusHelper
     * @param ImportStatus $importStatus
     * @param CategoryAttributeUpdater $categoryAttributeUpdater
     * @param GeneralConfig $generalConfig
     * @param CategoryDataSender $categoryDataSender
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryPostStatusHelper $categoryPostStatusHelper,
        ImportStatus $importStatus,
        CategoryAttributeUpdater $categoryAttributeUpdater,
        GeneralConfig $generalConfig,
        CategoryDataSender $categoryDataSender
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryPostStatusHelper = $categoryPostStatusHelper;
        $this->importStatus = $importStatus;
        $this->categoryAttributeUpdater = $categoryAttributeUpdater;
        $this->generalConfig = $generalConfig;
        $this->categoryDataSender = $categoryDataSender;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getData('category');

        if (!$this->generalConfig->getIsRealTimeSynchronizationEnabled()) {
            return;
        }

        $storeIds = $category->getStoreIds();

        if (!is_array($storeIds)) {
            return;
        }

        foreach ($storeIds as $storeId) {
            $storeCategory = $this->categoryRepository->get($category->getId(), $storeId);
            $this->processCategory($storeCategory);
        }
    }

    /**
     * @param CategoryInterface $category
     * @throws NoSuchEntityException
     */
    protected function processCategory($category)
    {
        $response = $this->categoryDataSender->send($category);
        $isImported = $this->importStatus->getImportStatus($response);
        $this->categoryAttributeUpdater->setIsImported($category->getId(), $isImported, $category->getStoreId());

        $postStatus = $category->getCustomAttribute(UpgradeSchema::OMNISEND_POST_STATUS);

        if (!$this->categoryPostStatusHelper->isPosted($postStatus) && $isImported) {
            $this->categoryAttributeUpdater->setPostStatus($category->getId(), 1, $category->getStoreId());
        }
    }
}
