<?php

namespace Omnisend\Omnisend\Model\Api\Request;

use Exception;
use Omnisend\Omnisend\Model\RequestBodyBuilder\RequestBodyBuilderFactoryInterface;
use Omnisend\Omnisend\Model\RequestDataBuilderInterface;
use Omnisend\Omnisend\Model\RequestServiceInterface;

class Contact implements RequestInterface
{
    const URL_EXTENSION = 'contacts';
    const REQUEST_ENTITY_TYPE = 'contact';

    /**
     * @var RequestDataBuilderInterface
     */
    private $requestDataBuilder;

    /**
     * @var RequestBodyBuilderFactoryInterface
     */
    private $contactBodyBuilderFactory;

    /**
     * @var RequestServiceInterface
     */
    private $requestService;

    /**
     * @param RequestDataBuilderInterface $requestDataBuilder
     * @param RequestBodyBuilderFactoryInterface $contactBodyBuilderFactory
     * @param RequestServiceInterface $requestService
     */
    public function __construct(
        RequestDataBuilderInterface $requestDataBuilder,
        RequestBodyBuilderFactoryInterface $contactBodyBuilderFactory,
        RequestServiceInterface $requestService
    ) {
        $this->requestDataBuilder = $requestDataBuilder;
        $this->contactBodyBuilderFactory = $contactBodyBuilderFactory;
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
            ),
            self::REQUEST_ENTITY_TYPE
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
            ),
            self::REQUEST_ENTITY_TYPE
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
                $this->contactBodyBuilderFactory->create()->build($data)
            ),
            self::REQUEST_ENTITY_TYPE
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
                $this->contactBodyBuilderFactory->create()->build($data)
            ),
            self::REQUEST_ENTITY_TYPE
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
