<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

interface HistoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return int|null
     */
    public function getHistoryId(): ?int;

    /**
     * @param int|null $historyId
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setHistoryId(?int $historyId): HistoryInterface;

    /**
     * @return int|null
     */
    public function getRuleQuoteId(): ?int;

    /**
     * @param int|null $ruleQuoteId
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setRuleQuoteId($ruleQuoteId): HistoryInterface;

    /**
     * @return int|null
     */
    public function getScheduleId(): ?int;

    /**
     * @param int|null $scheduleId
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setScheduleId($scheduleId): HistoryInterface;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setStatus(?string $status): HistoryInterface;

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string;

    /**
     * @param string|null $publicKey
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setPublicKey(?string $publicKey): HistoryInterface;

    /**
     * @return string|null
     */
    public function getEmailSubject(): ?string;

    /**
     * @param string|null $emailSubject
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setEmailSubject(?string $emailSubject): HistoryInterface;

    /**
     * @return string|null
     */
    public function getEmailBody(): ?string;

    /**
     * @param string|null $emailBody
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setEmailBody(?string $emailBody): HistoryInterface;

    /**
     * @return int|null
     */
    public function getSalesRuleId(): ?string;

    /**
     * @param int|null $salesRuleId
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setSalesRuleId($salesRuleId): HistoryInterface;

    /**
     * @return int|null
     */
    public function getSalesRuleCouponId(): ?int;

    /**
     * @param int|null $salesRuleCouponId
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setSalesRuleCouponId(?int $salesRuleCouponId): HistoryInterface;

    /**
     * @return string|null
     */
    public function getSalesRuleCoupon(): ?string;

    /**
     * @param string|null $salesRuleCoupon
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setSalesRuleCoupon(?string $salesRuleCoupon): HistoryInterface;

    /**
     * @return string|null
     */
    public function getScheduledAt(): ?string;

    /**
     * @param string|null $scheduledAt
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setScheduledAt(?string $scheduledAt): HistoryInterface;

    /**
     * @return string|null
     */
    public function getExecutedAt(): ?string;

    /**
     * @param string|null $executedAt
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setExecutedAt(?string $executedAt): HistoryInterface;

    /**
     * @return string|null
     */
    public function getFinishedAt(): ?string;

    /**
     * @param string|null $finishedAt
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setFinishedAt(?string $finishedAt): HistoryInterface;

    /**
     * @return int
     */
    public function getOpenedCount(): int;

    /**
     * @param int $count
     * @return HistoryInterface
     */
    public function setOpenedCount(int $count): HistoryInterface;

    /**
     * @return string|null
     */
    public function getSalesRuleCouponExpirationDate(): ?string;

    /**
     * @param string|null $salesRuleCouponExpirationDate
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setSalesRuleCouponExpirationDate(?string $salesRuleCouponExpirationDate): HistoryInterface;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Amasty\Acart\Api\Data\HistoryExtensionInterface|null
     */
    public function getExtensionAttributes(): ?HistoryExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Amasty\Acart\Api\Data\HistoryExtensionInterface $extensionAttributes
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     */
    public function setExtensionAttributes(
        HistoryExtensionInterface $extensionAttributes
    ): HistoryInterface;
}
