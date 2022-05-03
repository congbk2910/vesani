<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model;

class SalesRule extends \Magento\SalesRule\Model\Rule
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Acart\Model\ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }
}
