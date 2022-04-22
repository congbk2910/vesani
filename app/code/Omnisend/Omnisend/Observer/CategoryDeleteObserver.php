<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Model\EntityDataSender\Category as CategoryDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CategoryDeleteObserver
 * @package Omnisend\Omnisend\Observer
 */
class CategoryDeleteObserver implements ObserverInterface
{
    const DELETION_ERROR_MESSAGE = 'Category %s deletion is impossible, because request limit for store %s is reached.';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;
    /**
     * @var CategoryDataSender
     */
    protected $categoryDataSender;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CategoryDeleteObserver constructor.
     * @param StoreManagerInterface $storeManager
     * @param ResponseRateManagerInterface $responseRateManager
     * @param CategoryDataSender $categoryDataSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ResponseRateManagerInterface $responseRateManager,
        CategoryDataSender $categoryDataSender,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->responseRateManager = $responseRateManager;
        $this->categoryDataSender = $categoryDataSender;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getData('category');

        if (!$category || !$category instanceof CategoryInterface || !$categoryId = $category->getId()) {
            return;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $this->deleteCategory($categoryId, $store->getId());
        }
    }

    /**
     * @param $categoryId
     * @param $storeId
     */
    protected function deleteCategory($categoryId, $storeId)
    {
        if (!$this->responseRateManager->check($storeId)) {
            $this->logger->critical(sprintf(self::DELETION_ERROR_MESSAGE, $categoryId, $storeId));
            return;
        }
        $this->categoryDataSender->delete($categoryId, $storeId);
    }
}
