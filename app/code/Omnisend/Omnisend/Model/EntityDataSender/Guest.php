<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\OmnisendContactEventDispatcher;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class Guest
 * @package Omnisend\Omnisend\Model\EntityDataSender
 */
class Guest implements EntityDataSenderInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestInterface
     */
    protected $guestContactRequest;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var OmnisendContactEventDispatcher
     */
    protected $omnisendContactEventDispatcher;

    /**
     * Guest constructor.
     * @param StoreManagerInterface $storeManager
     * @param OmnisendContactEventDispatcher $omnisendContactEventDispatcher
     * @param Json $serializer
     * @param RequestInterface $guestContactRequest
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        OmnisendContactEventDispatcher $omnisendContactEventDispatcher,
        Json $serializer,
        RequestInterface $guestContactRequest,
        LoggerInterface $logger
    ) {
        $this->guestContactRequest = $guestContactRequest;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
        $this->omnisendContactEventDispatcher = $omnisendContactEventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        try {
            $response = $this->guestContactRequest->post($data, $this->storeManager->getStore()->getId());
            if (empty($response)) {
                return null;
            }

            $responseBody = $this->serializer->unserialize($response);
            $contactId = $responseBody['contactID'];
            if (!empty($contactId)) {
                $this->omnisendContactEventDispatcher->dispatchContactAccessEvent($contactId);
            }

            return $response;
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }
    }
}
