<?php
namespace Svea\Checkout\Model\Client\DTO;

class CancelOrderAmount extends AbstractRequest
{

    /** @var int */
    protected $CancelledAmount;

    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return int
     */
    public function getCancelledAmount()
    {
        return $this->CancelledAmount;
    }

    /**
     * @param int $amount
     * @return CancelOrderAmount
     */
    public function setCancelledAmount($amount)
    {
        $this->CancelledAmount = $amount;
        return $this;
    }

    public function toArray()
    {
        return [
            "CancelledAmount" => $this->getCancelledAmount()
        ];
    }


}