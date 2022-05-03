<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Queue;

use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Controller\Adminhtml\Queue;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;

class Edit extends Queue
{
    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    public function __construct(
        Action\Context $context,
        HistoryRepositoryInterface $historyRepository
    ) {
        parent::__construct($context);
        $this->historyRepository = $historyRepository;
    }

    public function execute()
    {
        $historyId = (int)$this->getRequest()->getParam('id');
        try {
            $history = $this->historyRepository->getById($historyId);
        } catch (NotFoundException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while editing the queue.'));

            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Acart::acart_rule');
        $resultPage->setActiveMenu('Amasty_Acart::acart');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit queue item #%1', $history->getHistoryId()));

        return $resultPage;
    }
}
