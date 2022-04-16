<?php
namespace Svea\Checkout\Model\Client\DTO;

class RefundPayment extends AbstractRequest
{
    /**
     * Required
     * @var $OrderRowIds int[]
     */
    protected $OrderRowIds;

    /**
     * Optional
     * One of: 0=Default,2=Post,3=Email,4=EInvoiceB2B
     * @var $InvoiceDistributionType string
     */
    protected $InvoiceDistributionType;

    /**
     * @return int[]
     */
    public function getOrderRowIds()
    {
        return $this->OrderRowIds;
    }

    /**
     * @param int[] $OrderRowIds
     * @return RefundPayment
     */
    public function setOrderRowIds($OrderRowIds)
    {
        $this->OrderRowIds = $OrderRowIds;
        return $this;
    }


    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        $data = [];
        $rows = $this->getOrderRowIds() ? $this->getOrderRowIds() : [];
        $data['OrderRowIds'] = $rows;

        return $data;
    }



}