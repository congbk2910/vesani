<?php declare(strict_types=1);

namespace Svea\Checkout\Model\Resource\CampaignInfo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Svea\Checkout\Model\CampaignInfo as CampaignInfoModel;
use Svea\Checkout\Model\Resource\CampaignInfo as CampaignInfoResource;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            CampaignInfoModel::class,
            CampaignInfoResource::class
        );
    }
}
