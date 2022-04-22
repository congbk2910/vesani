<?php

namespace Cminds\Salesrep\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\UrlInterface;

class ConfigProvider implements ConfigProviderInterface
{
    private $salesrepHelper;

    private $scopeConfig;

    private $storeManagerInterface;

    private $urlInterface;
    private $customerSession;

    public function __construct(
        \Cminds\Salesrep\Helper\Data $salesrepHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        UrlInterface $urlInterface,
        CustomerSession $customerSession
    ) {
        $this->salesrepHelper = $salesrepHelper;
        $this->scopeConfig = $scopeConfig;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->urlInterface = $urlInterface;
        $this->customerSession = $customerSession;
    }

    /**
     * Return config array.
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [
            'salesrep' => [
                'isSalesrepEnabled' => $this->isSalesrepEnabled(),
                'getSalesrepList' => $this->salesrepHelper->getAdminsForFrontend(),
                'getSalesrepLabel' => $this->getSalesrepLabel(),
                'getSalesrepNote' => $this->getSalesrepNote(),
                'getAjaxUrl' => $this->getAjaxUrl(),
                'getCheckoutAjaxUrl' => $this->getCheckoutAjaxUrl(),
                'isAvailableByParam' => $this->isAvailableByParam(),
                'selectorVisibleParam' => $this->getSelectorVisibleParam()
            ],
        ];
        return $config;
    }

    /**
     * Returns true if salesrep module is enabled
     *
     * @return bool
     */
    public function isSalesrepEnabled()
    {
        $moduleEnabled = $this->salesrepHelper->isModuleEnabled();
        $frontendSelector = $this->salesrepHelper->showFrontendSelector();
        $assignedSalesRep = $this->customerSession->getCustomer()->getSalesrepRepId();

        if ($moduleEnabled && $frontendSelector && !$assignedSalesRep) {
            return true;
        }

        return false;
    }

    public function isAvailableByParam()
    {
        return $this->salesrepHelper->isAvailableByParam();
    }

    public function getSelectorVisibleParam()
    {
        return $this->salesrepHelper->getSelectorVisibleParam();
    }

    public function getSalesrepLabel()
    {
        $label = $this->salesrepHelper->getCheckoutLabel();

        return __($label);
    }

    public function getSalesrepNote()
    {
        $note = $this->salesrepHelper->getCheckoutNote();

        return __($note);
    }

    public function getAjaxUrl()
    {
        return $this->urlInterface->getUrl(
            'salesrep/checkout/selectsalesrepcheckout'
        );
    }

    public function getCheckoutAjaxUrl()
    {
        return $this->urlInterface->getUrl(
            'salesrep/checkout/selectsalesrep'
        );
    }
}
