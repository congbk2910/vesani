<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Queue;

use Amasty\Acart\Controller\Adminhtml\Queue;
use Amasty\Acart\Model\ResourceModel\History\CollectionFactory;
use Amasty\Acart\Api\QueueManagementInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Ui\Component\MassAction\Filter;

class MassCancel extends Queue
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->queueManagement = $queueManagement;
    }

    public function execute()
    {
        try {
            $this->filter->applySelectionOnTargetProvider();
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $canceled = $failed = 0;

            foreach ($collection->getItems() as $history) {
                try {
                    $this->queueManagement->markAsDeleted($history);
                    $canceled++;
                } catch (CouldNotSaveException $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                    $failed++;
                }
            }

            if ($canceled != 0) {
                $this->messageManager->addSuccessMessage(
                    __('%1 queue record(s) has been successfully canceled', $canceled)
                );
            }
            if ($failed != 0) {
                $this->messageManager->addErrorMessage(
                    __('%1 queue record(s) has been failed to cancel', $failed)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while canceling queue record(s). Please review the error log.')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/queue/index');
    }
}
