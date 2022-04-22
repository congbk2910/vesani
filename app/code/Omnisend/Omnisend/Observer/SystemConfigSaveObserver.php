<?php

namespace Omnisend\Omnisend\Observer;

use Exception;
use Magento\Config\Model\Config\Factory as ConfigFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Controller\Account\Verify;
use Omnisend\Omnisend\Model\EntityDataSender\Account as Client;
use Omnisend\Omnisend\Model\RequestBodyBuilder\Account;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class SystemConfigSaveObserver
 * @package Omnisend\Omnisend\Observer
 */
class SystemConfigSaveObserver implements ObserverInterface
{
    const CONFIG_PATH = 'omnisend_config/general/is_verified';
    const API_KEY_CONFIG_PATH = 'omnisend_config/general/api_key';
    const CONFIG_SECTION = 'omnisend_config';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ResourceInterface
     */
    protected $moduleResource;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * SystemConfigSaveObserver constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     * @param ResourceInterface $moduleResource
     * @param Client $client
     * @param ConfigFactory $configFactory
     * @param EncryptorInterface $encryptor
     * @param Json $json
     * @param EncoderInterface $encoder
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        ResourceInterface $moduleResource,
        Client $client,
        ConfigFactory $configFactory,
        EncryptorInterface $encryptor,
        Json $json,
        EncoderInterface $encoder,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->client = $client;
        $this->configFactory = $configFactory;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->moduleResource = $moduleResource;
        $this->encryptor = $encryptor;
        $this->json = $json;
        $this->logger = $logger;
        $this->encoder = $encoder;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->scopeConfig->getValue(self::CONFIG_PATH)) {
            return;
        }

        $data = [];
        $configData = $observer->getEvent()->getData('configData');

        if ($configData['section'] !== self::CONFIG_SECTION) {
            return;
        }

        if (empty($this->scopeConfig->getValue(self::API_KEY_CONFIG_PATH))) {
            return;
        }

        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getData('request');
        $store = $request->getParam('store');
        $storeId = $this->storeManager->getStore($store)->getId();

        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
        $apiKey = $this->scopeConfig->getValue(self::API_KEY_CONFIG_PATH);
        $pluginVersion = $this->moduleResource->getDbVersion('Omnisend_Omnisend');
        $key = $this->json->serialize([
            Verify::PLATFORM => 'Magento 2',
            Verify::PLATFORM_VERSION => '',
            Verify::PHP_VERSION => phpversion(),
            Verify::PLUGIN_VERSION => $pluginVersion,
            Verify::API_KEY => $apiKey,
        ]);
        $hash = $this->encoder->encode($this->encryptor->encrypt($key));
        $this->logger->debug(self::class . " - Outgoing Hash: " . $hash);

        $data[Account::WEBSITE] = $baseUrl;
        $data[Account::STORE_ID] = $storeId;
        $data[Account::VERSION] = $pluginVersion;
        $data[Account::VERIFICATION_URL] = $baseUrl . 'omnisend/account/verify?h=' . $hash;

        $this->logger->debug(self::class . " - Verification Url: " . $data[Account::VERIFICATION_URL]);

        $response = $this->client->send($data);
        $this->logger->debug(self::class . " - Response:", $response);

        if (empty($response)) {
            $this->logger->error(self::class . " - Error: No response from the Omnisend API.");
            return;
        }

        if (!array_key_exists('verified', $response)) {
            $this->logger->error(self::class . " - Error: Could not verify response.");
            return;
        }

        if ($response['verified']) {
            $configData['groups']['general']['fields']['is_verified']['value'] = "1";
            $configModel = $this->configFactory->create(['data' => $configData]);
            $configModel->save();
            return;
        }

        if (isset($response['error'])) {
            $this->logger->error(self::class . " - Error:", $response);
            throw new Exception($response['error']);
        }
    }
}
