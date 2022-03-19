<?php
/**
 * Copyright © 2021 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Block\Brand;

class BrandList extends \Codazon\Shopbybrandpro\Block\Brand\AbstractBrandList
{    
    protected $brandCollection;
    
    public function getViewModel()
    {
        return $this->helper->getCoreHelper()->getCoreRegistry()->registry('brands_data');
    }
    
    protected function _getBrandCollection()
    {
        if ($this->brandCollection === null) {
            $this->brandCollection = $this->initializeBrandCollection();
        }
        return $this->brandCollection;
    }
    
    public function getBrandCollection()
    {
        return $this->_getBrandCollection();
    }
    
    public function setCollection($collection)
    {
        $this->brandCollection = $collection;
        return $this;
    }
    
    protected function _beforeToHtml()
    {
        $collection = $this->_getBrandCollection();

        $this->addToolbarBlock($collection);

        if (!$collection->isLoaded()) {
            $collection->load();
        }
        return parent::_beforeToHtml();
    }
    
    private function addToolbarBlock($collection)
    {
        $toolbarLayout = $this->getToolbarFromLayout();

        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }
    
    private function getToolbarFromLayout()
    {
        
        $blockName = $this->getToolbarBlockName();
        $toolbarLayout = false;
        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }
        return $toolbarLayout;
    }
    
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
    
    private function initializeBrandCollection()
    {
        $collection = $this->helper->getCoreHelper()->getCoreRegistry()->registry('brand_collection');
        $this->firstCharFilter($collection);
        $this->_eventManager->dispatch(
            'brand_list_collection',
            ['collection' => $collection]
        );
        return $collection;
    }
    
    public function firstCharFilter($collection)
    {
        if ($firstChar = $this->getRequest()->getParam('first_char')) {
            if ($firstChar === 'num') {
                $or = []; $num = [];
                for ($i = 0; $i <= 9; $i++) {
                    $or[] = ['like' => $i . '%'];
                    $num[] = $i;
                }
                $collection->addFieldToFilter('name', $or);
            } else {
                $collection->addFieldToFilter('name', ['like' => $firstChar . '%']);
            }
        }
        return $this;
    }
    
    private function configureToolbar($toolbar, $collection)
    {
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }
    
    public function getToolbarBlock()
    {
        $block = $this->getToolbarFromLayout();

        if (!$block) {
            $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        }

        return $block;
    }
    
    public function getMode()
    {
        if ($this->getChildBlock('toolbar')) {
            return $this->getChildBlock('toolbar')->getCurrentMode();
        }

        return $this->getDefaultListingMode();
    }
    
    private function getDefaultListingMode()
    {
        $defaultToolbar = $this->getToolbarBlock();
        $availableModes = $defaultToolbar->getModes();
        $mode = $this->getData('mode');
        if (!$mode || !isset($availableModes[$mode])) {
            $mode = $defaultToolbar->getCurrentMode();
        }
        return $mode;
    }
    
    public function getBrandItemsArray()
    {
        $viewModel = $this->getViewModel();
        $collection = $this->getBrandCollection();
        return $this->helper->getBrandItemsArray(
            $collection, 
            $viewModel->getData('brand_thumbnail_width'),
            $viewModel->getData('brand_thumbnail_height')
        );
    }
    
    public function getFilterUrl($params = [], $removedParam = null)
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = false;
        $urlParams['_use_rewrite'] = true;
        if ($removedParam) {
            $params[$removedParam] = null;
        }
        $urlParams['_query'] = $params;
        $urlParams['p'] = false;
        return $this->getUrl('*/*/*', $urlParams);
    }
    
    public function getThumbnailImage($brand, array $options = [])
    {
		return $this->helper->getBrandImage($brand, 'brand_thumbnail', $options);
	}

	public function getBrandPageUrl($brand)
    {
		return $this->helper->getBrandPageUrl($brandModel);
	}
}