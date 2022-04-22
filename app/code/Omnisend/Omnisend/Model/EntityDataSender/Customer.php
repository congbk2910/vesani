<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;
use Magento\Customer\Api\Data\CustomerInterface;
use Omnisend\Omnisend\Api\OmnisendContactRepositoryInterface;
use Omnisend\Omnisend\Helper\SearchCriteria\OmnisendContact as OmnisendContactSearchCriteria;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\OmnisendContact;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;
use Omnisend\Omnisend\Model\OmnisendContactFactory;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Magento\Framework\App\RequestInterface  as AppRequestInterface;

class Customer implements EntityDataSenderInterface
{
    /**
     * @var RequestInterface
     */
    protected $customerRequest;

    /**
     * @var OmnisendContactFactory
     */
    protected $omnisendContactFactory;

    /**
     * @var OmnisendContactRepositoryInterface
     */
    protected $omnisendContactRepository;

    /**
     * @var OmnisendContactSearchCriteria
     */
    protected $omnisendContactSearchCriteria;

    /**
     * @var OmnisendContactEventDispatcher
     */
    protected $omnisendContactEventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var AppRequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $customerRequest
     * @param OmnisendContactFactory $omnisendContactFactory
     * @param OmnisendContactRepositoryInterface $omnisendContactRepository
     * @param OmnisendContactSearchCriteria $omnisendContactSearchCriteria
     * @param OmnisendContactEventDispatcher $omnisendContactEventDispatcher
     * @param ImportStatus $importStatus
     * @param LoggerInterface $logger
     * @param AppRequestInterface $request
     * @param Json $serializer
     */
    public function __construct(
        RequestInterface $customerRequest,
        OmnisendContactFactory $omnisendContactFactory,
        OmnisendContactRepositoryInterface $omnisendContactRepository,
        OmnisendContactSearchCriteria $omnisendContactSearchCriteria,
        OmnisendContactEventDispatcher $omnisendContactEventDispatcher,
        ImportStatus $importStatus,
        LoggerInterface $logger,
        AppRequestInterface  $request,
        Json $serializer
    ) {
        $this->customerRequest = $customerRequest;
        $this->omnisendContactFactory = $omnisendContactFactory;
        $this->omnisendContactRepository = $omnisendContactRepository;
        $this->omnisendContactSearchCriteria = $omnisendContactSearchCriteria;
        $this->omnisendContactEventDispatcher = $omnisendContactEventDispatcher;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->importStatus = $importStatus;
        $this->request = $request;
    }

    /**
     * @param CustomerInterface $customer
     * @return null|string|array
     */
    public function send($customer)
    {
        try {
            $uri = $this->request->getRequestUri();
            $this->logger->debug(self::class . ": " . $uri);
            if (trim($uri, '/') == 'newsletter/manage/save') {
                return null;
            }

            $response = $this->customerRequest->post($customer, $customer->getStoreId());

            if (!$this->importStatus->getImportStatus($response)) {
                return $response;
            }

            $searchCriteria = $this->omnisendContactSearchCriteria
                ->getOmnisendContactInStoreByCustomerIdSearchCriteria(
                    $customer->getId(),
                    $customer->getStoreId()
                );

            /** @var OmnisendContact $omnisendContact */
            $omnisendContact = $this->omnisendContactRepository->getList($searchCriteria)->getFirstItem();

            $responseArr = $this->serializer->unserialize($response);

            if (!$omnisendContact->getData()) {
                $omnisendContact = $this->omnisendContactFactory->create();
            }

            $omnisendContact->setCustomerId($customer->getId());
            $omnisendContact->setOmnisendId($responseArr['contactID']);
            $omnisendContact->setStoreId($customer->getStoreId());

            $this->omnisendContactRepository->save($omnisendContact);
            $this->omnisendContactEventDispatcher->dispatchContactAccessEvent($omnisendContact->getOmnisendId());

            return $response;
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }
    }
}
