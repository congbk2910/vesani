<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\DataProvider\Reports;

use Amasty\Acart\Model\Config\Source\DataRange;
use Amasty\Acart\Model\Date;
use Amasty\Acart\Model\OptionSource\Campaigns;
use Amasty\Acart\Model\StatisticsManagement;
use Magento\Directory\Model\Currency;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Store\Model\StoreManagerInterface;

class Listing implements DataProviderInterface
{
    public const ABANDONMENT_RATE = 'rated_total';
    public const EMAILS_SENT = 'sent_total';
    public const CARTS_RESTORED = 'restored_total';
    public const ORDERS_PLACED = 'placed_total';
    public const POTENTIAL_REVENUE = 'potential_revenue';
    public const RECOVERED_REVENUE = 'recovered_revenue';
    public const EFFICIENCY = 'efficiency';
    public const OPEN_RATE = 'open_rate';
    public const CLICK_RATE = 'click_rate';
    public const RECOVERED_CARTS_RATE = 'recovered_carts_rate';
    public const EMAIL_STATISTICS = 'email_statistics';
    public const STATISTICS = 'statistics';
    public const CHARTS = 'charts';
    public const CAMPAIGNS_STATISTICS = 'campaigns_statistics';
    public const TOP_5_FORGET_PRODUCTS = 'top_5_forget_products';
    public const BASE_PRICE_FORMAT = 'basePriceFormat';

    /**
     * @var StatisticsManagement
     */
    private $statisticsManagement;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Campaigns
     */
    private $campaignsSource;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Data Provider name
     *
     * @var string
     */
    private $name;

    /**
     * Data Provider Primary Identifier name
     *
     * @var string
     */
    private $primaryFieldName;

    /**
     * Data Provider Request Parameter Identifier name
     *
     * @var string
     */
    private $requestFieldName;

    /**
     * @var array
     */
    private $meta = [];

    /**
     * Provider configuration data
     *
     * @var array
     */
    private $data = [];

    public function __construct(
        StatisticsManagement $statisticsManagement,
        StoreManagerInterface $storeManager,
        Campaigns $campaignsSource,
        RequestInterface $request,
        Date $date,
        FormatInterface $localeFormat,
        ScopeConfigInterface $scopeConfig,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->statisticsManagement = $statisticsManagement;
        $this->storeManager = $storeManager;
        $this->campaignsSource = $campaignsSource;
        $this->request = $request;
        $this->date = $date;
        $this->localeFormat = $localeFormat;
        $this->scopeConfig = $scopeConfig;
        $this->name = $name;
        $this->primaryFieldName = $primaryFieldName;
        $this->requestFieldName = $requestFieldName;
        $this->meta = $meta;
        $this->data = $data;
    }

    public function getData(): array
    {
        $data = $this->loadDataByParams();

        return $data;
    }

    private function loadDataByParams(): array
    {
        $data = [];
        list($websiteId, $dateRange, $dateFrom, $dateTo, $campaignId) = $this->getParamsFromRequest();
        list($dateFrom, $dateTo) = $this->getDate((string)$dateRange, $dateFrom, $dateTo);

        $storeIds = $this->getStoreIdsByWebsiteId($websiteId);
        $campaignIds = $this->getCampaignIds($campaignId);

        $data[self::BASE_PRICE_FORMAT] = $this->localeFormat->getPriceFormat(
            null,
            $this->getGlobalBaseCurrencyCode()
        );

        $emailsStatistics = $this->statisticsManagement->getEmailStatistics(
            $storeIds,
            $dateTo,
            $dateFrom,
            $campaignIds
        );
        $data[self::STATISTICS] = [
            0 => [
                self::ABANDONMENT_RATE => $this->statisticsManagement->getAbandonmentRate(
                    $storeIds,
                    $dateTo,
                    $dateFrom,
                    $campaignIds
                ),
                self::RECOVERED_CARTS_RATE => $this->statisticsManagement->getRecoveredCartsRate(
                    $storeIds,
                    $dateTo,
                    $dateFrom,
                    $campaignIds
                ),
                self::EMAILS_SENT => $emailsStatistics['sent'],
                self::OPEN_RATE => $emailsStatistics['open_rate'],
                self::CLICK_RATE => $emailsStatistics['click_rate'],
                self::EFFICIENCY => $emailsStatistics['efficiency'],
                self::CARTS_RESTORED => $this->statisticsManagement->getTotalRestoredCarts(
                    $storeIds,
                    $dateTo,
                    $dateFrom,
                    $campaignIds
                ),
                self::ORDERS_PLACED => $this->statisticsManagement->getOrdersViaRestoredCarts(
                    $storeIds,
                    $dateTo,
                    $dateFrom,
                    $campaignIds
                ),
                self::POTENTIAL_REVENUE => $this->statisticsManagement->getTotalAbandonedMoney(
                    $storeIds,
                    $dateTo,
                    $dateFrom,
                    $campaignIds
                ),
                self::RECOVERED_REVENUE => $this->statisticsManagement->getAbandonmentRevenue(
                    $storeIds,
                    $dateTo,
                    $dateFrom,
                    $campaignIds
                )
            ]
        ];

        $data[self::CHARTS] = [
            self::EMAILS_SENT => $data[self::STATISTICS][0][self::EMAILS_SENT],
            self::CARTS_RESTORED => $data[self::STATISTICS][0][self::CARTS_RESTORED],
            self::RECOVERED_REVENUE => $data[self::STATISTICS][0][self::RECOVERED_REVENUE],
            self::POTENTIAL_REVENUE => $data[self::STATISTICS][0][self::POTENTIAL_REVENUE],
            self::ORDERS_PLACED => $data[self::STATISTICS][0][self::ORDERS_PLACED],
            self::ABANDONMENT_RATE => $data[self::STATISTICS][0][self::ABANDONMENT_RATE]
        ];

        $data[self::CAMPAIGNS_STATISTICS] = $this->statisticsManagement->getCampaignStatistics(
            $storeIds,
            $dateTo,
            $dateFrom,
            $campaignIds
        );

        $data[self::TOP_5_FORGET_PRODUCTS] = $this->statisticsManagement->getTop5ForgetProducts(
            $storeIds,
            $dateTo,
            $dateFrom,
            $campaignIds
        );

        return $data;
    }

