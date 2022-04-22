<?php

namespace Cminds\Salesrep\Observer\Adminhtml\CustomerSave;

use Magento\Framework\App\Action\Context;
use Psr\Log\LoggerInterface;

class SaveSalesrep extends CustomerSaveAbstract
{
    private $customerRepositoryInterface;
    private $loggerInterface;
    private $authSession;

    public function __construct(
        Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customer,
        \Magento\Backend\Model\Auth\Session $authSession,
        LoggerInterface $loggerInterface
    ) {
        parent::__construct(
            $context
        );
        $this->customerRepositoryInterface = $customer;
        $this->loggerInterface = $loggerInterface;
        $this->authSession = $authSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customer = $observer->getCustomer();
            $postData = $this->request->getParams();

            if (isset($postData['customer']) && !isset($postData['customer']['entity_id'])) {
                $postData['salesrep_rep_id'] = (int)$this->authSession->getUser()->getId();
            }

            $customerModel = $this->customerRepositoryInterface
                ->getById($customer->getId());

            if (isset($postData['salesrep_rep_id'])) {
                $customerModel->setCustomAttribute(
                    'salesrep_rep_id',
                    $postData['salesrep_rep_id']
                );
            }
            $this->customerRepositoryInterface->save($customerModel);
        } catch (\Exception $e) {
            $this->loggerInterface->debug($e->getFile() . $e->getLine() . $e->getMessage());
        }
    }
}
