<?php


namespace Svea\Checkout\Model\Client\Api;

use Svea\Checkout\Model\Client\ApiClient;
use Svea\Checkout\Model\Client\ClientException;
use Svea\Checkout\Model\Client\DTO\CreateOrder;
use Svea\Checkout\Model\Client\DTO\UpdateOrderCart;
use Svea\Checkout\Model\Client\DTO\GetOrderResponse;

class Checkout extends ApiClient
{

    /**
     * @param CreateOrder $createOrder
     * @return GetOrderResponse
     * @throws ClientException
     */
    public function createNewOrder(CreateOrder $createOrder)
    {
        try {
            $response = $this->post("/api/orders", $createOrder);
        } catch (ClientException $e) {
            // handle?
            throw $e;
        }

        return new GetOrderResponse($response);
    }


    /**
     * @param UpdateOrderCart $cart
     * @param $orderId
     * @return GetOrderResponse
     * @throws ClientException
     */
    public function updateOrder(UpdateOrderCart $cart, $orderId)
    {
        try {
            $response = $this->put("/api/orders/". $orderId, $cart);
        } catch (ClientException $e) {
            // handle?
            throw $e;
        }

        return new GetOrderResponse($response);
    }
    
    /**
     * @param string $orderId
     * @return GetOrderResponse
     * @throws ClientException
     */
    public function getOrder($orderId)
    {
        try {
            $response = $this->get("/api/orders/" . $orderId);
        } catch (ClientException $e) {
            // handle?
            throw $e;
        }

        return new GetOrderResponse($response);
    }

}