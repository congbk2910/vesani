<?php

namespace Omnisend\Omnisend\Model;

interface RequestHeaderBuilderInterface
{
    const X_API_KEY_LABEL = 'x-api-key: ';
    const CONTENT_TYPE_LABEL = 'content-type: ';
    const CONTENT_TYPE = 'application/json';

    /**
     * @param $storeId
     * @param $type
     * @return array
     */
    public function build($storeId, $type);
}
