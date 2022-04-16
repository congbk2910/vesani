<?php
namespace Svea\Checkout\Model\Quote\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class Fee extends AbstractTotal
{


    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $amount = $quote->getSveaInvoiceFee();
        if (!$amount) {
            return $this;
        }

        $total->setTotalAmount('svea_invoice_fee', $amount);
        $total->setBaseTotalAmount('svea_invoice_fee', $amount);
        return $this;
    }

    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $amount = $total->getTotalAmount('svea_invoice_fee');
        if (!$amount) {
            return [];
        }
        return [
            'code' => 'svea_invoice_fee',
            'title' => __('Invoice Fee'),
            'value' => $total->getTotalAmount('svea_invoice_fee')
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Invoice Fee');
    }
}