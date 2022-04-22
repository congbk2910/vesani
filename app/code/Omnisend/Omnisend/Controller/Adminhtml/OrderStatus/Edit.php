<?php

namespace Omnisend\Omnisend\Controller\Adminhtml\OrderStatus;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Api\OmnisendOrderStatusRepositoryInterface;
use Omnisend\Omnisend\Model\OmnisendOrderStatusFactory;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Backend::content';

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var OmnisendOrderStatusRepositoryInterface
     */
    private $omnisendOrderStatusRepository;

    /**
     * @var OmnisendOrderStatusFactory
     */
    private $omnisendOrderStatusFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param OmnisendOrderStatusRepositoryInterface $omnisendOrderStatusRepository
     * @param OmnisendOrderStatusFactory $omnisendOrderStatusFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        OmnisendOrderStatusRepositoryInterface $omnisendOrderStatusRepository,
        OmnisendOrderStatusFactory $omnisendOrderStatusFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->omnisendOrderStatusRepository = $omnisendOrderStatusRepository;
        $this->omnisendOrderStatusFactory = $omnisendOrderStatusFactory;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $statusId = $this->getRequest()->getParam(OmnisendOrderStatusInterface::STATUS);
        $orderStatus = $this->omnisendOrderStatusFactory->create();

        if ($statusId && ($orderStatus = $this->omnisendOrderStatusRepository->getById($statusId)) && !$orderStatus->getStatus()) {
            $this->messageManager->addError(__('This entry no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $data = $this->_getSession()->getFormData(true);

        if (!empty($data)) {
            $orderStatus->setData($data);
        }

        $this->coreRegistry->register('order_status', $orderStatus);
        $resultPage = $this->initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Order Statuses'));

        $resultPage->getConfig()
            ->getTitle()
            ->prepend($orderStatus->getStatus() ? $orderStatus->getStatus() : __('New Order Status'));

        return $resultPage;
    }

    /**
     * @return Page
     */
    protected function initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnisend_Omnisend::omnisend_order_status_list');

        return $resultPage;
    }
}
