<?php

namespace Omnisend\Omnisend\Model\Api\Request;

use Exception;
use Omnisend\Omnisend\Model\RequestBodyBuilder\RequestBodyBuilderFactoryInterface;
use Omnisend\Omnisend\Model\RequestDataBuilderInterface;
use Omnisend\Omnisend\Model\RequestServiceInterface;

/**
 * Class Category
 * @package Omnisend\Omnisend\Model\Api\Request
 */
class Category implements RequestInterface
{
    const URL_EXTENSION = 'categories';

    /**
     * @var RequestDataBuilderInterface
     */
    private $requestDataBuilder;

    /**
     * @var RequestBodyBuilderFactoryInterface
     */
    private $categoryBodyBuilderFactory;

    /**
     * @var RequestServiceInterface
     */
    private $requestService;

    /**
     * @param RequestDataBuilderInterface $requestDataBuilder
     * @param RequestBodyBuilderFactoryInterface $categoryBodyBuilderFactory
     * @param RequestServiceInterface $requestService
     */
    public function __construct(
        RequestDataBuilderInterface $requestDataBuilder,
        RequestBodyBuilderFactoryInterface $categoryBodyBuilderFactory,
        RequestServiceInterface $requestService
    ) {
        $this->requestDataBuilder = $requestDataBuilder;
        $this->categoryBodyBuilderFactory = $categoryBodyBuilderFactory;
        $this->requestService = $requestService;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function post($data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                null,
                $storeId,
                RequestInterface::REQUEST_TYPE_POST,
                $this->categoryBodyBuilderFactory->create()->build($data)
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function put($id, $data, $storeId)
    {
        return $this->requestService->call(
            $this->requestDataBuilder->build(
                self::URL_EXTENSION,
                $id,
                $storeId,
                RequestInterface::REQUEST_TYPE_PUT,
                $this->categoryBodyBuilderFactory->create()->build($data)
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
    public function patch($id, $data, $storeId)
    {
        throw new Exception("Not implemented in Omnisend API yet.");
    }

    /**
     * @inheritDoc
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
