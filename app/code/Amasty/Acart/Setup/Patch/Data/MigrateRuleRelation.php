<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Setup\Patch\Data;

use Amasty\Acart\Setup\Operation\MigrateRuleRelationData;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class MigrateRuleRelation implements DataPatchInterface
{
    /**
     * @var MigrateRuleRelationData
     */
    private $migrateRuleRelationData;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        MigrateRuleRelationData $migrateRuleRelationData,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->migrateRuleRelationData = $migrateRuleRelationData;
        $this->moduleDataSetup = $moduleDataSetup;
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
        $this->migrateRuleRelationData->execute($this->moduleDataSetup);
    }
}
