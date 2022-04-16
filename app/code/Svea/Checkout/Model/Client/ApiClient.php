<?php
namespace Svea\Checkout\Model\Client;

/**
 * Class Client
 * @package Svea\Checkout\Model\Api
 */
abstract class ApiClient extends BaseClient
{

    /**
     * We test the http client with the correct Api URL
     * @param \Svea\Checkout\Helper\Data $helper
     * @param $store
     */
    protected function setGuzzleHttpClient(\Svea\Checkout\Helper\Data $helper, $store = null)
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $helper->getApiUrl($store),
        //    'verify' => false,
        ]);
    }

}









