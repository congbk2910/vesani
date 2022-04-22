<?php

namespace Omnisend\Omnisend\Model;

class RequestData implements RequestDataInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @param $header
     * @return void
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @param $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }
}
