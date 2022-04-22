<?php

namespace Omnisend\Omnisend\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Snippet
 * @package Omnisend\Omnisend\Block
 */
class Snippet extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var GeneralConfig
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Snippet constructor.
     * @param Context $context
     * @param GeneralConfig $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        GeneralConfig $config,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $context->getStoreManager();
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Get the Account Id.
     *
     * @return string
     */
    public function getAccountId()
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $apiKey = $this->config->getApiKey($storeId);
            if (empty($apiKey)) {
                return '';
            }
            $parts = explode('-', $apiKey);
            return $parts[0];
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }
}
