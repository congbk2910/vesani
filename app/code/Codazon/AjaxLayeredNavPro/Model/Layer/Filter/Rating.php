<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\AjaxLayeredNavPro\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Resolver;
use \Magento\Framework\App\ObjectManager;
/**
 * Layer attribute filter
 */
class Rating extends AbstractFilter
{   
    protected $objectManager;
    
    protected $helper;
    
    protected $enableMultiSelect;
    
    protected $sqlFieldName;
    
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->objectManager = ObjectManager::getInstance();
        $this->helper = $this->objectManager->get('Codazon\AjaxLayeredNavPro\Helper\Data');
        $this->enableMultiSelect = $this->helper->enableMultiSelect();
        $this->_requestVar = $this->helper->getRatingCode();
        $this->sqlFieldName = $this->helper->getAvgRatingPercentFieldName();        
    }
    
    public function getName()
    {
        return $this->helper->getRatingFilterLabel();
    }
    
    public function applyToCollection($productCollection, $request, $requestVar)
    {
        $attributeValue = $request->getParam($requestVar);
        if ($attributeValue && is_numeric($attributeValue)) {
            $percent = 100 * $attributeValue / 5;
            $this->helper->ratingFilter($productCollection, $attributeValue);
        }
        return $productCollection;
    }
    
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);
        $productCollection = $this->getLayer()->getProductCollection();
       
        if (empty($attributeValue) && !is_numeric($attributeValue)) {
            return $this;
        }
        $this->setData('skip_seo', true);
        $productCollection = $this->getLayer()->getProductCollection();
        
        $productCollection->setFlag('before_apply_faceted_data_'.$this->_requestVar,
            $this->_getRatingsData($productCollection)
        );
        $this->helper->ratingFilter($productCollection, $attributeValue);
        
        $label = $this->_getRatingLabel($attributeValue);
        $this->getLayer()
            ->getState()
            ->addFilter($this->_createItem($label, $attributeValue));
        return $this;
    }
    
    
    protected function _getRatingLabel($score)
    {
        if ($this->helper->getRatingFilterType() == 'interval') {
            $maxScore = $score;
            $minScore = $score - 1;
            if ($minScore == 0) {
                return __('1 star');
            }
            return __('%1 < star ≤ %2', $minScore, $maxScore);
        } else {
            return ($score == 1) ? __('%1 star and above', $score) : __('%1 stars and above', $score);
        }
    }
    
    

    protected function _getRatingsData($collection)
    {
        $connection = $collection->getConnection();
        $storeId = $collection->getStoreId();
        $options = [];
        $ratingType = $this->helper->getRatingFilterType();
        if ($ratingType == 'interval') {
            for ($i = 5; $i > 0; $i--) {
                $maxPercent = 100 * $i / 5;
                $minPercent = 100 * ($i-1) / 5;
                $cloneCollection = clone $collection;
                $cloneCollection->getSelect()->where("({$minPercent} < {$this->sqlFieldName}) AND ({$this->sqlFieldName} <= {$maxPercent})");
                $this->helper->attachRatingAvgPercentFieldToCollection($cloneCollection);
                $options[] = [
                    'label' => $this->_getRatingLabel($i),
                    'value' => $i,
                    'count' => $connection->fetchOne($cloneCollection->getSelectCountSql())
                ];
            }
        } else {
            for ($i = 4; $i > 0; $i--) {
                $percent = 100 * $i / 5;
                $cloneCollection = clone $collection;
                $cloneCollection->getSelect()->where("{$this->sqlFieldName} >= {$percent}");
                            
                $this->helper->attachRatingAvgPercentFieldToCollection($cloneCollection);
                
                $options[] = [
                    'label' => $this->_getRatingLabel($i),
                    'value' => $i,
                    'count' => $connection->fetchOne($cloneCollection->getSelectCountSql())
                ];
            }
        }
        return $options;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
     protected function _getItemsData()
     {
        $productCollection = $this->getLayer()->getProductCollection();
        if ($data = $productCollection->getFlag('before_apply_faceted_data_'.$this->_requestVar)) {
        } else {
            $data = $this->_getRatingsData($productCollection);
        }
        $this->setData('items_data', $data);
        return $data;
    }
}
