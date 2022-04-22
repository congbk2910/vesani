<?php

namespace Omnisend\Omnisend\Setup;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Helper\OrderStatusHelper;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendOrderStatus\Collection as OmnisendOrderStatusCollection;

class InstallData implements InstallDataInterface
{
    const IS_IMPORTED = 'is_imported';
    const IS_IMPORTED_LABEL = 'Is Imported';
    const DEFAULT_IS_IMPORTED_VALUE = 0;
    const IMPORTED_ATTRIBUTE_VALUE = 1;

    const EMAIL_CHANGED = 'email_changed';
    const EMAIL_CHANGED_LABEL = 'Email Changed';

    /**
     * @var bool
     */
    private $newCustomerAttributeCreated = false;

    /**
     * @var bool
     */
    private $newProductAttributeCreated = false;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var CustomerCollection
     */
    private $customerCollection;

    /**
     * @var Attribute
     */
    private $eavAttribute;

    /**
     * @var OmnisendOrderStatusCollection
     */
    private $omnisendOrderStatusCollection;

    /**
     * @var ProductCollection
     */
    private $productCollection;

    /**
     * InstallData constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerCollection $customerCollection
     * @param Attribute $eavAttribute
     * @param OmnisendOrderStatusCollection $omnisendOrderStatusCollection
     * @param ProductCollection $productCollection
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        EavSetupFactory $eavSetupFactory,
        CustomerCollection $customerCollection,
        Attribute $eavAttribute,
        OmnisendOrderStatusCollection $omnisendOrderStatusCollection,
        ProductCollection $productCollection
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerCollection = $customerCollection;
        $this->eavAttribute = $eavAttribute;
        $this->omnisendOrderStatusCollection = $omnisendOrderStatusCollection;
        $this->productCollection = $productCollection;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Exception
     * @throws LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addAttributeToCustomer($setup);
        $this->addAttributeToOrder($setup);
        $this->addAttributeToProduct($setup);
        $this->addEmailChangedAttributeToCustomer($setup);

        $this->setDefaultIsImportedValuesForExistingUsers($setup);
        $this->setDefaultIsImportedValuesForExistingProducts($setup);

        $this->populateOmnisendOrderStatusTable($setup);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws Exception
     * @throws LocalizedException
     */
    protected function addAttributeToCustomer(ModuleDataSetupInterface $setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if ($customerSetup->getAttribute(Customer::ENTITY, self::IS_IMPORTED)) {
            return;
        }

        $customerSetup->addAttribute(Customer::ENTITY, self::IS_IMPORTED, [
            'type' => 'int',
            'label' => self::IS_IMPORTED_LABEL,
            'input' => 'boolean',
            'required' => false,
            'default' => '0',
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'user_defined' => true,
            'system' => 0
        ]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);

        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerIsImportedAttribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, self::IS_IMPORTED);

