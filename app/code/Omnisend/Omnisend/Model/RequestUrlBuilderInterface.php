<?php

namespace Omnisend\Omnisend\Model;

interface RequestUrlBuilderInterface
{
    /**
     * @param $extension
     * @param $parameter
     * @return string
     */
    public function build($extension, $parameter);
}
