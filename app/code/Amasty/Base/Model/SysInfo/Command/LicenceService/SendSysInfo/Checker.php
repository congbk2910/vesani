<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;

class Checker
{
    public function isChangedCacheValue(?string $cacheValue, string $newValue): bool
    {
        return !($cacheValue && hash_equals($cacheValue, $newValue));
    }
}
