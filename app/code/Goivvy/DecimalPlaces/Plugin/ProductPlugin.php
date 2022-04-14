<?php
/**
 * @component  Goivvy_DecimalPlaces
 * @copyright  Copyright (c) 2017 Goivvy.com. (https://www.goivvy.com)
 * @license    https://www.goivvy.com/license Commercial License
 * @author     Goivvy.com developer team <team@goivvy.com>
 */

namespace Goivvy\DecimalPlaces\Plugin;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class ProductPlugin {
   
   protected $_scopeConfig;
      
   public function __construct(ScopeConfigInterface $scopeConfig)
   {
       $this->_scopeConfig = $scopeConfig; 
   }
   
   public function aroundformat(\Magento\Framework\Pricing\PriceCurrencyInterface $priceModel, callable $proceed, $amount, $includeContainer = true, $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION, $scope = null, $currency = null)
   {
       $active = $this->_scopeConfig->getValue('goivvydecimalplaces/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       if($active){
         $place = $this->_scopeConfig->getValue('goivvydecimalplaces/general/place', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $place = $this->formatPlace($amount,$place);
         return $proceed($amount,$includeContainer,$place,$scope,$currency); 
       }else
         return $proceed($amount,$includeContainer,$precision,$scope,$currency);
   }

   public function aroundconvertAndRound(\Magento\Framework\Pricing\PriceCurrencyInterface $priceModel, callable $proceed, $amount, $scope = null, $currency = null, $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION)
   {
       $active = $this->_scopeConfig->getValue('goivvydecimalplaces/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       if($active){
         $place = $this->_scopeConfig->getValue('goivvydecimalplaces/general/place', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $place = $this->formatPlace($amount,$place);
         return $proceed($amount,$scope,$currency,$place); 
       }else
         return $proceed($amount,$scope,$currency,$precision);
   }

   public function aroundconvertAndFormat(\Magento\Framework\Pricing\PriceCurrencyInterface $priceModel, callable $proceed, $amount, $includeContainer = true, $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION, $scope = null, $currency = null
   )
   {
       $active = $this->_scopeConfig->getValue('goivvydecimalplaces/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       if($active){
         $place = $this->_scopeConfig->getValue('goivvydecimalplaces/general/place', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $place = $this->formatPlace($amount,$place);
         return $proceed($amount,$includeContainer,$place); 
       }else
         return $proceed($amount,$includeContainer,$precision);
   }
   
   public function aroundround(\Magento\Framework\Pricing\PriceCurrencyInterface $priceModel, callable $proceed, $price)
   {
       $active = $this->_scopeConfig->getValue('goivvydecimalplaces/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       if($active){
         $place = $this->_scopeConfig->getValue('goivvydecimalplaces/general/place', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $place = $this->formatPlace($price,$place);
         return round($price,$place);; 
       }else
         return $proceed($price);
   }

   public function formatPlace($amout, $place) {
    $pos = strpos($amout, '.');
    if($pos === false) {
      return 2;
    } else {
      $amount =  rtrim(rtrim($amout, '0'), '.');
      $number = strlen(substr(strrchr($amount, "."), 1));
      if ($number >= 1 && $number <=2) {
        return 2;
      } elseif($number == 3 )  {
        return 3;
      } else {
        return $place;
      }
    }
   }
}
