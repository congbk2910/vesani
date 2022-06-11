<?php

namespace Sharespine\Api\Model\Payment;

/**
 * Pay In Store payment method model
 */
class Ahlens extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'sharespine_ahlens';
}