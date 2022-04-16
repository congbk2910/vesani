<?php
namespace Svea\Checkout\Model\Client\DTO;

class RefundPaymentAmount extends AbstractRequest
{

    /**
     * @var int $creditedAmount
     */
    protected $creditedAmount;

    /**
     * @return int
     */
    public function getCreditedAmount()
    {
        return $this->creditedAmount;
    }

    /**
     * @param int $creditedAmount
     * @return RefundPaymentAmount
     */
    public function setCreditedAmount($creditedAmount)
    {
        $this->creditedAmount = $creditedAmount;
        return $this;
    }

    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        $data = [];
        $data['CreditedAmount'] = $this->getCreditedAmount();

        return $data;
    }



}