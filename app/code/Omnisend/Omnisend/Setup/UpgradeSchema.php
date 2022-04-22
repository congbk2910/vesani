<?php

namespace Omnisend\Omnisend\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Omnisend\Omnisend\Api\Data\OmnisendRequestInterface;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest;
use Omnisend\Omnisend\Model\ResourceModel\Subscriber;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const OMNISEND_POST_STATUS = 'omnisend_post_status';
    const OMNISEND_POST_STATUS_LABEL = 'Is entity currently on Omnisend';
    const OMNISEND_PREVIOUS_SUBSCRIBER_STATUS = 'A flag used to keep state of the previous subscriber status';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            $this->addOmnisendPostStatusAttributeToQuote($setup);
            $this->addOmnisendPostStatusAttributeToOrder($setup);
            $this->addOmnisendSubscriberStatusAttributeToNewsletterSubscriber($setup);
        }

        if (version_compare($context->getVersion(), '1.1.12') < 0) {
            $this->createOmnisendRequestLogTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addOmnisendPostStatusAttributeToQuote(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            self::OMNISEND_POST_STATUS,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => self::OMNISEND_POST_STATUS_LABEL,
                'required' => false,
                'default' => '0'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addOmnisendPostStatusAttributeToOrder(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            self::OMNISEND_POST_STATUS,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => self::OMNISEND_POST_STATUS_LABEL,
                'required' => false,
                'default' => '0'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addOmnisendSubscriberStatusAttributeToNewsletterSubscriber(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('newsletter_subscriber'),
            Subscriber::OMNISEND_PREVIOUS_SUBSCRIBER_STATUS,
            [
                'type' => Table::TYPE_INTEGER,
                'comment' => self::OMNISEND_PREVIOUS_SUBSCRIBER_STATUS,
                'required' => false,
                'default' => null
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    protected function createOmnisendRequestLogTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(OmnisendRequest::TABLE_NAME);

        if ($setup->getConnection()->isTableExists($tableName) != true) {
            $table = $setup->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    OmnisendRequestInterface::RECORD_ID,
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Record ID'
                )
                ->addColumn(
                    OmnisendRequestInterface::REQUEST_URL,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Request URL'
                )
                ->addColumn(
                    OmnisendRequestInterface::REQUEST_METHOD,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Request Method'
                )
                ->addColumn(
                    OmnisendRequestInterface::REQUEST_BODY,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Request Body'
                )
                ->addColumn(
                    OmnisendRequestInterface::STORE_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Store ID'
                )
                ->addColumn(
                    OmnisendRequestInterface::RESPONSE_CODE,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Response Code'
                )
                ->addColumn(
                    OmnisendRequestInterface::RESPONSE_BODY,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Response Body'
                )
                ->setComment('Table For Omnisend Requests');

            $setup->getConnection()->createTable($table);
        }
    }
}
