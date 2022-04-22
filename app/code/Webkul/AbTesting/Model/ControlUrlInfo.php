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
use Webkul\AbTesting\Api\Data\ControlUrlInfoInterface;
use Magento\Framework\DataObject\IdentityInterface;

class ControlUrlInfo extends AbstractModel implements ControlUrlInfoInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * AbTesting ControlUrlInfo cache tag.
     */
    const CACHE_TAG = 'abtesting_control_url_info';

    /**
     * @var string
     */
    protected $_cacheTag = 'abtesting_control_url_info';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'abtesting_control_url_info';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\AbTesting\Model\ResourceModel\ControlUrlInfo::class);
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
     * @return \Webkul\AbTesting\Model\ControlUrlInfo
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
     * @return \Webkul\AbTesting\Api\Data\ControlUrlInfoInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
