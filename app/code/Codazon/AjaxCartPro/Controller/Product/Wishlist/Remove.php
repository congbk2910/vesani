<?php
/**
 * Copyright © 2018 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\AjaxCartPro\Controller\Product\Wishlist;

use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Product\AttributeValueProvider;

class Remove extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        Action\Context $context,
        WishlistProviderInterface $wishlistProvider,
        Validator $formKeyValidator,
        AttributeValueProvider $attributeValueProvider = null,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->formKeyValidator = $formKeyValidator;
        $this->attributeValueProvider = $attributeValueProvider
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(AttributeValueProvider::class);
        parent::__construct($context);
    }
    
    public function execute()
    {
        $postResult = [
            'success'   =>  false,
        ];
        
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $postResult['message'] = __('Your session has expired.');
            return $this->returnResult($postResult);
        }

        $id = (int)$this->getRequest()->getParam('item');
        /** @var Item $item */
        $item = $this->_objectManager->create(Item::class)->load($id);
        if (!$item->getId()) {
            $postResult['message'] = __('Item not found.');
            return $this->returnResult($postResult);
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            $postResult['message'] = __('Item not found.');
            return $this->returnResult($postResult);
        }
        try {
            $item->delete();
            $wishlist->save();
            $productName = $this->attributeValueProvider
                ->getRawAttributeValue($item->getProductId(), 'name');
            $postResult['message'] = __('%1 has been removed from your Wish List.', $productName);
            $postResult['success'] = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $postResult['message'] = __('We can\'t delete the item from Wish List right now because of an error: %1.', $e->getMessage());
        } catch (\Exception $e) {
            $postResult['message'] = __('We can\'t delete the item from the Wish List right now.');
        }

        $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();

        return $this->returnResult($postResult);
    }
    
    protected function returnResult($postResult) {
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($postResult)
        );
    }
}