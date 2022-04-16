<?php

namespace Svea\Checkout\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CampaignInfo extends AbstractDb
{
    /**
     * Define main table
     */

    public function _construct()
    {
        $this->_init('svea_campaign_info', 'entity_id');
    }
}
