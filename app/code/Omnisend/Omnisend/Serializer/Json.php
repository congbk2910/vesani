<?php

namespace Omnisend\Omnisend\Serializer;

use InvalidArgumentException;

/**
 * Class Json
 * @package Omnisend\Omnisend\Serializer
 */
class Json
{
    /**
     * Serialize data into string
     *
     * @param string|int|float|bool|array|null $data
     * @return string|bool
     * @throws InvalidArgumentException
     */
    public function serialize($data)
    {
        $result = json_encode($data);
        if (false === $result) {
            throw new InvalidArgumentException("Unable to serialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }

    /**
     * Unserialize the given string
     *
     * @param string $string
     * @return string|int|float|bool|array|null
     * @throws InvalidArgumentException
     */
    public function unserialize($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Unable to unserialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }
}
