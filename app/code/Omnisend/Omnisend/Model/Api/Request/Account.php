<?php

namespace Omnisend\Omnisend\Model\Api\Request;

use Exception;
use Omnisend\Omnisend\Model\RequestBodyBuilder\RequestBodyBuilderFactoryInterface;
use Omnisend\Omnisend\Model\RequestDataBuilderInterface;
use Omnisend\Omnisend\Model\RequestServiceInterface;

/**
 * Class Account
 * @package Omnisend\Omnisend\Model\Api\Request
 */
class Account implements RequestInterface
{
    const URL_EXTENSION = 'accounts';

    /**
     * @var RequestDataBuilderInterface
     */
    private $requestDataBuilder;

    /**
     * @var RequestBodyBuilderFactoryInterface
     */
    private $accountBodyBuilderFactory;

    /**
     * @var RequestServiceInterface
     */
    private $requestService;

    /**
     * @param RequestDataBuilderInterface $requestDataBuilder
     * @param RequestBodyBuilderFactoryInterface $accountBodyBuilderFactory
     * @param RequestServiceInterface $requestService
     */
    public function __construct(
        RequestDataBuilderInterface $requestDataBuilder,
        RequestBodyBuilderFactoryInterface $accountBodyBuilderFactory,
        RequestServiceInterface $requestService
    ) {
        $this->requestDataBuilder = $requestDataBuilder;
        $this->accountBodyBuilderFactory = $accountBodyBuilderFactory;
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
                null,
                $storeId,
                RequestInterface::REQUEST_TYPE_GET
            )
        );
    }

    /**
     * @param $storeId
     * @return string|void
     * @throws Exception
     */
    public function getList($storeId)
    {
        throw new Exception("Not implemented in Omnisend API yet.");
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
                $this->accountBodyBuilderFactory->create()->build($data)
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
     * @return string|void
     * @throws Exception
     */
    public function patch($id, $data, $storeId)
    {
        throw new Exception("Not implemented in Omnisend API yet.");
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
