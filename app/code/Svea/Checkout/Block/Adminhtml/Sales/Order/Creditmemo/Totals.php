<?php


namespace Svea\Checkout\Block\Adminhtml\Sales\Order\Creditmemo;


class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Svea\Checkout\Helper\Data
     */
    protected $_sveaHelper;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Svea\Checkout\Helper\Data $sveaHelper,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        
        $this->_sveaHelper = $sveaHelper;
        $this->_currency = $currency;
        
        parent::__construct($context, $data);
    }


    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        if(!$this->getSource()->getSveaInvoiceFee()) {
            return $this;
        }
        
        $total = new \Magento\Framework\DataObject([
            'code' => 'svea_invoice_fee',
            'value' => $this->getSource()->getSveaInvoiceFee(),
            'label' => $this->_sveaHelper->getInvoiceFeeLabel(),
        ]);

        // add it to totals!
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
    
}