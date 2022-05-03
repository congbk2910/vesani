<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\QuoteEmailExtensionInterface;
use Amasty\Acart\Api\Data\QuoteEmailInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class QuoteEmail extends AbstractExtensibleModel implements QuoteEmailInterface
{
    public const QUOTE_EMAIL_ID = 'quote_email_id';
    public const QUOTE_ID = 'quote_id';
    public const CUSTOMER_EMAIL = 'customer_email';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\QuoteEmail::class);
        $this->setIdFieldName(self::QUOTE_EMAIL_ID);
    }

    public function getQuoteEmailId(): ?int
    {
        return $this->getData(self::QUOTE_EMAIL_ID);
    }

    public function setQuoteEmailId(?int $quoteEmailId): QuoteEmailInterface
    {
        $this->setData(self::QUOTE_EMAIL_ID, $quoteEmailId);

        return $this;
    }

    public function getQuoteId(): ?int
    {
        return $this->getData(self::QUOTE_ID);
    }

    public function setQuoteId($quoteId): QuoteEmailInterface
    {
        $this->setData(self::QUOTE_ID, $quoteId);

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    public function setCustomerEmail(?string $customerEmail): QuoteEmailInterface
    {
        $this->setData(self::CUSTOMER_EMAIL, $customerEmail);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?QuoteEmailExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(QuoteEmailExtensionInterface $extensionAttributes): QuoteEmailInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
