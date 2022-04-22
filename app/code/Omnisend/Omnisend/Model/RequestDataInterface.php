<?php

namespace Omnisend\Omnisend\Model;

interface RequestDataInterface
{
    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @return array
     */
    public function getHeader();

    /**
     * @param $url
     * @return void
     */
    public function setUrl($url);

    /**
     * @param $type
     * @return void
     */
    public function setType($type);

    /**
     * @param $body
     * @return void
     */
    public function setBody($body);

    /**
     * @param $header
     * @return void
     */
    public function setHeader($header);
}
