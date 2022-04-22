<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AbTesting\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\AbTesting\Api\Data\TrackMasterInterface;
use Magento\Framework\DataObject\IdentityInterface;

class TrackMaster extends AbstractModel implements TrackMasterInterface, IdentityInterface
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'tracks_master';
 
    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * AbTesting TrackMaster cache tag.
     */
    const CACHE_TAG = 'tracks_master';

    /**
     * @var string
     */
    protected $_cacheTag = 'tracks_master';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'tracks_master';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\AbTesting\Model\ResourceModel\TrackMaster::class);
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteProduct();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Product.
     *
     * @return \Webkul\AbTesting\Model\TrackMaster
     */
    public function noRouteProduct()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\AbTesting\Api\Data\TrackMasterInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

     /**
      * Insert multiple
      *
      * @param array $data
      * @return void
      * @throws \Magento\Framework\Exception\AlreadyExistsException
      * @throws \Exception
      */
    public function insertMultiple($data, $tableName = self::TABLE_NAME)
    {
        try {
            $tableName = $this->getResource()->getTable(self::TABLE_NAME);
            $this->connection = $this->getResource()->getConnection();
            return $this->connection->insertMultiple($tableName, $data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
