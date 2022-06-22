<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Model;

class Page extends \Codazon\GoogleAmpManager\Model\AbstractModel
{

    const ENTITY = 'cdz_amp_cms_page';
    
    protected function _construct()
    {
        $this->_init('Codazon\GoogleAmpManager\Model\ResourceModel\Page');
    }
}
