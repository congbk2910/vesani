<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 *
 * @package Cminds\Salesrep\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.17', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('admin_user'),
                'salesrep_rep_commission_calculation_type',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Rep Commission Calculation Type'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.1.19', '<')) {
            $this->addGroupTable($installer);
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('admin_user'),
                'salesrep_coordinator_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => null,
                    'after' => 'salesrep_manager_commission_rate',
                    'comment' => 'Salesrep Coordinator Id'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('admin_user'),
                'salesrep_coordinator_commission_rate',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'scale' => 2,
                    'precision' => 12,
                    'nullable' => true,
                    'default' => null,
                    'after' => 'salesrep_coordinator_id',
                    'comment' => 'Coordinator Commission Rate'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('salesrep'),
                'coordinator_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Coordinator Id'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('salesrep'),
                'coordinator_name',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Coordinator Name'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('salesrep'),
                'coordinator_commission_earned',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'scale' => 2,
                    'precision' => 12,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Coordinator Commission Earned'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('salesrep'),
                'coordinator_commission_status',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Coordinator Commission Status'
                ]
            );
        }

        $installer->endSetup();
    }

    protected function addGroupTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('salesrep_commission_group')
        );

        $table
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Group ID'
            )->addColumn(
                'commission_rate',
                Table::TYPE_DECIMAL,
                '12,4',
                [
                    'nullable' => false
                ],
                'Group commission'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Name of the group'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                1,
                [
                    'default' => 1,
                    'nullable' => false
                ],
                'Is Group Active'
            );

        $installer->getConnection()->createTable($table);

    }
}
