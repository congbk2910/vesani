<?php
/**
 * Copyright © 2018 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Codazon\AjaxCartPro\Controller\Product\Compare;

use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Codazon\AjaxCartPro\Controller\Product\Compare
{
    public function execute()
    {
        $postResult = ['success' => false];
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $postResult['message'] = __('Your session has expired.');
            return $this->returnResult($postResult);
        }

        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId && ($this->_customerVisitor->getId() || $this->_customerSession->isLoggedIn())) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            $compareListUrl = $this->_url->getUrl('catalog/product_compare');
            if ($product) {
                $this->_catalogProductCompareList->addProduct($product);
                $productName = $this->_objectManager->get(
                    \Magento\Framework\Escaper::class
                )->escapeHtml($product->getName());
                $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
                $postResult = [
                    'success' => true,
                    'product' => [
                        'id'    => $product->getId(),
                        'name'  => $productName,
                    ],
                    'compare_list_url' => $compareListUrl,
                    'message' => __('You added product %1 to the <a href="%2">comparison list</a>.', $productName, $compareListUrl)
                ];
            }
            $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();
        } else {
            $postResult = ['success' => false, 'product' => $product->getId(), 'message' => __('We can\'t add this item to your comparison list right now.')];
        }
        return $this->returnResult($postResult);
    }
    
    protected function returnResult($postResult) {
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($postResult)
        );
    }
}