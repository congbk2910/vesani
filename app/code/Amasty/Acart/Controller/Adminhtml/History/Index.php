<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\History;

use Amasty\Acart\Controller\Adminhtml\History;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Amasty\Acart\Model\Indexer;

class Index extends History
{
    /**
     * @var Indexer
     */
    private $indexer;

    public function __construct(
        Action\Context $context,
        Indexer $indexer
    ) {
        parent::__construct($context);
        $this->indexer = $indexer;
    }

    public function execute()
    {
        try {
            $this->indexer->run();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Error. Please see the log for more information.')
            );
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Acart::acart_history');
        $resultPage->addBreadcrumb(__('Marketing'), __('Marketing'));
        $resultPage->getConfig()->getTitle()->prepend(__('History'));

        return $resultPage;
    }
}
