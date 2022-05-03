<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\HistoryDetailInterface;
use Amasty\Acart\Api\Data\HistoryDetailInterfaceFactory;
use Amasty\Acart\Api\Data\HistoryInterface;
use Amasty\Acart\Api\RuleQuoteRepositoryInterface;
use Amasty\Acart\Model\History\ProductDetails\DetailSaver;
use Amasty\Acart\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Framework\Stdlib;
use Magento\Quote\Model\Quote;

class RuleQuoteFromRuleAndQuoteFactory
{
    /**
     * @var Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var RuleQuoteFactory
     */
    private $ruleQuoteFactory;

    /**
     * @var RuleQuoteRepositoryInterface
     */
    private $ruleQuoteRepository;

    /**
     * @var HistoryFromRuleQuoteFactory
     */
    private $historyFromRuleQuoteFactory;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var HistoryDetailInterfaceFactory
     */
    private $detailFactory;

    /**
     * @var DetailSaver
     */
    private $detailSaver;

    public function __construct(
        Stdlib\DateTime\DateTime $date,
        Stdlib\DateTime $dateTime,
        RuleQuoteFactory $ruleQuoteFactory,
        RuleQuoteRepositoryInterface $ruleQuoteRepository,
        \Amasty\Acart\Model\HistoryFromRuleQuoteFactory $historyFromRuleQuoteFactory,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        HistoryDetailInterfaceFactory $detailFactory,
        DetailSaver $detailSaver
    ) {
        $this->date = $date;
        $this->dateTime = $dateTime;
        $this->ruleQuoteFactory = $ruleQuoteFactory;
        $this->ruleQuoteRepository = $ruleQuoteRepository;
        $this->historyFromRuleQuoteFactory = $historyFromRuleQuoteFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->detailFactory = $detailFactory;
        $this->detailSaver = $detailSaver;
    }

    /**
     * @param Rule $rule
     * @param Quote $quote
     * @param bool $testMode
     *
     * @return RuleQuote
     */
    public function create(
        \Amasty\Acart\Model\Rule $rule,
        Quote $quote,
        $testMode = false
    ) {
        /** @var RuleQuote $ruleQuote */
        $ruleQuote = $this->ruleQuoteFactory->create();
        $customerEmail = $quote->getCustomerEmail();
        if (!$customerEmail
            && $quote->getExtensionAttributes()
            && $quote->getExtensionAttributes()->getAmAcartQuoteEmail()
            && $quote->getExtensionAttributes()->getAmAcartQuoteEmail()->getCustomerEmail()
        ) {
            $customerEmail = $quote->getExtensionAttributes()->getAmAcartQuoteEmail()->getCustomerEmail();
        }

        if ($customerEmail) {
            $time = $this->date->gmtTimestamp();
            $ruleQuote->setData(
                [
                    'rule_id' => $rule->getRuleId(),
                    'quote_id' => $quote->getId(),
                    'store_id' => $quote->getStoreId(),
                    'status' => RuleQuote::STATUS_PROCESSING,
                    'customer_id' => $quote->getCustomerId(),
                    'customer_email' => $customerEmail,
                    'customer_firstname' => $quote->getCustomerFirstname(),
                    'customer_lastname' => $quote->getCustomerLastname(),
                    'customer_phone' => $this->getCustomerTelephone($quote),
                    'test_mode' => $testMode,
                    'created_at' => $this->dateTime->formatDate($time)
                ]
            );

            $this->ruleQuoteRepository->save($ruleQuote);
            $histories = [];

            /** @var \Amasty\Acart\Model\ResourceModel\Schedule\Collection $scheduleCollection */
            $scheduleCollection = $this->scheduleCollectionFactory->create();
            $scheduleCollection->addFieldToFilter(Schedule::RULE_ID, $rule->getRuleId());

            foreach ($scheduleCollection as $schedule) {
                $history = $this->historyFromRuleQuoteFactory->create($ruleQuote, $schedule, $rule, $quote, $time);
                $this->saveHistoryDetails($history, $quote);

                $histories[] = $history;
            }

            if (!$histories) {
                throw new \Magento\Framework\Exception\LocalizedException(__("Rule do not have any Schedule"));
            }

            $ruleQuote->setData('assigned_history', $histories);
        }

        return $ruleQuote;
    }

    private function saveHistoryDetails(HistoryInterface $history, Quote $quote)
    {
        foreach ($quote->getAllItems() as $quoteItem) {
            /** @var HistoryDetailInterface $detail */
            $detail = $this->detailFactory->create();
            $detail->setHistoryId($history->getHistoryId());
            $detail->setProductName((string)$quoteItem->getName());
            $detail->setProductPrice((float)$quoteItem->getPrice());
            $detail->setProductSku((string)$quoteItem->getSku());
            $detail->setProductQty((int)$quoteItem->getQty());
            $detail->setStoreId((int)$quoteItem->getStoreId());
            $detail->setCurrencyCode((string)$quote->getCurrency()->getQuoteCurrencyCode());
            $this->detailSaver->execute($detail);
        }
    }

    private function getCustomerTelephone($quote): ?string
    {
        $billingAddress = $quote->getBillingAddress();
        if ($billingAddress->getTelephone()) {
            return $billingAddress->getTelephone();
        }

        return $quote->getShippingAddress()->getTelephone();
    }
}
