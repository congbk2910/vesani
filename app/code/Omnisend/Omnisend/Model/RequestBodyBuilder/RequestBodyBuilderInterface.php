<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

interface RequestBodyBuilderInterface
{
    /**
     * @param $object
     * @return string
     */
    public function build($object);
}
