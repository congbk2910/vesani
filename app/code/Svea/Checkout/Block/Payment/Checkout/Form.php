<?php

namespace Svea\Checkout\Block\Payment\Checkout;
/**
 * Payment method form base block
 */
class Form extends \Magento\Payment\Block\Form
{
    /**
     * @var string
     */
    protected $_template = 'Svea_Checkout::payment/checkout/form.phtml';

}
