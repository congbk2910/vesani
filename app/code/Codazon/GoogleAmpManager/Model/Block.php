<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Model;

class Block extends \Codazon\GoogleAmpManager\Model\AbstractModel
{

    const ENTITY = 'cdz_amp_blog_post';
    
    protected function _construct()
    {
        $this->_init('Codazon\GoogleAmpManager\Model\ResourceModel\Block');
    }
}
