<?php

namespace Omnisend\Omnisend\Helper;

use Exception;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Psr\Log\LoggerInterface;

class CookieHelper
{
    const COOKIE_OMNISEND_EMAIL_ID = 'omnisendEmailID';
    const COOKIE_OMNISEND_CONTACT_ID = 'omnisendContactID';
    const COOKIE_OMNISEND_ANONYMOUS_ID = 'omnisendAnonymousID';
    const COOKIE_LIFETIME_OMNISEND_CONTACT_ID = 2592000;
    const COOKIE_OMNISEND_REDIRECT = 'omnisendRedirect';
    const COOKIE_DURATION = 3600;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param LoggerInterface $logger
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        LoggerInterface $logger,
        SessionManagerInterface $sessionManager
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->logger = $logger;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @return null|string
     */
    public function getOmnisendEmailId()
    {
        return $this->cookieManager->getCookie(self::COOKIE_OMNISEND_EMAIL_ID);
    }

    /**
     * @return null|string
     */
    public function getOmnisendContactId()
    {
        return $this->cookieManager->getCookie(self::COOKIE_OMNISEND_CONTACT_ID);
    }

    /**
     * @return null|string
     */
    public function getAnonymousContactId()
    {
        return $this->cookieManager->getCookie(self::COOKIE_OMNISEND_ANONYMOUS_ID);
    }

    /**
     * @param string $contactId
     */
    public function setOmnisendContactIdCookie($contactId)
    {
        $cookieMetadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(self::COOKIE_LIFETIME_OMNISEND_CONTACT_ID)
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setPath($this->sessionManager->getCookiePath());

        try {
            $this->cookieManager->setPublicCookie(self::COOKIE_OMNISEND_CONTACT_ID, $contactId, $cookieMetadata);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @return null|string
     */
    public function getOmnisendRedirectCookie()
    {
        return $this->cookieManager->getCookie(self::COOKIE_OMNISEND_REDIRECT);
    }

    /**
     * @param int $redirectFlag
     */
    public function setOmnisendRedirectCookie($redirectFlag)
    {
        $cookieMetadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(self::COOKIE_DURATION)
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setPath($this->sessionManager->getCookiePath());

        try {
            $this->cookieManager->setPublicCookie(self::COOKIE_OMNISEND_REDIRECT, $redirectFlag, $cookieMetadata);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    public function deleteOmnisendRedirectCookie()
    {
        if (!$this->getOmnisendRedirectCookie()) {
            return;
        }

        $cookieMetadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setPath($this->sessionManager->getCookiePath());

        try {
            $this->cookieManager->deleteCookie(self::COOKIE_OMNISEND_REDIRECT, $cookieMetadata);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
