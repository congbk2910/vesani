<?php


namespace Svea\Checkout\Service;


class GetCurrentSveaOrderId extends GetCurrentQuote
{

    public function getSveaOrderId()
    {
        return $this->checkoutSession->getSveaOrderId();
    }
}