<?php
namespace Svea\Checkout\Model\Client;

/**
 * Class Client
 * @package Svea\Checkout\Model\Api
 */
abstract class OrderManagementClient extends BaseClient
{

    public function __construct(Context $apiContext)
    {
        parent::__construct($apiContext);
    }

    /**
     * We test the http client with the correct Api URL
     * @param \Svea\Checkout\Helper\Data $helper
     * @param $store
     */
    protected function setGuzzleHttpClient(\Svea\Checkout\Helper\Data $helper, $store = null)
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $helper->getAdminApiUrl($store),
        //    'verify' => false,
        ]);
    }

}









