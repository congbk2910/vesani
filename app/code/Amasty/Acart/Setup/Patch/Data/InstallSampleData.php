<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Setup\Patch\Data;

use Amasty\Acart\Setup\SampleData\Installer;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallSampleData implements DataPatchInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var Installer
     */
    private $installer;

    public function __construct(
        ResourceInterface $moduleResource,
        Installer $installer
    ) {
        $this->moduleResource = $moduleResource;
        $this->installer = $installer;
    }

    public function apply()
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_Acart');

        // Check if module was already installed or not.
        // If setup_version present in DB then we don't need to install fixtures, because setup_version is a marker.
        if (!$setupDataVersion) {
            $this->installer->install();
        }
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
