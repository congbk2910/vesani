<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

class CouponForHistoryFactory
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $salesRuleFactory;

    /**
     * @var \Magento\SalesRule\Model\Coupon\Massgenerator
     */
    private $couponGenerator;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory
     */
    private $couponCollectionFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    private $groupCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection|null
     */
    private $groupCollection = null;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\SalesRule\Model\RuleFactory $salesRuleFactory,
        \Magento\SalesRule\Model\Coupon\Massgenerator $couponMassgenerator,
        \Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory $couponCollectionFactory,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->date = $date;
        $this->dateTime = $dateTime;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->couponGenerator = $couponMassgenerator;
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->serializer = $serializer;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function create(
        \Amasty\Acart\Api\Data\RuleQuoteInterface $ruleQuote,
        \Amasty\Acart\Api\Data\ScheduleInterface $schedule,
        \Amasty\Acart\Model\Rule $rule
    ) {
        $store = $this->storeManager->getStore($ruleQuote->getStoreId());
        /** @var \Magento\SalesRule\Model\Rule $salesRule */
        $salesRule = $this->salesRuleFactory->create();
        $salesRule->setData(
            [
                'name' => 'Amasty: Abandoned Cart Coupon #' . $ruleQuote->getCustomerEmail(),
                'is_active' => '1',
                'website_ids' => [
                    $store->getWebsiteId()
                ],
                'customer_group_ids' => $rule->getCustomerGroupIds() ?: $this->getAllCustomerGroupIds(),
                'coupon_code' => strtoupper(uniqid()),
                'uses_per_coupon' => 1,
                'coupon_type' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC,
                'from_date' => '',
                'to_date' => $this->getCouponToDate((int)$schedule->getExpiredInDays(), $schedule->getDeliveryTime()),
                'uses_per_customer' => 1,
                'simple_action' => $schedule->getSimpleAction(),
                'discount_amount' => $schedule->getDiscountAmount(),
                'stop_rules_processing' => '0',
            ]
        );

        if ($schedule->getDiscountQty() > 0) {
            $salesRule->setDiscountQty($schedule->getDiscountQty());
        }

        if ($schedule->getDiscountStep() > 0) {
            $salesRule->setDiscountStep($schedule->getDiscountStep());
        }

        $salesRule->setConditionsSerialized($this->serializer->serialize($this->getConditions($rule)));
        $salesRule->save();

        return $salesRule;
    }

    private function getConditions(\Amasty\Acart\Model\Rule $rule): array
    {
        $salesRuleConditions = [];
        $conditions = $rule->getSalesRule()->getConditions()->asArray();

        if (isset($conditions['conditions'])) {
            foreach ($conditions['conditions'] as $idx => $condition) {
                if ($condition['attribute'] !== \Amasty\Acart\Model\SalesRule\Condition\Carts::ATTRIBUTE_CARDS_NUM) {
                    $salesRuleConditions[] = $condition;
                }
            }
        }

        return [
            'type' => \Magento\SalesRule\Model\Rule\Condition\Combine::class,
            'attribute' => '',
            'operator' => '',
            'value' => '1',
            'is_value_processed' => '',
            'aggregator' => 'all',
            'conditions' => $salesRuleConditions
        ];
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     *
     * @return \Magento\SalesRule\Model\Coupon|null
     */
    public function generateCouponPool(\Magento\SalesRule\Model\Rule $rule)
    {
        $this->couponGenerator->setData(
            [
                'rule_id' => $rule->getId(),
                'qty' => 1,
                'length' => 12,
                'format' => 'alphanum',
                'prefix' => '',
                'suffix' => '',
                'dash' => '0',
                'uses_per_coupon' => $rule->getUsesPerCoupon(),
                'usage_per_customer' => $rule->getUsesPerCustomer(),
                'to_date' => '',
            ]
        );
        $this->couponGenerator->generatePool();
        /** @var \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection */
        $couponCollection = $this->couponCollectionFactory->create();
        $couponCollection->addFieldToFilter('main_table.rule_id', $rule->getId())
            ->getSelect()
            ->joinLeft(
                ['h' => $couponCollection->getTable('amasty_acart_history')],
                'main_table.coupon_id = h.sales_rule_coupon_id',
                []
            )->where('h.history_id is null')
            ->order('main_table.coupon_id desc')
            ->limit(1);
        /** @var \Magento\SalesRule\Model\Coupon $salesCoupon */
        $salesCoupon = $couponCollection->getLastItem();

        return $salesCoupon->getCouponId() ? $salesCoupon : null;
    }

    private function getAllCustomerGroupIds(): array
    {
        /** @var \Magento\Customer\Model\ResourceModel\Group\Collection $groupCollection */
        $groupCollection = $this->groupCollectionFactory->create();

        return $groupCollection->getAllIds();
    }

    private function getCouponToDate(int $days, int $deliveryTime)
    {
        return $this->dateTime->formatDate($this->date->gmtTimestamp() + $days * 24 * 3600 + $deliveryTime);
    }
}
