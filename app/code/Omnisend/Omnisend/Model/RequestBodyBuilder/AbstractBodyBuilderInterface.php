<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

interface AbstractBodyBuilderInterface
{
    /**
     * @return array
     */
    public function getData();

    /**
     * @param array $data
     * @return void
     */
    public function setData($data);

    /**
     * @param $index
     * @param $value
     * @return void
     */
    public function addData($index, $value);
}
