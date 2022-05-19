<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $this->addImageTagsColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.1.6', '<')) {
            $this->addSaleRepColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.1.7', '<')) {
            $this->addParentQuoteColumn($setup);
        }
        
        $setup->endSetup();
    }
    
    private function addImageTagsColumn(SchemaSetupInterface $setup)
    {
        
            $connection = $setup->getConnection();
            $tableName = $setup->getTable('fme_sharecart');
            $connection->addColumn(
                $tableName,
                'cartname',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'cart Name'
                ]
            );
            $setup->startSetup();
            $installer = $setup;
            $installer->endSetup();
    }

    private function addSaleRepColumn(SchemaSetupInterface $setup)
    {
        
            $connection = $setup->getConnection();
            $tableName = $setup->getTable('fme_sharecart');
            $connection->addColumn(
                $tableName,
                'order_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Order ID'
                ]
            );
            $connection->addColumn(
                $tableName,
                'is_used',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Is Used'
                ]
            );
            $setup->startSetup();
            $installer = $setup;
            $installer->endSetup();
    }

    private function addImageTagsColumn(SchemaSetupInterface $setup)
    {
        
            $connection = $setup->getConnection();
            $tableName = $setup->getTable('quote');
            $connection->addColumn(
                $tableName,
                'parent_quote_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Parent Quote ID'
                ]
            );
            $setup->startSetup();
            $installer = $setup;
            $installer->endSetup();
    }
    
}
