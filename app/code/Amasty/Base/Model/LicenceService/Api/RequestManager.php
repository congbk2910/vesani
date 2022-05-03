<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model\LicenceService\Api;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;
use Amasty\Base\Model\LicenceService\Request\Url\Builder;
use Amasty\Base\Model\LicenceService\Response\Data\RegisteredInstance;
use Amasty\Base\Utils\Http\Curl;
use Amasty\Base\Utils\Http\CurlFactory;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Exception\LocalizedException;

class RequestManager
{
    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverter;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Builder
     */
    private $urlBuilder;

    public function __construct(
        SimpleDataObjectConverter $simpleDataObjectConverter,
        CurlFactory $curlFactory,
        Builder $urlBuilder
    ) {
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->curlFactory = $curlFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param string $domain
     * @return RegisteredInstance
     * @throws LocalizedException
     */
    public function registerInstance(string $domain): RegisteredInstance
    {
        /** @var Curl $curl */
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build('/api/v1/instance_client/registration');
        $postParams = json_encode(['domain' => $domain]);

        return $curl->request($url, $postParams);
    }

    /**
     * @param InstanceInfo $instanceInfo
     * @return void
     * @throws LocalizedException
     */
    public function updateInstanceInfo(InstanceInfo $instanceInfo): void
    {
        /** @var Curl $curl */
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build(
            '/api/v1/instance_client/'. $instanceInfo->getSystemInstanceKey() . '/collect'
        );
        $postParams = $this->simpleDataObjectConverter->convertKeysToCamelCase($instanceInfo->toArray());
        $postParams = json_encode($postParams);

        $curl->request($url, $postParams);
    }

    /**
     * @param string $systemInstanceKey
     * @return void
     * @throws LocalizedException
     */
    public function ping(string $systemInstanceKey): void
    {
        /** @var Curl $curl */
        $curl = $this->curlFactory->create();
        $url = $this->urlBuilder->build('/api/v1/instance_client/'. $systemInstanceKey . '/ping');

        $curl->request($url);
    }
}
