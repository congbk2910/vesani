<?php
namespace Cminds\Salesrep\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    protected $config;

    public function __construct(
        \Magento\Config\Model\ResourceModel\Config $config
    ) {
        $this->config = $config;
    }

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();


        $installer->getConnection()->addColumn(
            $installer->getTable('admin_user'),
            'salesrep_rep_commission_rate',
            [
                'type' => Table::TYPE_DECIMAL,
                'scale' => 2,
                'precision' => 12,
                'nullable' => true,
                'default' => null,
                'comment' => 'Rep Commission Rate'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('admin_user'),
            'salesrep_manager_id',
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Salesrep Manager Id'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('admin_user'),
            'salesrep_manager_commission_rate',
            [
                'type' => Table::TYPE_DECIMAL,
                'scale' => 2,
                'precision' => 12,
                'nullable' => true,
                'default' => null,
                'comment' => 'Manager Commission Rate'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('catalog_product_entity'),
            'salesrep_rep_commission_rate',
            [
                'type' => Table::TYPE_DECIMAL,
                'scale' => 2,
                'precision' => 12,
                'nullable' => true,
                'default' => null,
                'comment' => 'Rep Commission Rate'
            ]
        );

        $table = $installer->getConnection()
            ->newTable($installer->getTable('salesrep'))
            ->addColumn(
                'salesrep_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                10,
                ['nullable' => false, 'unsigned' => true],
                'Order ID'
            )
            ->addColumn(
                'rep_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                'rep_name',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                'rep_commission_earned',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false]
            )
            ->addColumn(
                'rep_commission_status',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                'manager_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                'manager_name',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true]
            )
            ->addColumn(
                'manager_commission_earned',
                Table::TYPE_DECIMAL,
                [12, 2],
                ['nullable' => false]
            )
            ->addColumn(
                'manager_commission_status',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true]
            )
            ->addForeignKey(
                'FK_SALESREP_ORDER',
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('salesrep');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
