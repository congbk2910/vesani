<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\CustomerAttributeUpdater;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\CustomerEmailChangeHandlerInterface;
use Omnisend\Omnisend\Model\EntityDataSender\Customer as CustomerDataSender;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Psr\Log\LoggerInterface;

class CustomerUpdateObserver implements ObserverInterface
{
    const ARRAY_INDEX_CHANGE_EMAIL = 'change_email';

    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var CustomerDataSender
     */
    protected $customerDataSender;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var CustomerAttributeUpdater
     */
    protected $customerAttributeUpdater;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CustomerEmailChangeHandlerInterface
     */
    protected $customerEmailChangeHandler;

    /**
     * @var OmnisendContactEventDispatcher
     */
    protected $omnisendContactEventDispatcher;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param CustomerDataSender $customerDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param CustomerAttributeUpdater $customerAttributeUpdater
     * @param RequestInterface $request
     * @param CustomerEmailChangeHandlerInterface $customerEmailChangeHandler
     * @param OmnisendContactEventDispatcher $omnisendContactEventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        CustomerDataSender $customerDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        CustomerAttributeUpdater $customerAttributeUpdater,
        RequestInterface $request,
        CustomerEmailChangeHandlerInterface $customerEmailChangeHandler,
        OmnisendContactEventDispatcher $omnisendContactEventDispatcher,
        LoggerInterface $logger
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->customerDataSender = $customerDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->customerAttributeUpdater = $customerAttributeUpdater;
        $this->request = $request;
        $this->customerEmailChangeHandler = $customerEmailChangeHandler;
        $this->omnisendContactEventDispatcher = $omnisendContactEventDispatcher;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->logger->debug(self::class . "Newsletter Subscription Test");
        $customer = $this->getCustomer($observer);

        if (!$customer instanceof Customer) {
            return;
        }

        $customerId = $customer->getId();
        $storeId = $customer->getStoreId();

        if (!$this->responseRateManager->check($storeId) ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled()
        ) {
            $this->omnisendContactEventDispatcher->dispatchCustomerContactIdUpdateEvent($customerId, $storeId);
            $this->customerAttributeUpdater->setIsImported($customerId, 0);
            $this->processEmailChangedFlag($customerId);

            return;
        }

        if ($this->checkForEmailChange()) {
            $response = $this->customerEmailChangeHandler->handleEmailChange($customer);
        } else {
            $response = $this->customerDataSender->send($customer);
        }

        $isImported = $this->importStatus->getImportStatus($response);
        $this->customerAttributeUpdater->setIsImported($customerId, $isImported);
    }

    /**
     * @param Observer $observer
     * @return Customer|boolean
     */
    protected function getCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        if ($customer instanceof Customer) {
            return $customer;
        }

        $customerAddress = $observer->getEvent()->getCustomerAddress();

        if (!$customerAddress instanceof Address || !$addressId = $customerAddress->getEntityId()) {
            return false;
        }

        $customer = $customerAddress->getCustomer();

        if (!$customer) {
            return false;
        }

        if (!$customer->getDefaultShipping()) {
            $customer->setDefaultShipping($addressId);
        }

        return $customer;
    }

    /**
     * @param int $customerId
     */
    protected function processEmailChangedFlag($customerId)
    {
        if (!$this->checkForEmailChange()) {
            return;
        }

        $this->customerAttributeUpdater->setEmailChangedFlag($customerId, 1);
    }

    /**
     * @return bool
     */
    protected function checkForEmailChange()
    {
        $postValue = $this->request->getPostValue();

        if (is_array($postValue) && isset($postValue[self::ARRAY_INDEX_CHANGE_EMAIL])) {
            return true;
        }

        return false;
    }
}
