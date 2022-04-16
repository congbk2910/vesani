<?php declare(strict_types=1);

namespace Svea\Checkout\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ProductCampaign extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = 'widget/product_campaign.phtml';

    /**
     * @var \Svea\Checkout\ViewModel\Widget\ProductCampaign\ViewModel
     */
    private $viewModel;

    /**
     * ProductCampaign constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Svea\Checkout\ViewModel\Widget\ProductCampaign\ViewModel $viewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewModel = $viewModel;
    }

    /**
     * @return \Svea\Checkout\ViewModel\Widget\ProductCampaign\ViewModel
     */
    public function getViewModel()
    {
        return $this->viewModel;
    }

    /**
     * @return array|mixed|null
     */
    public function getCustomPostsLabel()
    {
        return $this->getData('posts');
    }
}
