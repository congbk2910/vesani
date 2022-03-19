<?php
/**
 * Copyright © 2021 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\Shopbybrandpro\Block\Brand\BrandList;

use Codazon\Shopbybrandpro\Helper\BrandList;
use Codazon\Shopbybrandpro\Model\Brand\BrandList\Toolbar as ToolbarModel;
use Codazon\Shopbybrandpro\Model\Brand\BrandList\ToolbarMemorizer;
use Magento\Framework\App\ObjectManager;

class Toolbar extends \Magento\Framework\View\Element\Template
{

    protected $_collection = null;

    protected $_availableOrder = null;

    protected $_availableMode = [];

    protected $_enableViewSwitcher = true;

    protected $_isExpanded = true;

    protected $_orderField = null;

    protected $_direction = BrandList::DEFAULT_SORT_DIRECTION;

    protected $_viewMode = null;

    protected $_paramsMemorizeAllowed = true;

    protected $_template = 'Magento_Catalog::product/list/toolbar.phtml';

    protected $_brandConfig;

    protected $_catalogSession;

    protected $_toolbarModel;

    private $toolbarMemorizer;

    protected $_brandListHelper;

    protected $urlEncoder;

    protected $_postDataHelper;

    private $httpContext;

    private $formKey;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Codazon\Shopbybrandpro\Model\Config $brandConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        BrandList $brandListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = [],
        ToolbarMemorizer $toolbarMemorizer = null,
        \Magento\Framework\App\Http\Context $httpContext = null,
        \Magento\Framework\Data\Form\FormKey $formKey = null
    ) {
        $this->_catalogSession = $catalogSession;
        $this->_brandConfig = $brandConfig;
        $this->_toolbarModel = $toolbarModel;
        $this->urlEncoder = $urlEncoder;
        $this->_brandListHelper = $brandListHelper;
        $this->_postDataHelper = $postDataHelper;
        $this->toolbarMemorizer = $toolbarMemorizer ?: ObjectManager::getInstance()->get(
            ToolbarMemorizer::class
        );
        $this->httpContext = $httpContext ?: ObjectManager::getInstance()->get(
            \Magento\Framework\App\Http\Context::class
        );
        $this->formKey = $formKey ?: ObjectManager::getInstance()->get(
            \Magento\Framework\Data\Form\FormKey::class
        );
        parent::__construct($context, $data);
    }

    /**
     * Disable list state params memorizing
     *
     * @return $this
     * @deprecated 103.0.1
     */
    public function disableParamsMemorizing()
    {
        $this->_paramsMemorizeAllowed = false;
        return $this;
    }

    /**
     * Memorize parameter value for session
     *
     * @param string $param parameter name
     * @param mixed $value parameter value
     * @return $this
     * @deprecated 103.0.1
     */
    protected function _memorizeParam($param, $value)
    {
        if ($this->_paramsMemorizeAllowed && !$this->_catalogSession->getParamsMemorizeDisabled()) {
            $this->_catalogSession->setData($param, $value);
        }
        return $this;
    }

    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }
        return $this;
    }

    /**
     * Return products collection instance
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Return current page from request
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_toolbarModel->getCurrentPage();
    }

    /**
     * Get grid products sort order field
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_current_grid_order');
        if ($order) {
            return $order;
        }

        $orders = $this->getAvailableOrders();
        $defaultOrder = $this->getOrderField();

        if (!isset($orders[$defaultOrder])) {
            $keys = array_keys($orders);
            $defaultOrder = $keys[0];
        }

        $order = $this->toolbarMemorizer->getOrder();
        if (!$order || !isset($orders[$order])) {
            $order = $defaultOrder;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::ORDER_PARAM_NAME, $order, $defaultOrder);
        }

        $this->setData('_current_grid_order', $order);
        return $order;
    }

    /**
     * Retrieve current direction
     *
     * @return string
     */
    public function getCurrentDirection()
    {
        $dir = $this->_getData('_current_grid_direction');
        if ($dir) {
            return $dir;
        }

        $directions = ['asc', 'desc'];
        $dir = strtolower($this->toolbarMemorizer->getDirection());
        if (!$dir || !in_array($dir, $directions)) {
            $dir = $this->_direction;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::DIRECTION_PARAM_NAME, $dir, $this->_direction);
        }

        $this->setData('_current_grid_direction', $dir);
        return $dir;
    }

    /**
     * Set default Order field
     *
     * @param string $field
     * @return $this
     */
    public function setDefaultOrder($field)
    {
        $this->loadAvailableOrders();
        if (isset($this->_availableOrder[$field])) {
            $this->_orderField = $field;
        }
        return $this;
    }

   
    public function setDefaultDirection($dir)
    {
        if (in_array(strtolower($dir), ['asc', 'desc'])) {
            $this->_direction = strtolower($dir);
        }
        return $this;
    }

    public function getAvailableOrders()
    {
        $this->loadAvailableOrders();
        return $this->_availableOrder;
    }

    public function setAvailableOrders($orders)
    {
        $this->_availableOrder = $orders;
        return $this;
    }

    public function addOrderToAvailableOrders($order, $value)
    {
        $this->loadAvailableOrders();
        $this->_availableOrder[$order] = $value;
        return $this;
    }

    public function removeOrderFromAvailableOrders($order)
    {
        $this->loadAvailableOrders();
        if (isset($this->_availableOrder[$order])) {
            unset($this->_availableOrder[$order]);
        }
        return $this;
    }

    public function isOrderCurrent($order)
    {
        return $order == $this->getCurrentOrder();
    }

    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

    public function getPagerEncodedUrl($params = [])
    {
        return $this->urlEncoder->encode($this->getPagerUrl($params));
    }

    public function getCurrentMode()
    {
        $mode = $this->_getData('_current_grid_mode');
        if ($mode) {
            return $mode;
        }
        $defaultMode = $this->_brandListHelper->getDefaultViewMode($this->getModes());
        $mode = $this->toolbarMemorizer->getMode();
        
        if (!$mode || !isset($this->_availableMode[$mode])) {
            $mode = $defaultMode;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::MODE_PARAM_NAME, $mode, $defaultMode);
        }
        $this->setData('_current_grid_mode', $mode);
        return $mode;
    }

    public function isModeActive($mode)
    {
        return $this->getCurrentMode() == $mode;
    }

    public function getModes()
    {
        if ($this->_availableMode === []) {
            $this->_availableMode = $this->_brandListHelper->getAvailableViewMode();
        }
        return $this->_availableMode;
    }

    public function setModes($modes)
    {
        $this->getModes();
        if (!isset($this->_availableMode)) {
            $this->_availableMode = $modes;
        }
        return $this;
    }

    public function disableViewSwitcher()
    {
        $this->_enableViewSwitcher = false;
        return $this;
    }

    public function enableViewSwitcher()
    {
        $this->_enableViewSwitcher = true;
        return $this;
    }

    public function isEnabledViewSwitcher()
    {
        return $this->_enableViewSwitcher;
    }

    public function disableExpanded()
    {
        $this->_isExpanded = false;
        return $this;
    }

    public function enableExpanded()
    {
        $this->_isExpanded = true;
        return $this;
    }

    public function isExpanded()
    {
        return $this->_isExpanded;
    }

    public function getDefaultPerPageValue()
    {
        if ($this->getCurrentMode() == 'list' && ($default = $this->getDefaultListPerPage())) {
            return $default;
        } elseif ($this->getCurrentMode() == 'grid' && ($default = $this->getDefaultGridPerPage())) {
            return $default;
        }
        return $this->_brandListHelper->getDefaultLimitPerPageValue($this->getCurrentMode());
    }

    public function getAvailableLimit()
    {
        return $this->_brandListHelper->getAvailableLimit($this->getCurrentMode());
    }

    public function getLimit()
    {
        $limit = $this->_getData('_current_limit');
        if ($limit) {
            return $limit;
        }

        $limits = $this->getAvailableLimit();
        $defaultLimit = $this->getDefaultPerPageValue();
        if (!$defaultLimit || !isset($limits[$defaultLimit])) {
            $keys = array_keys($limits);
            $defaultLimit = $keys[0];
        }

        $limit = $this->toolbarMemorizer->getLimit();
        if (!$limit || !isset($limits[$limit])) {
            $limit = $defaultLimit;
        }

        if ($this->toolbarMemorizer->isMemorizingAllowed()) {
            $this->httpContext->setValue(ToolbarModel::LIMIT_PARAM_NAME, $limit, $defaultLimit);
        }

        $this->setData('_current_limit', $limit);
        return $limit;
    }

    public function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }

    public function getFirstNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize() * ($collection->getCurPage() - 1) + 1;
    }

    public function getLastNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize() * ($collection->getCurPage() - 1) + $collection->count();
    }

    public function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }

    public function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }

    public function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    public function getPagerHtml()
    {
        $pagerBlock = $this->getChildBlock('brand_list_toolbar_pager');

        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(
                false
            )->setShowPerPage(
                false
            )->setShowAmounts(
                false
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->getLimit()
            )->setCollection(
                $this->getCollection()
            );

            return $pagerBlock->toHtml();
        }

        return '';
    }

    /**
     * Retrieve widget options in json format
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = [])
    {
        $defaultMode = $this->_brandListHelper->getDefaultViewMode($this->getModes());
        $options = [
            'mode' => ToolbarModel::MODE_PARAM_NAME,
            'direction' => ToolbarModel::DIRECTION_PARAM_NAME,
            'order' => ToolbarModel::ORDER_PARAM_NAME,
            'limit' => ToolbarModel::LIMIT_PARAM_NAME,
            'modeDefault' => $defaultMode,
            'directionDefault' => $this->_direction ?: BrandList::DEFAULT_SORT_DIRECTION,
            'orderDefault' => $this->getOrderField(),
            'limitDefault' => $this->_brandListHelper->getDefaultLimitPerPageValue($defaultMode),
            'url' => $this->getPagerUrl(),
            'formKey' => $this->formKey->getFormKey(),
            'post' => $this->toolbarMemorizer->isMemorizingAllowed() ? true : false
        ];
        $options = array_replace_recursive($options, $customOptions);
        return json_encode(['productListToolbarForm' => $options]);
    }

    /**
     * Get order field
     *
     * @return null|string
     */
    protected function getOrderField()
    {
        if ($this->_orderField === null) {
            $this->_orderField = $this->_brandListHelper->getDefaultSortField();
        }
        return $this->_orderField;
    }

    /**
     * Load Available Orders
     *
     * @return $this
     */
    private function loadAvailableOrders()
    {
        if ($this->_availableOrder === null) {
            $this->_availableOrder = $this->_brandConfig->getAttributeUsedForSortByArray();
        }
        return $this;
    }
}
