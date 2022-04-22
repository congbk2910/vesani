<?php

namespace Omnisend\Omnisend\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class GeneralConfig
{
    const SECTION_OMNISEND_CONFIG = 'omnisend_config/';
    const GROUP_GENERAL = 'general/';
    const SECTION_OMNISEND_UPDATE_QUOTES = 'update_quotes/';
    const GROUP_CRON = 'cron/';
    const FIELD_BASE_URL = 'base_url';
    const FIELD_API_KEY = 'api_key';
    const FIELD_IS_CRON_SYNCHRONIZATION_ENABLED = 'is_cron_synchronization_enabled';
    const FIELD_IS_REAL_TIME_SYNCHRONIZATION_ENABLED = 'is_real_time_synchronization_enabled';
    const FIELD_MAXIMUM_ENTITIES_PER_CRON = 'maximum_entities_per_cron';
    const FIELD_OMNISEND_URL = 'omnisend_url';
    const FIELD_INSTRUCTIONS_URL = 'instructions_url';
    const FIELD_IS_VERIFIED = 'is_verified';
    const FIELD_DELTA = 'delta';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_BASE_URL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getApiKey($storeId)
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getIsCronSynchronizationEnabled()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_IS_CRON_SYNCHRONIZATION_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getIsRealTimeSynchronizationEnabled()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_IS_REAL_TIME_SYNCHRONIZATION_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getMaximumEntitiesPerCron()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_MAXIMUM_ENTITIES_PER_CRON,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getQuoteCronDelta()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_UPDATE_QUOTES . self::GROUP_CRON . self::FIELD_DELTA
        );
    }

    /**
     * @return string
     */
    public function getOmnisendUrl()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_OMNISEND_URL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getInstructionsUrl()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_INSTRUCTIONS_URL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getIsVerified()
    {
        return $this->scopeConfig->getValue(
            self::SECTION_OMNISEND_CONFIG . self::GROUP_GENERAL . self::FIELD_IS_VERIFIED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
