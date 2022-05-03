<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\RuleExtensionInterface;
use Amasty\Acart\Api\Data\RuleExtensionInterfaceFactory;
use Amasty\Acart\Api\Data\RuleInterface;
use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface;
use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterfaceFactory;
use Amasty\Acart\Api\Data\ScheduleInterface;
use Amasty\Acart\Api\Data\ScheduleInterfaceFactory;
use Amasty\Acart\Api\ScheduleEmailTemplateRepositoryInterface;
use Amasty\Acart\Api\ScheduleRepositoryInterface;
use Amasty\Acart\Model\EmailTemplate as EmailTemplateModel;
use Amasty\Acart\Model\ResourceModel\Schedule\Collection as ScheduleCollection;
use Amasty\Acart\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Amasty\Acart\Model\Schedule as ScheduleModel;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Model\AbstractModel;

class Rule extends AbstractModel implements RuleInterface
{
    public const RULE_ID = 'rule_id';
    public const NAME = 'name';
    public const IS_ACTIVE = 'is_active';
    public const PRIORITY = 'priority';
    public const CONDITIONS_SERIALIZED = 'conditions_serialized';
    public const STORE_IDS = 'store_ids';
    public const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    public const CANCEL_CONDITION = 'cancel_condition';
    public const UTM_SOURCE = 'utm_source';
    public const UTM_MEDIUM = 'utm_medium';
    public const UTM_TERM = 'utm_term';
    public const UTM_CONTENT = 'utm_content';
    public const UTM_CAMPAIGN = 'utm_campaign';

    public const CANCEL_CONDITION_CLICKED = 'clicked';
    public const CANCEL_CONDITION_ANY_PRODUCT_WENT_OUT_OF_STOCK = 'any_product_went_out_of_stock';
    public const CANCEL_CONDITION_ALL_PRODUCTS_WENT_OUT_OF_STOCK = 'all_products_went_out_of_stock';
    public const CANCEL_CONDITION_ALL_PRODUCTS_WERE_DISABLED = 'all_products_were_disabled';
    public const SALES_RULE_PRODUCT_CONDITION_NAMESPACE = \Magento\SalesRule\Model\Rule\Condition\Product::class;

    public const RULE_ACTIVE = '1';
    public const RULE_INACTIVE = '0';

    public const FORM_NAMESPACE = 'amasty_acart_rule_form';
    public const CURRENT_AMASTY_ACART_RULE = 'current_amasty_acart_rule';

    /**
     * @var \Amasty\Acart\Model\SalesRule
     */
    protected $salesRule;

    /**
     * @var ScheduleCollection
     */
    protected $scheduleCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    /**
     * @var SalesRuleFactory
     */
    protected $salesRuleFactory;

    /**
     * @var Customer\AddressFactory
     */
    private $addressFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\CollectionFactory
     */
    private $addressCollectionFactory;

    /**
     * @var RuleExtensionInterfaceFactory
     */
    private $extensionFactory;

    /**
     * @var ScheduleRepositoryInterface
     */
    private $scheduleRepository;

    /**
     * @var ScheduleInterfaceFactory
     */
    private $scheduleFactory;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @var ScheduleEmailTemplateRepositoryInterface
     */
    private $emailTemplateRepository;

