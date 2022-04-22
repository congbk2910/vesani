<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

interface RequestBodyBuilderFactoryInterface
{
    /**
     * @return RequestBodyBuilderInterface
     */
    public function create();
}
