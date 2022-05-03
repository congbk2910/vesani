<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Test\Unit\Model\LicenceService\Api;

use Amasty\Base\Model\LicenceService\Api\RequestManager;
use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo;
use Amasty\Base\Model\LicenceService\Request\Url\Builder;
use Amasty\Base\Model\LicenceService\Response\Data\RegisteredInstance;
use Amasty\Base\Utils\Http\Curl;
use Amasty\Base\Utils\Http\CurlFactory;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

class RequestManagerTest extends TestCase
{
    /**
     * @var RequestManager
     */
    private $model;

    /**
     * @var SimpleDataObjectConverter
     */
    private $simpleDataObjectConverterMock;

    /**
     * @var CurlFactory
     */
    private $curlFactoryMock;

    /**
     * @var Builder
     */
    private $urlBuilderMock;

    protected function setUp(): void
    {
        $this->simpleDataObjectConverterMock = $this->createPartialMock(
            SimpleDataObjectConverter::class,
            ['convertKeysToCamelCase']
        );
        $this->curlFactoryMock = $this->createPartialMock(CurlFactory::class, ['create']);
        $this->urlBuilderMock = $this->createPartialMock(Builder::class, ['build']);

        $this->model = new RequestManager(
            $this->simpleDataObjectConverterMock,
            $this->curlFactoryMock,
            $this->urlBuilderMock
        );
    }

    public function testRegisterInstance(): void
    {
        list($curlMock, $domain, $url, $postParams, $registeredInstanceMock) = $this->registerInstanceInit();

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $postParams)
            ->willReturn($registeredInstanceMock);

        $this->assertEquals($registeredInstanceMock, $this->model->registerInstance($domain));
    }

    public function testRegisterInstanceOnException(): void
    {
        list($curlMock, $domain, $url, $postParams) = $this->registerInstanceInit();

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $postParams)
            ->willThrowException(new LocalizedException(__('Invalid request.')));

        $this->expectException(LocalizedException::class);
        $this->model->registerInstance($domain);
    }

    private function registerInstanceInit(): array
    {
        $domain = 'https://amasty.com';
        $path = '/api/v1/instance_client/registration';
        $url = 'https://amasty-licence.com' . $path;
        $postParams = json_encode(['domain' => $domain]);
        $curlMock = $this->createPartialMock(Curl::class, ['request']);
        $registeredInstanceMock = $this->createMock(RegisteredInstance::class);

        $this->curlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($path)
            ->willReturn($url);

        return [$curlMock, $domain, $url, $postParams, $registeredInstanceMock];
    }

    public function testUpdateInstanceInfo(): void
    {
        list($curlMock, $instanceInfoMock, $url, $instanceInfoString) = $this->updateInstanceInfoInit();

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString);

        $this->model->updateInstanceInfo($instanceInfoMock);
    }

    public function testUpdateInstanceInfoOnException(): void
    {
        list($curlMock, $instanceInfoMock, $url, $instanceInfoString) = $this->updateInstanceInfoInit();

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url, $instanceInfoString)
            ->willThrowException(new LocalizedException(__('Invalid request.')));

        $this->expectException(LocalizedException::class);
        $this->model->updateInstanceInfo($instanceInfoMock);
    }

    private function updateInstanceInfoInit(): array
    {
        $instanceInfoString = json_encode(['systemInstanceKey' => 'key', 'modules' => [], 'domains' => []]);
        $instanceInfo = [
            'systemInstanceKey' => 'key',
            'modules' => [],
            'domains' => []
        ];
        $path = '/api/v1/instance_client/' . $instanceInfo['systemInstanceKey'] . '/collect';
        $url = 'https://amasty-licence.com' . $path;
        $curlMock = $this->createPartialMock(Curl::class, ['request']);
        $instanceInfoMock = $this->createPartialMock(
            InstanceInfo::class,
            ['getSystemInstanceKey', 'toArray']
        );

        $this->curlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($path)
            ->willReturn($url);

        $instanceInfoMock
            ->expects($this->once())
            ->method('getSystemInstanceKey')
            ->willReturn($instanceInfo['systemInstanceKey']);
        $instanceInfoMock
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($instanceInfo);

        $this->simpleDataObjectConverterMock
            ->expects($this->once())
            ->method('convertKeysToCamelCase')
            ->willReturn($instanceInfo);

        return [$curlMock, $instanceInfoMock, $url, $instanceInfoString];
    }

    public function testPing(): void
    {
        list($curlMock, $systemInstanceKey, $url) = $this->pingInit();

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url);

        $this->model->ping($systemInstanceKey);
    }

    public function testPingOnException(): void
    {
        list($curlMock, $systemInstanceKey, $url) = $this->pingInit();

        $curlMock
            ->expects($this->once())
            ->method('request')
            ->with($url)
            ->willThrowException(new LocalizedException(__('Invalid request.')));

        $this->expectException(LocalizedException::class);
        $this->model->ping($systemInstanceKey);
    }

    private function pingInit(): array
    {
        $systemInstanceKey = 'key';
        $path = '/api/v1/instance_client/' . $systemInstanceKey . '/ping';
        $url = 'https://amasty-licence.com' . $path;
        $curlMock = $this->createPartialMock(Curl::class, ['request']);

        $this->curlFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($curlMock);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('build')
            ->with($path)
            ->willReturn($url);

        return [$curlMock, $systemInstanceKey, $url];
    }
}
