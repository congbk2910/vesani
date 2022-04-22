<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Omnisend\Omnisend\Setup\InstallData;

class Customer extends AbstractDb
{
    const TABLE_CUSTOMER = 'customer_entity';
    const TABLE_CUSTOMER_ENTITY_INT = 'customer_entity_int';

    const ENTITY_ID = 'entity_id';
    const ATTRIBUTE_ID = 'attribute_id';
    const VALUE = 'value';

    /**
     * @var Attribute
     */
    private $eavAttribute;

    /**
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        $connectionName = null
    ) {
        $this->eavAttribute = $eavAttribute;

        parent::__construct($context, $connectionName);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_CUSTOMER, self::ENTITY_ID);
    }

    /**
     * @param $customerId
     * @param $isImported
     */
    public function updateIsImported($customerId, $isImported)
    {
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(CustomerModel::ENTITY, InstallData::IS_IMPORTED);

        $this->getConnection()->insertOnDuplicate(
            $this->getTable(self::TABLE_CUSTOMER_ENTITY_INT),
            [
                self::ENTITY_ID => $customerId,
                self::ATTRIBUTE_ID => $isImportedAttributeId,
                self::VALUE => $isImported
            ],
            [self::VALUE]
        );
    }

    /**
     * @param $customerId
     * @param $emailChanged
     * @throws LocalizedException
     */
    public function updateEmailChanged($customerId, $emailChanged)
    {
        $emailChangedAttributeId = $this->eavAttribute->getIdByCode(CustomerModel::ENTITY, InstallData::EMAIL_CHANGED);

        $this->getConnection()->insertOnDuplicate(
            $this->getTable(self::TABLE_CUSTOMER_ENTITY_INT),
            [
                self::ENTITY_ID => $customerId,
                self::ATTRIBUTE_ID => $emailChangedAttributeId,
                self::VALUE => $emailChanged
            ],
            [self::VALUE]
        );
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function resetIsImportedValues()
    {
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(CustomerModel::ENTITY, InstallData::IS_IMPORTED);

        return $this->getConnection()->update(
            $this->getTable(self::TABLE_CUSTOMER_ENTITY_INT),
            [self::VALUE => InstallData::DEFAULT_IS_IMPORTED_VALUE],
            self::ATTRIBUTE_ID . ' = ' . $isImportedAttributeId . ' AND ' .
            self::VALUE . ' = ' . InstallData::IMPORTED_ATTRIBUTE_VALUE
        );
    }
}
