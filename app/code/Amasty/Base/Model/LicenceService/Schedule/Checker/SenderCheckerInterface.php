<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/


namespace Amasty\Base\Model\LicenceService\Schedule\Checker;

interface SenderCheckerInterface
{
    /**
     * @param string $flag
     * @return bool
     */
    public function isNeedToSend(string $flag): bool;
}
