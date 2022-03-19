<?php
/**
 * Copyright © 2021 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Block\Brand;

class AbstractBrandList extends \Magento\Framework\View\Element\Template
{
    
    protected $helper;
    
    protected $coreHelper;
    
    protected $brandCollection;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Codazon\Shopbybrandpro\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->coreHelper = $helper->getCoreHelper();
    }
    
    protected function _getBrandCollection()
    {
        if ($this->brandCollection === null) {
            $this->brandCollection = $this->helper->getBrandCollection();
        }
        return $this->brandCollection;
    }
    
    public function setCollection($collection)
    {
        $this->brandCollection = $collection;
        return $this;
    }
    
    public function getBrandCollection()
    {
        return $this->_getBrandCollection();
    }
    
    public function getHelper()
    {
        return $this->helper;
    }
    
    public function getCoreHelper()
    {
        return $this->coreHelper;
    }
    
    public function getAlphabetTable() {
        $alphabetString = $this->getData('alphabet_table');
        if (!$alphabetString) {
            $alphabetString = $this->coreHelper->getConfig('codazon_shopbybrand/all_brand_page/alphabet_table')?:'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
        }
        return explode(',', $alphabetString);
    }
}