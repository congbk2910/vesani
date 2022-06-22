<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Block\Widget;

use Codazon\GoogleAmpManager\Model\AmpConfig;

use Codazon\GoogleAmpManager\Helper\Data;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{    
    protected $productCollectionFactory;
    
    protected $filterData;
       
    protected $show;
        
    protected $objectManager;
    
    public function getObjectManager()
    {
        if ($this->objectManager === null) {
            $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }
        return $this->objectManager;
    }
    
    protected function _getBestSellingCollection()
    {
        $orderItemCol = $this->getObjectManager()->get('Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory')->create()
            ->addFieldToSelect(['product_id'])
            ->addFieldToFilter('parent_item_id', array('null' => true));
        $orderItemCol->getSelect()
            ->columns(array('ordered_qty' => 'SUM(`main_table`.`qty_ordered`)'))
            ->group('main_table.product_id')
            ->joinInner(
                array('sfo' => $orderItemCol->getTable('sales_order')),
                "(main_table.order_id = sfo.entity_id) AND (sfo.state <> 'canceled')",
                []
            );
        $collection = $this->_getAllProductProductCollection();
        $collection->getSelect()
            ->joinLeft(
                array('sfoi' => $orderItemCol->getSelect()),
                'e.entity_id = sfoi.product_id',
                array('ordered_qty' => 'sfoi.ordered_qty')
            )
            ->where('sfoi.ordered_qty > 0')
            ->order('ordered_qty desc');
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
    
    protected function _getAllProductProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1));

        if ($productIds = $this->getData('product_ids')) {
            if (!is_array($productIds)) {
                $productIds = explode(',', $productIds);
            }
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            $productIds = implode(',', $productIds);
            $collection->getSelect()->order(new \Zend_Db_Expr("FIELD(e.entity_id, $productIds)"));
        }
        if ($this->getData('conditions_encoded')) {
            $conditions = $this->getConditions();
            $conditions->collectValidatedAttributes($collection);
            $this->sqlBuilder->attachConditionToCollection($collection, $conditions);
        }
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
                case 'best_selling_products':
                    $collection = $this->_getBestSellingCollection();
                    break;
                case 'new_products':
                    $collection = $this->_getNewCollection();
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
    
    public function subString($str, $strLenght)
    {
        $str = $this->stripTags($str);
        if(strlen($str) > $strLenght) {
            $strCutTitle = substr($str, 0, $strLenght);
            $str = substr($strCutTitle, 0, strrpos($strCutTitle, ' '))."&hellip;";
        }
        return $str;
    }
    
    public function getTemplate()
    {
        if ($template = $this->getData('custom_template')) {
            return $template;
        } else {
            return 'Codazon_GoogleAmpManager::amp/widget/amp-products-list/default.phtml';
        }
    }
    
    public function getElementShow()
    {
        if ($this->show === null) {
            $this->show = explode(',', $this->getData('show'));
        }
        return $this->show;
    }
    
    public function displayOnListing($item)
    {
    	return in_array($item, $this->getElementShow());
    }
    
}