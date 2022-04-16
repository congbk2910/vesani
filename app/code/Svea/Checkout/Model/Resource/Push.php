<?php

namespace Svea\Checkout\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Svea Push resource model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Push extends AbstractDb
{
    /**
     * Define main table
     */

    public function _construct()
    {
        $this->_init('svea_push', 'entity_id');
    }

    /**
     * Check if object is new
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isObjectNotNew(\Magento\Framework\Model\AbstractModel $object)
    {
        return $object->getId() !== null;
    }

}
