<?php

namespace Omnisend\Omnisend\Model\Api\Request;

use Omnisend\Omnisend\Model\RequestBodyBuilder\RequestBodyBuilderFactoryInterface;
use Omnisend\Omnisend\Model\RequestDataBuilderInterface;
use Omnisend\Omnisend\Model\RequestServiceInterface;

class Cart implements RequestInterface
{
    const URL_EXTENSION = 'carts';

    /**
     * @var RequestDataBuilderInterface
     */
    private $requestDataBuilder;

    /**
     * @var RequestBodyBuilderFactoryInterface
     */
    private $cartBodyBuilderFactory;

    /**
     * @var RequestServiceInterface
     */
    private $requestService;

    /**
     * @param RequestDataBuilderInterface $requestDataBuilder
     * @param RequestBodyBuilderFactoryInterface $cartBodyBuilderFactory
     * @param RequestServiceInterface $requestService
     */
    public function __construct(
        RequestDataBuilderInterface $requestDataBuilder,
        RequestBodyBuilderFactoryInterface $cartBodyBuilderFactory,
        RequestServiceInterface $requestService
    ) {
        $this->requestDataBuilder = $requestDataBuilder;
        $this->cartBodyBuilderFactory = $cartBodyBuilderFactory;
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
                $this->cartBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @param $id
     * @param $data
     * @param $storeId
     * @return null|string
     */
    public function put($id, $data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                $id,
                $storeId,
                RequestInterface::REQUEST_TYPE_PUT,
                $this->cartBodyBuilderFactory->create()->build($data)
            )
        );
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
                $this->cartBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @param $id
     * @param $storeId
     * @return null|string
     */
    public function delete($id, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                $id,
                $storeId,
                RequestInterface::REQUEST_TYPE_DELETE
            )
        );
    }
}
