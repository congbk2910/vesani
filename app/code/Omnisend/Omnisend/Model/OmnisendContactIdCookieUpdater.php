<?php

namespace Omnisend\Omnisend\Model;

use Omnisend\Omnisend\Helper\CookieHelper;

class OmnisendContactIdCookieUpdater
{
    /**
     * @var CookieHelper
     */
    protected $cookieHelper;

    /**
     * @param CookieHelper $cookieHelper
     */
    public function __construct(CookieHelper $cookieHelper)
    {
        $this->cookieHelper = $cookieHelper;
    }

    /**
     * @param string $contactId
     */
    public function handleCookieUpdateRequest($contactId)
    {
        $storedContactId = $this->cookieHelper->getOmnisendContactId();

        if ($storedContactId && $storedContactId == $contactId) {
            return;
        }

        $this->cookieHelper->setOmnisendContactIdCookie($contactId);
    }
}
