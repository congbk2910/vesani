<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

interface ScheduleInterface
{
    /**
     * @return int|null
     */
    public function getScheduleId(): ?int;

    /**
     * @param int|null $scheduleId
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setScheduleId(?int $scheduleId): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getRuleId(): ?int;

    /**
     * @param int|null $ruleId
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setRuleId($ruleId): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getTemplateId(): ?int;

    /**
     * @param int|null $templateId
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setTemplateId($templateId): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getDays(): ?int;

    /**
     * @param int|null $days
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setDays(?int $days): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getHours(): ?int;

    /**
     * @param int|null $hours
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setHours(?int $hours): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getMinutes(): ?int;

    /**
     * @param int|null $minutes
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setMinutes(?int $minutes): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getSimpleAction(): ?string;

    /**
     * @param string|null $simpleAction
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setSimpleAction(?string $simpleAction): ScheduleInterface;

    /**
     * @return float|null
     */
    public function getDiscountAmount(): float;

    /**
     * @param float|null $discountAmount
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setDiscountAmount(?float $discountAmount): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getExpiredInDays(): ?int;

    /**
     * @param int|null $expiredInDays
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setExpiredInDays(?int $expiredInDays): ScheduleInterface;

    /**
     * @return float|null
     */
    public function getDiscountQty(): ?float;

    /**
     * @param float|null $discountQty
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setDiscountQty(?float $discountQty): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getDiscountStep(): ?int;

    /**
     * @param int|null $discountStep
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setDiscountStep(?int $discountStep): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getSubtotalIsGreaterThan(): ?int;

    /**
     * @param int|null $subtotalIsGreaterThan
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setSubtotalIsGreaterThan(?int $subtotalIsGreaterThan): ScheduleInterface;

    /**
     * @return bool
     */
    public function getUseShoppingCartRule(): bool;

    /**
     * @param bool $useShoppingCartRule
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setUseShoppingCartRule($useShoppingCartRule): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getSalesRuleId(): ?string;

    /**
     * @param int|null $salesRuleId
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setSalesRuleId($salesRuleId): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setCreatedAt(?string $createdAt): ScheduleInterface;

    /**
     * @return int|null
     */
    public function getSendSameCoupon(): ?int;

    /**
     * @param int|null $customerEmail
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setSendSameCoupon(?int $customerEmail): ScheduleInterface;

    /**
     * @return ScheduleEmailTemplateInterface|null
     */
    public function getEmailTemplate(): ?ScheduleEmailTemplateInterface;

    /**
     * @param ScheduleEmailTemplateInterface $emailTemplate|null
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setEmailTemplate(?ScheduleEmailTemplateInterface $emailTemplate): ScheduleInterface;

    /**
     * @return bool
     */
    public function getUseCampaignUtm(): bool;

    /**
     * @param bool $useCampaignUtm
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setUseCampaignUtm($useCampaignUtm): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getUtmSource(): ?string;

    /**
     * @param string $utmSource
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setUtmSource(string $utmSource): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getUtmMedium(): ?string;

    /**
     * @param string $utmMedium
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setUtmMedium(string $utmMedium): ScheduleInterface;

    /**
     * @return string|null
     */
    public function getUtmCampaign(): ?string;

    /**
     * @param string $utmCampaign
     *
     * @return \Amasty\Acart\Api\Data\ScheduleInterface
     */
    public function setUtmCampaign(string $utmCampaign): ScheduleInterface;
}
