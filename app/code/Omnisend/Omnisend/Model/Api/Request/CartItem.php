<?php

namespace Omnisend\Omnisend\Model\Api\Request;

use Exception;
use Magento\Quote\Model\Quote\Item;
use Omnisend\Omnisend\Model\RequestBodyBuilder\CartItemFactory as CartItemBodyBuilderFactory;
use Omnisend\Omnisend\Model\RequestDataBuilderInterface;
use Omnisend\Omnisend\Model\RequestServiceInterface;

/**
 * Class CartItem
 * @package Omnisend\Omnisend\Model\Api\Request
 */
class CartItem implements CartItemRequestInterface
{
    const URL_EXTENSION = 'carts/{cartId}/products';

    /**
     * @var RequestDataBuilderInterface
     */
    protected $requestDataBuilder;

    /**
     * @var CartItemBodyBuilderFactory
     */
    protected $cartItemBodyBuilderFactory;

    /**
     * @var RequestServiceInterface
     */
    protected $requestService;

    /**
     * @param RequestDataBuilderInterface $requestDataBuilder
     * @param CartItemBodyBuilderFactory $cartItemBodyBuilderFactory
     * @param RequestServiceInterface $requestService
     */
    public function __construct(
        RequestDataBuilderInterface $requestDataBuilder,
        CartItemBodyBuilderFactory $cartItemBodyBuilderFactory,
        RequestServiceInterface $requestService
    ) {
        $this->requestDataBuilder = $requestDataBuilder;
        $this->cartItemBodyBuilderFactory = $cartItemBodyBuilderFactory;
        $this->requestService = $requestService;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function get($id, $storeId)
    {
        throw new Exception("Not implemented in Omnisend API yet.");
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getList($storeId)
    {
        throw new Exception("Not implemented in Omnisend API yet.");
    }

    /**
     * @inheritDoc
     */
    public function post($cartId, $data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                str_replace('{cartId}', $cartId, self::URL_EXTENSION),
                null,
                $storeId,
                CartItemRequestInterface::REQUEST_TYPE_POST,
                $this->cartItemBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function put($cartId, $itemId, $data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                str_replace('{cartId}', $cartId, self::URL_EXTENSION),
                $itemId,
                $storeId,
                CartItemRequestInterface::REQUEST_TYPE_PUT,
                $this->cartItemBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function patch($cartId, $itemId, $data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                str_replace('{cartId}', $cartId, self::URL_EXTENSION),
                $itemId,
                $storeId,
                CartItemRequestInterface::REQUEST_TYPE_PATCH,
                $this->cartItemBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function delete($cartId, $itemId, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                str_replace('{cartId}', $cartId, self::URL_EXTENSION),
                $itemId,
                $storeId,
                CartItemRequestInterface::REQUEST_TYPE_DELETE
            )
        );
    }
}
