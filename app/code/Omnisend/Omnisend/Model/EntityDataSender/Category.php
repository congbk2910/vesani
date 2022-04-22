<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Magento\Catalog\Api\Data\CategoryInterface;
use Omnisend\Omnisend\Helper\CategoryPostStatusHelper;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\RequestService;
use Omnisend\Omnisend\Setup\UpgradeSchema;
use Psr\Log\LoggerInterface;

/**
 * Class Category
 * @package Omnisend\Omnisend\Model\EntityDataSender
 */
class Category implements EntityDataSenderInterface
{
    /**
     * @var RequestInterface
     */
    protected $categoryRequest;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var CategoryPostStatusHelper
     */
    protected $categoryPostStatusHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Category constructor.
     * @param RequestInterface $categoryRequest
     * @param ImportStatus $importStatus
     * @param CategoryPostStatusHelper $categoryPostStatusHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $categoryRequest,
        ImportStatus $importStatus,
        CategoryPostStatusHelper $categoryPostStatusHelper,
        LoggerInterface $logger
    ) {
        $this->categoryRequest = $categoryRequest;
        $this->importStatus = $importStatus;
        $this->categoryPostStatusHelper = $categoryPostStatusHelper;
        $this->logger = $logger;
    }

    /**
     * @param CategoryInterface $category
     * @return string|void|null
     */
    public function send($category)
    {
        try {
            $postStatus = $category->getCustomAttribute(UpgradeSchema::OMNISEND_POST_STATUS);

            if ($this->categoryPostStatusHelper->isPosted($postStatus)) {
                return $this->putFirst($category);
            }

            return $this->postFirst($category);
        } catch (\Exception $e) {
            $this->logger->error(self::class . "::send(): " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param CategoryInterface $category
     * @return null|string
     */
    protected function postFirst($category)
    {
        $postResponse = $this->categoryRequest->post($category, $category->getStoreId());

        if (!$this->importStatus->getImportStatus($postResponse)) {
            return $this->categoryRequest->put($category->getId(), $category, $category->getStoreId());
        }

        return $postResponse;
    }

    /**
     * @param CategoryInterface $category
     * @return null|string
     */
    protected function putFirst($category)
    {
        $response = $this->categoryRequest->put($category->getId(), $category, $category->getStoreId());

        if (!$this->importStatus->getImportStatus($response)) {
            return $this->categoryRequest->post($category, $category->getStoreId());
        }

        return $response;
    }

    /**
     * @param int $categoryId
     * @param int $storeId
     */
    public function delete($categoryId, $storeId)
    {
        try {
            if (!$categoryId || !$storeId) {
                return;
            }

            $response = $this->categoryRequest->get($categoryId, $storeId);

            if ($response === null || $response == RequestService::HTTP_RESPONSE_NOT_FOUND) {
                return;
            }
            $this->categoryRequest->delete($categoryId, $storeId);
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return;
        }
    }
}
