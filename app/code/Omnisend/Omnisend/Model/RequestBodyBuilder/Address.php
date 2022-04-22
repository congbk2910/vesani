<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Sales\Api\Data\OrderAddressInterface;

class Address extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const COMPANY = 'company';
    const COUNTRY_CODE = 'countryCode';
    const STATE = 'state';
    const STATE_CODE = 'stateCode';
    const CITY = 'city';
    const ADDRESS = 'address';
    const ADDRESS2 = 'address2';
    const POSTAL_CODE = 'postalCode';

    /**
     * @param OrderAddressInterface $address
     * @return array
     */
    public function build($address)
    {
        $streetLines = $address->getStreet();

        $this->addData(self::FIRST_NAME, $address->getFirstname());
        $this->addData(self::LAST_NAME, $address->getLastname());
        $this->addData(self::COMPANY, $address->getCompany());
        $this->addData(self::COUNTRY_CODE, $address->getCountryId());
        $this->addData(self::STATE, $address->getRegion());
        $this->addData(self::STATE_CODE, $address->getRegionCode());
        $this->addData(self::CITY, $address->getCity());
        $this->addData(self::ADDRESS, $this->getStreetLine($streetLines, 0));
        $this->addData(self::ADDRESS2, $this->getStreetLine($streetLines, 1));
        $this->addData(self::POSTAL_CODE, $address->getPostcode());

        return $this->getData();
    }

    /**
     * @param string[] $streetLines
     * @param int $index
     * @return string|null
     */
    protected function getStreetLine($streetLines, $index)
    {
        if ($streetLines && is_array($streetLines) && array_key_exists($index, $streetLines)) {
            return $streetLines[$index];
        }

        return null;
    }
}
