<?php declare(strict_types=1);

namespace Svea\Checkout\Block\Widget\ViewModel;

class ProductCampaign
{
    /**
     * @var \Svea\Checkout\Api\GetAvailablePartPaymentCampaigns
     */
    private $campaignService;

    /**
     * @var \Svea\Checkout\Block\Widget\ProductProvider
     */
    private $productProvider;

    /**
     * ProductCampaign constructor.
     *
     * @param \Svea\Checkout\Block\Widget\ProductProvider $productProvider
     * @param \Svea\Checkout\Api\GetAvailablePartPaymentCampaigns $campaignService
     */
    public function __construct(
        \Svea\Checkout\Block\Widget\ProductProvider $productProvider,
        \Svea\Checkout\Api\GetAvailablePartPaymentCampaigns $campaignService
    ) {
        $this->campaignService = $campaignService;
        $this->productProvider = $productProvider;
    }
}