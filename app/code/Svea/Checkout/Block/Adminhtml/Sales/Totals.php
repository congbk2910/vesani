<?php


namespace Svea\Checkout\Block\Adminhtml\Sales;


class Totals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Svea\Checkout\Helper\Data
     */
    protected $_sveaHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Svea\Checkout\Helper\Data $sveaHelper,
        array $data = []
    ) {
        
        $this->_sveaHelper = $sveaHelper;

        parent::__construct($context, $data);
    }


    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
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
        $this->getParentBlock()->addTotal($total, 'svea_invoice_fee');
        return $this;
    }
    
}