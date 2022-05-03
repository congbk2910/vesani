<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\RuleQuoteInterface;
use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Api\RuleQuoteRepositoryInterface;
use Amasty\Acart\Api\RuleRepositoryInterface;
use Magento\Framework\Model\AbstractModel;

class RuleQuote extends AbstractModel implements RuleQuoteInterface
{
    public const RULE_QUOTE_ID = 'rule_quote_id';
    public const QUOTE_ID = 'quote_id';
    public const RULE_ID = 'rule_id';
    public const STATUS = 'status';
    public const STORE_ID = 'store_id';
    public const CUSTOMER_ID = 'customer_id';
    public const CUSTOMER_EMAIL = 'customer_email';
    public const CUSTOMER_FIRSTNAME = 'customer_firstname';
    public const CUSTOMER_LASTNAME = 'customer_lastname';
    public const CUSTOMER_PHONE = 'customer_phone';
    public const TEST_MODE = 'test_mode';
    public const CREATED_AT = 'created_at';
    public const ABANDONED_STATUS = 'abandoned_status';

    public const COMPLETE_QUOTE_REASON_PLACE_ORDER = 'place_order';
    public const COMPLETE_QUOTE_REASON_CLICK_LINK = 'click_by_link';
    public const COMPLETE_QUOTE_REASON_UPDATE_QUOTE = 'update_quote';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETE = 'complete';
    public const ABANDONED_RESTORED_STATUS = 'restored';
    public const ABANDONED_NOT_RESTORED_STATUS = 'notrestored';

