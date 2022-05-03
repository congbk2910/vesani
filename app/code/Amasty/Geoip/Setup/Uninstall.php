<?php
declare(strict_types=1);

namespace Amasty\Geoip\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->uninstallTables($setup)
            ->uninstallConfigData($setup);
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $tablesToDrop = [
            'amasty_geoip_block_v6',
            'amasty_geoip_location',
            'amasty_geoip_block'
        ];
        foreach ($tablesToDrop as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            "`path` LIKE 'amgeoip%'"
        );

        return $this;
    }
}
