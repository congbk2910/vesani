<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/


declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Provider\Collector;

use Amasty\Base\Model\ModuleInfoProvider;
use Magento\Framework\Module\ModuleListInterface;

class Module implements CollectorInterface
{
    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    public function __construct(
        ModuleInfoProvider $moduleInfoProvider,
        ModuleListInterface $moduleList
    ) {
        $this->moduleInfoProvider = $moduleInfoProvider;
        $this->moduleList = $moduleList;
    }

    public function get(): array
    {
        $modulesData = [];
        $moduleNames = $this->moduleList->getNames();

        foreach ($moduleNames as $moduleName) {
            if (strpos($moduleName, 'Magento_') !== false) {
                continue;
            }

            $modulesData[$moduleName] = $this->getModuleData($moduleName);
        }

        return $modulesData;
    }

    protected function getModuleData(string $moduleName): array
    {
        $moduleInfo = $this->moduleInfoProvider->getModuleInfo($moduleName);
        $moduleVersion = $moduleInfo[ModuleInfoProvider::MODULE_VERSION_KEY] ?? '';

        return [ModuleInfoProvider::MODULE_VERSION_KEY => $moduleVersion];
    }
}
