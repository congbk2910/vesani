<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

interface BlacklistInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return int|null
     */
    public function getBlacklistId(): ?int;

    /**
     * @param int|null $blacklistId
     *
     * @return \Amasty\Acart\Api\Data\BlacklistInterface
     */
    public function setBlacklistId(?int $blacklistId);

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * @param string|null $customerEmail
     *
     * @return \Amasty\Acart\Api\Data\BlacklistInterface
     */
    public function setCustomerEmail(?string $customerEmail): BlacklistInterface;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Amasty\Acart\Api\Data\BlacklistExtensionInterface|null
     */
    public function getExtensionAttributes(): ?BlacklistExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Amasty\Acart\Api\Data\BlacklistExtensionInterface $extensionAttributes
     * @return \Amasty\Acart\Api\Data\BlacklistInterface
     */
    public function setExtensionAttributes(
        BlacklistExtensionInterface $extensionAttributes
    ): BlacklistInterface;
}
