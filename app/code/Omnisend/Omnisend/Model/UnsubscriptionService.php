<?php

namespace Omnisend\Omnisend\Model;

use Exception;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Newsletter\Model\Subscriber;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;
use Omnisend\Omnisend\Api\OmnisendContactRepositoryInterface;
use Omnisend\Omnisend\Api\OmnisendGuestSubscriberRepositoryInterface;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Observer\CustomerUpdateObserver;
use Psr\Log\LoggerInterface;

class UnsubscriptionService implements UnsubscriptionServiceInterface
{
    /**
     * @var OmnisendContactRepositoryInterface
     */
    protected $contactRepository;

    /**
     * @var OmnisendGuestSubscriberRepositoryInterface
     */
    protected $guestSubscriberRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RequestInterface
     */
    protected $subscriberRequest;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @param OmnisendContactRepositoryInterface $contactRepository
     * @param OmnisendGuestSubscriberRepositoryInterface $guestSubscriberRepository
     * @param LoggerInterface $logger
     * @param RequestInterface $subscriberRequest
     * @param ImportStatus $importStatus
     */
    public function __construct(
        OmnisendContactRepositoryInterface $contactRepository,
        OmnisendGuestSubscriberRepositoryInterface $guestSubscriberRepository,
        LoggerInterface $logger,
        RequestInterface $subscriberRequest,
        ImportStatus $importStatus
    ) {
        $this->contactRepository = $contactRepository;
        $this->guestSubscriberRepository = $guestSubscriberRepository;
        $this->logger = $logger;
        $this->subscriberRequest = $subscriberRequest;
        $this->importStatus = $importStatus;
    }

    /**
     * @param Subscriber $subscription
     * @param OmnisendContactInterface[] $contacts
     * @param OmnisendGuestSubscriberInterface[] $guestSubscribers
     * @return bool
     */
    public function unsubscribeFromAllStores($subscription, $contacts, $guestSubscribers)
    {
        if (!$subscription instanceof Subscriber) {
            return false;
        }

        $unsubscribeStatus = true;

        if ($subscription->getId()) {
            $subscription->setData(CustomerUpdateObserver::ARRAY_INDEX_CHANGE_EMAIL, 1);
        }

        foreach ($contacts as $contact) {
            $unsubscribeSuccessful = $this->unsubscribeFromStore($subscription, $contact);

            if (!$unsubscribeSuccessful) {
                $unsubscribeStatus = false;
            }
        }

        foreach ($guestSubscribers as $guestSubscriber) {
            $this->deleteGuestSubscriber($guestSubscriber);
        }

        return $unsubscribeStatus;
    }

    /**
     * @param Subscriber $subscription
     * @param OmnisendContactInterface $contact
     * @return bool
     */
    protected function unsubscribeFromStore($subscription, $contact)
    {
        if (!$contact instanceof OmnisendContactInterface) {
            return false;
        }

        $deleteAllowed = true;

        if ($subscription->getId()) {
            $response = $this->subscriberRequest->post(
                $subscription,
                $contact->getStoreId()
            );

            if (!$this->importStatus->getImportStatus($response)) {
                $deleteAllowed = false;
            }
        }

        if ($deleteAllowed) {
            $this->deleteContact($contact);
        }

        return $deleteAllowed;
    }

    /**
     * @param OmnisendContactInterface $contact
     */
    protected function deleteContact($contact)
    {
        try {
            $this->contactRepository->delete($contact);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }

    /**
     * @param OmnisendGuestSubscriberInterface $guestSubscriber
     */
    protected function deleteGuestSubscriber($guestSubscriber)
    {
        try {
            $this->guestSubscriberRepository->delete($guestSubscriber);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }
}
