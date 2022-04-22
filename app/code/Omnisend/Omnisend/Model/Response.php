<?php

namespace Omnisend\Omnisend\Model;

class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected $responseData;

    /**
     * @var string
     */
    protected $responseError;

    /**
     * @var bool
     */
    protected $hasError;

    /**
     * @var int
     */
    protected $responseCode;

    /**
     * @param $data
     * @param $responseCode
     * @return void
     */
    public function setResponse($data, $responseCode)
    {
        $responseData = json_decode($data, true);
        $this->setResponseCode($responseCode);

        if (isset($responseData['error'])) {
            $this->setError($data);
            $this->setHasError(true);

            return;
        }

        $this->setData($data);
        $this->setHasError(false);
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->responseData;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->responseError;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * @param $data
     * @return void
     */
    public function setData($data)
    {
        $this->responseData = $data;
    }

    /**
     * @param $error
     * @return void
     */
    public function setError($error)
    {
        $this->responseError = $error;
    }

    /**
     * @param $hasError
     * @return void
     */
    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
    }

    /**
     * @param $responseCode
     * @return void
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }
}
