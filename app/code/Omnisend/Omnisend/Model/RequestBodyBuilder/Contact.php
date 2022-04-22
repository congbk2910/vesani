<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Exception;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Model\Customer;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Exception\LocalizedException;
use Omnisend\Omnisend\Helper\DateOfBirthHelper;
use Omnisend\Omnisend\Helper\GenderHelper;
use Omnisend\Omnisend\Helper\GmtDateHelper;
use Omnisend\Omnisend\Model\SubscriptionStatusManagerInterface;
use Psr\Log\LoggerInterface;

class Contact extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const EMAIL = 'email';
    const CREATED_AT = 'createdAt';
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const STATUS = 'status';
    const STATUS_DATE = 'statusDate';
    const SEND_WELCOME_EMAIL = 'sendWelcomeEmail';
    const GENDER = 'gender';
    const BIRTHDATE = 'birthdate';
    const COUNTRY = 'country';
    const PHONE = 'phone';

    /**
     * @var GmtDateHelper
     */
    protected $gmtDateHelper;

    /**
     * @var SubscriptionStatusManagerInterface
     */
    protected $subscriptionStatusManager;

    /**
     * @var GenderHelper
     */
    protected $genderHelper;

    /**
     * @var DateOfBirthHelper
     */
    protected $dateOfBirthHelper;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param GmtDateHelper $gmtDateHelper
     * @param SubscriptionStatusManagerInterface $subscriptionStatusManager
     * @param GenderHelper $genderHelper
     * @param DateOfBirthHelper $dateOfBirthHelper
     * @param AddressRepositoryInterface $addressRepository
     * @param CountryFactory $countryFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        GmtDateHelper $gmtDateHelper,
        SubscriptionStatusManagerInterface $subscriptionStatusManager,
        GenderHelper $genderHelper,
        DateOfBirthHelper $dateOfBirthHelper,
        AddressRepositoryInterface $addressRepository,
        CountryFactory $countryFactory,
        LoggerInterface $logger
    ) {
        $this->gmtDateHelper = $gmtDateHelper;
        $this->subscriptionStatusManager = $subscriptionStatusManager;
        $this->genderHelper = $genderHelper;
        $this->dateOfBirthHelper = $dateOfBirthHelper;
        $this->addressRepository = $addressRepository;
        $this->countryFactory = $countryFactory;
        $this->logger = $logger;
    }

    /**
     * @param CustomerInterface|Customer $customer
     * @return string
     */
    public function build($customer)
    {
        if (!$customer instanceof Customer && !$customer instanceof CustomerInterface) {
            return json_encode($this->getData());
        }

        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();
        $storeId = $customer->getStoreId();

        $subscriptionData = $this->subscriptionStatusManager->handleCustomerSubscriptionStatus(
            $customerId,
            $customerEmail,
            $storeId
        );

        $this->addData(self::EMAIL, $customerEmail);
        $this->addData(self::STATUS, $subscriptionData[SubscriptionStatusManagerInterface::SUBSCRIPTION_STATUS]);
        $this->addData(self::STATUS_DATE, $subscriptionData[SubscriptionStatusManagerInterface::STATUS_DATE]);
        $this->addData(self::CREATED_AT, $this->gmtDateHelper->getGmtDate($customer->getCreatedAt()));
        $this->addData(self::LAST_NAME, $customer->getLastname());
        $this->addData(self::FIRST_NAME, $customer->getFirstname());
        $this->addData(self::SEND_WELCOME_EMAIL, true);
        $this->addData(self::GENDER, $this->genderHelper->getGenderString($customer->getGender()));
        $this->addData(self::BIRTHDATE, $this->dateOfBirthHelper->formatCustomerDateOfBirth($customer->getDob()));

        $this->appendAddressData($this->getAddress($customer->getDefaultShipping()));

        return json_encode($this->getData());
    }

    /**
     * @param string $addressId
     * @return AddressInterface|null
     */
    protected function getAddress($addressId)
    {
        if (!$addressId) {
            return null;
        }

        try {
            return $this->addressRepository->getById($addressId);
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());

            return null;
        }
    }

    /**
     * @param AddressInterface $address
     * @return void
     */
    protected function appendAddressData($address)
    {
        if (!$address instanceof AddressInterface) {
            return;
        }

        $region = $address->getRegion();

        if ($region instanceof RegionInterface) {
            $this->addData(Address::STATE, $region->getRegion());
        }

        $this->addData(self::PHONE, $address->getTelephone());
        $this->addData(self::COUNTRY, $this->getCountryName($address->getCountryId()));
        $this->addData(Address::COUNTRY_CODE, $address->getCountryId());
        $this->addData(Address::CITY, $address->getCity());
        $this->addData(Address::POSTAL_CODE, $address->getPostcode());
        $this->addData(Address::ADDRESS, $this->getStreetLine($address->getStreet()));
    }

    /**
     * @param string $countryCode
     * @return null|string
     */
    protected function getCountryName($countryCode)
    {
        if (!$countryCode) {
            return null;
        }

        $country = $this->countryFactory->create();

        try {
            $country->loadByCode($countryCode);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());

            return null;
        }

        return $country->getName();
    }

    /**
     * @param array $streets
     * @return null|string
     */
    protected function getStreetLine($streets)
    {
        if (isset($streets[0])) {
            return $streets[0];
        }

        return null;
    }
}
