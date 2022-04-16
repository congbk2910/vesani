<?php
namespace Svea\Checkout\Model\Client\DTO;

class CancelOrder extends AbstractRequest
{

    /** @var $IsCancelled bool */
    protected $IsCancelled;

    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return bool
     */
    public function getIsCancelled()
    {
        return $this->IsCancelled;
    }

    /**
     * @param bool $IsCancelled
     * @return CancelOrder
     */
    public function setIsCancelled($IsCancelled)
    {
        $this->IsCancelled = $IsCancelled;
        return $this;
    }

    public function toArray()
    {

        return [
            "IsCancelled" => $this->getIsCancelled()
        ];
    }


}