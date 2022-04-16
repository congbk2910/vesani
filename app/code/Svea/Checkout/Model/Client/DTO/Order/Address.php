<?php
namespace Svea\Checkout\Model\Client\DTO\Order;

class Address
{

    /**
     * @var $FullName string
     */
    protected $FullName;

    /**
     * @var $FirstName string
     */
    protected $FirstName;

    /**
     * @var $LastName string
     */
    protected $LastName;

    /**
     * @var string $StreetAddress
     */
    protected $StreetAddress;

    /**
     * @var string $CoAddress
     */
    protected $CoAddress;

    /**
     * @var string $PostalCode
     */
    protected $PostalCode;

    /**
     * @var string $city
     */
    protected $City;

    /**
     * @var string $CountryCode
     */
    protected $CountryCode;

    /**
     * @var $IsGeneric bool
     */
    protected $IsGeneric;

    /**
     * @var $AddressLines string[]
     */
    protected $AddressLines;

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->FullName;
    }

    /**
     * @param string $FullName
     * @return ConsumerShippingAddress
     */
    public function setFullName($FullName)
    {
        $this->FullName = $FullName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->FirstName;
    }

    /**
     * @param string $FirstName
     * @return ConsumerShippingAddress
     */
    public function setFirstName($FirstName)
    {
        $this->FirstName = $FirstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->LastName;
    }

    /**
     * @param string $LastName
     * @return ConsumerShippingAddress
     */
    public function setLastName($LastName)
    {
        $this->LastName = $LastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->StreetAddress;
    }

    /**
     * @param string $StreetAddress
     * @return ConsumerShippingAddress
     */
    public function setStreetAddress($StreetAddress)
    {
        $this->StreetAddress = $StreetAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getCoAddress()
    {
        return $this->CoAddress;
    }

    /**
     * @param string $CoAddress
     * @return ConsumerShippingAddress
     */
    public function setCoAddress($CoAddress)
    {
        $this->CoAddress = $CoAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->PostalCode;
    }

    /**
     * @param string $PostalCode
     * @return ConsumerShippingAddress
     */
    public function setPostalCode($PostalCode)
    {
        $this->PostalCode = $PostalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->City;
    }

    /**
     * @param string $City
     * @return ConsumerShippingAddress
     */
    public function setCity($City)
    {
        $this->City = $City;
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
     * @return ConsumerShippingAddress
     */
    public function setCountryCode($CountryCode)
    {
        $this->CountryCode = $CountryCode;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsGeneric()
    {
        return $this->IsGeneric;
    }

    /**
     * @param bool $IsGeneric
     * @return ConsumerShippingAddress
     */
    public function setIsGeneric($IsGeneric)
    {
        $this->IsGeneric = $IsGeneric;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAddressLines()
    {
        return $this->AddressLines;
    }

    /**
     * @param string[] $AddressLines
     * @return ConsumerShippingAddress
     */
    public function setAddressLines($AddressLines)
    {
        $this->AddressLines = $AddressLines;
        return $this;
    }



}