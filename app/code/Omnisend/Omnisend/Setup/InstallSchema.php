<?php

namespace Omnisend\Omnisend\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Omnisend\Omnisend\Api\Data\OmnisendContactInterface;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Api\Data\OmnisendRateLimitInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createOmnisendContactTable($setup);
        $this->createOmnisendGuestSubscriberTable($setup);
        $this->createOmnisendOrderStatusTable($setup);
        $this->createOmnisendRateLimitTable($setup);

        $this->addAttributeToQuote($setup);
        $this->addAttributeToNewsletterSubscriber($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    protected function createOmnisendContactTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(OmnisendContactInterface::TABLE_NAME);

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    OmnisendContactInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    OmnisendContactInterface::LABEL_ID
                )
                ->addColumn(
                    OmnisendContactInterface::CUSTOMER_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    OmnisendContactInterface::LABEL_CUSTOMER_ID
                )
                ->addColumn(
                    OmnisendContactInterface::OMNISEND_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    OmnisendContactInterface::LABEL_OMNISEND_ID
                )
                ->addColumn(
                    OmnisendContactInterface::STORE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    OmnisendContactInterface::LABEL_STORE_ID
                )
                ->setComment('Table For Mapping Magento And Omnisend Customers');

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    protected function createOmnisendGuestSubscriberTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(OmnisendGuestSubscriberInterface::TABLE_NAME);

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    OmnisendGuestSubscriberInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    OmnisendGuestSubscriberInterface::LABEL_ID
                )
                ->addColumn(
                    OmnisendGuestSubscriberInterface::SUBSCRIBER_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    OmnisendGuestSubscriberInterface::LABEL_SUBSCRIBER_ID
                )
                ->addColumn(
                    OmnisendGuestSubscriberInterface::OMNISEND_ID,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    OmnisendGuestSubscriberInterface::LABEL_OMNISEND_ID
                )
                ->addColumn(
                    OmnisendGuestSubscriberInterface::STORE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    OmnisendGuestSubscriberInterface::LABEL_STORE_ID
                )
                ->setComment('Table For Mapping Magento Guest Subscribers And Omnisend Contacts');

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    protected function createOmnisendOrderStatusTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(OmnisendOrderStatusInterface::TABLE_NAME);

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    OmnisendOrderStatusInterface::STATUS,
                    Table::TYPE_TEXT,
                    32,
                    [
                        'nullable' => false,
                        'primary' => true
                    ],
                    OmnisendOrderStatusInterface::LABEL_STATUS
                )
                ->addColumn(
                    OmnisendOrderStatusInterface::PAYMENT_STATUS,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    OmnisendOrderStatusInterface::LABEL_PAYMENT_STATUS
                )
                ->addColumn(
                    OmnisendOrderStatusInterface::FULFILLMENT_STATUS,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    OmnisendOrderStatusInterface::LABEL_FULFILLMENT_STATUS
                )
                ->setComment('Table For Mapping Magento And Omnisend Order Statuses');

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    protected function createOmnisendRateLimitTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(OmnisendRateLimitInterface::TABLE_NAME);

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    OmnisendRateLimitInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    OmnisendRateLimitInterface::LABEL_ID
                )
                ->addColumn(
                    OmnisendRateLimitInterface::LIMIT_TOTAL,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    OmnisendRateLimitInterface::LABEL_LIMIT_TOTAL
                )
                ->addColumn(
                    OmnisendRateLimitInterface::LIMIT_REMAINING,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    OmnisendRateLimitInterface::LABEL_LIMIT_REMAINING
                )
                ->addColumn(
                    OmnisendRateLimitInterface::RESETS_IN,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    OmnisendRateLimitInterface::LABEL_RESETS_IN
                )
                ->addColumn(
                    OmnisendRateLimitInterface::UPDATED_AT,
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => Table::TIMESTAMP_INIT
                    ],
                    OmnisendRateLimitInterface::LABEL_UPDATED_AT
                )
                ->setComment('Table For Omnisend API Rate Limit Tracking');

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addAttributeToQuote(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'is_imported',
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => 'Is Imported',
                'required' => false,
                'default' => '0'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addAttributeToNewsletterSubscriber(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('newsletter_subscriber'),
            InstallData::IS_IMPORTED,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => InstallData::IS_IMPORTED,
                'required' => false,
                'default' => '0'
            ]
        );
    }
}
