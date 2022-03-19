<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\Core\Setup;

use Magento\Eav\Setup\EavSetup;

class CoreSetup extends EavSetup
{
    
    public function getDefaultEntities()
    {
        $entities = array (
        );
        return $entities;
    }
}
