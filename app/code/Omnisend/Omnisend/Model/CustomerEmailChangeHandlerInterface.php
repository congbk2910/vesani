<?php

namespace Omnisend\Omnisend\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;

interface CustomerEmailChangeHandlerInterface
{
    /**
     * @param Customer|CustomerInterface $customer
     * @return null|string
     */
    public function handleEmailChange($customer);
}
