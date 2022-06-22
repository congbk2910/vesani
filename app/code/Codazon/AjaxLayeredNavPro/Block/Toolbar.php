<?php 
namespace Codazon\AjaxLayeredNavPro\Block;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    public function getCollection()
    {
        $productList = $this->getLayout()->getBlock('category.products.list');
        // use sortable parameters
        $orders = $productList->getAvailableOrders();
        if ($orders) {
            $this->setAvailableOrders($orders);
        }
        $sort = $productList->getSortBy();
        if ($sort) {
            $this->setDefaultOrder($sort);
        }
        $dir = $productList->getDefaultDirection();
        if ($dir) {
            $this->setDefaultDirection($dir);
        }
        $modes = $productList->getModes();
        if ($modes) {
            $this->setModes($modes);
        }
        $coo = $productList->getLoadedProductCollection();
        // set collection to productList and apply sort
        $this->setCollection($productList->getLoadedProductCollection());
        return parent::getCollection();
    }
}