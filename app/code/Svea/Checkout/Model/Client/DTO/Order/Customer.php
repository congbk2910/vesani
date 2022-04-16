<?php
namespace Svea\Checkout\Model\Client\DTO\Order;

class Customer
{

    /** @var $Id int */
    protected $Id;

    /** @var $NationalId */
    protected $NationalId;

    /** @var $CountryCode string */
    protected $CountryCode;

    /** @var $IsCompany bool */
    protected $IsCompany;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param int $Id
     * @return Customer
     */
    public function setId($Id)
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNationalId()
    {
        return $this->NationalId;
    }

    /**
     * @param mixed $NationalId
     * @return Customer
     */
    public function setNationalId($NationalId)
    {
        $this->NationalId = $NationalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->CountryCode;
    }

    /**
     * @param string $CountryCode
     * @return Customer
     */
    public function setCountryCode($CountryCode)
    {
        $this->CountryCode = $CountryCode;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCompany()
    {
        return $this->IsCompany;
    }

    /**
     * @param bool $IsCompany
     * @return Customer
     */
    public function setIsCompany($IsCompany)
    {
        $this->IsCompany = $IsCompany;
        return $this;
    }

    

}