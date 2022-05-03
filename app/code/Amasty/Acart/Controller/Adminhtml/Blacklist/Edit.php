<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Blacklist;

use Amasty\Acart\Controller\Adminhtml\Blacklist;
use Amasty\Acart\Api\BlacklistRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;

class Edit extends Blacklist
{
    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;

    public function __construct(
        Action\Context $context,
        BlacklistRepositoryInterface $blacklistRepository
    ) {
        parent::__construct($context);
        $this->blacklistRepository = $blacklistRepository;
    }

    public function execute()
    {
        $title = __('New Blacklist Email');

        if ($blacklistId = (int)$this->getRequest()->getParam('id')) {
            try {
                $blacklist = $this->blacklistRepository->getById($blacklistId);
                $title = __('Editing Blacklist Email %1', $blacklist->getName());
            } catch (NotFoundException $e) {
                $this->messageManager->addErrorMessage(__('This blacklist email no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('amasty_acart/*/index');

                return $resultRedirect;
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Acart::acart_blacklist');
        $resultPage->setActiveMenu('Amasty_Acart::acart');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
