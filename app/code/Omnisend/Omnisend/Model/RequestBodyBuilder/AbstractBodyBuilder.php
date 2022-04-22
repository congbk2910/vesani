<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

abstract class AbstractBodyBuilder implements AbstractBodyBuilderInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function addData($index, $value)
    {
        if (!is_null($value)) {
            $this->data[$index] = $value;
        }
    }
}
