<?php

namespace Omnisend\Omnisend\Model\Attribute\IsImported;

use Magento\Framework\Exception\NoSuchEntityException;
use Omnisend\Omnisend\Model\ResourceModel\CategoryFactory;

class CategoryAttributeUpdater
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param $entityId
     * @param $isImported
     * @param $storeId
     * @throws NoSuchEntityException
     */
    public function setIsImported($entityId, $isImported, $storeId)
    {
        $category = $this->categoryFactory->create();
        $category->updateIsImported($entityId, $isImported, $storeId);
    }

    /**
     * @param int $entityId
     * @param int $postStatus
     * @param int $storeId
     * @throws NoSuchEntityException
     */
    public function setPostStatus($entityId, $postStatus, $storeId)
    {
        $category = $this->categoryFactory->create();
        $category->updatePostStatus($entityId, $postStatus, $storeId);
    }
}
