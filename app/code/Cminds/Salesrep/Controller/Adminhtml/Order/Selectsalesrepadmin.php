<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Order;

class Selectsalesrepadmin extends \Magento\Backend\App\Action
{
    protected $checkoutSession;
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();

        if (isset($data['selectedSalesrep'])) {
            $this->checkoutSession->setSelectedSalesrepId((int)$data['selectedSalesrep']);
        }

        $result = $this->resultJsonFactory->create();

        $result->setData(['success' => true]);

        return $result;
    }
}