    private function getDate(string $dateRange, ?string $dateFrom, ?string $dateTo): array
    {
        if ($dateRange === DataRange::OVERALL) {
            $dateFrom = null;
            $dateTo = null;
        } elseif (!$dateRange == DataRange::CUSTOM) {
            $dateFrom = $this->date->getDateWithOffsetByDays((-1) * ($dateRange - 1));
            $dateTo = $this->date->getDateWithOffsetByDays(1);
        } else {
            $dateFrom = $this->date->date('Y-m-d', $dateFrom);
            $dateTo = $this->date->getDateWithOffsetByTime($dateTo, Date::SECONDS_IN_DAY - 1);
        }

        return [$dateFrom, $dateTo];
    }

    /**
     * @return array
     */
    private function getParamsFromRequest(): array
    {
        $filters = $this->request->getParam('filters', []);
        return [
            $filters[FilterConstants::WEBSITE] ?? FilterConstants::ALL,
            $filters[FilterConstants::DATE_RANGE] ?? DataRange::LAST_DAY,
            $filters[FilterConstants::DATE_FROM] ?? null,
            $filters[FilterConstants::DATE_TO] ?? null,
            $filters[FilterConstants::CAMPAIGNS] ?? FilterConstants::ALL
        ];
    }

    private function getStoreIdsByWebsiteId(string $websiteId): array
    {
        $storeIds = [];
        $stores = $this->storeManager->getStores(true);

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        foreach ($stores as $store) {
            if ($websiteId === FilterConstants::ALL || $websiteId === $store['website_id']) {
                $storeIds[] = $store->getId();
            }
        }

        return $storeIds;
    }

    private function getCampaignIds(string $campaignId): array
    {
        $campaignIds = [];
        $campaigns = $this->campaignsSource->getCampaignsList();

        foreach ($campaigns as $campaign) {
            if ($campaignId === FilterConstants::ALL || $campaignId === $campaign['value']) {
                $campaignIds[] = $campaign['value'];
            }
        }

        return $campaignIds;
    }

    private function getGlobalBaseCurrencyCode(): string
    {
        return $this->scopeConfig->getValue(
            Currency::XML_PATH_CURRENCY_BASE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * Get config data
     *
     * @return mixed
     */
    public function getConfigData(): array
    {
        return $this->data['config'] ?? [];
    }

    /**
     * Get Data Provider name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get primary field name
     *
     * @return string
     */
    public function getPrimaryFieldName(): string
    {
        return $this->primaryFieldName;
    }

    /**
     * Get field name in request
     *
     * @return string
     */
    public function getRequestFieldName(): string
    {
        return $this->requestFieldName;
    }

    /**
     * Return Meta
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Get field Set meta info
     *
     * @param string $fieldSetName
     * @return array
     */
    public function getFieldSetMetaInfo($fieldSetName): array
    {
        return $this->meta[$fieldSetName] ?? [];
    }

    /**
     * Return fields meta info
     *
     * @param string $fieldSetName
     * @return array
     */
    public function getFieldsMetaInfo($fieldSetName): array
    {
        return $this->meta[$fieldSetName]['children'] ?? [];
    }

    /**
     * Return field meta info
     *
     * @param string $fieldSetName
     * @param string $fieldName
     * @return array
     */
    public function getFieldMetaInfo($fieldSetName, $fieldName): array
    {
        return $this->meta[$fieldSetName]['children'][$fieldName] ?? [];
    }

    /**
     * Add field filter to collection
     *
     * @param Filter $filter
     * @return null
     */
    public function addFilter(Filter $filter): ?string
    {
        return null;
    }

    /**
     * Returns search criteria
     *
     * @return null
     */
    public function getSearchCriteria(): ?string
    {
        return null;
    }

    /**
     * Returns SearchResult
     *
     * @return null
     */
    public function getSearchResult(): ?string
    {
        return null;
    }

    /**
     * Alias for self::setOrder()
     *
     * @param string $field
     * @param string $direction
     * @return null
     */
    public function addOrder($field, $direction): ?string
    {
        return null;
    }

    /**
     * Set Query limit
     *
     * @param int $offset
     * @param int $size
     * @return null
     */
    public function setLimit($offset, $size): ?string
    {
        return null;
    }

    /**
     * Set data
     *
     * @param mixed $config
     * @return void
     */
    public function setConfigData($config): void
    {
        $this->data['config'] = $config;
    }
}
