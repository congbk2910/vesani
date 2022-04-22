<?php

namespace Omnisend\Omnisend\Model;

use Omnisend\Omnisend\Model\Api\Request\RequestInterface;
use Omnisend\Omnisend\Model\Config\GeneralConfig;

class RequestHeaderBuilder implements RequestHeaderBuilderInterface
{
    /**
     * @var GeneralConfig
     */
    private $omnisendGeneralConfig;

    /**
     * RequestHeaderBuilder constructor.
     * @param GeneralConfig $omnisendGeneralConfig
     */
    public function __construct(GeneralConfig $omnisendGeneralConfig)
    {
        $this->omnisendGeneralConfig = $omnisendGeneralConfig;
    }

    /**
     * @param $storeId
     * @param $type
     * @return array
     */
    public function build($storeId, $type)
    {
        $header = $this->appendToHeader(
            [],
            RequestHeaderBuilderInterface::X_API_KEY_LABEL,
            $this->omnisendGeneralConfig->getApiKey($storeId)
        );

        if ($this->doesPostContent($type)) {
            $header = $this->appendToHeader(
                $header,
                RequestHeaderBuilderInterface::CONTENT_TYPE_LABEL,
                RequestHeaderBuilderInterface::CONTENT_TYPE
            );
        }

        return $header;
    }

    /**
     * @param $header
     * @param $label
     * @param $value
     * @return array
     */
    public function appendToHeader($header, $label, $value)
    {
        $header[] = $label . $value;

        return $header;
    }

    /**
     * @param $type
     * @return bool
     */
    public function doesPostContent($type)
    {
        if ($type != RequestInterface::REQUEST_TYPE_GET && $type != RequestInterface::REQUEST_TYPE_DELETE) {
            return true;
        }

        return false;
    }
}
