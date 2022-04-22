<?php

namespace Cminds\Salesrep\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;
use Cminds\Salesrep\Helper\Data as SalesRepHelper;

class Selectsalesrep extends \Magento\Framework\App\Action\Action
{
    protected $checkoutSession;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        SalesRepHelper $salesrepHelper
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->salesrepHelper = $salesrepHelper;
    }

    public function execute()
    {
        // check if module is enabled
        if ((bool) $this->salesrepHelper->isModuleEnabled() === false) {
            return true;
        }

        $data = $this->getRequest()->getParams();

        if (isset($data['selectedSalesrep'])) {
            $this->checkoutSession->setSelectedSalesrepId((int)$data['selectedSalesrep']);
        }
        echo json_encode(['success'=> true]);
    }
}