<?php

namespace Cminds\Salesrep\Block\Email;

use Magento\Framework\View\Element\Template\Context;

class Report extends \Magento\Framework\View\Element\Template
{
    protected $dateTime;

    protected $priceCurrency;

    protected $currencyHelper;
    protected $_template = "email/report.phtml";

    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Pricing\Helper\Data $currencyHelper
    ) {
        parent::__construct($context);
        $this->dateTime = $dateTime;
        $this->priceCurrency = $priceCurrency;
        $this->currencyHelper = $currencyHelper;
    }

    public function getDate($format = null, $date)
    {
        return $this->dateTime->date($format, $date);
    }

    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

    public function getCurrency($price)
    {
        return $this->currencyHelper->currency($price, true, false);
    }
}
