<?php

namespace Goivvy\DecimalPlaces\Helper;


use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_categoryCollectionFactory;
    protected $_categoryFactory;
    protected $_productRepository;
    protected $_groupCollection;
    protected $_productCollectionFactory;

    public function __construct(
        Context $context
    )
    {
        parent::__construct($context);
    }

    public function roundUpTo5Cents($value) {
        $valueInString = strval(round($value,2));
        if (strpos($valueInString, ".") == 0) $valueInString = $valueInString.".00";
        $valueArray = explode(".", $valueInString);
        $substringValue = substr($valueArray[1], 1);
         
        if (($substringValue >= 3 && $substringValue <= 5) || ($substringValue >= 6 && $substringValue <= 7)) {
            $tempValue = str_replace(substr($valueArray[1], 1), 5, substr($valueArray[1], 1));
            $tempValue = substr($valueArray[1],0,1).$tempValue;
            $newvalue = floatval($valueArray[0].".".$tempValue);
        } elseif($substringValue >= 1 && $substringValue <= 2) {
            $tempValue = str_replace(substr($valueArray[1], 1), 0, substr($valueArray[1], 1));
            $tempValue = substr($valueArray[1],0,1).$tempValue;
            $newvalue = floatval($valueArray[0].".".$tempValue);
        } elseif($substringValue == 0) {
            $newvalue = floatval($value);
        } else {
            $newFloat = floatval($valueArray[0].".".substr($valueArray[1],0,1));
            $newvalue = ($newFloat+0.1);
        }
         
        return $newvalue;
    }
}
