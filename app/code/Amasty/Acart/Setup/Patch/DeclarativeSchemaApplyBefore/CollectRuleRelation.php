<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Setup\Patch\DeclarativeSchemaApplyBefore;

use Amasty\Acart\Model\ResourceModel\Rule as RuleResource;
use Amasty\Acart\Model\Rule as RuleModel;
use Amasty\Acart\Setup\Operation\CollectRuleRelationData;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CollectRuleRelation implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CollectRuleRelationData
     */
    private $collectRuleRelationData;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectRuleRelationData $collectRuleRelationData
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->collectRuleRelationData = $collectRuleRelationData;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $ruleTable = $this->moduleDataSetup->getTable(RuleResource::RULE_TABLE);
        if ($this->moduleDataSetup->getConnection()->isTableExists($ruleTable)
            && $this->moduleDataSetup->getConnection()->tableColumnExists(
                $ruleTable,
                RuleModel::CUSTOMER_GROUP_IDS
            )
            && $this->moduleDataSetup->getConnection()->tableColumnExists(
                $ruleTable,
                RuleModel::STORE_IDS
            )
        ) {
            $this->collectRuleRelationData->execute();
        }
    }
}
