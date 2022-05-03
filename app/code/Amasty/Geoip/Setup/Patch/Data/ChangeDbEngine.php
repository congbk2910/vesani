<?php
declare(strict_types=1);

namespace Amasty\Geoip\Setup\Patch\Data;

use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class ChangeDbEngine implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleResource = $moduleResource;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $setupDataVersion = (string)$this->moduleResource->getDataVersion('Amasty_Geoip');
        if ($setupDataVersion && version_compare($setupDataVersion, '1.5.1', '<')) {
            $tables = [
                'amasty_geoip_block_v6',
                'amasty_geoip_block',
                'amasty_geoip_location'
            ];
            foreach ($tables as $table) {
                $this->moduleDataSetup->getConnection()->changeTableEngine(
                    $this->moduleDataSetup->getTable($table),
                    'INNODB'
                );
            }
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
