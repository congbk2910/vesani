<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Omnisend\Omnisend\Api\Data\OmnisendRateLimitInterface;

class OmnisendRateLimit extends AbstractModel implements OmnisendRateLimitInterface, IdentityInterface
{
    const CACHE_TAG = OmnisendRateLimitInterface::TABLE_NAME;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnisend\Omnisend\Model\ResourceModel\OmnisendRateLimit');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
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
    public function getLimitTotal()
    {
        return $this->getData(self::LIMIT_TOTAL);
    }

    /**
     * {@inheritDoc}
     */
    public function getLimitRemaining()
    {
        return $this->getData(self::LIMIT_REMAINING);
    }

    /**
     * {@inheritDoc}
     */
    public function getResetsIn()
    {
        return $this->getData(self::RESETS_IN);
    }

    /**
     * {@inheritDoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
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
    public function setLimitTotal($limitTotal)
    {
        return $this->setData(self::LIMIT_TOTAL, $limitTotal);
    }

    /**
     * {@inheritDoc}
     */
    public function setLimitRemaining($limitRemaining)
    {
        return $this->setData(self::LIMIT_REMAINING, $limitRemaining);
    }

    /**
     * {@inheritDoc}
     */
    public function setResetsIn($resetsIn)
    {
        return $this->setData(self::RESETS_IN, $resetsIn);
    }

    /**
     * {@inheritDoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
