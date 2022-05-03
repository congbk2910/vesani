<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

abstract class Rule extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_Acart::acart_rule';

    protected function initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Acart::acart_rule');
        $resultPage->addBreadcrumb(__('Marketing'), __('Marketing'));
        $resultPage->addBreadcrumb(__('Abandoned Cart Campaigns'), __('Abandoned Cart Campaigns'));

        return $resultPage;
    }
}
