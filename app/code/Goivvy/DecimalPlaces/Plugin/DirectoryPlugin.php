<?php
/**
 * @component  Goivvy_DecimalPlaces
 * @copyright  Copyright (c) 2017 Goivvy.com. (https://www.goivvy.com)
 * @license    https://www.goivvy.com/license Commercial License
 * @author     Goivvy.com developer team <team@goivvy.com>
 */

namespace Goivvy\DecimalPlaces\Plugin;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class DirectoryPlugin {
   
   protected $_scopeConfig;
      
   public function __construct(ScopeConfigInterface $scopeConfig)
   {
       $this->_scopeConfig = $scopeConfig; 
   }

   public function aroundformatPrecision(
        \Magento\Directory\Model\Currency $currency,
        callable $proceed,
        $price,
        $precision,
        $options = [],
        $includeContainer = true,
        $addBrackets = false
    ) {
       $active = $this->_scopeConfig->getValue('goivvydecimalplaces/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       if($active){
         $place = $this->_scopeConfig->getValue('goivvydecimalplaces/general/place', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
         $precision = $place;
         $options['precision'] = $place;
         return $proceed($price,$precision,$options,$includeContainer,$addBrackets);
       }else
         return $proceed($price,$precision,$options,$includeContainer,$addBrackets);
   }
}
