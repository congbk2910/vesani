<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Setup;

use Amasty\Acart\Model\History\ProductDetails\ResourceModel\Detail;
use Amasty\Acart\Model\ResourceModel\Blacklist;
use Amasty\Acart\Model\ResourceModel\EmailTemplate;
use Amasty\Acart\Model\ResourceModel\GuestCustomerQuotes;
use Amasty\Acart\Model\ResourceModel\History;
use Amasty\Acart\Model\ResourceModel\QuoteEmail;
use Amasty\Acart\Model\ResourceModel\Rule;
use Amasty\Acart\Model\ResourceModel\RuleQuote;
use Amasty\Acart\Model\ResourceModel\Schedule;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public const TABLE_NAMES = [
        Detail::TABLE_NAME,
        EmailTemplate::TABLE_NAME,
        GuestCustomerQuotes::TABLE_NAME,
        Rule::RULE_CUSTOMER_GROUP_TABLE,
        Rule::RULE_STORE_TABLE,
        'amasty_acart_attribute',
        Blacklist::TABLE_NAME,
        QuoteEmail::TABLE_NAME,
        History::TABLE_NAME,
        RuleQuote::MAIN_TABLE,
        Schedule::TABLE_NAME,
        Rule::RULE_TABLE
    ];

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this
            ->uninstallTables($setup)
            ->uninstallConfigData($setup);
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $setup->startSetup();
        foreach (self::TABLE_NAMES as $tableName) {
            $setup->getConnection()->dropTable($setup->getTable($tableName));
        }
        $setup->endSetup();

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $configTable = $setup->getTable('core_config_data');
        $setup->getConnection()->delete($configTable, "`path` LIKE 'amasty_acart/%'");

        return $this;
    }
}
