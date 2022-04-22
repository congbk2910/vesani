<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;

class OmnisendOrderStatus extends AbstractModel implements OmnisendOrderStatusInterface, IdentityInterface
{
    const CACHE_TAG = OmnisendOrderStatusInterface::TABLE_NAME;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnisend\Omnisend\Model\ResourceModel\OmnisendOrderStatus');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getStatus()];
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentStatus()
    {
        return $this->getData(self::PAYMENT_STATUS);
    }

    /**
     * {@inheritDoc}
     */
    public function getFulfillmentStatus()
    {
        return $this->getData(self::FULFILLMENT_STATUS);
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentStatus($paymentStatus)
    {
        return $this->setData(self::PAYMENT_STATUS, $paymentStatus);
    }

    /**
     * {@inheritDoc}
     */
    public function setFulfillmentStatus($fulfillmentStatus)
    {
        return $this->setData(self::FULFILLMENT_STATUS, $fulfillmentStatus);
    }
}
