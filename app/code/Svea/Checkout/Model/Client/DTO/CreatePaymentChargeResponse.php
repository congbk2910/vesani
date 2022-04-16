<?php
namespace Svea\Checkout\Model\Client\DTO;


class CreatePaymentChargeResponse
{


    /** @var string $queueId */
    protected $queueId;

    /**
     * CreatePaymentChargeResponse constructor.
     * @param $location string
     */
    public function __construct($location = "")
    {
        if ($location !== "") {
            preg_match("/queue\/\d.*/", $location, $matches);
            if (isset($matches[0])) {
                $queueId = str_replace("queue/","", $matches[0]);
                $this->setQueueId($queueId);
            }
        }
    }

    /**
     * @return string
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * @param string $queueId
     * @return CreatePaymentChargeResponse
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;
        return $this;
    }


}