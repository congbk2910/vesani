<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\ShippingCostCalculator\Helper;

use Magento\Framework\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $coreRegistry;
    
    protected $storeManager;
    
    protected $context;
    
    protected $countries;
    
    protected $localeFormat;
    
    protected $currencyFactory;
    
    protected $cart;
    
    protected $customer;
    
    protected $defaultShippingAdderss;
    
    protected $urlBuilder;
    
    protected $mediaUrl;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->scopeConfig = $context->getScopeConfig();
        $this->localeFormat = $localeFormat;
        $this->currencyFactory = $currencyFactory;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->mediaUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }
    
    public function getMediaUrl($path = '')
    {
       return $this->mediaUrl . $path;
    }
    
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path, 'store');
    }
    
    public function enableDisplaying()
    {
        return $this->getConfig('codazon_shipping_cost_calculator/general/enable');
    }
    
    public function getCountryOptionArray()
    {
        if ($this->countries === null) {
            $this->countries = $this->objectManager->get('Magento\Directory\Model\Config\Source\Country')->toOptionArray();
        }
        return $this->countries;
    }
    
    public function getPriceFormat()
	{
		return $this->localeFormat->getPriceFormat();
	}
    
    public function getSelectedShippingMethods()
    {
        return $this->getConfig('codazon_shipping_cost_calculator/general/selected_method');
    }
	
    public function getCart()
    {
        if (null === $this->cart) {
            $this->cart = $this->objectManager->get('Magento\Checkout\Model\Cart');
        }
        return $this->cart;
    }
    
    public function getCustomer()
    {
        if (null === $this->customer) {
            $this->customer = $this->objectManager->get('Magento\Customer\Model\Session')->getCustomer();
        }
        return $this->customer;
    }
   
    public function getDefaultShippingAddress()
    {
        if (null === $this->defaultShippingAdderss) {
            $this->defaultShippingAdderss = false;
            if ($this->getCustomer()->getId()) {
                $defaultShipping = $this->customer->getDefaultShipping();
                if ($defaultShipping) {
                    $this->defaultShippingAdderss = $this->objectManager->get('Magento\Customer\Api\AddressRepositoryInterface')->getById($defaultShipping);                
                }
            }
        }
        return $this->defaultShippingAdderss;
    }
    
    public function prepareShippingAddress($block)
    {
        $defaultShippingAdrress = $this->getDefaultShippingAddress();
        if ($defaultShippingAdrress) {
            $block->setCountryId($defaultShippingAdrress->getCountryId());
            $block->setRegion($defaultShippingAdrress->getRegion());
            $block->setRegionId($defaultShippingAdrress->getRegionId());
            $block->setPostCode($defaultShippingAdrress->getPostcode());
        }
        return $this;
    }
    
    public function usePriceInclucdingTax()
    {
        return $this->getConfig('codazon_shipping_cost_calculator/general/use_price_inclucding_tax');
    }
    
    public function getCustomerSession()
    {
        return $this->objectManager->get('Magento\Customer\Model\Session');
    }
    
    
}