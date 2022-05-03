<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Queue;

use Amasty\Acart\Api\QueueManagementInterface;
use Amasty\Acart\Controller\Adminhtml\Queue;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Queue
{
    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    public function __construct(
        Action\Context $context,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->queueManagement = $queueManagement;
    }

    public function execute()
    {
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->queueManagement->markAsDeletedById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the queue.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete the queue right now. Please review the log and try again.')
                );

                return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/edit', ['id' => $id]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a queue to delete.'));
        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/');
    }
}
