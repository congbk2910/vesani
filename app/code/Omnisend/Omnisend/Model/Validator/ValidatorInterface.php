<?php

namespace Omnisend\Omnisend\Model\Validator;

interface ValidatorInterface
{
    /**
     * @param array $data
     * @return boolean
     */
    public function validate($data);
}
