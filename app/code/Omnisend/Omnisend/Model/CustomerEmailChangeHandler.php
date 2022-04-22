<?php

namespace Omnisend\Omnisend\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Omnisend\Omnisend\Model\Attribute\IsImported\CustomerAttributeUpdater;
use Omnisend\Omnisend\Model\Attribute\IsImported\SubscriberAttributeUpdater;
use Omnisend\Omnisend\Model\EntityDataSender\Customer as CustomerEntitySender;
use Psr\Log\LoggerInterface;

class CustomerEmailChangeHandler implements CustomerEmailChangeHandlerInterface
{
    /**
     * @var CustomerEntitySender
     */
    protected $customerEntitySender;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerAttributeUpdater
     */
    protected $customerAttributeUpdater;

    /**
     * @var OmnisendContactProviderInterface
     */
    protected $omnisendContactProvider;

    /**
     * @var UnsubscriptionServiceInterface
     */
    protected $unsubscriptionService;

    /**
     * @var SubscriberAttributeUpdater
     */
    protected $subscriberAttributeUpdater;

    /**
     * @var SubscriptionProviderInterface
     */
    protected $subscriptionProvider;

    /**
     * @param CustomerEntitySender $customerEntitySender
     * @param LoggerInterface $logger
     * @param CustomerAttributeUpdater $customerAttributeUpdater
     * @param OmnisendContactProviderInterface $omnisendContactProvider
     * @param UnsubscriptionServiceInterface $unsubscriptionService
     * @param SubscriberAttributeUpdater $subscriberAttributeUpdater
     * @param SubscriptionProviderInterface $subscriptionProvider
     */
    public function __construct(
        CustomerEntitySender $customerEntitySender,
        LoggerInterface $logger,
        CustomerAttributeUpdater $customerAttributeUpdater,
        OmnisendContactProviderInterface $omnisendContactProvider,
        UnsubscriptionServiceInterface $unsubscriptionService,
        SubscriberAttributeUpdater $subscriberAttributeUpdater,
        SubscriptionProviderInterface $subscriptionProvider
    ) {
        $this->customerEntitySender = $customerEntitySender;
        $this->logger = $logger;
        $this->customerAttributeUpdater = $customerAttributeUpdater;
        $this->omnisendContactProvider = $omnisendContactProvider;
        $this->unsubscriptionService = $unsubscriptionService;
        $this->subscriberAttributeUpdater = $subscriberAttributeUpdater;
        $this->subscriptionProvider = $subscriptionProvider;
    }

    /**
     * @param Customer|CustomerInterface $customer
     * @return null|string
     */
    public function handleEmailChange($customer)
    {
        if (!$customer instanceof Customer && !$customer instanceof CustomerInterface) {
            return null;
        }

        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();
        $storeId = $customer->getStoreId();

        $contacts = $this->omnisendContactProvider->getOmnisendContactsByCustomerId($customerId);

        if (!count($contacts)) {
            return $this->postCustomerWithChangedEmail($customer);
        }

        $customerSubscription = $this->subscriptionProvider->getSubscription($customerId, $customerEmail, $storeId);
        $subscriberId = $customerSubscription->getId();
        $guestSubscribers = $this->omnisendContactProvider->getOmnisendGuestSubscribersBySubscriberId($subscriberId);

        $wasSuccess = $this->unsubscriptionService->unsubscribeFromAllStores(
            $customerSubscription,
            $contacts,
            $guestSubscribers
        );

        if ($wasSuccess && $subscriberId) {
            $this->subscriberAttributeUpdater->updatePreviousSubscriberStatus($subscriberId, null);
        }

        if ($wasSuccess) {
            return $this->postCustomerWithChangedEmail($customer);
        }

        $this->customerAttributeUpdater->setEmailChangedFlag($customerId, 1);
        $this->logger->critical('Email change data synchronisation was not successful for customer - ' . $customerId);

        return null;
    }

    /**
     * @param Customer|CustomerInterface $customer
     * @return null|string
     */
    protected function postCustomerWithChangedEmail($customer)
    {
        $this->customerAttributeUpdater->setEmailChangedFlag($customer->getId(), 0);

        return $this->customerEntitySender->send($customer);
    }
}