    /**
     * @var ResourceModel\History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var RuleQuoteRepositoryInterface
     */
    private $ruleQuoteRepository;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleQuote\CollectionFactory
     */
    private $ruleQuoteCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\History\CollectionFactory $historyCollectionFactory,
        RuleFactory $ruleFactory,
        \Amasty\Acart\Model\ResourceModel\RuleQuote $resource,
        RuleQuoteRepositoryInterface $ruleQuoteRepository,
        HistoryRepositoryInterface $historyRepository,
        RuleRepositoryInterface $ruleRepository,
        ResourceModel\RuleQuote\CollectionFactory $ruleQuoteCollectionFactory,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->ruleFactory = $ruleFactory;
        $this->ruleQuoteRepository = $ruleQuoteRepository;
        $this->historyRepository = $historyRepository;
        $this->ruleRepository = $ruleRepository;
        $this->ruleQuoteCollectionFactory = $ruleQuoteCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\RuleQuote::class);
        $this->setIdFieldName(self::RULE_QUOTE_ID);
    }

    public function getRuleQuoteId(): ?int
    {
        return $this->getData(self::RULE_QUOTE_ID);
    }

    public function setRuleQuoteId(?int $ruleQuoteId): RuleQuoteInterface
    {
        $this->setData(self::RULE_QUOTE_ID, $ruleQuoteId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuoteId(): ?int
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @param int|null $quoteId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setQuoteId($quoteId): RuleQuoteInterface
    {
        $this->setData(self::QUOTE_ID, $quoteId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRuleId(): ?int
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * @param int|null $ruleId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setRuleId($ruleId): RuleQuoteInterface
    {
        $this->setData(self::RULE_ID, $ruleId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string|null $status
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setStatus(?string $status): RuleQuoteInterface
    {
        $this->setData(self::STATUS, $status);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @param int|null $storeId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setStoreId(?int $storeId): RuleQuoteInterface
    {
        $this->setData(self::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param int|null $customerId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerId(?int $customerId): RuleQuoteInterface
    {
        $this->setData(self::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @param string|null $customerEmail
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerEmail(?string $customerEmail): RuleQuoteInterface
    {
        $this->setData(self::CUSTOMER_EMAIL, $customerEmail);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerFirstname(): ?string
    {
        return $this->getData(self::CUSTOMER_FIRSTNAME);
    }

    /**
     * @param string|null $customerFirstname
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerFirstname(?string $customerFirstname): RuleQuoteInterface
    {
        $this->setData(self::CUSTOMER_FIRSTNAME, $customerFirstname);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerLastname(): ?string
    {
        return $this->getData(self::CUSTOMER_LASTNAME);
    }

    /**
     * @param string|null $customerLastname
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerLastname(?string $customerLastname): RuleQuoteInterface
    {
        $this->setData(self::CUSTOMER_LASTNAME, $customerLastname);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerPhone(): ?string
    {
        return $this->getData(self::CUSTOMER_PHONE);
    }

    /**
     * @param string|null $customerPhone
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerPhone(?string $customerPhone): RuleQuoteInterface
    {
        $this->setData(self::CUSTOMER_PHONE, $customerPhone);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTestMode(): int
    {
        return $this->getData(self::TEST_MODE);
    }

    /**
     * @param int|null $testMode
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setTestMode(?int $testMode): RuleQuoteInterface
    {
        $this->setData(self::TEST_MODE, $testMode);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string|null $createdAt
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCreatedAt(?string $createdAt): RuleQuoteInterface
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAbandonedStatus(): ?string
    {
        return $this->getData(self::ABANDONED_STATUS);
    }

    /**
     * @param string|null $abandonedStatus
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setAbandonedStatus(?string $abandonedStatus): RuleQuoteInterface
    {
        $this->setData(self::ABANDONED_STATUS, $abandonedStatus);

        return $this;
    }

    /**
     * @param string $reason
     *
     * @return void
     */
    public function complete($reason = '')
    {
        /** @var ResourceModel\History\Collection $pendingHistoryCollection */
        $pendingHistoryCollection = $this->historyCollectionFactory->create();
        $pendingHistoryCollection
            ->addFieldToFilter(History::RULE_QUOTE_ID, $this->getRuleQuoteId())
            ->addFieldToFilter(History::STATUS, History::STATUS_PROCESSING);

        switch ($reason) {
            case self::COMPLETE_QUOTE_REASON_UPDATE_QUOTE:
            case self::COMPLETE_QUOTE_REASON_PLACE_ORDER:
            case self::COMPLETE_QUOTE_REASON_CLICK_LINK:
                $this->setStatus(self::STATUS_COMPLETE);
                $this->ruleQuoteRepository->save($this);
                break;
            default:
                if (!$pendingHistoryCollection->getSize()) {
                    $this->setStatus(self::STATUS_COMPLETE);
                    $this->ruleQuoteRepository->save($this);
                }
        }
    }

    public function clickByLink()
    {
        $rule = $this->ruleRepository->get($this->getRuleId());
        $cancelConditions = explode(',', $rule->getCancelCondition());

        if (in_array(Rule::CANCEL_CONDITION_CLICKED, $cancelConditions)) {
            foreach ($this->getProcessingRuleQuotes($this->getCustomerEmail()) as $ruleQuote) {
                $ruleQuote->complete(self::COMPLETE_QUOTE_REASON_CLICK_LINK);
                /** @var ResourceModel\History\Collection $historyCollection */
                $historyCollection = $this->historyCollectionFactory->create()
                    ->addFieldToFilter(History::RULE_QUOTE_ID, $ruleQuote->getRuleQuoteId());

                foreach ($historyCollection as $history) {
                    $history->setStatus(History::STATUS_CANCEL_EVENT);
                    $this->historyRepository->save($history);
                }
            }
        }
    }

    public function updateQuote(int $quoteId)
    {
        $this->completeByQuoteId(
            $quoteId,
            self::COMPLETE_QUOTE_REASON_UPDATE_QUOTE,
            self::COMPLETE_QUOTE_REASON_UPDATE_QUOTE
        );
    }

    public function buyQuote(int $quoteId)
    {
        $this->completeByQuoteId(
            $quoteId,
            self::COMPLETE_QUOTE_REASON_PLACE_ORDER,
            self::COMPLETE_QUOTE_REASON_PLACE_ORDER
        );
    }

    private function completeByQuoteId(int $quoteId, string $historyStatus, string $reason)
    {
        /** @var ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->ruleQuoteCollectionFactory->create();
        $ruleQuoteCollection
            ->addFieldToFilter(self::QUOTE_ID, $quoteId)
            ->addOrder(self::RULE_QUOTE_ID)
            ->setPageSize(1);

        $ruleQuote = $ruleQuoteCollection->getFirstItem();

        if ($ruleQuote->getRuleQuoteId()) {
            foreach ($this->getProcessingRuleQuotes($ruleQuote->getCustomerEmail()) as $ruleQuote) {
                $ruleQuote->complete($reason);
                /** @var ResourceModel\History\Collection $historyCollection */
                $historyCollection = $this->historyCollectionFactory->create()
                    ->addFieldToFilter(History::RULE_QUOTE_ID, $ruleQuote->getRuleQuoteId())
                    ->addFieldToFilter(History::STATUS, History::STATUS_PROCESSING);

                foreach ($historyCollection as $history) {
                    $history->setStatus($historyStatus);
                    $this->historyRepository->save($history);
                }
            }
        }
    }

    private function getProcessingRuleQuotes(string $customerEmail)
    {
        /** @var ResourceModel\RuleQuote\Collection $ruleQuoteCollection */
        $ruleQuoteCollection = $this->ruleQuoteCollectionFactory->create();
        $ruleQuoteCollection
            ->addFieldToFilter(self::CUSTOMER_EMAIL, $customerEmail)
            ->addFieldToFilter(self::STATUS, self::STATUS_PROCESSING);

        return $ruleQuoteCollection;
    }

    public function getRule()
    {
        return $this->ruleRepository->get($this->getRuleId());
    }
}
