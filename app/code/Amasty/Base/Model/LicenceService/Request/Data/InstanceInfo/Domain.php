<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;

use Amasty\Base\Model\SimpleDataObject;

class Domain extends SimpleDataObject
{
    public const URL = 'url';

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->getData(self::URL);
    }
}
