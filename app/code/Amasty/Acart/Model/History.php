<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\BlacklistRepositoryInterface;
use Amasty\Acart\Api\Data\HistoryExtensionInterface;
use Amasty\Acart\Api\Data\HistoryInterface;
use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Api\RuleQuoteRepositoryInterface;
use Amasty\Acart\Api\RuleRepositoryInterface;
use Amasty\Acart\Model\Mail\MessageBuilder\MessageBuilder;
use Amasty\Acart\Model\Mail\MessageBuilder\MessageBuilderFactory;
use Amasty\Acart\Model\Mail\TrackingPixelModifier;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class History extends AbstractExtensibleModel implements HistoryInterface
{
    public const HISTORY_ID = 'history_id';
    public const RULE_QUOTE_ID = 'rule_quote_id';
    public const SCHEDULE_ID = 'schedule_id';
    public const STATUS = 'status';
    public const PUBLIC_KEY = 'public_key';
    public const EMAIL_SUBJECT = 'email_subject';
    public const EMAIL_BODY = 'email_body';
    public const SALES_RULE_ID = 'sales_rule_id';
    public const SALES_RULE_COUPON_ID = 'sales_rule_coupon_id';
    public const SALES_RULE_COUPON = 'sales_rule_coupon';
    public const SCHEDULED_AT = 'scheduled_at';
    public const EXECUTED_AT = 'executed_at';
    public const FINISHED_AT = 'finished_at';
    public const OPENED_COUNT = 'opened';
    public const SALES_RULE_COUPON_EXPIRATION_DATE = 'sales_rule_coupon_expiration_date';

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SENT = 'sent';
    public const STATUS_CANCEL_EVENT = 'cancel_event';
    public const STATUS_BLACKLIST = 'blacklist';
    public const STATUS_ADMIN = 'admin';
    public const STATUS_NOT_NEWSLETTER_SUBSCRIBER = 'not_newsletter_subscriber';

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $_quoteFactory;

    /**
     * @var RuleQuoteRepositoryInterface
     */
    private $ruleQuoteRepository;

    /**
     * @var \Magento\Framework\Mail\MessageFactory
     */
    private $messageFactory;

    /**
     * @var \Magento\Framework\Mail\TransportInterfaceFactory
     */
    private $mailTransportFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $salesRuleFactory;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection
     */
    private $newsletterSubscriberCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MessageBuilder
     */
    private $messageBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var TrackingPixelModifier
     */
    private $trackingPixelModifier;

    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory,
        \Magento\Framework\Mail\MessageFactory $messageFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        RuleQuoteRepositoryInterface $ruleQuoteRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\SalesRule\Model\RuleFactory $salesRuleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Acart\Model\ConfigProvider $configProvider,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection $newsletterSubscriberCollection,
        MessageBuilderFactory $messageBuilderFactory,
        \Amasty\Acart\Model\UrlManager $urlManager,
        HistoryRepositoryInterface $historyRepository,
        BlacklistRepositoryInterface $blacklistRepository,
        RuleRepositoryInterface $ruleRepository,
        TrackingPixelModifier $trackingPixelModifier,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->dateTime = $dateTime;
        $this->date = $date;
        $this->messageFactory = $messageFactory;
        $this->mailTransportFactory = $mailTransportFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->ruleQuoteRepository = $ruleQuoteRepository;
        $this->stockRegistry = $stockRegistry;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->storeManager = $storeManager;
        $this->newsletterSubscriberCollection = $newsletterSubscriberCollection;
        $this->messageBuilder = $messageBuilderFactory->create();
        $this->configProvider = $configProvider;
        $this->urlManager = $urlManager;
        $this->historyRepository = $historyRepository;
        $this->blacklistRepository = $blacklistRepository;
        $this->ruleRepository = $ruleRepository;
        $this->trackingPixelModifier = $trackingPixelModifier;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\History::class);
        $this->setIdFieldName(self::HISTORY_ID);
    }

    public function getHistoryId(): ?int
    {
        return $this->getData(self::HISTORY_ID);
    }

    public function setHistoryId(?int $historyId): HistoryInterface
    {
        $this->setData(self::HISTORY_ID, $historyId);

        return $this;
    }

    public function getRuleQuoteId(): ?int
    {
        return $this->getData(self::RULE_QUOTE_ID);
    }

    public function setRuleQuoteId($ruleQuoteId): HistoryInterface
    {
        $this->setData(self::RULE_QUOTE_ID, $ruleQuoteId);

        return $this;
    }

    public function getScheduleId(): ?int
    {
        return $this->getData(self::SCHEDULE_ID);
    }

    public function setScheduleId($scheduleId): HistoryInterface
    {
        $this->setData(self::SCHEDULE_ID, $scheduleId);

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus(?string $status): HistoryInterface
    {
        $this->setData(self::STATUS, $status);

        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->getData(self::PUBLIC_KEY);
    }

    public function setPublicKey(?string $publicKey): HistoryInterface
    {
        $this->setData(self::PUBLIC_KEY, $publicKey);

        return $this;
    }

    public function getEmailSubject(): ?string
    {
        return $this->getData(self::EMAIL_SUBJECT);
    }

    public function setEmailSubject(?string $emailSubject): HistoryInterface
    {
        $this->setData(self::EMAIL_SUBJECT, $emailSubject);

        return $this;
    }

    public function getEmailBody(): string
    {
        return $this->getData(self::EMAIL_BODY) ?? '';
    }

    public function setEmailBody(?string $emailBody): HistoryInterface
    {
        $this->setData(self::EMAIL_BODY, $emailBody);

        return $this;
    }

    public function getSalesRuleId(): ?string
    {
        return $this->getData(self::SALES_RULE_ID);
    }

    public function setSalesRuleId($salesRuleId): HistoryInterface
    {
        $this->setData(self::SALES_RULE_ID, $salesRuleId);

        return $this;
    }

    public function getSalesRuleCouponId(): ?int
    {
        return $this->getData(self::SALES_RULE_COUPON_ID);
    }

    public function setSalesRuleCouponId(?int $salesRuleCouponId): HistoryInterface
    {
        $this->setData(self::SALES_RULE_COUPON_ID, $salesRuleCouponId);

        return $this;
    }

    public function getSalesRuleCoupon(): ?string
    {
        return $this->getData(self::SALES_RULE_COUPON);
    }

    public function setSalesRuleCoupon(?string $salesRuleCoupon): HistoryInterface
    {
        $this->setData(self::SALES_RULE_COUPON, $salesRuleCoupon);

        return $this;
    }

    public function getScheduledAt(): ?string
    {
        return $this->getData(self::SCHEDULED_AT);
    }

    public function setScheduledAt(?string $scheduledAt): HistoryInterface
    {
        $this->setData(self::SCHEDULED_AT, $scheduledAt);

        return $this;
    }

    public function getExecutedAt(): ?string
    {
        return $this->getData(self::EXECUTED_AT);
    }

    public function setExecutedAt(?string $executedAt): HistoryInterface
    {
        $this->setData(self::EXECUTED_AT, $executedAt);

        return $this;
    }

    public function getFinishedAt(): ?string
    {
        return $this->getData(self::FINISHED_AT);
    }

    public function setFinishedAt(?string $finishedAt): HistoryInterface
    {
        $this->setData(self::FINISHED_AT, $finishedAt);

        return $this;
    }

    public function getOpenedCount(): int
    {
        return (int)$this->getData(self::OPENED_COUNT);
    }

    public function setOpenedCount(int $count): HistoryInterface
    {
        $this->setData(self::OPENED_COUNT, $count);

        return $this;
    }

    public function getSalesRuleCouponExpirationDate(): ?string
    {
        return $this->getData(self::SALES_RULE_COUPON_EXPIRATION_DATE);
    }

    public function setSalesRuleCouponExpirationDate(?string $salesRuleCouponExpirationDate): HistoryInterface
    {
        $this->setData(self::SALES_RULE_COUPON_EXPIRATION_DATE, $salesRuleCouponExpirationDate);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?HistoryExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(HistoryExtensionInterface $extensionAttributes): HistoryInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param null|int $storeId
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->getStoreId();
        }

        return $this->storeManager->getStore($storeId);
    }

    /**
     * @param bool $testMode
     */
    public function execute($testMode = false)
    {
        if (!$this->_cancel()) {
            $this->setExecutedAt($this->dateTime->formatDate($this->date->gmtTimestamp()));
            $this->historyRepository->save($this);

            if ($testMode) {
                $this->_sendEmail($testMode);
                $status = self::STATUS_SENT;
            } else {
                try {
                    $blacklist = $this->blacklistRepository->getByCustomerEmail($this->getCustomerEmail());
                } catch (NotFoundException $e) {
                    $blacklist = null;
                }

                if ($blacklist && $blacklist->getBlacklistId()) {
                    $status = self::STATUS_BLACKLIST;
                } elseif (!$this->validateNewsletterSubscribersOnly($this->getCustomerEmail())) {
                    $status = self::STATUS_NOT_NEWSLETTER_SUBSCRIBER;
                } else {
                    $this->_sendEmail($testMode);
                    $status = self::STATUS_SENT;
                }
            }

            $this->setStatus($status);

            $this->setFinishedAt($this->dateTime->formatDate($this->date->gmtTimestamp()));
            $this->historyRepository->save($this);
        } else {
            $this->setStatus(self::STATUS_CANCEL_EVENT);
            $this->historyRepository->save($this);
            $ruleQuote = $this->ruleQuoteRepository->getById((int)$this->getRuleQuoteId());
            $ruleQuote->complete();
        }
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    private function validateNewsletterSubscribersOnly($email)
    {
        if (!$this->configProvider->isEmailsToNewsletterSubscribersOnly($this->getStoreId())) {
            return true;
        }

        /** @var \Magento\Newsletter\Model\Subscriber|null $newsletterSubscriber */
        $newsletterSubscriber = $this->newsletterSubscriberCollection->getItemByColumnValue(
            'subscriber_email',
            $email
        );

        return $newsletterSubscriber
            && $newsletterSubscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     *
     * @return bool|\Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    protected function _getStockItem($quoteItem)
    {
        if (!$quoteItem
            || !$quoteItem->getProductId()
            || !$quoteItem->getQuote()
            || $quoteItem->getQuote()->getIsSuperMode()
        ) {
            return false;
        }

        $stockItem = $this->stockRegistry->getStockItem(
            $quoteItem->getProduct()->getId(),
            $quoteItem->getProduct()->getStore()->getWebsiteId()
        );

        return $stockItem;
    }

    /**
     * @return bool
     */
    protected function _cancel()
    {
        $cancel = false;

        if ($this->getCancelCondition()) {
            foreach (explode(',', $this->getCancelCondition()) as $cancelCondition) {
                $quote = $this->_quoteFactory->create()->load($this->getQuoteId());

                if (!$quote->getId()) {
                    $quote = $quote->loadByIdWithoutStore($this->getQuoteId());
                }

                $quoteValidation = $this->_validateCancelQuote($quote);

                switch ($cancelCondition) {
                    case \Amasty\Acart\Model\Rule::CANCEL_CONDITION_ALL_PRODUCTS_WENT_OUT_OF_STOCK:
                        if (!$quoteValidation['all_products']) {
                            $cancel = true;
                        }
                        break;
                    case \Amasty\Acart\Model\Rule::CANCEL_CONDITION_ANY_PRODUCT_WENT_OUT_OF_STOCK:
                        if (!$quoteValidation['any_products']) {
                            $cancel = true;
                        }
                        break;
                    case \Amasty\Acart\Model\Rule::CANCEL_CONDITION_ALL_PRODUCTS_WERE_DISABLED:
                        if ($quoteValidation['all_disabled']) {
                            $cancel = true;
                        }
                        break;
                }
            }
        }

        return $cancel;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return array
     */
    protected function _validateCancelQuote($quote)
    {
        $inStock = 0;

        foreach ($quote->getAllItems() as $item) {
            $stockItem = $this->_getStockItem($item);

            if ($stockItem) {
                if ($stockItem->getIsInStock()) {
                    $inStock++;
                }
            }
        }

        return [
            'all_products' => (($inStock == 0) ? false : true),
            'any_products' => (((count($quote->getAllItems()) - $inStock) != 0) ? false : true),
            'all_disabled' => ((count($quote->getAllVisibleItems()) == 0) ? true : false)
        ];
    }

    /**
     * @param bool $testMode
     */
    private function _sendEmail($testMode = false)
    {
        $bcc = $this->configProvider->getBcc($this->getStoreId());
        $isBssMethod = ($this->configProvider->getCopyMethod($this->getStoreId()) === 'bcc');
        $safeMode = $this->configProvider->isSafeMode($this->getStoreId());
        $recipientEmail = $this->configProvider->getRecipientEmailForTest();
        $to = $this->getCustomerEmail();
        $body = $this->getEmailBody();

        if ($testMode || $safeMode) {
            if ($recipientEmail) {
                $to = $recipientEmail;
            } else {
                throw new LocalizedException(
                    __('Please fill in the test email in the extension configuration section')
                );
            }
        }

        if (!$testMode && !$safeMode && $bcc) {
            $bcc = array_map('trim', explode(',', $bcc));

            if (!$isBssMethod) {
                $this->createAndSendMessage($bcc, $this->prepareCopyToEmailBody($body));
                $bcc = null;
            }
        } else {
            $bcc = null;
        }

        $this->createAndSendMessage($to, $body, $bcc);
    }

    /**
     * @param array|string $toEmail
     * @param string $body
     * @param null|array $bcc
     *
     * @throws \Magento\Framework\Exception\MailException
     */
    private function createAndSendMessage($toEmail, $body, $bcc = null)
    {
        $senderName = $this->configProvider->getSenderName($this->getStoreId());
        $senderEmail = $this->configProvider->getSenderEmail($this->getStoreId());
        $replyToEmail = $this->configProvider->getReplyToEmail($this->getStoreId());
        // Compatibility with Mageplaza_Smtp
        $isSetMpSmtpStoreId = $this->_registry->registry('mp_smtp_store_id');

        if ($isSetMpSmtpStoreId === null) {
            $this->_registry->register('mp_smtp_store_id', $this->getStoreId());
        }

        $name = [
            $this->getCustomerFirstname(),
            $this->getCustomerLastname(),
        ];
        // phpcs:ignore
        $emailSubject = html_entity_decode($this->getEmailSubject(), ENT_QUOTES);
        /** @var \Magento\Framework\Mail\Message $message */
        $message = $this->messageFactory->create();
        $message->addTo($toEmail, implode(' ', $name));
        $message->setSubject($emailSubject);

        if (method_exists($message, 'setFromAddress')) {
            $message->setFromAddress($senderEmail, $senderName);
        } else {
            $message->setFrom($senderEmail, $senderName);
        }

        $body = $this->trackingPixelModifier->execute($this->getPublicKey() ?? '', $body);

        if (method_exists($message, 'setBodyHtml')) {
            $message->setBodyHtml($body);
        } else {
            $message->setBody($body)
                ->setMessageType(\Magento\Framework\Mail\MessageInterface::TYPE_HTML);
        }

        if ($replyToEmail) {
            $message->setReplyTo($replyToEmail);
        }

        if ($bcc) {
            $message->addBcc($bcc);
        }

        if ($message instanceof \Webkul\Rmasystem\Mail\Message) {
            $message->setPartsToBody();
        }

        // This is a compatibility fill for the implemented EmailMessageInterface in Magento 2.3.3.
        if ($this->messageBuilder) {
            $message = $this->messageBuilder->build($message);
        }

        $mailTransport = $this->mailTransportFactory->create(
            [
                'message' => $message
            ]
        );
        $mailTransport->sendMessage();

        if ($isSetMpSmtpStoreId === null) {
            $this->_registry->unregister('mp_smtp_store_id');
        }
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        return $this->ruleQuoteRepository->getById((int)$this->getRuleQuoteId())->getRule();
    }

    /**
     * @param string $body
     *
     * @return string
     */
    private function prepareCopyToEmailBody($body)
    {
        $this->urlManager->init($this->getRule(), $this);
        $cartUrl = $this->urlManager->mageUrl('checkout/cart/index');
        $replaceUrl = $this->urlManager->frontUrl();

        return str_replace($cartUrl, $replaceUrl, $body);
    }
}
