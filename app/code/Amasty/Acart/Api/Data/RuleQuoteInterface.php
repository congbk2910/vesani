<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

interface RuleQuoteInterface
{
    /**
     * @return int|null
     */
    public function getRuleQuoteId(): ?int;

    /**
     * @param int|null $ruleQuoteId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setRuleQuoteId(?int $ruleQuoteId): RuleQuoteInterface;

    /**
     * @return int|null
     */
    public function getQuoteId(): ?int;

    /**
     * @param int|null $quoteId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setQuoteId($quoteId): RuleQuoteInterface;

    /**
     * @return int|null
     */
    public function getRuleId(): ?int;

    /**
     * @param int|null $ruleId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setRuleId($ruleId): RuleQuoteInterface;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setStatus(?string $status): RuleQuoteInterface;

    /**
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * @param int|null $storeId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setStoreId(?int $storeId): RuleQuoteInterface;

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * @param int|null $customerId
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerId(?int $customerId): RuleQuoteInterface;

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * @param string|null $customerEmail
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerEmail(?string $customerEmail): RuleQuoteInterface;

    /**
     * @return string|null
     */
    public function getCustomerFirstname(): ?string;

    /**
     * @param string|null $customerFirstname
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerFirstname(?string $customerFirstname): RuleQuoteInterface;

    /**
     * @return string|null
     */
    public function getCustomerLastname(): ?string;

    /**
     * @param string|null $customerLastname
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerLastname(?string $customerLastname): RuleQuoteInterface;

    /**
     * @return string|null
     */
    public function getCustomerPhone(): ?string;

    /**
     * @param string|null $customerPhone
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCustomerPhone(?string $customerPhone): RuleQuoteInterface;

    /**
     * @return int|null
     */
    public function getTestMode(): int;

    /**
     * @param int|null $testMode
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setTestMode(?int $testMode): RuleQuoteInterface;

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * @param string|null $createdAt
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setCreatedAt(?string $createdAt): RuleQuoteInterface;

    /**
     * @return string|null
     */
    public function getAbandonedStatus(): ?string;

    /**
     * @param string|null $abandonedStatus
     *
     * @return \Amasty\Acart\Api\Data\RuleQuoteInterface
     */
    public function setAbandonedStatus(?string $abandonedStatus): RuleQuoteInterface;
}
