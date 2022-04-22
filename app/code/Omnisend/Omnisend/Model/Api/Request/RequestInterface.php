<?php

namespace Omnisend\Omnisend\Model\Api\Request;

interface RequestInterface
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
     * @param $data
     * @param $storeId
     * @return string
     */
    public function post($data, $storeId);

    /**
     * @param $id
     * @param $data
     * @param $storeId
     * @return string
     */
    public function put($id, $data, $storeId);

    /**
     * @param $id
     * @param $data
     * @param $storeId
     * @return string
     */
    public function patch($id, $data, $storeId);

    /**
     * @param $id
     * @param $storeId
     * @return string
     */
    public function delete($id, $storeId);
}