    /**
     * @var ScheduleEmailTemplateInterfaceFactory
     */
    private $emailTemplateFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Amasty\Base\Model\Serializer $serializer,
        \Amasty\Acart\Model\SalesRuleFactory $salesRuleFactory,
        \Amasty\Acart\Model\ResourceModel\Rule $resource,
        \Amasty\Acart\Model\Customer\AddressFactory $addressFactory,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory,
        RuleExtensionInterfaceFactory $extensionFactory,
        ScheduleRepositoryInterface $scheduleRepository,
        ScheduleInterfaceFactory $scheduleFactory,
        ScheduleCollectionFactory $scheduleCollectionFactory,
        ScheduleEmailTemplateRepositoryInterface $emailTemplateRepository,
        ScheduleEmailTemplateInterfaceFactory $emailTemplateFactory,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->dateTime = $dateTime;
        $this->date = $date;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->serializer = $serializer;
        $this->addressFactory = $addressFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->extensionFactory = $extensionFactory;
        $this->scheduleRepository = $scheduleRepository;
        $this->scheduleFactory = $scheduleFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->emailTemplateFactory = $emailTemplateFactory;
    }

    /**
     * @param $ruleId
     *
     * @return $this
     */
    public function loadById($ruleId)
    {
        $this->_resource->load($this, $ruleId);

        return $this;
    }

    /**
     * _construct
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
    }

    /**
     * @return mixed
     */
    public function getSalesRule()
    {
        if (!$this->salesRule) {
            $this->salesRule = $this->salesRuleFactory->create()->load($this->getRuleId());
        }

        return $this->salesRule;
    }

    public function saveSchedule()
    {
        $savedIds = [];
        $schedule = $this->getSchedule();

        if (!is_array($schedule)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The schedule should be completed.'));
        }

        foreach ($schedule as $scheduleConfig) {
            if (isset($scheduleConfig['schedule_id'])) {
                $scheduleModel = $this->scheduleRepository->getById($scheduleConfig['schedule_id']);
            } else {
                /** @var ScheduleInterface|ScheduleModel $scheduleModel */
                $scheduleModel = $this->scheduleFactory->create();
            }

            $emailTemplate = $scheduleConfig['custom_template'] ?? [];
            unset($scheduleConfig['custom_template']);

            $scheduleModel->addData($scheduleConfig);
            $scheduleModel->setRuleId($this->getRuleId());
            $scheduleModel->setSalesRuleId($scheduleModel->getSalesRuleId() ?: null);
            $scheduleModel->setTemplateId($scheduleModel->getTemplateId() ?: null);
            $scheduleModel->setEmailTemplate($this->prepareEmailTemplate($emailTemplate));
            $this->scheduleRepository->save($scheduleModel);
            $savedIds[] = $scheduleModel->getId();
        }

        /** @var \Amasty\Acart\Model\ResourceModel\Schedule\Collection $scheduleCollection */
        $scheduleCollection = $this->scheduleCollectionFactory->create();
        $scheduleCollection->addFieldToFilter(Schedule::RULE_ID, $this->getRuleId())
            ->addFieldToFilter(Schedule::SCHEDULE_ID, ['nin' => $savedIds]);

        foreach ($scheduleCollection as $scheduleToDelete) {
            $this->scheduleRepository->delete($scheduleToDelete);
        }

        $ruleProductAttributes = $this->_getUsedAttributes($this->getConditionsSerialized());

        if (count($ruleProductAttributes)) {
            $this->getResource()->saveAttributes($this->getRuleId(), $ruleProductAttributes);
        }
    }

    private function prepareEmailTemplate(array $emailTemplate): ?ScheduleEmailTemplateInterface
    {
        if (empty($emailTemplate)) {
            return null;
        }

        if (isset($emailTemplate[EmailTemplateModel::TEMPLATE_ID])) {
            $emailTemplateModel = $this->emailTemplateRepository->getById(
                (int)$emailTemplate[EmailTemplateModel::TEMPLATE_ID]
            );
        } else {
            $emailTemplateModel = $this->emailTemplateFactory->create();
        }

        $emailTemplateModel->addData($emailTemplate);
        $emailTemplateModel->setTemplateType(
            $emailTemplateModel->getTemplateType() ?: $emailTemplateModel::TYPE_HTML
        );
        $emailTemplateModel->setTemplateCode(
            $emailTemplateModel->getTemplateCode() ?: 'AmastyAbandonedCartReminder' . uniqid()
        );

        return $emailTemplateModel;
    }

    /**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     *
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = [];
        $data = $this->serializer->unserialize($serializedString);

        if (is_array($data) && array_key_exists('conditions', $data)) {
            $result = $this->recursiveFindAttributes($data);
        }

        return array_filter($result);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function recursiveFindAttributes($data)
    {
        $arrayIterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data));
        $result = [];
        $conditionAttribute = false;

        foreach ($arrayIterator as $key => $value) {
            if ($key == 'type' && $value == self::SALES_RULE_PRODUCT_CONDITION_NAMESPACE) {
                $conditionAttribute = true;
            }

            if ($key == 'attribute' && $conditionAttribute) {
                $result[] = $value;
                $conditionAttribute = false;
            }
        }

        return $result;
    }

    /**
     * @return ScheduleCollection
     */
    public function getScheduleCollection()
    {
        if (!$this->scheduleCollection) {
            $this->scheduleCollection = $this->scheduleCollectionFactory->create()
                ->addFieldToFilter('rule_id', $this->getRuleId());
        }

        return $this->scheduleCollection;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return bool
     */
    private function validateAddress(\Magento\Quote\Model\Quote $quote)
    {
        $isValid = false;
        $quoteAddressIds = [];

        foreach ($quote->getAllAddresses() as $address) {
            if ($address->getAddressType() == \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING) {
                $address->setCollectShippingRates(true);
                $address->collectShippingRates();
            }

            $this->_initAddress($address, $quote);

            if ($this->getSalesRule()->validate($address)) {
                $isValid = true;
                break;
            } elseif ($address->getCustomerAddressId()) {
                $quoteAddressIds[] = $address->getCustomerAddressId();
            }
        }

        if (!$isValid && $quote->getCustomerGroupId() != GroupManagement::NOT_LOGGED_IN_ID) {
            /** @var \Magento\Customer\Model\ResourceModel\Address\Collection $collection */
            $collection = $this->addressCollectionFactory->create();
            $collection->addFieldToFilter('parent_id', $quote->getCustomerId())
                ->addAttributeToSelect('*');

            if (!empty($quoteAddressIds)) {
                $collection->addFieldToFilter('entity_id', ['nin' => $quoteAddressIds]);
            }

            foreach ($collection->getItems() as $address) {
                if ($address instanceof \Magento\Customer\Model\Address) {
                    $address = $this->addressFactory->create()
                        ->setAddress($address->getDataModel())
                        ->setQuote($quote);
                }

                if ($this->getSalesRule()->validate($address)) {
                    $isValid = true;
                    break;
                }
            }
        }

        return $isValid;
    }

    protected function _initAddress($address, $quote)
    {
        $address->setData('total_qty', $quote->getData('items_qty'));

        return $address;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return bool
     */
    public function validate(\Magento\Quote\Model\Quote $quote)
    {
        $storesIds = $this->getStoreIds();
        $customerGroupIds = $this->getCustomerGroupIds();
        $validStore = $validCustomerGroup = true;

        if (!empty($storesIds)) {
            $validStore = in_array($quote->getStoreId(), $storesIds);
        }

        if (!empty($customerGroupIds)) {
            $validCustomerGroup = in_array($quote->getCustomerGroupId(), $customerGroupIds);
        }

        return $validStore
            && $validCustomerGroup
            && $this->validateAddress($quote);
    }

    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    public function setRuleId($id)
    {
        $this->setData(self::RULE_ID, $id);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(?string $name): RuleInterface
    {
        $this->setData(self::NAME, $name);

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->getData(self::IS_ACTIVE);
    }

    public function setIsActive(bool $isActive): RuleInterface
    {
        $this->setData(self::IS_ACTIVE, $isActive);

        return $this;
    }

    public function getPriority(): int
    {
        return $this->getData(self::PRIORITY);
    }

    public function setPriority(int $priority): RuleInterface
    {
        $this->setData(self::PRIORITY, $priority);

        return $this;
    }

    public function getConditionsSerialized(): ?string
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    public function setConditionsSerialized(string $conditionsSerialized): RuleInterface
    {
        $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);

        return $this;
    }

    public function getStoreIds(): array
    {
        return $this->getData(self::STORE_IDS) ?? [];
    }

    public function setStoreIds(array $storeIds): RuleInterface
    {
        $this->setData(self::STORE_IDS, $storeIds);

        return $this;
    }

    public function getCustomerGroupIds(): array
    {
        return $this->getData(self::CUSTOMER_GROUP_IDS) ?? [];
    }

    public function setCustomerGroupIds(array $customerGroupIds): RuleInterface
    {
        $this->setData(self::CUSTOMER_GROUP_IDS, $customerGroupIds);

        return $this;
    }

    public function getCancelCondition(): string
    {
        return $this->getData(self::CANCEL_CONDITION);
    }

    public function setCancelCondition(string $cancelCondition): RuleInterface
    {
        $this->setData(self::CANCEL_CONDITION, $cancelCondition);

        return $this;
    }

    public function getUtmSource(): ?string
    {
        return $this->getData(self::UTM_SOURCE);
    }

    public function setUtmSource(string $utmSource): RuleInterface
    {
        $this->setData(self::UTM_SOURCE, $utmSource);

        return $this;
    }

    public function getUtmMedium(): ?string
    {
        return $this->getData(self::UTM_MEDIUM);
    }

    public function setUtmMedium(string $utmMedium): RuleInterface
    {
        $this->setData(self::UTM_MEDIUM, $utmMedium);

        return $this;
    }

    public function getUtmTerm(): ?string
    {
        return $this->getData(self::UTM_TERM);
    }

    public function setUtmTerm(string $utmTerm): RuleInterface
    {
        $this->setData(self::UTM_TERM, $utmTerm);

        return $this;
    }

    public function getUtmContent(): ?string
    {
        return $this->getData(self::UTM_CONTENT);
    }

    public function setUtmContent(string $utmContent): RuleInterface
    {
        $this->setData(self::UTM_CONTENT, $utmContent);

        return $this;
    }

    public function getUtmCampaign(): ?string
    {
        return $this->getData(self::UTM_CAMPAIGN);
    }

    public function setUtmCampaign(string $utmCampaign): RuleInterface
    {
        $this->setData(self::UTM_CAMPAIGN, $utmCampaign);

        return $this;
    }

    public function getExtensionAttributes(): RuleExtensionInterface
    {
        if (null === $this->getData(self::EXTENSION_ATTRIBUTES_KEY)) {
            $this->setExtensionAttributes($this->extensionFactory->create());
        }

        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    public function setExtensionAttributes(RuleExtensionInterface $extensionAttributes): RuleInterface
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);

        return $this;
    }
}
