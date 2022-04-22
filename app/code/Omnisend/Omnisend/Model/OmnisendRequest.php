<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\Model\AbstractModel;
use Omnisend\Omnisend\Api\Data\OmnisendRequestInterface;

/**
 * Class OmnisendRequestRecord
 * @package Omnisend\Omnisend\Model
 */
class OmnisendRequest extends AbstractModel implements OmnisendRequestInterface
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'record_id';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'omnisend_request';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'request';

    public function _construct()
    {
        $this->_init(\Omnisend\Omnisend\Model\ResourceModel\OmnisendRequest::class);
    }

    /**
     * @inheritDoc
     */
    public function getRequestUrl()
    {
        return $this->getData(self::REQUEST_URL);
    }

    /**
     * @inheritDoc
     */
    public function setRequestUrl($url)
    {
        return $this->setData(self::REQUEST_URL, $url);
    }

    /**
     * @inheritDoc
     */
    public function getRequestMethod()
    {
        return $this->getData(self::REQUEST_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setRequestMethod($method)
    {
        return $this->setData(self::REQUEST_METHOD, $method);
    }

    /**
     * @inheritDoc
     */
    public function getRequestBody()
    {
        return $this->getData(self::REQUEST_BODY);
    }

    /**
     * @inheritDoc
     */
    public function setRequestBody($body)
    {
        return $this->setData(self::REQUEST_BODY, $body);
    }

    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($id)
    {
        return $this->setData(self::STORE_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getResponseCode()
    {
        return $this->getData(self::RESPONSE_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setResponseCode($code)
    {
        return $this->setData(self::RESPONSE_CODE, $code);
    }

    /**
     * @inheritDoc
     */
    public function getResponseBody()
    {
        return $this->getData(self::RESPONSE_BODY);
    }

    /**
     * @inheritDoc
     */
    public function setResponseBody($body)
    {
        return $this->setData(self::RESPONSE_BODY, $body);
    }
}
