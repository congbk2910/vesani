<?php
namespace Cminds\Salesrep\Model;

use Magento\Framework\Model\AbstractModel;

class CommissionGroup extends AbstractModel
{

    protected function _construct()
    {
        $this->_init('Cminds\Salesrep\Model\ResourceModel\CommissionGroup');
    }
}
