<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\GoogleAmpManager\Controller\Amphandle\Review\Product;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Controller\Product as ProductController;


class ListAjax extends ProductController
{
    protected $_product;

    protected function getProduct()
    {
        if ($this->_product === null) {
            $this->_product = $this->initProduct();
        }
        return $this->_product;
    }

    public function execute()
    {
        $product = $this->getProduct();
        $result = [];
        $result['items'] = [];      
        if ($product) {
            $helper = $this->_objectManager->get(\Codazon\GoogleAmpManager\Helper\Data::class);
            $collection = $this->_objectManager->get(\Magento\Review\Model\ResourceModel\Review\CollectionFactory::class)
                ->create()->addStoreFilter(
                    $product->getStoreId()
                )->setPageSize(4)->setCurPage((int)$this->getRequest()->getParam('p', 1))->addStatusFilter(
                    \Magento\Review\Model\Review::STATUS_APPROVED
                )->addEntityFilter(
                    'product',
                    $product->getId()
                )->setDateOrder();
            $collection->load()->addRateVotes();
            if ($collection->count()) {
                foreach ($collection->getItems() as $item) {
                    $data = $item->getData();
                    $data['rating_votes'] = $item->getRatingVotes()->getData();
                    $data['created_at'] = $helper->formatDate($data['created_at']);
                    $result['items'][] = $data;
                }
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}
