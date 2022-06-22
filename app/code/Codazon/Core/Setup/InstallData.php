<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\Core\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {
    
    private $setupFactory;

    public function __construct(
        \Codazon\Core\Setup\CoreSetupFactory $setupFactory
    ) {
        $this->setupFactory = $setupFactory;
    }
    
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $moduleSetup = $this->setupFactory->create(['setup' => $setup]);
        $moduleSetup->installEntities();	
        $setup->endSetup();
    }
    
}
