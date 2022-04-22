<?php

namespace Omnisend\Omnisend\Model;

interface RequestDataBuilderInterface
{
    /**
     * @param $urlExtension
     * @param $urlParameter
     * @param $storeId
     * @param $type
     * @param $body
     * @return RequestDataInterface
     */
    public function build($urlExtension, $urlParameter, $storeId, $type, $body = null);
}
