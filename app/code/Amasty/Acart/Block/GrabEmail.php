<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Block;

use Amasty\Acart\Model\ConfigProvider;
use Amasty\Acart\Model\Country;
use Amasty\Geoip\Model\Geolocation;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\View\Element\Template\Context;

class GrabEmail extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var Geolocation
     */
    private $geolocation;

    /**
     * @var Country
     */
    private $country;

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        ConfigProvider $configProvider,
        RemoteAddress $remoteAddress,
        Geolocation $geolocation,
        Country $country,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->configProvider = $configProvider;
        $this->remoteAddress = $remoteAddress;
        $this->geolocation = $geolocation;
        $this->country = $country;
    }

    public function getGrabUrl(): string
    {
        return $this->_urlBuilder->getUrl('amasty_acart/email/grab');
    }

    public function isGrabbingAllowed(): bool
    {
        try {
            return $this->checkoutSession->getQuote() && !$this->customerSession->getCustomerId();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function isNeedLogEmail(): bool
    {
        if ($this->configProvider->isDisableLoggingForGuests()) {
            try {
                $ip = $this->remoteAddress->getRemoteAddress();
                $geolocationData = $this->geolocation->locate($ip);
                $countryCode = (string)$geolocationData->getData('country') ?? '';

                return !$this->country->isEEACountry($countryCode);
            } catch (\Exception $e) {
                null;
            }
        }

        return true;
    }
}
