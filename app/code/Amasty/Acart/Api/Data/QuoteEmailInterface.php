<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

interface QuoteEmailInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return int|null
     */
    public function getQuoteEmailId(): ?int;

    /**
     * @param int|null $quoteEmailId
     *
     * @return \Amasty\Acart\Api\Data\QuoteEmailInterface
     */
    public function setQuoteEmailId(?int $quoteEmailId);

    /**
     * @return int|null
     */
    public function getQuoteId(): ?int;

    /**
     * @param int|null $quoteId
     *
     * @return \Amasty\Acart\Api\Data\QuoteEmailInterface
     */
    public function setQuoteId($quoteId): QuoteEmailInterface;

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string;

    /**
     * @param string|null $customerEmail
     *
     * @return \Amasty\Acart\Api\Data\QuoteEmailInterface
     */
    public function setCustomerEmail(?string $customerEmail): QuoteEmailInterface;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Amasty\Acart\Api\Data\QuoteEmailExtensionInterface|null
     */
    public function getExtensionAttributes(): ?QuoteEmailExtensionInterface;

    /**
     * Set an extension attributes object.
     *
     * @param \Amasty\Acart\Api\Data\QuoteEmailExtensionInterface $extensionAttributes
     * @return \Amasty\Acart\Api\Data\QuoteEmailInterface
     */
    public function setExtensionAttributes(
        QuoteEmailExtensionInterface $extensionAttributes
    ): QuoteEmailInterface;
}
