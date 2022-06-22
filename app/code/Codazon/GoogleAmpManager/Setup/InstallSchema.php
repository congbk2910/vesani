<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
        $table = $connection->newTable($installer->getTable('cdz_amp_cms_page'))
        ->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            "Entity ID"
        )->addColumn(
            'page_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            "Page ID"
        )->addColumn(
            'amp_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '4M',
            ['nullable' => true],
            "AMP Content"
        )->addColumn(
            'options',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '4M',
            ['nullable' => true],
            "Options"
        )->addForeignKey(
            $installer->getFkName(
                'cdz_amp_cms_page',
                'page_id',
                'cms_page',
                'page_id'
            ),
            'page_id',
            $installer->getTable('cms_page'),
            'page_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment('CMS Page AMP Table');
        
        $connection->createTable($table);

        $table = $connection->newTable($installer->getTable('cdz_amp_cms_block'))->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            "Entity ID"
        )->addColumn(
            'block_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            "Block ID"
        )->addColumn(
            'amp_content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '4M',
            ['nullable' => true],
            "AMP Content"
        )->addColumn(
            'options',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '4M',
            ['nullable' => true],
            "Options"
        )->addForeignKey(
            $installer->getFkName(
                'cdz_amp_cms_block',
                'block_id',
                'cms_block',
                'block_id'
            ),
            'block_id',
            $installer->getTable('cms_block'),
            'block_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment('CMS Block AMP Table');
        $connection->createTable($table);

        if ($connection->isTableExists($installer->getTable('magefan_blog_post'))) {
            $table = $connection->newTable($installer->getTable('cdz_amp_blog_post'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                "Entity ID"
            )->addColumn(
                'post_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                "Post ID"
            )->addColumn(
                'amp_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '4M',
                ['nullable' => true],
                "AMP Content"
            )->addColumn(
                'options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '4M',
                ['nullable' => true],
                "Options"
            )->addForeignKey(
                $installer->getFkName(
                    'cdz_amp_blog_post',
                    'post_id',
                    'magefan_blog_post',
                    'post_id'
                ),
                'post_id',
                $installer->getTable('magefan_blog_post'),
                'post_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Blog Post AMP Table');
            $connection->createTable($table);
        }

        $installer->endSetup();
    }
}

