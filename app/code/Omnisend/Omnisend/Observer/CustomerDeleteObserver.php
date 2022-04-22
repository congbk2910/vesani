<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Omnisend\Omnisend\Model\OmnisendContactProviderInterface;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Omnisend\Omnisend\Model\UnsubscriptionServiceInterface;
use Psr\Log\LoggerInterface;

class CustomerDeleteObserver implements ObserverInterface
{
    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var OmnisendContactProviderInterface
     */
    protected $omnisendContactProvider;

    /**
     * @var UnsubscriptionServiceInterface
     */
    protected $unsubscriptionService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param OmnisendContactProviderInterface $omnisendContactProvider
     * @param UnsubscriptionServiceInterface $unsubscriptionService
     * @param LoggerInterface $logger
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        OmnisendContactProviderInterface $omnisendContactProvider,
        UnsubscriptionServiceInterface $unsubscriptionService,
        LoggerInterface $logger,
        SubscriberFactory $subscriberFactory
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->omnisendContactProvider = $omnisendContactProvider;
        $this->unsubscriptionService = $unsubscriptionService;
        $this->logger = $logger;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        if (!$customer instanceof Customer) {
            return;
        }

        $customerId = $customer->getId();
        $contacts = $this->omnisendContactProvider->getOmnisendContactsByCustomerId($customerId);

        if (!count($contacts)) {
            return;
        }

        if (!$this->responseRateManager->check($customer->getStoreId())) {
            $this->logger->critical(
                'Request limit reached during customer unsubscription from Omnisend, for customer: ' . $customerId
            );

            return;
        }

        $customerSubscription = $this->subscriberFactory->create()->loadByCustomerId($customerId);
        $subscriberId = $customerSubscription->getId();
        $guestSubscribers = $this->omnisendContactProvider->getOmnisendGuestSubscribersBySubscriberId($subscriberId);

        $this->unsubscriptionService->unsubscribeFromAllStores(
            $customerSubscription,
            $contacts,
            $guestSubscribers
        );
    }
}
