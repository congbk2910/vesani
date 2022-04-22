<?php

namespace Omnisend\Omnisend\Model;

interface RequestServiceInterface
{
    /**
     * @param RequestDataInterface $requestData
     * @param string $entity
     * @return string
     */
    public function call(RequestDataInterface $requestData, $entity = "all");
}
