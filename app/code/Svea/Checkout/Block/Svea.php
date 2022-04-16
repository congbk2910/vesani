<?php
namespace Svea\Checkout\Block;


class Svea extends \Magento\Framework\View\Element\Template
{


    /**
     * @var \Svea\Checkout\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    protected $getCurrentSveaOrderIdService;

    protected $iframeSnippet;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Svea\Checkout\Helper\Data $helper
     * @param \Svea\Checkout\Service\GetCurrentSveaOrderId $getCurrentSveaOrderIdService,
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Svea\Checkout\Helper\Data $helper,
        \Svea\Checkout\Service\GetCurrentSveaOrderId $getCurrentSveaOrderIdService,
        array $data = []
    )
    {

        $this->getCurrentSveaOrderIdService = $getCurrentSveaOrderIdService;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }


    public function getSveaCheckoutKey()
    {
        return $this->getHelper()->getMerchantId();
    }


    public function getSveaOrderId()
    {
        return $this->getCurrentSveaOrderIdService->getSveaOrderId();
    }

    public function getIframeSnippet()
    {
        return $this->iframeSnippet;
    }

    public function setIframeSnippet($snippet)
    {
        $this->iframeSnippet = $snippet;
    }

    public function getHelper(){
        return $this->helper;
    }
 }

