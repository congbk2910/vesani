<?php
/**
 *
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\GoogleAmpManager\Controller\Amphandle\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    private function getCartUrl()
    {
        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }
    
    
    protected function goBack($backUrl = null, $product = null)
    {
        $result = [];
        $result['items_qty'] = $this->cart->getItemsQty();
        $result['items_count'] = $this->cart->getItemsCount();
        $result['success'] = false;
        
        
        if ($product) {
            $result['success'] = true;
            $result['message_type'] = 'success';
            $result['message'] = __(
                'You added %1 to your shopping cart.',
                $product->getName()
            );
        } else {
            $message = $this->messageManager->getMessages()->getLastAddedMessage();
            $result['message_type'] = $message->getType();
            $result['message'] = $message->getText();
        }
        
        $result['cart_url'] = $this->getCartUrl();
        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }
}