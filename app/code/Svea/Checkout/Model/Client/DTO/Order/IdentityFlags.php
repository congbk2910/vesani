<?php
namespace Svea\Checkout\Model\Client\DTO\Order;

use Svea\Checkout\Model\Client\DTO\AbstractRequest;

class IdentityFlags extends AbstractRequest
{

    /** @var $HideNotYou bool */
    protected $HideNotYou;

    /** @var $HideChangeAddress bool */
    protected $HideChangeAddress;

    /** @var $HideAnonymous bool */
    protected $HideAnonymous;

    /**
     * @return bool
     */
    public function getHideNotYou()
    {
        return $this->HideNotYou;
    }

    /**
     * @param bool $HideNotYou
     * @return IdentityFlags
     */
    public function setHideNotYou($HideNotYou)
    {
        $this->HideNotYou = $HideNotYou;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHideChangeAddress()
    {
        return $this->HideChangeAddress;
    }

    /**
     * @param bool $HideChangeAddress
     * @return IdentityFlags
     */
    public function setHideChangeAddress($HideChangeAddress)
    {
        $this->HideChangeAddress = $HideChangeAddress;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHideAnonymous()
    {
        return $this->HideAnonymous;
    }

    /**
     * @param bool $HideAnonymous
     * @return IdentityFlags
     */
    public function setHideAnonymous($HideAnonymous)
    {
        $this->HideAnonymous = $HideAnonymous;
        return $this;
    }


    

    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toArray()
    {
        $data = [];
        if ($this->getHideAnonymous()) {
            $data['HideAnonymous'] = true;
        }

        if ($this->getHideChangeAddress()) {
            $data['HideChangeAddress'] = true;
        }

        if ($this->getHideNotYou()) {
            $data['HideNotYou'] = true;
        }

        return $data;
    }

}