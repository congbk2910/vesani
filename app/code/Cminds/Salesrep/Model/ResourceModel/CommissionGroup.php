<?php
namespace Cminds\Salesrep\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CommissionGroup extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('salesrep_commission_group', 'entity_id');
    }
}
