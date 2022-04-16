<?php

namespace Svea\Checkout\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $connection = $installer->getConnection();

        $pushTable = $installer->getTable('svea_push');
        $installer->run("
            CREATE TABLE IF NOT EXISTS `{$pushTable}` (
              `entity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `sid` varchar(255) NOT NULL,
              `order_id` int(11) DEFAULT NULL COMMENT 'magento order id',
              `error`  tinyint(3) NOT NULL,
              `error_msg` TEXT,
              `created_at` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`entity_id`),
              UNIQUE KEY `UNQ_ORDER_ID` (`sid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $quoteData = [
            'svea_order_id' => ['type' => Table::TYPE_TEXT, 'length' => '255', 'comment' => 'svea_order_id', 'nullable' => true, 'default' => ''],
            'svea_client_order_id' => ['type' => Table::TYPE_TEXT, 'length' => '255', 'comment' => 'svea_client_order_id', 'nullable' => true, 'default' => ''],
            'svea_hash' => ['type' => Table::TYPE_TEXT, 'length' => '255', 'comment' => 'svea_hash', 'nullable' => true, 'default' => ''],
        ];
        $orderData = [
            'svea_order_id' => ['type' => Table::TYPE_TEXT, 'length' => '255', 'comment' => 'svea_order_id', 'nullable' => true, 'default' => ''],
        ];

        $alterTables = [
            'quote' => $quoteData,
            'sales_order' => $orderData,
        ];

        foreach ($alterTables as $_table => $columns) {
            $table = $installer->getTable($_table);
            $tableInfo = $connection->describeTable($table);
            foreach ($columns as $column => $definition) {
                if (isset($tableInfo[$column])) {
                    continue;
                }

                $connection->addColumn($table, $column, $definition);
            }
        }


        $_table = 'quote';
        $table = $installer->getTable($_table);
        $idxName = $installer->getIdxName($_table, ['svea_order_id']);
        $connection->addIndex($table, $idxName, ['svea_order_id']);

        $_table = 'sales_order';
        $table = $installer->getTable($_table);
        $idxName = $installer->getIdxName($_table, ['svea_order_id']);
        $connection->addIndex($table, $idxName, ['svea_order_id']);

        $installer->endSetup();
    }
}
