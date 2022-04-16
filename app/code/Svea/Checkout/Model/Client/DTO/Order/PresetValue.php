<?php
namespace Svea\Checkout\Model\Client\DTO\Order;

use Svea\Checkout\Model\Client\DTO\AbstractRequest;

class PresetValue extends AbstractRequest
{

    protected $validTypeNames = [
        'NationalId',
        'EmailAddress',
        'PhoneNumber',
        'PostalCode',
        'IsCompany',
    ];

    /** @var $TypeName string */
    protected $TypeName;

    /** @var $Value string */
    protected $Value;

    /** @var bool $IsReadOnly bool */
    protected $IsReadOnly = false;

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->TypeName;
    }

    /**
     * @param string $TypeName
     * @return PresetValue
     */
    public function setTypeName($TypeName)
    {
        $this->TypeName = $TypeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->Value;
    }

    /**
     * @param string $Value
     * @return PresetValue
     */
    public function setValue($Value)
    {
        $this->Value = $Value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->IsReadOnly;
    }

    public function setNationalId($nationalId)
    {
        $this->setTypeName('NationalId');
        $this->setValue($nationalId);
        return $this;
    }

    public function setEmailAddress($emailAddress)
    {
        $this->setTypeName('EmailAddress');
        $this->setValue($emailAddress);
        return $this;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->setTypeName('PhoneNumber');
        $this->setValue($phoneNumber);
        return $this;
    }

    public function setPostalCode($postalCode)
    {
        $this->setTypeName('PostalCode');
        $this->setValue($postalCode);
        return $this;
    }

    public function setIsCompany($isCompany)
    {
        $this->setTypeName('IsCompany');
        $this->setValue($isCompany);
        return $this;
    }


    /**
     * @param bool $IsReadOnly
     * @return PresetValue
     */
    public function setIsReadOnly($IsReadOnly)
    {
        $this->IsReadOnly = $IsReadOnly;
        return $this;
    }



    public function toJSON()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'TypeName' => $this->getTypeName(),
            'Value' => $this->getValue(),
            'IsReadonly' => $this->isReadOnly(),
        ];
    }

}