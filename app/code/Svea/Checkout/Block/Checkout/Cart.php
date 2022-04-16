<?php
namespace Svea\Checkout\Block\Checkout;


class Cart extends \Magento\Checkout\Block\Cart\Totals
{
    /**
     * @var \Magento\Sales\Model\Order\Address
     */
    protected $_address;

    /**
     * Return review shipping address
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * Return review quote totals
     *
     * @return array
     */
    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }



    /**
     * @var \Svea\Checkout\Helper\Data
     */
    protected $helper;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Config $salesConfig,
        \Svea\Checkout\Helper\Data $helper,
        array $layoutProcessors = [],
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $customerSession, $checkoutSession,$salesConfig, $layoutProcessors,$data);
    }

    public function showCouponCode()
    {

        return $this->helper->showCouponLayout();
    }
}


