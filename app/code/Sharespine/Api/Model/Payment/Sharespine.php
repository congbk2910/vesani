<?php

namespace Sharespine\Api\Model\Payment;

/**
 * Pay In Store payment method model
 */
class Sharespine extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'sharespine_sharespine';
}