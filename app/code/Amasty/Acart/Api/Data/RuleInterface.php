<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

interface RuleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return int|null
     */
    public function getRuleId();

    /**
     * @param int|null $id
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setRuleId($id);

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setName(?string $name): RuleInterface;

    /**
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * @param bool $isActive
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setIsActive(bool $isActive): RuleInterface;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param int $priority
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setPriority(int $priority): RuleInterface;

    /**
     * @return string|null
     */
    public function getConditionsSerialized(): ?string;

    /**
     * @param string $conditionsSerialized
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setConditionsSerialized(string $conditionsSerialized): RuleInterface;

    /**
     * @return int[]
     */
    public function getStoreIds(): array;

    /**
     * @param array $storeIds
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setStoreIds(array $storeIds): RuleInterface;

    /**
     * @return int[]
     */
    public function getCustomerGroupIds(): array;

    /**
     * @param array $customerGroupIds
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setCustomerGroupIds(array $customerGroupIds): RuleInterface;

    /**
     * @return string
     */
    public function getCancelCondition(): string;

    /**
     * @param string $cancelCondition
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setCancelCondition(string $cancelCondition): RuleInterface;

    /**
     * @return string|null
     */
    public function getUtmSource(): ?string;

    /**
     * @param string $utmSource
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setUtmSource(string $utmSource): RuleInterface;

    /**
     * @return string|null
     */
    public function getUtmMedium(): ?string;

    /**
     * @param string $utmMedium
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setUtmMedium(string $utmMedium): RuleInterface;

    /**
     * @return string|null
     */
    public function getUtmTerm(): ?string;

    /**
     * @param string $utmTerm
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setUtmTerm(string $utmTerm): RuleInterface;

    /**
     * @return string|null
     */
    public function getUtmContent(): ?string;

    /**
     * @param string $utmContent
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setUtmContent(string $utmContent): RuleInterface;

    /**
     * @return string|null
     */
    public function getUtmCampaign(): ?string;

    /**
     * @param string $utmCampaign
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setUtmCampaign(string $utmCampaign): RuleInterface;

    /**
     * @return \Amasty\Acart\Api\Data\RuleExtensionInterface
     */
    public function getExtensionAttributes(): RuleExtensionInterface;

    /**
     * @param \Amasty\Acart\Api\Data\RuleExtensionInterface $extensionAttributes
     *
     * @return \Amasty\Acart\Api\Data\RuleInterface
     */
    public function setExtensionAttributes(
        RuleExtensionInterface $extensionAttributes
    ): RuleInterface;
}
