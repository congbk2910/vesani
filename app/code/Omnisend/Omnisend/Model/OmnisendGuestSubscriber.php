<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Omnisend\Omnisend\Api\Data\OmnisendGuestSubscriberInterface;
use Omnisend\Omnisend\Model\ResourceModel\OmnisendGuestSubscriber as OmnisendGuestSubscriberResource;

class OmnisendGuestSubscriber extends AbstractModel implements OmnisendGuestSubscriberInterface, IdentityInterface
{
    const CACHE_TAG = OmnisendGuestSubscriberInterface::TABLE_NAME;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(OmnisendGuestSubscriberResource::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function getOmnisendId()
    {
        return $this->getData(self::OMNISEND_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setOmnisendId($omnisendId)
    {
        return $this->setData(self::OMNISEND_ID, $omnisendId);
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscriberId()
    {
        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setSubscriberId($subscriberId)
    {
        return $this->setData(self::SUBSCRIBER_ID, $subscriberId);
    }

    /**
     * {@inheritDoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getSubscriberId()];
    }
}
