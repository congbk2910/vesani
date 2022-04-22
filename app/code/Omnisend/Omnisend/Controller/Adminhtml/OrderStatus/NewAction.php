<?php

namespace Omnisend\Omnisend\Controller\Adminhtml\OrderStatus;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class NewAction extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Backend::content';

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(Context $context, ForwardFactory $resultForwardFactory)
    {
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
