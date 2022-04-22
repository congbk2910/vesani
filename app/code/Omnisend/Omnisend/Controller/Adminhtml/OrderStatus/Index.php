<?php

namespace Omnisend\Omnisend\Controller\Adminhtml\OrderStatus;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Backend::content';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnisend_Omnisend::omnisend_order_status_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Order Statuses List'));

        return $resultPage;
    }
}
