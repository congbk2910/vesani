<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\ResourceModel\Rule;
use Amasty\Acart\Setup\Operation\MigrateRuleRelation\RuleRegistry;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class MigrateRuleRelationData
{
    /**
     * @var RuleRegistry
     */
    private $ruleRegistry;

    public function __construct(
        RuleRegistry $ruleRegistry
    ) {
        $this->ruleRegistry = $ruleRegistry;
    }

    public function execute(ModuleDataSetupInterface $setup)
    {
        $this->migrateStoreIds($setup);
        $this->migrateCustomerGroupIds($setup);
    }

    private function migrateStoreIds(ModuleDataSetupInterface $setup)
    {
        if ($relationData = $this->ruleRegistry->registry(RuleRegistry::STORE_IDS)) {
            $ruleStoreRelationTable = $setup->getTable(Rule::RULE_STORE_TABLE);
            $setup->getConnection()->insertMultiple($ruleStoreRelationTable, $relationData);
        }
    }

    private function migrateCustomerGroupIds(ModuleDataSetupInterface $setup)
    {
        if ($relationData = $this->ruleRegistry->registry(RuleRegistry::CUSTOMER_GROUP_IDS)) {
            $ruleCustomerGroupRelationTable = $setup->getTable(Rule::RULE_CUSTOMER_GROUP_TABLE);
            $setup->getConnection()->insertMultiple($ruleCustomerGroupRelationTable, $relationData);
        }
    }
}
