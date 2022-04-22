<?php

namespace Omnisend\Omnisend\Setup;

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Helper\OrderStatusHelper;
use Zend_Validate_Exception;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var CategoryCollection
     */
    protected $categoryCollection;

    /**
     * @var Attribute
     */
    protected $eavAttribute;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param CategoryCollection $categoryCollection
     * @param Attribute $eavAttribute
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CategoryCollection $categoryCollection,
        Attribute $eavAttribute
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categoryCollection = $categoryCollection;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->changeDefaultOmnisendOrderStatusValues($setup);
        }

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            $this->setOmnisendOrderStatusValuesForPaypal($setup);
            $this->addOmnisendPostStatusAttributeToProduct($setup);
        }

        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            $this->updateCompletedOrderStatusMappings($setup);
        }

        if (version_compare($context->getVersion(), '1.1.7') < 0) {
            $this->addOmnisendImportStatusAttributeToCategory($setup);
            $this->addOmnisendPostStatusAttributeToCategory($setup);
        }

        if (version_compare($context->getVersion(), '1.1.13') < 0) {
            $this->setDefaultIsImportedValuesForExistingCategories($setup);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws Exception
     */
    protected function changeDefaultOmnisendOrderStatusValues(ModuleDataSetupInterface $setup)
    {
        $tableName = $setup->getTable(OmnisendOrderStatusInterface::TABLE_NAME);

        if (!$setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $data = [
            [
                OmnisendOrderStatusInterface::STATUS => 'canceled',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_CANCELED,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'closed',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_REFUNDED,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'complete',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_PAID,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DELIVERED
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'fraud',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_CANCELED,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'holded',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'payment_review',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'pending',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'pending_payment',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'processing',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ]
        ];

        $columns = [
            OmnisendOrderStatusInterface::PAYMENT_STATUS,
            OmnisendOrderStatusInterface::FULFILLMENT_STATUS
        ];

        $setup->getConnection()->insertOnDuplicate(
            $tableName,
            $data,
            $columns
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws Exception
     */
    protected function setOmnisendOrderStatusValuesForPaypal(ModuleDataSetupInterface $setup)
    {
        $tableName = $setup->getTable(OmnisendOrderStatusInterface::TABLE_NAME);

        if (!$setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $data = [
            [
                OmnisendOrderStatusInterface::STATUS => 'fraud',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'paypal_canceled_reversal',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_CANCELED,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'paypal_reversed',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_REFUNDED,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'pending_paypal',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_AWAITING_PAYMENT,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ],
            [
                OmnisendOrderStatusInterface::STATUS => 'processing',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_PAID,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_DO_NOT_CHANGE
            ]
        ];

        $columns = [
            OmnisendOrderStatusInterface::PAYMENT_STATUS,
            OmnisendOrderStatusInterface::FULFILLMENT_STATUS
        ];

        $setup->getConnection()->insertOnDuplicate(
            $tableName,
            $data,
            $columns
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    protected function addOmnisendPostStatusAttributeToProduct(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($eavSetup->getAttribute(Product::ENTITY, UpgradeSchema::OMNISEND_POST_STATUS)) {
            return;
        }

        $eavSetup->addAttribute(
            Product::ENTITY,
            UpgradeSchema::OMNISEND_POST_STATUS,
            [
                'type' => 'int',
                'label' => UpgradeSchema::OMNISEND_POST_STATUS_LABEL,
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
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    protected function addOmnisendPostStatusAttributeToCategory(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($eavSetup->getAttribute(Category::ENTITY, UpgradeSchema::OMNISEND_POST_STATUS)) {
            return;
        }

        $eavSetup->addAttribute(Category::ENTITY, UpgradeSchema::OMNISEND_POST_STATUS, [
            'type'     => 'int',
            'label'    => UpgradeSchema::OMNISEND_POST_STATUS_LABEL,
            'input'    => 'boolean',
            'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'visible'  => true,
            'default'  => '0',
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => 'Display Settings',
        ]);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    protected function addOmnisendImportStatusAttributeToCategory(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if ($eavSetup->getAttribute(Category::ENTITY, InstallData::IS_IMPORTED)) {
            return;
        }

        $eavSetup->addAttribute(Category::ENTITY, InstallData::IS_IMPORTED, [
            'type'     => 'int',
            'label'    => InstallData::IS_IMPORTED_LABEL,
            'input'    => 'boolean',
            'source'   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'visible'  => true,
            'default'  => '0',
            'required' => false,
            'global'   => ScopedAttributeInterface::SCOPE_STORE,
            'group'    => 'Display Settings',
        ]);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function updateCompletedOrderStatusMappings($setup)
    {
        $tableName = $setup->getTable(OmnisendOrderStatusInterface::TABLE_NAME);

        if (!$setup->getConnection()->isTableExists($tableName)) {
            return;
        }

        $data = [
            [
                OmnisendOrderStatusInterface::STATUS => 'complete',
                OmnisendOrderStatusInterface::PAYMENT_STATUS => OrderStatusHelper::PAYMENT_STATUS_VALUE_PAID,
                OmnisendOrderStatusInterface::FULFILLMENT_STATUS => OrderStatusHelper::FULFILLMENT_STATUS_VALUE_FULFILLED
            ]
        ];

        $columns = [
            OmnisendOrderStatusInterface::PAYMENT_STATUS,
            OmnisendOrderStatusInterface::FULFILLMENT_STATUS
        ];

        $setup->getConnection()->insertOnDuplicate(
            $tableName,
            $data,
            $columns
        );
    }

    protected function setDefaultIsImportedValuesForExistingCategories(ModuleDataSetupInterface $setup)
    {
        $existingCategoryIds = $this->categoryCollection->getAllIds();
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(Category::ENTITY, InstallData::IS_IMPORTED);

        if (empty($existingCategoryIds)) {
            return;
        }

        $connection = $setup->getConnection();
        $tableName = $setup->getTable('catalog_category_entity_int');
        $columnName = 'entity_id';
        $fkColumnName = $connection->tableColumnExists($tableName, $columnName) ? $columnName : 'row_id';

        $data = [];

        foreach ($existingCategoryIds as $categoryId) {
            array_push($data, [
                'attribute_id' => $isImportedAttributeId,
                'store_id' => 0,
                $fkColumnName => $categoryId,
                'value' => InstallData::DEFAULT_IS_IMPORTED_VALUE
            ]);
        }

        $setup->getConnection()->insertOnDuplicate($tableName, $data);
    }
}
