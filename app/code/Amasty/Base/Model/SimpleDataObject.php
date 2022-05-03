<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model;

use Magento\Framework\DataObject;

/**
 * phpcs:ignoreFile
 */
class SimpleDataObject extends DataObject
{
    /**
     * @param array $keys
     * @return array
     */
    public function toArray(array $keys = []): array
    {
        if (empty($keys)) {
            return $this->__toArray();
        }

        return parent::toArray($keys);
    }

    public function __toArray(): array
    {
        $data = $this->_data;
        $hasToArray = function ($model) {
            return is_object($model) && method_exists($model, '__toArray') && is_callable([$model, '__toArray']);
        };
        foreach ($data as $key => $value) {
            if ($hasToArray($value)) {
                $data[$key] = $value->__toArray();
            } elseif (is_array($value)) {
                foreach ($value as $nestedKey => $nestedValue) {
                    if ($hasToArray($nestedValue)) {
                        $value[$nestedKey] = $nestedValue->__toArray();
                    }
                }
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
