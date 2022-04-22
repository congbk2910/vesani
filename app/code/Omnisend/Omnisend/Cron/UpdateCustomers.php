<?php

namespace Omnisend\Omnisend\Cron;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Helper\SearchCriteria\EntityInterface as EntitySearchCriteriaInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\AttributeUpdaterInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\CustomerEmailChangeHandlerInterface;
use Omnisend\Omnisend\Model\EntityDataSender\Customer as CustomerDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Omnisend\Omnisend\Setup\InstallData;

class UpdateCustomers
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var GeneralConfig
     */
    private $generalConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EntitySearchCriteriaInterface
     */
    private $entitySearchCriteria;

    /**
     * @var ResponseRateManagerInterface
     */
    private $responseRateManager;

    /**
     * @var CustomerDataSender
     */
    private $customerDataSender;

    /**
     * @var AttributeUpdaterInterface
     */
    private $customerAttributeUpdater;

    /**
     * @var ImportStatus
     */
    private $importStatus;

    /**
     * @var CustomerEmailChangeHandlerInterface
     */
    private $customerEmailChangeHandler;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param GeneralConfig $generalConfig
     * @param StoreManagerInterface $storeManager
     * @param EntitySearchCriteriaInterface $entitySearchCriteria
     * @param ResponseRateManagerInterface $responseRateManager
     * @param CustomerDataSender $customerDataSender
     * @param AttributeUpdaterInterface $customerAttributeUpdater
     * @param ImportStatus $importStatus
     * @param CustomerEmailChangeHandlerInterface $customerEmailChangeHandler
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        GeneralConfig $generalConfig,
        StoreManagerInterface $storeManager,
        EntitySearchCriteriaInterface $entitySearchCriteria,
        ResponseRateManagerInterface $responseRateManager,
        CustomerDataSender $customerDataSender,
        AttributeUpdaterInterface $customerAttributeUpdater,
        ImportStatus $importStatus,
        CustomerEmailChangeHandlerInterface $customerEmailChangeHandler
    ) {
        $this->customerRepository = $customerRepository;
        $this->generalConfig = $generalConfig;
        $this->storeManager = $storeManager;
        $this->entitySearchCriteria = $entitySearchCriteria;
        $this->responseRateManager = $responseRateManager;
        $this->customerDataSender = $customerDataSender;
        $this->customerAttributeUpdater = $customerAttributeUpdater;
        $this->importStatus = $importStatus;
        $this->customerEmailChangeHandler = $customerEmailChangeHandler;
    }

    /**
     * @throws LocalizedException
     */
    public function execute()
    {
        if (!$this->generalConfig->getIsCronSynchronizationEnabled()) {
            return;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeId = $store->getId();
            $isImported = 0;

            $searchCriteria = $this->entitySearchCriteria->getEntityInStoreByImportStatusSearchCriteria(
                $isImported,
                $storeId
            );

            $customers = $this->customerRepository
                ->getList($searchCriteria)
                ->getItems();

            if (!$this->sendCustomers($customers, $storeId)) {
                return;
            }
        }
    }

    /**
     * @param CustomerInterface[] $customers
     * @param $storeId
     * @return bool
     */
    public function sendCustomers($customers, $storeId)
    {
        if (!$this->responseRateManager->check($storeId)) {
            return false;
        }

        foreach ($customers as $customer) {
            $this->processCustomer($customer);
        }

        return true;
    }

    /**
     * @param CustomerInterface $customer
     */
    public function processCustomer(CustomerInterface $customer)
    {
        $emailChangedAttribute = $customer->getCustomAttribute(InstallData::EMAIL_CHANGED);

        if ($emailChangedAttribute && $emailChangedAttribute->getValue()) {
            $response = $this->customerEmailChangeHandler->handleEmailChange($customer);
        } else {
            $response = $this->customerDataSender->send($customer);
        }

        $isImported = $this->importStatus->getImportStatus($response);
        $this->customerAttributeUpdater->setIsImported($customer->getId(), $isImported);
    }
}
