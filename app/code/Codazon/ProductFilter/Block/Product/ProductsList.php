<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Codazon\ProductFilter\Block\Product;
/**
 * Catalog Products List widget block
 * Class ProductsList
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\DataObject\IdentityInterface;


class ProductsList extends AbstractProduct implements BlockInterface, IdentityInterface
{
    /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Name of request parameter for page number value
     *
     * @deprecated @see $this->getData('page_var_name')
     */
    const PAGE_VAR_NAME = 'np';

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;
    /**
     * Instance of pager block
     *
     * @var Pager
     */
    protected $pager;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Builder
     */
    protected $sqlBuilder;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var Conditions
     */
    protected $conditionsHelper;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private $json;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var EncoderInterface|null
     */
    private $urlEncoder;

    /**
     * @var \Magento\Framework\View\Element\RendererList
     */
    private $rendererListBlock;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    protected $urlHelper;
    protected $bestSellerCollectionFactory;
    protected $imageHelperFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Codazon\ProductFilter\Model\ResourceModel\Bestsellers\CollectionFactory $bestSellerCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Codazon\ProductFilter\Block\ImageBuilderFactory $customImageBuilderFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->bestSellerCollectionFactory = $bestSellerCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->urlHelper = $urlHelper;
        $this->conditionsHelper = $conditionsHelper;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        //$this->reviewFactory = $reviewFactory;
        $this->customImageBuilderFactory = $customImageBuilderFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');
		$conditions = json_encode($this->getData());
        return [
            'PRODUCT_FILTER_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_storeManager->getStore()->getCurrentCurrency()->getCode(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            $this->getProductsPerPage(),
            $this->getProductsCount(),
            intval($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1)),
            $this->getProductsPerPage(),
            $conditions
        ];
    }

    public function getProductsCount()
    {
        if ($this->hasData('products_count')) {
            return $this->getData('products_count');
        }

        if (null === $this->getData('products_count')) {
            $this->setData('products_count', self::DEFAULT_PRODUCTS_COUNT);
        }

        return $this->getData('products_count');
    }

    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    protected function getPageSize()
    {
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    protected function getConditions($adConditions = null)
    {
        $conditions = $this->getData('conditions_encoded')
            ? $this->getData('conditions_encoded')
            : $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }
        if($adConditions){
            $adConditions = $this->conditionsHelper->decode($adConditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])
                && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
            ) {
                $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
            }
        }
        if($adConditions){
            $conditions = array_merge($conditions, $adConditions);
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            if(get_class($renderer) == 'Magento\Swatches\Block\Product\Renderer\Listing\Configurable\Interceptor'){
                return '';
            }
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    public function getAddToCartUrl($product, $additional = [])
    {
        /*if ($product->getTypeInstance()->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = [];
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }*/

        return $this->_cartHelper->getAddUrl($product, $additional);
    }
    
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product, $additional = []) : array
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $listBlock = $objectManager->get('\Magento\Catalog\Block\Product\ListProduct');
        $url =  $listBlock->getAddToCartUrl($product);
        //$url = $this->getAddToCartUrl($product,$additional);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
    
    protected function _getBestSellingCollection()
    {
        $collection = $this->bestSellerCollectionFactory->create();
        $bestSellerTable = $collection->getTable('sales_bestsellers_aggregated_daily');
        $collection->getSelect()->join(array('r' => $bestSellerTable), 'r.product_id=e.entity_id', array('*'))->group('e.entity_id');
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1));

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        return $collection;
    }

    protected function _getNewCollection()
    {
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_getAllProductProductCollection();        
        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        );

        return $collection;
    }
    
    protected function _getMostViewedCollection()
    {
        $collection = $this->objectManager->get(\Magento\Reports\Model\ResourceModel\Product\CollectionFactory::class)->create();
        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addViewsCount()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
        ->setPageSize($this->getPageSize());
        $this->setData('order_by', false);
        return $collection;
    }
    
    protected function _getLastXDaysMostViewedCollection(int $day = 30)
    {
        $today = time();
        $last = $today - (60*60*24*$day);
        $from = $this->_localeDate->date($last)->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $to = $this->_localeDate->date($today)->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        
        $collection = $this->objectManager->get(\Magento\Reports\Model\ResourceModel\Product\CollectionFactory::class)->create();
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addViewsCount($from, $to)
            ->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
        ->setPageSize($this->getPageSize());
        $this->setData('order_by', false);
        return $collection;
    }
    
    protected function _getAllProductProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->setFlag('has_stock_status_filter', true)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1));

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        return $collection;
    }

    protected function _getDealsCollection()
    {
        $now = date('Y-m-d');
        $collection = $this->_getAllProductProductCollection();
        
        $con = "^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`special_to_date`,`operator`:`^)=`,`value`:`{$now}`^],`1--2`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`special_price`,`operator`:`^)=`,`value`:`0`^]^]";
        $conditions = $this->getConditions($con);
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        return $collection;
    }

    public function createCollection()
    {
        $isAjax = !($this->getData('ajax_load'));
        $collection = null;
        if ($isAjax) {
            $displayType = $this->getDisplayType();
            switch ($displayType) {
                case 'all_products':
                    $collection = $this->_getAllProductProductCollection();
                    break;
                case 'bestseller_products':
                    $collection = $this->_getBestSellingCollection();
                    break;
                case 'new_products':
                    $collection = $this->_getNewCollection();
                    break;
                case 'most_viewed_products':
                    $collection = $this->_getMostViewedCollection();
                    break;
                case 'last_month_most_viewed_products':
                    $collection = $this->_getLastXDaysMostViewedCollection();
                    break;
                case 'deals':
                    $collection = $this->_getDealsCollection();
                    break;
            }
            if ($this->getData('order_by')) {
                $sort = explode(' ', $this->getData('order_by'));
                $collection->addAttributeToSort($sort[0], $sort[1]);
            }
            $this->_eventManager->dispatch(
                'catalog_block_product_list_collection',
                ['collection' => $collection]
            );
        }
        return $collection;
    }

    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->createCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    /**
     * Get value of widgets' title parameter
     *
     * @return mixed|string
     */

    public function getTemplate()
    {
        $template = $this->getData('filter_template');
        if($template == 'custom')
        {
            return $this->getData('custom_template');
        }
        else
        {
            return $template;
        }
    }
    
    public function isShow($item)
    {
    	$show = explode(",",$this->getData('show'));    	    	
    	if (in_array($item,$show) !== false) {
			return true;
		}else{
			return false;
		}
    }
    
    public function getImage($product, $imageId, $attributes = [])
    {
        $width = $this->getData('thumb_width');
        $height = $this->getData('thumb_height');
        $attributes = array('resize_width'=>$width,'resize_height'=>$height);

        $imageBuilder = $this->customImageBuilderFactory->create();
        return $imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->cdzcreate();
    }
    
    public function getBlockId()
    {
    	return uniqid("cdz_block_");
    }

    public function getNameInLayout(){
        return "cdz.productfilter.product.list";
    }
    
    protected function _toHtml(){
        $isAjax = $this->getData('is_ajax');
        //$isAjax = true;
        if($isAjax){
            return parent::_toHtml();
		}else{
		    $data = [
                'is_ajax'           =>  1,
                'title'             =>  $this->getData('title'),
                'display_type'      =>  $this->getData('display_type'),
                'products_count'    =>  $this->getData('products_count'),
                'order_by'          =>  $this->getData('order_by'),
                'show'              =>  $this->getData('show'),
                'thumb_width'       =>  $this->getData('thumb_width'),
                'thumb_height'      =>  $this->getData('thumb_height'),
                'filter_template'   =>  $this->getData('filter_template'),
                'custom_template'   =>  $this->getData('custom_template'),
                'show_slider'       =>  $this->getData('show_slider'),
                'slider_item'       =>  $this->getData('slider_item'),
                'conditions_encoded'        =>  $this->getData('conditions_encoded')
            ];
            $block = $this->getLayout()->createBlock('\Magento\Framework\View\Element\Template');
            $block->setTemplate('Codazon_ProductFilter::ajax/first_load.phtml');
            $block->setData('json_data',json_encode($data));
            return $block->toHtml();
		}
	}
}
