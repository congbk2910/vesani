<?php

namespace Omnisend\Omnisend\Model;

use Omnisend\Omnisend\Model\Config\GeneralConfig;

class RequestUrlBuilder implements RequestUrlBuilderInterface
{
    /**
     * @var GeneralConfig
     */
    private $omnisendConfigHelper;

    /**
     * RequestUrlBuilder constructor.
     * @param GeneralConfig $omnisendConfigHelper
     */
    public function __construct(GeneralConfig $omnisendConfigHelper)
    {
        $this->omnisendConfigHelper = $omnisendConfigHelper;
    }

    /**
     * @param $extension
     * @param $parameter
     * @return string
     */
    public function build($extension, $parameter)
    {
        $url = $this->omnisendConfigHelper->getBaseUrl();
        $url = $this->appendToUrl($url, $extension);

        if ($parameter != null) {
            $url = $this->appendToUrl($url, $parameter);
        }

        return $url;
    }

    /**
     * @param $url
     * @param $parameter
     * @return string
     */
    protected function appendToUrl($url, $parameter)
    {
        return $url . '/' . $parameter;
    }
}
