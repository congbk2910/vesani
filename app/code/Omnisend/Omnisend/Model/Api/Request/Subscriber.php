<?php

namespace Omnisend\Omnisend\Model\Api\Request;

use Exception;
use Omnisend\Omnisend\Model\RequestBodyBuilder\RequestBodyBuilderFactoryInterface;
use Omnisend\Omnisend\Model\RequestDataBuilderInterface;
use Omnisend\Omnisend\Model\RequestServiceInterface;

class Subscriber implements RequestInterface
{
    const URL_EXTENSION = 'contacts';

    /**
     * @var RequestDataBuilderInterface
     */
    private $requestDataBuilder;

    /**
     * @var RequestBodyBuilderFactoryInterface
     */
    private $subscriberBodyBuilderFactory;

    /**
     * @var RequestServiceInterface
     */
    private $requestService;

    /**
     * @param RequestDataBuilderInterface $requestDataBuilder
     * @param RequestBodyBuilderFactoryInterface $subscriberBodyBuilderFactory
     * @param RequestServiceInterface $requestService
     */
    public function __construct(
        RequestDataBuilderInterface $requestDataBuilder,
        RequestBodyBuilderFactoryInterface $subscriberBodyBuilderFactory,
        RequestServiceInterface $requestService
    ) {
        $this->requestDataBuilder = $requestDataBuilder;
        $this->subscriberBodyBuilderFactory = $subscriberBodyBuilderFactory;
        $this->requestService = $requestService;
    }

    /**
     * @param $id
     * @param $storeId
     * @return null|string
     */
    public function get($id, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                $id,
                $storeId,
                RequestInterface::REQUEST_TYPE_GET
            )
        );
    }

    /**
     * @param $storeId
     * @return null|string
     */
    public function getList($storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                null,
                $storeId,
                RequestInterface::REQUEST_TYPE_GET
            )
        );
    }

    /**
     * @param $data
     * @param $storeId
     * @return null|string
     */
    public function post($data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                null,
                $storeId,
                RequestInterface::REQUEST_TYPE_POST,
                $this->subscriberBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @param $id
     * @param $data
     * @param $storeId
     * @return string|void
     * @throws Exception
     */
    public function put($id, $data, $storeId)
    {
        throw new Exception("Not implemented in Omnisend API yet.");
    }

    /**
     * @param $id
     * @param $data
     * @param $storeId
     * @return null|string
     */
    public function patch($id, $data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                $id,
                $storeId,
                RequestInterface::REQUEST_TYPE_PATCH,
                $this->subscriberBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @param $id
     * @param $storeId
     * @return string|void
     * @throws Exception
     */
    public function delete($id, $storeId)
    {
        throw new Exception("Not implemented in Omnisend API yet.");
    }
}
