<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Exception;
use Magento\Framework\Json\Helper\Data;
use Magento\Newsletter\Model\Subscriber as BaseSubscriber;
use Magento\Newsletter\Model\Subscriber as NewsletterSubscriber;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;
use Omnisend\Omnisend\Api\OmnisendGuestSubscriberRepositoryInterface;
use Omnisend\Omnisend\Helper\SearchCriteria\OmnisendGuestSubscriber as OmnisendSubscriberSearchCriteria;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;
use Omnisend\Omnisend\Model\OmnisendGuestSubscriberFactory;
use Omnisend\Omnisend\Observer\CustomerUpdateObserver;
use Psr\Log\LoggerInterface;

class Subscriber implements EntityDataSenderInterface
{
    /**
     * @var RequestInterface
     */
    protected $subscriberRequest;

    /**
     * @var OmnisendGuestSubscriberFactory
     */
    protected $omnisendGuestSubscriberFactory;

    /**
     * @var OmnisendGuestSubscriberRepositoryInterface
     */
    protected $omnisendGuestSubscriberRepository;

    /**
     * @var OmnisendSubscriberSearchCriteria
     */
    protected $omnisendSubscriberSearchCriteria;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OmnisendContactEventDispatcher
     */
    protected $omnisendContactEventDispatcher;

    /**
     * @param RequestInterface $subscriberRequest
     * @param OmnisendGuestSubscriberFactory $omnisendGuestSubscriberFactory
     * @param OmnisendGuestSubscriberRepositoryInterface $omnisendGuestSubscriberRepository
     * @param OmnisendSubscriberSearchCriteria $omnisendSubscriberSearchCriteria
     * @param Data $jsonHelper
     * @param LoggerInterface $logger
     * @param OmnisendContactEventDispatcher $omnisendContactEventDispatcher
     */
    public function __construct(
        RequestInterface $subscriberRequest,
        OmnisendGuestSubscriberFactory $omnisendGuestSubscriberFactory,
        OmnisendGuestSubscriberRepositoryInterface $omnisendGuestSubscriberRepository,
        OmnisendSubscriberSearchCriteria $omnisendSubscriberSearchCriteria,
        Data $jsonHelper,
        LoggerInterface $logger,
        OmnisendContactEventDispatcher $omnisendContactEventDispatcher
    ) {
        $this->subscriberRequest = $subscriberRequest;
        $this->omnisendGuestSubscriberFactory = $omnisendGuestSubscriberFactory;
        $this->omnisendGuestSubscriberRepository = $omnisendGuestSubscriberRepository;
        $this->omnisendSubscriberSearchCriteria = $omnisendSubscriberSearchCriteria;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->omnisendContactEventDispatcher = $omnisendContactEventDispatcher;
    }

    /**
     * @param NewsletterSubscriber $subscriber
     * @return null|string
     */
    public function send($subscriber)
    {
        try {
            $subscriberId = $subscriber->getSubscriberId();

            $searchCriteria = $this->omnisendSubscriberSearchCriteria->getOmnisendSubscriberInStoreBySubscriberIdSearchCriteria(
                $subscriberId,
                $subscriber->getStoreId()
            );

            $omnisendContact = $this->omnisendGuestSubscriberRepository->getList($searchCriteria)->getFirstItem();

            if ($omnisendContact->getData() && $omnisendContact->getOmnisendId()) {
                $this->omnisendContactEventDispatcher->dispatchContactAccessEvent($omnisendContact->getOmnisendId());
                return $this->subscriberRequest->post($subscriber, $subscriber->getStoreId());
            }

            $response = $this->jsonHelper->jsonDecode($this->subscriberRequest->post($subscriber, $subscriber->getStoreId()));

            if ($response == null) {
                return $response;
            }

            $this->processOmnisendSubscriberContact($omnisendContact, $subscriber, $subscriberId, $response);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }

    }

    /**
     * @param BaseSubscriber $subscriber
     * @param OmnisendGuestSubscriberInterface[] $guestSubscribers
     */
    public function unsubscribe($subscriber, $guestSubscribers)
    {
        try {
            if (!$subscriber instanceof BaseSubscriber) {
                return;
            }

            $subscriber->setData(CustomerUpdateObserver::ARRAY_INDEX_CHANGE_EMAIL, 1);

            foreach ($guestSubscribers as $guestSubscriber) {
                $this->unsubscribeFromStore($subscriber, $guestSubscriber);
            }
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return;
        }
    }

    /**
     * @param BaseSubscriber $subscriber
     * @param OmnisendGuestSubscriberInterface $guestSubscriber
     */
    protected function unsubscribeFromStore($subscriber, $guestSubscriber)
    {
        if (!$guestSubscriber instanceof OmnisendGuestSubscriberInterface) {
            return;
        }
        $this->subscriberRequest->post(
            $subscriber,
            $guestSubscriber->getStoreId()
        );

        $this->omnisendGuestSubscriberRepository->delete($guestSubscriber);
    }

    /**
     * @param OmnisendGuestSubscriberInterface $omnisendContact
     * @param NewsletterSubscriber $subscriber
     * @param int $subscriberId
     * @param array $response
     */
    protected function processOmnisendSubscriberContact($omnisendContact, $subscriber, $subscriberId, $response)
    {
        if (!$omnisendContact->getData()) {
            $omnisendContact = $this->omnisendGuestSubscriberFactory->create();
        }

        $omnisendContact->setSubscriberId($subscriberId);
        $omnisendContact->setOmnisendId($response['contactID']);
        $omnisendContact->setStoreId($subscriber->getStoreId());

        try {
            $this->omnisendGuestSubscriberRepository->save($omnisendContact);
            $this->omnisendContactEventDispatcher->dispatchContactAccessEvent($omnisendContact->getOmnisendId());
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }
}
