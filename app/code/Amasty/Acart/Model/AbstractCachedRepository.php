<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model;

use Magento\Framework\Model\AbstractModel;

abstract class AbstractCachedRepository
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @var array
     */
    private $mapFields = [];

    private function getKey($field, $value): string
    {
        return sha1(sprintf('%s-%s', $field, $value));
    }

    private function addIdToMap(int $id, $field, $value): string
    {
        return $this->mapFields[$id][] = $this->getKey($field, $value);
    }

    protected function invalidateCache(AbstractModel $model)
    {
        $keys = $this->mapFields[$model->getId()] ?? [];

        foreach ($keys as $key) {
            unset($this->cache[$key]);
        }
    }

    protected function getFromCache($field, $value)
    {
        $key = $this->getKey($field, $value);

        return $this->cache[$key] ?? null;
    }

    protected function addToCache($field, $value, AbstractModel $model): AbstractModel
    {
        $key = $this->addIdToMap((int)$model->getId(), $field, $value);

        return $this->cache[$key] = $model;
    }
}
