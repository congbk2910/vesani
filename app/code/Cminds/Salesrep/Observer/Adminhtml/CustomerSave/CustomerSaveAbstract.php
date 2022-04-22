<?php

namespace Cminds\Salesrep\Observer\Adminhtml\CustomerSave;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Context;

abstract class CustomerSaveAbstract implements ObserverInterface
{
    protected $request;
    protected $objectManager;

    public function __construct(
        Context $context
    ) {
        $this->request = $context->getRequest();
        $this->objectManager = $context->getObjectManager();
    }

    public function execute(Observer $observer)
    {
        return $this;
    }
}
