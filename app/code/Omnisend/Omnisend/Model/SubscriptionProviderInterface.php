<?php

namespace Omnisend\Omnisend\Model;

use Magento\Newsletter\Model\Subscriber;

interface SubscriptionProviderInterface
{
    /**
     * @param int $id
     * @param string $email
     * @param int $storeId
     * @return Subscriber
     */
    public function getSubscription($id, $email, $storeId);
}
