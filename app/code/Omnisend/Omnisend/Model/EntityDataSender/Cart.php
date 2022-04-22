<?php

namespace Omnisend\Omnisend\Model\EntityDataSender;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Omnisend\Omnisend\Model\Api\Request\CartItemRequestInterface;
use Omnisend\Omnisend\Model\Api\Request\RequestInterface;

/**
 * Class Cart
 * @package Omnisend\Omnisend\Model\EntityDataSender
 */
class Cart implements EntityDataSenderInterface
{
    /**
     * @var RequestInterface
     */
    protected $cartRequest;

    /**
     * @var CartItemRequestInterface
     */
    protected $cartItemRequest;

    /**
     * Cart constructor.
     * @param RequestInterface $cartRequest
     * @param CartItemRequestInterface $cartItemRequest
     */
    public function __construct(
        RequestInterface $cartRequest,
        CartItemRequestInterface $cartItemRequest
    ) {
        $this->cartRequest = $cartRequest;
        $this->cartItemRequest = $cartItemRequest;
    }

    /**
     * @param Item $quoteItem
     * @return string|void|null
     */
    public function send($quoteItem)
    {
        $quote = $quoteItem->getQuote();
        $postStatus = $quote->getData('omnisend_post_status');

        if ($quote->getItemsCount() < 1) {
            return $this->deleteCart($quote);
        }

        if (!$postStatus) {
            return $this->createCart($quote);
        }
    }

    public function createCart(Quote $quote)
    {
        return $this->cartRequest->post(
            $quote,
            $quote->getStoreId()
        );
    }

    public function deleteCart(Quote $quote)
    {
        return $this->cartRequest->delete(
            $quote->getId(),
            $quote->getStoreId()
        );
    }

    public function addCartItem(Item $quoteItem)
    {
        return $this->cartItemRequest->post(
            $quoteItem->getQuoteId(),
            $quoteItem,
            $quoteItem->getStoreId()
        );
    }

    public function removeCartItem(Item $quoteItem)
    {
        return $this->cartItemRequest->delete(
            $quoteItem->getQuoteId(),
            $quoteItem->getId(),
            $quoteItem->getStoreId()
        );
    }

    public function updateCartItem(Item $quoteItem)
    {
        return $this->cartItemRequest->patch(
            $quoteItem->getQuoteId(),
            $quoteItem->getId(),
            $quoteItem,
            $quoteItem->getStoreId()
        );
    }
}
