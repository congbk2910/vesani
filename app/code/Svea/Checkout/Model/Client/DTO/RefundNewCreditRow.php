<?php
namespace Svea\Checkout\Model\Client\DTO;

class RefundNewCreditRow extends AbstractRequest
{

    /**
     * @var $name string
     */
    protected $name;

    /**
     * @var $unitPrice int
     */
    protected $unitPrice;

    /**
     * @var $vatPercent int
     */
    protected $vatPercent;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RefundNewCreditRow
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * @param int $unitPrice
     * @return RefundNewCreditRow
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @return int
     */
    public function getVatPercent()
    {
        return $this->vatPercent;
    }

    /**
     * @param int $vatPercent
     * @return RefundNewCreditRow
     */
    public function setVatPercent($vatPercent)
    {
        $this->vatPercent = $vatPercent;
        return $this;
    }

    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        $data = [
            "NewCreditOrderRow" => [
                "Name" => $this->getName(),
                "UnitPrice" => $this->getUnitPrice(),
                "VatPercent" => $this->getVatPercent(),
            ]
        ];


        return $data;
    }



}