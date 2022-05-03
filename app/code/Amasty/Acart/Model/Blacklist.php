<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\BlacklistExtensionInterface;
use Amasty\Acart\Api\Data\BlacklistInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class Blacklist extends AbstractExtensibleModel implements BlacklistInterface
{
    public const BLACKLIST_ID = 'blacklist_id';
    public const CUSTOMER_EMAIL = 'customer_email';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Blacklist::class);
        $this->setIdFieldName(self::BLACKLIST_ID);
    }

    public function getBlacklistId(): ?int
    {
        return $this->getData(self::BLACKLIST_ID);
    }

    public function setBlacklistId(?int $blacklistId): BlacklistInterface
    {
        $this->setData(self::BLACKLIST_ID, $blacklistId);

        return $this;
    }

    public function getCustomerEmail(): ?string
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    public function setCustomerEmail(?string $customerEmail): BlacklistInterface
    {
        $this->setData(self::CUSTOMER_EMAIL, $customerEmail);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?BlacklistExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(BlacklistExtensionInterface $extensionAttributes): BlacklistInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
