<?php

namespace Omnisend\Omnisend\Model;

interface ResponseInterface
{
    /**
     * @param $data
     * @param $responseCode
     * @return void
     */
    public function setResponse($data, $responseCode);

    /**
     * @return string
     */
    public function getData();

    /**
     * @return string
     */
    public function getError();

    /**
     * @return int
     */
    public function getResponseCode();

    /**
     * @return bool
     */
    public function hasError();

    /**
     * @param $data
     * @return void
     */
    public function setData($data);

    /**
     * @param $error
     * @return void
     */
    public function setError($error);

    /**
     * @param $responseCode
     * @return void
     */
    public function setResponseCode($responseCode);

    /**
     * @param $hasError
     * @return void
     */
    public function setHasError($hasError);
}
