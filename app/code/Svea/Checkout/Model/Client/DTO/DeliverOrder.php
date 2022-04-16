<?php
namespace Svea\Checkout\Model\Client\DTO;

class DeliverOrder extends AbstractRequest
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
     * @return DeliverOrder
     */
    public function setOrderRowIds($OrderRowIds)
    {
        $this->OrderRowIds = $OrderRowIds;
        return $this;
    }


    /**
     * @return string
     */
    public function getInvoiceDistributionType()
    {
        return $this->InvoiceDistributionType;
    }

    /**
     * @param string $InvoiceDistributionType
     * @return DeliverOrder
     */
    public function setInvoiceDistributionType($InvoiceDistributionType)
    {
        $this->InvoiceDistributionType = $InvoiceDistributionType;
        return $this;
    }

    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        $data = [];
        if ($this->getInvoiceDistributionType()) {
            $data['InvoiceDistributionType'] = $this->getInvoiceDistributionType();
        }

        $rows = $this->getOrderRowIds() ? $this->getOrderRowIds() : [];
        $data['OrderRowIds'] = $rows;

        return $data;
    }


}