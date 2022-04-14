<?php
/**
 * @component  Goivvy_DecimalPlaces
 * @copyright  Copyright (c) 2017 Goivvy.com. (https://www.goivvy.com)
 * @license    https://www.goivvy.com/license Commercial License
 * @author     Goivvy.com developer team <team@goivvy.com>
 */

namespace Goivvy\DecimalPlaces\Plugin;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class FormatPlugin {
   
   protected $_scopeConfig;
      
   public function __construct(ScopeConfigInterface $scopeConfig)
   {
       $this->_scopeConfig = $scopeConfig; 
   }
   
   public function aroundgetPriceFormat(\Magento\Framework\Locale\FormatInterface $format, callable $proceed, $localeCode = null, $localeCurrency = null)
   {
       $active = $this->_scopeConfig->getValue('goivvydecimalplaces/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       if($active){
         // $place = $this->_scopeConfig->getValue('goivvydecimalplaces/general/place', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $place = 2;
         $return  = $proceed($localeCode,$localeCurrency);
         $return['precision'] = $place;   
         $return['requiredPrecision'] = $place;
         return $return;
       }else
         return $proceed($localeCode,$localeCurrency);
   }
}
