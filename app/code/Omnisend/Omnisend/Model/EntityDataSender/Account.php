<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Exception;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class Account
 * @package Omnisend\Omnisend\Model\EntityDataSender
 */
class Account implements EntityDataSenderInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestInterface
     */
    protected $accountRequest;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Account constructor.
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $accountRequest
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        RequestInterface $accountRequest,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->accountRequest = $accountRequest;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * @param array $data
     * @return array|bool|float|int|mixed|string|null
     * @throws Exception
     */
    public function send($data)
    {
        try {
            $response = $this->json
                ->unserialize($this->accountRequest->get(null, $data['store_id']));
            if (isset($response['verified']) && $response['verified']) {
                return $response;
            }

            $response = $this->accountRequest->post($data, $data['store_id']);
            if (!$response) {
                throw new Exception('Unable to verify store with Omnisend');
            }
            return $this->json->unserialize($response);
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param String $storeId
     * @return array|bool|float|int|mixed|string|null
     * @throws Exception
     */
    public function get($storeId)
    {
        try {
            $response = $this->accountRequest->get(null, $storeId);
            if (!$response) {
                throw new Exception('Unable to verify store with Omnisend');
            }
            return $this->json->unserialize($response);
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return null;
        }
    }
}
