<?php

namespace Omnisend\Omnisend\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Observer\SystemConfigSaveObserver;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class Verify
 * @package Omnisend\Omnisend\Controller\Account
 */
class Verify extends Action
{
    const PLATFORM = 'platform';
    const PLATFORM_VERSION = 'platform_version';
    const PHP_VERSION = 'php_version';
    const PLUGIN_VERSION = 'plugin_version';
    const API_KEY = 'api_key';

    /**
     * @var Http
     */
    protected $httpRequest;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductMetadataInterface
     */
    protected $metadata;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DecoderInterface
     */
    protected $decoder;

    /**
     * @var array
     */
    private $_info;

    /**
     * Verify constructor.
     * @param Context $context
     * @param Http $httpRequest
     * @param Json $serializer
     * @param EncryptorInterface $encryptor
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ProductMetadataInterface $metadata
     * @param DecoderInterface $decoder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Http $httpRequest,
        Json $serializer,
        EncryptorInterface $encryptor,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ProductMetadataInterface $metadata,
        DecoderInterface $decoder,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->httpRequest = $httpRequest;
        $this->serializer = $serializer;
        $this->encryptor = $encryptor;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->metadata = $metadata;
        $this->logger = $logger;
        $this->decoder = $decoder;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->validate($this->httpRequest)) {
            $this->logger->debug(self::class . ": " . "Failed validation");
            return $this->getResponse()->representJson(
                $this->serializer->serialize([
                    'success' => false,
                    'error' => true,
                    'message' => 'Invalid request.',
                ])
            );
        }

        $this->logger->debug(self::class . ": " . "Validation successful");
        return $this->getResponse()->representJson(
            $this->serializer->serialize([
                'success' => true,
                'systemInfo' => [
                    self::PLATFORM => $this->_info[self::PLATFORM],
                    self::PLATFORM_VERSION => $this->_info[self::PLATFORM_VERSION],
                    self::PHP_VERSION => $this->_info[self::PHP_VERSION],
                    self::PLUGIN_VERSION => $this->_info[self::PLUGIN_VERSION],
                ],
            ])
        );
    }

    /**
     * Perform custom request validation.
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function validate(RequestInterface $request)
    {
        try {
            $encodedHash = $request->getParam('h');
            $this->logger->info(self::class . " - Incoming hash: " . $encodedHash);
            if (empty($encodedHash)) {
                return false;
            }

            $hash = $this->decoder->decode($encodedHash);
            $json = $this->encryptor->decrypt($hash);
            $this->logger->info(self::class . " - Incoming json:  " . $json);
            $this->_info = $this->serializer->unserialize($json);
            $apiKey = $this->scopeConfig->getValue(SystemConfigSaveObserver::API_KEY_CONFIG_PATH);
            return $apiKey == $this->_info[self::API_KEY];
        } catch (\Exception $e) {
            $this->logger->error(self::class . ": " . $e->getMessage());
            return false;
        }
    }
}
