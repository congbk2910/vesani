<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model\LicenceService\Request\Url;

use Amasty\Base\Model\Config;

class Builder
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function build($path, $params = []): string
    {
        $apiUrl = $this->config->getLicenceServiceApiUrl();
        $requestParams = [$apiUrl, $path];
        if (!empty($params)) {
            $requestParams[] = '?';
            $requestParams[] = http_build_query($params);
        }

        return implode('', $requestParams);
    }
}
