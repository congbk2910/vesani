<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Blacklist;

use Amasty\Acart\Controller\Adminhtml\Blacklist;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Index extends Blacklist
{
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Acart::acart_blacklist');
        $resultPage->addBreadcrumb(__('Marketing'), __('Marketing'));
        $resultPage->getConfig()->getTitle()->prepend(__('Blacklist'));

        return $resultPage;
    }
}
