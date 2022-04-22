<?php

namespace Omnisend\Omnisend\Model\Validator;

class QuoteRecoveryDataValidator implements ValidatorInterface
{
    const HASH_REGULAR_EXPRESSION = '/^[a-zA-Z0-9]{32}$/';

    /**
     * @param array $data
     * @return boolean
     */
    public function validate($data)
    {
        if (!is_array($data) || !isset($data['cart'])) {
            return false;
        }

        return $this->validateHash($data['cart']);
    }

    /**
     * @param string $hash
     * @return bool
     */
    protected function validateHash($hash)
    {
        return ($hash && preg_match(self::HASH_REGULAR_EXPRESSION, $hash));
    }
}
