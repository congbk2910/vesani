<?php

namespace Omnisend\Omnisend\Model\Api\Request;

interface CartItemRequestInterface
{
    const REQUEST_TYPE_GET = 'GET';
    const REQUEST_TYPE_POST = 'POST';
    const REQUEST_TYPE_PUT = 'PUT';
    const REQUEST_TYPE_PATCH = 'PATCH';
    const REQUEST_TYPE_DELETE = 'DELETE';

    /**
     * @param $id
     * @param $storeId
     * @return string
     */
    public function get($id, $storeId);

    /**
     * @param $storeId
     * @return string
     */
    public function getList($storeId);

    /**
     * @param $cartId
     * @param $data
     * @param $storeId
     * @return string
     */
    public function post($cartId, $data, $storeId);

    /**
     * @param $cartId
     * @param $itemId
     * @param $data
     * @param $storeId
     * @return string
     */
    public function put($cartId, $itemId, $data, $storeId);

    /**
     * @param $cartId
     * @param $itemId
     * @param $data
     * @param $storeId
     * @return string
     */
    public function patch($cartId, $itemId, $data, $storeId);

    /**
     * @param $cartId
     * @param $itemId
     * @param $storeId
     * @return string
     */
    public function delete($cartId, $itemId, $storeId);
}
