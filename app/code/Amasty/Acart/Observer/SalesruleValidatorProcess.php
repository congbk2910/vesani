<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Observer;

use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Api\RuleQuoteRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NotFoundException;

class SalesruleValidatorProcess implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RuleQuoteRepositoryInterface
     */
    private $ruleQuoteRepository;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        HistoryRepositoryInterface $historyRepository,
        RuleQuoteRepositoryInterface $ruleQuoteRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->historyRepository = $historyRepository;
        $this->ruleQuoteRepository = $ruleQuoteRepository;
    }

    public function execute(EventObserver $observer)
    {
        if ($this->scopeConfig->getValue('amasty_acart/general/customer_coupon')) {
            $salesRule = $observer->getEvent()->getRule();

            try {
                $history = $this->historyRepository->getBySalesRuleId($salesRule->getId());
            } catch (NotFoundException $e) {
                $history = null;
            }

            if ($history) {
                try {
                    $ruleQuote = $this->ruleQuoteRepository->getById(
                        (int)$history->getRuleQuoteId()
                    );
                } catch (NotFoundException $e) {
                    $ruleQuote = null;
                }

                if ($ruleQuote) {
                    $customerEmail = $ruleQuote->getCustomerId()
                        ?
                        $observer->getEvent()->getQuote()->getCustomer()->getEmail()
                        :
                        $observer->getEvent()->getQuote()->getBillingAddress()->getEmail();

                    if ($ruleQuote->getQuoteId() != $observer->getEvent()->getQuote()->getId()
                        && $customerEmail != $ruleQuote->getCustomerEmail()
                    ) {
                        $observer->getEvent()->getQuote()->setCouponCode("");
                    }
                }
            }
        }
    }
}
