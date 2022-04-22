<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Product\Commission;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Commission group grid action
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class Index extends AbstractGroup
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cminds_Salesrep::commission_groups');
        $resultPage->addBreadcrumb(__('Manage Product Commission Groups'), __('Manage Product Commission Groups'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Product Commission Groups'));
        return $resultPage;
    }
}
