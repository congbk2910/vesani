<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omnisend\Omnisend\Helper\CookieHelper;
use Omnisend\Omnisend\Model\OmnisendContactIdCookieUpdater;

class ContactAccessObserver implements ObserverInterface
{
    /**
     * @var OmnisendContactIdCookieUpdater
     */
    protected $omnisendContactIdCookieUpdater;

    /**
     * @param OmnisendContactIdCookieUpdater $omnisendContactIdCookieUpdater
     */
    public function __construct(OmnisendContactIdCookieUpdater $omnisendContactIdCookieUpdater)
    {
        $this->omnisendContactIdCookieUpdater = $omnisendContactIdCookieUpdater;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $contactId = $observer->getEvent()->getData(CookieHelper::COOKIE_OMNISEND_CONTACT_ID);

        if (!$contactId) {
            return;
        }

        $this->omnisendContactIdCookieUpdater->handleCookieUpdateRequest($contactId);
    }
}
