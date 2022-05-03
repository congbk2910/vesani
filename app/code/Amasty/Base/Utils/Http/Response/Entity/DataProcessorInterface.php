<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/


namespace Amasty\Base\Utils\Http\Response\Entity;

interface DataProcessorInterface
{
    public function process(array $data): array;
}
