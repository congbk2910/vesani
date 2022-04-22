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
use Webkul\AbTesting\Api\Data\GoalsDataInterface;
use Magento\Framework\DataObject\IdentityInterface;

class GoalsData extends AbstractModel implements GoalsDataInterface, IdentityInterface
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'abtesting_goals_data';
 
    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * AbTesting TestMain cache tag.
     */
    const CACHE_TAG = 'abtesting_goals_data';

    /**
     * @var string
     */
    protected $_cacheTag = 'abtesting_goals_data';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'abtesting_goals_data';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\AbTesting\Model\ResourceModel\GoalsData::class);
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
     * @return \Webkul\AbTesting\Model\GoalsData
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
     * @return \Webkul\AbTesting\Api\Data\GoalsDataInterface
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
