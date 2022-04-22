<?php

namespace Omnisend\Omnisend\Model;

class RequestDataBuilder implements RequestDataBuilderInterface
{
    /**
     * @var RequestDataFactory
     */
    private $requestDataFactory;

    /**
     * @var RequestUrlBuilderInterface
     */
    private $requestUrlBuilder;

    /**
     * @var RequestHeaderBuilderInterface
     */
    private $requestHeaderBuilder;

    /**
     * RequestDataBuilder constructor.
     * @param RequestDataFactory $requestDataFactory
     * @param RequestUrlBuilderInterface $requestUrlBuilder
     * @param RequestHeaderBuilderInterface $requestHeaderBuilder
     */
    public function __construct(
        RequestDataFactory $requestDataFactory,
        RequestUrlBuilderInterface $requestUrlBuilder,
        RequestHeaderBuilderInterface $requestHeaderBuilder
    ) {
        $this->requestDataFactory = $requestDataFactory;
        $this->requestUrlBuilder = $requestUrlBuilder;
        $this->requestHeaderBuilder = $requestHeaderBuilder;
    }

    /**
     * @param $urlExtension
     * @param $urlParameter
     * @param $storeId
     * @param $type
     * @param $body
     * @return RequestDataInterface
     */
    public function build($urlExtension, $urlParameter, $storeId, $type, $body = null)
    {
        $requestData = $this->requestDataFactory->create();

        $requestData->setUrl($this->requestUrlBuilder->build($urlExtension, $urlParameter));
        $requestData->setType($type);
        $requestData->setBody($body);
        $requestData->setHeader($this->requestHeaderBuilder->build($storeId, $type));
        $requestData->setStoreId($storeId);

        return $requestData;
    }
}
