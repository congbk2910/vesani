<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface;
use Amasty\Acart\Api\Data\ScheduleInterface;
use Magento\Framework\Model\AbstractModel;

class Schedule extends AbstractModel implements ScheduleInterface
{
    public const SCHEDULE_ID = 'schedule_id';
    public const RULE_ID = 'rule_id';
    public const TEMPLATE_ID = 'template_id';
    public const DAYS = 'days';
    public const HOURS = 'hours';
    public const MINUTES = 'minutes';
    public const SIMPLE_ACTION = 'simple_action';
    public const DISCOUNT_AMOUNT = 'discount_amount';
    public const EXPIRED_IN_DAYS = 'expired_in_days';
    public const DISCOUNT_QTY = 'discount_qty';
    public const DISCOUNT_STEP = 'discount_step';
    public const SUBTOTAL_IS_GREATER_THAN = 'subtotal_is_greater_than';
    public const USE_SHOPPING_CART_RULE = 'use_shopping_cart_rule';
    public const SALES_RULE_ID = 'sales_rule_id';
    public const CREATED_AT = 'created_at';
    public const SEND_SAME_COUPON = 'send_same_coupon';
    public const EMAIL_TEMPLATE = 'email_template';
    public const USE_CAMPAIGN_UTM = 'use_campaign_utm';
    public const UTM_SOURCE = 'utm_source';
    public const UTM_MEDIUM = 'utm_medium';
    public const UTM_CAMPAIGN = 'utm_campaign';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Schedule::class);
        $this->setIdFieldName(self::SCHEDULE_ID);
    }

    public function getScheduleId(): ?int
    {
        return $this->getData(self::SCHEDULE_ID);
    }

    public function setScheduleId(?int $scheduleId): ScheduleInterface
    {
        $this->setData(self::SCHEDULE_ID, $scheduleId);

        return $this;
    }

    public function getRuleId(): ?int
    {
        return $this->getData(self::RULE_ID);
    }

    public function setRuleId($ruleId): ScheduleInterface
    {
        $this->setData(self::RULE_ID, $ruleId);

        return $this;
    }

    public function getTemplateId(): ?int
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    public function setTemplateId($templateId): ScheduleInterface
    {
        $this->setData(self::TEMPLATE_ID, $templateId);

        return $this;
    }

    public function getDays(): ?int
    {
        return $this->getData(self::DAYS);
    }

    public function setDays(?int $days): ScheduleInterface
    {
        $this->setData(self::DAYS, $days);

        return $this;
    }

    public function getHours(): ?int
    {
        return $this->getData(self::HOURS);
    }

    public function setHours(?int $hours): ScheduleInterface
    {
        $this->setData(self::HOURS, $hours);

        return $this;
    }

    public function getMinutes(): ?int
    {
        return $this->getData(self::MINUTES);
    }

    public function setMinutes(?int $minutes): ScheduleInterface
    {
        $this->setData(self::MINUTES, $minutes);

        return $this;
    }

    public function getSimpleAction(): ?string
    {
        return $this->getData(self::SIMPLE_ACTION);
    }

    public function setSimpleAction(?string $simpleAction): ScheduleInterface
    {
        $this->setData(self::SIMPLE_ACTION, $simpleAction);

        return $this;
    }

    public function getDiscountAmount(): float
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount(?float $discountAmount): ScheduleInterface
    {
        $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);

        return $this;
    }

    public function getExpiredInDays(): ?int
    {
        return $this->getData(self::EXPIRED_IN_DAYS);
    }

    public function setExpiredInDays(?int $expiredInDays): ScheduleInterface
    {
        $this->setData(self::EXPIRED_IN_DAYS, $expiredInDays);

        return $this;
    }

    public function getDiscountQty(): ?float
    {
        return $this->getData(self::DISCOUNT_QTY);
    }

    public function setDiscountQty(?float $discountQty): ScheduleInterface
    {
        $this->setData(self::DISCOUNT_QTY, $discountQty);

        return $this;
    }

    public function getDiscountStep(): ?int
    {
        return $this->getData(self::DISCOUNT_STEP);
    }

    public function setDiscountStep(?int $discountStep): ScheduleInterface
    {
        $this->setData(self::DISCOUNT_STEP, $discountStep);

        return $this;
    }

    public function getSubtotalIsGreaterThan(): ?int
    {
        return $this->getData(self::SUBTOTAL_IS_GREATER_THAN);
    }

    public function setSubtotalIsGreaterThan(?int $subtotalIsGreaterThan): ScheduleInterface
    {
        $this->setData(self::SUBTOTAL_IS_GREATER_THAN, $subtotalIsGreaterThan);

        return $this;
    }

    public function getUseShoppingCartRule(): bool
    {
        return (bool)$this->getData(self::USE_SHOPPING_CART_RULE);
    }

    public function setUseShoppingCartRule($useShoppingCartRule): ScheduleInterface
    {
        $this->setData(self::USE_SHOPPING_CART_RULE, $useShoppingCartRule);

        return $this;
    }

    public function getSalesRuleId(): ?string
    {
        return $this->getData(self::SALES_RULE_ID);
    }

    public function setSalesRuleId($salesRuleId): ScheduleInterface
    {
        $this->setData(self::SALES_RULE_ID, $salesRuleId);

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt(?string $createdAt): ScheduleInterface
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }

    public function getSendSameCoupon(): ?int
    {
        return $this->getData(self::SEND_SAME_COUPON);
    }

    public function setSendSameCoupon(?int $customerEmail): ScheduleInterface
    {
        $this->setData(self::SEND_SAME_COUPON, $customerEmail);

        return $this;
    }

    public function getEmailTemplate(): ?ScheduleEmailTemplateInterface
    {
        return $this->getData(self::EMAIL_TEMPLATE);
    }

    public function setEmailTemplate(?ScheduleEmailTemplateInterface $emailTemplate): ScheduleInterface
    {
        $this->setData(self::EMAIL_TEMPLATE, $emailTemplate);

        return $this;
    }

    public function getUseCampaignUtm(): bool
    {
        return (bool)$this->getData(self::USE_CAMPAIGN_UTM);
    }

    public function setUseCampaignUtm($useCampaignUtm): ScheduleInterface
    {
        $this->setData(self::USE_CAMPAIGN_UTM, $useCampaignUtm);

        return $this;
    }

    public function getUtmSource(): ?string
    {
        return $this->getData(self::UTM_SOURCE);
    }

    public function setUtmSource(string $utmSource): ScheduleInterface
    {
        $this->setData(self::UTM_SOURCE, $utmSource);

        return $this;
    }

    public function getUtmMedium(): ?string
    {
        return $this->getData(self::UTM_MEDIUM);
    }

    public function setUtmMedium(string $utmMedium): ScheduleInterface
    {
        $this->setData(self::UTM_MEDIUM, $utmMedium);

        return $this;
    }

    public function getUtmCampaign(): ?string
    {
        return $this->getData(self::UTM_CAMPAIGN);
    }

    public function setUtmCampaign(string $utmCampaign): ScheduleInterface
    {
        $this->setData(self::UTM_CAMPAIGN, $utmCampaign);

        return $this;
    }

    public function getConfig()
    {
        $config = $this->getData();

        unset($config['rule_id']);

        $config['discount_amount'] = (int)$config['discount_amount'];
        $config['discount_qty'] = (int)$config['discount_qty'];

        return $config;
    }

    public function getDeliveryTime(): int
    {
        return ($this->getDays() * 24 * 60 * 60) +
            ($this->getHours() * 60 * 60) +
            ($this->getMinutes() * 60);
    }
}
