<?php

namespace Svea\Checkout\Api;

use Magento\Catalog\Api\Data\ProductInterface;

interface GetAvailablePartPaymentCampaigns
{
    /**
     * @param ProductInterface $product
     *
     * @return Data\CampaignInfoInterface []
     */
    public function getAvailablePartPaymentCampaigns(ProductInterface $product);
}