        $customerIsImportedAttribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId
        ]);

        $customerIsImportedAttribute->save();

        $this->newCustomerAttributeCreated = true;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function addAttributeToProduct(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($eavSetup->getAttribute(Product::ENTITY, self::IS_IMPORTED)) {
            return;
        }

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::IS_IMPORTED,
            [
                'type' => 'int',
                'label' => self::IS_IMPORTED_LABEL,
                'input' => 'boolean',
                'required' => false,
                'default' => '0',
                'global'   => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'General',
                'visible' => true,
                'user_defined' => true,
                'system' => 0
            ]
        );

        $this->newProductAttributeCreated = true;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function addAttributeToOrder(ModuleDataSetupInterface $setup)
    {
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        if ($salesSetup->getAttribute(Order::ENTITY, self::IS_IMPORTED)) {
            return;
        }

        $salesSetup->addAttribute(Order::ENTITY, self::IS_IMPORTED, [
            'type' => 'int',
            'label' => self::IS_IMPORTED_LABEL,
            'input' => 'boolean',
            'required' => false,
            'default' => '0',
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'user_defined' => true,
            'system' => 0
        ]);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function populateOmnisendOrderStatusTable(ModuleDataSetupInterface $setup)
    {
        if ($this->omnisendOrderStatusCollection->getSize()) {
            return;
        }

        $data = [
            [
                'canceled',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_CANCELED,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_IN_PROGRESS
            ],
            [
                'closed',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_REFUNDED,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_IN_PROGRESS
            ],
            [
                'complete',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_PAID,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DELIVERED
            ],
            [
                'fraud',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_IN_PROGRESS
            ],
            [
                'holded',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_IN_PROGRESS
            ],
            [
                'payment_review',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_PAID,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_IN_PROGRESS
            ],
            [
                'pending',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_IN_PROGRESS
            ],
            [
                'pending_payment',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_IN_PROGRESS
            ],
            [
                'processing',
                OrderStatusHelper::PAYMENT_STATUS_VALUE_PAID,
                OrderStatusHelper::FULFILLMENT_STATUS_VALUE_FULFILLED
            ],
        ];

        $columns = [
            OmnisendOrderStatusInterface::STATUS,
            OmnisendOrderStatusInterface::PAYMENT_STATUS,
            OmnisendOrderStatusInterface::FULFILLMENT_STATUS
        ];

        $setup->getConnection()->insertArray(
            $setup->getTable(OmnisendOrderStatusInterface::TABLE_NAME),
            $columns,
            $data
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function setDefaultIsImportedValuesForExistingUsers(ModuleDataSetupInterface $setup)
    {
        if (!$this->newCustomerAttributeCreated) {
            return;
        }

        $existingCustomersIds = $this->customerCollection->getAllIds();
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(Customer::ENTITY, self::IS_IMPORTED);

        if (empty($existingCustomersIds)) {
            return;
        }

        $data = [];

        foreach ($existingCustomersIds as $customerId) {
            array_push($data, [
                'attribute_id' => $isImportedAttributeId,
                'entity_id' => $customerId,
                'value' => self::DEFAULT_IS_IMPORTED_VALUE
            ]);
        }

        $setup->getConnection()->insertMultiple('customer_entity_int', $data);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function setDefaultIsImportedValuesForExistingProducts(ModuleDataSetupInterface $setup)
    {
        if (!$this->newProductAttributeCreated) {
            return;
        }

        $existingProductsIds = $this->productCollection->getAllIds();
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(Product::ENTITY, self::IS_IMPORTED);

        if (empty($existingProductsIds)) {
            return;
        }

        $connection = $setup->getConnection();
        $tableName = $setup->getTable('catalog_product_entity_int');
        $columnName = 'entity_id';
        $fkColumnName = $connection->tableColumnExists($tableName, $columnName) ? $columnName : 'row_id';

        $data = [];

        foreach ($existingProductsIds as $productId) {
            array_push($data, [
                'attribute_id' => $isImportedAttributeId,
                'store_id' => 0,
                $fkColumnName => $productId,
                'value' => self::DEFAULT_IS_IMPORTED_VALUE
            ]);
        }

        $setup->getConnection()->insertMultiple('catalog_product_entity_int', $data);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws Exception
     * @throws LocalizedException
     */
    protected function addEmailChangedAttributeToCustomer(ModuleDataSetupInterface $setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if ($customerSetup->getAttribute(Customer::ENTITY, self::EMAIL_CHANGED)) {
            return;
        }

        $customerSetup->addAttribute(Customer::ENTITY, self::EMAIL_CHANGED, [
            'type' => 'int',
            'label' => self::EMAIL_CHANGED_LABEL,
            'input' => 'boolean',
            'required' => false,
            'default' => '0',
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'visible' => true,
            'user_defined' => true,
            'system' => 0
        ]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);

        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerEmailChangedAttribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, self::EMAIL_CHANGED);

        $customerEmailChangedAttribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId
        ]);

        $customerEmailChangedAttribute->save();
    }
}
