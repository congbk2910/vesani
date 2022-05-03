<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Model\EmailTemplate\EmailTemplateResolver;
use Amasty\Acart\Model\EmailTemplate\LegacyTemplateProcessor;
use Amasty\Base\Model\MagentoVersion;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Stdlib;
use Magento\Quote\Model\Quote;

class HistoryFromRuleQuoteFactory
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
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CouponForHistoryFactory
     */
    private $couponForHistoryFactory;

    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var FormatManager
     */
    private $formatManager;

    /**
     * @var EmailTemplateResolver
     */
    private $emailTemplateResolver;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $salesRuleFactory;

    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var LegacyTemplateProcessor
     */
    private $legacyTemplateProcessor;

    /**
     * @var array
     */
    private $sameCouponData = [];

    public function __construct(
        Stdlib\DateTime\DateTime $date,
        Stdlib\DateTime $dateTime,
        ConfigProvider $configProvider,
        CouponForHistoryFactory $couponForHistoryFactory,
        HistoryFactory $historyFactory,
        \Amasty\Acart\Model\UrlManager $urlManager,
        \Amasty\Acart\Model\FormatManager $formatManager,
        \Magento\SalesRule\Model\RuleFactory $salesRuleFactory,
        MagentoVersion $magentoVersion,
        HistoryRepositoryInterface $historyRepository,
        EmailTemplateResolver $emailTemplateResolver,
        LegacyTemplateProcessor $legacyTemplateProcessor
    ) {
        $this->date = $date;
        $this->dateTime = $dateTime;
        $this->configProvider = $configProvider;
        $this->couponForHistoryFactory = $couponForHistoryFactory;
        $this->historyFactory = $historyFactory;
        $this->urlManager = $urlManager;
        $this->formatManager = $formatManager;
        $this->emailTemplateResolver = $emailTemplateResolver;
        $this->magentoVersion = $magentoVersion;
        $this->historyRepository = $historyRepository;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->legacyTemplateProcessor = $legacyTemplateProcessor;
    }

    public function create(
        RuleQuote $ruleQuote,
        Schedule $schedule,
        Rule $rule,
        Quote $quote,
        $time
    ): History {
        $couponData = $this->getCouponData($schedule, $rule, $ruleQuote);
        $scheduleDeliveryTime = $schedule->getDeliveryTime() ?: 250;
        $deliveryTime = $this->configProvider->isDebugMode() ? 10 : $scheduleDeliveryTime;

        /** @var History $history */
        $history = $this->historyFactory->create();
        $history->setData(
            array_merge(
                [
                    History::RULE_QUOTE_ID => $ruleQuote->getRuleQuoteId(),
                    History::SCHEDULE_ID => $schedule->getScheduleId(),
                    History::STATUS => History::STATUS_PROCESSING,
                    History::PUBLIC_KEY => uniqid(),
                    History::SCHEDULED_AT => $this->dateTime->formatDate($time + $deliveryTime),
                    RuleQuote::STORE_ID => $ruleQuote->getStoreId()
                ],
                $couponData
            )
        );
        $this->historyRepository->save($history);
        $template = $this->createEmailTemplate($ruleQuote, $schedule, $rule, clone $quote, $history);

        if (version_compare($this->magentoVersion->get(), '2.3.4', '>=')) {
            $this->legacyTemplateProcessor->execute($template);
        }

        $emailBody = $template->processTemplate();
        // phpcs:ignore
        $emailSubject = html_entity_decode($template->getSubject(), ENT_QUOTES);
        $history->addData(
            [
                History::EMAIL_BODY => $emailBody,
                History::EMAIL_SUBJECT => $emailSubject,
                RuleQuote::CUSTOMER_EMAIL => $ruleQuote->getCustomerEmail(),
                RuleQuote::CUSTOMER_FIRSTNAME => $ruleQuote->getCustomerFirstname(),
                RuleQuote::CUSTOMER_LASTNAME => $ruleQuote->getCustomerLastname(),
                RuleQuote::CUSTOMER_PHONE => $ruleQuote->getCustomerPhone(),
            ]
        );
        $this->historyRepository->save($history);

        return $history;
    }

    /**
     * @param Schedule $schedule
     * @param Rule $rule
     * @param RuleQuote $ruleQuote
     *
     * @return array
     */
    private function getCouponData(
        Schedule $schedule,
        Rule $rule,
        RuleQuote $ruleQuote
    ) {
        $couponData = [];
        $salesCoupon = false;
        $salesRule = false;

        if ($schedule->getSendSameCoupon()) {
            $couponData = $this->sameCouponData;
        } else {
            if ($schedule->getUseShoppingCartRule()) {
                /** @var \Magento\SalesRule\Model\Rule $salesRule */
                $salesRule = $this->salesRuleFactory->create()->load($schedule->getSalesRuleId());

                if ($salesRule->getRuleId()) {
                    $salesCoupon = $this->couponForHistoryFactory->generateCouponPool($salesRule);
                }
            } elseif ($schedule->getSimpleAction()) {
                $salesRule = $this->couponForHistoryFactory->create($ruleQuote, $schedule, $rule);
            }

            if ($salesRule) {
                if ($salesCoupon) {
                    $couponData = [
                        'sales_rule_id' => $salesRule->getRuleId(),
                        'sales_rule_coupon_id' => $salesCoupon->getId(),
                        'sales_rule_coupon' => $salesCoupon->getCode(),
                        'sales_rule_coupon_expiration_date' => $salesCoupon->getExpirationDate(),
                    ];
                } else {
                    $couponData = [
                        'sales_rule_id' => $salesRule->getRuleId(),
                        'sales_rule_coupon_id' => null,
                        'sales_rule_coupon' => $salesRule->getCouponCode(),
                        'sales_rule_coupon_expiration_date' => $salesRule->getToDate(),
                    ];
                }

                $this->sameCouponData = $couponData;
            }
        }

        return $couponData;
    }

    /**
     * @param RuleQuote $ruleQuote
     * @param Schedule $schedule
     * @param Rule $rule
     * @param Quote $quote
     * @param History $history
     *
     * @return TemplateInterface
     */
    private function createEmailTemplate(
        RuleQuote $ruleQuote,
        Schedule $schedule,
        Rule $rule,
        Quote $quote,
        History $history
    ) {
        if ($history->getSalesRuleCoupon()) {
            $quote->setCouponCode($history->getSalesRuleCoupon());
        }

        $quote->collectTotals();
        $discount = $quote->getSubtotal() - $quote->getSubtotalWithDiscount();
        $quote->setData('discount', $discount);
        $quote->setData('tax', $this->getTax($quote));
        $vars = [
            'quote' => $quote,
            'rule' => $rule,
            'ruleQuote' => $ruleQuote,
            'history' => $history,
            'customerIsGuest' => $quote->getCustomerGroupId() == GroupManagement::NOT_LOGGED_IN_ID,
            'urlmanager' => $this->urlManager->init($rule, $history),
            'formatmanager' => $this->formatManager->init(
                [
                    FormatManager::TYPE_HISTORY => $history,
                    FormatManager::TYPE_QUOTE => $quote,
                    FormatManager::TYPE_RULE_QUOTE => $ruleQuote
                ]
            ),
        ];
        $template = $this->emailTemplateResolver->execute($schedule);
        $template->setVars($vars)
            ->setOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $ruleQuote->getStoreId()
                ]
            );

        return $template;
    }

    /**
     * @param Quote $quote
     * @return float
     */
    private function getTax($quote)
    {
        if ($tax = $quote->getTotals()['tax'] ?? null) {
            return (float)$tax->getValue();
        }
        $productTax = .0;

        foreach ($quote->getAllItems() as $item) {
            $productTax += $item->getTaxAmount();
        }

        return $productTax;
    }
}
