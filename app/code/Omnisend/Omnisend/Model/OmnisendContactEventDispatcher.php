<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\Event\Manager;
use Omnisend\Omnisend\Helper\CookieHelper;

class OmnisendContactEventDispatcher
{
    const EVENT_OMNISEND_CONTACT_ACCESS = 'omnisend_contact_access';
    const EVENT_OMNISEND_UPDATE_CUSTOMER_CONTACT_ID = 'omnisend_update_customer_contact_id';
    const EVENT_OMNISEND_UPDATE_GUEST_CONTACT_ID = 'omnisend_update_guest_contact_id';

    const CUSTOMER_ID = 'customer_id';
    const SUBSCRIBER_ID = 'subscriber_id';
    const STORE_ID = 'store_id';

    /**
     * @var Manager
     */
    protected $eventManager;

    /**
     * @param Manager $eventManager
     */
    public function __construct(Manager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @param string $contactId
     */
    public function dispatchContactAccessEvent($contactId)
    {
        if (!$contactId) {
            return;
        }

        $this->eventManager->dispatch(
            self::EVENT_OMNISEND_CONTACT_ACCESS,
            [
                CookieHelper::COOKIE_OMNISEND_CONTACT_ID => $contactId
            ]
        );
    }

    /**
     * @param int $customerId
     * @param int $storeId
     */
    public function dispatchCustomerContactIdUpdateEvent($customerId, $storeId)
    {
        if (!$customerId || !$storeId) {
            return;
        }

        $this->eventManager->dispatch(
            self::EVENT_OMNISEND_UPDATE_CUSTOMER_CONTACT_ID,
            [
                self::CUSTOMER_ID => $customerId,
                self::STORE_ID => $storeId
            ]
        );
    }

    /**
     * @param int $subscriberId
     * @param int $storeId
     */
    public function dispatchGuestContactIdUpdateEvent($subscriberId, $storeId)
    {
        if (!$subscriberId || !$storeId) {
            return;
        }

        $this->eventManager->dispatch(
            self::EVENT_OMNISEND_UPDATE_GUEST_CONTACT_ID,
            [
                self::SUBSCRIBER_ID => $subscriberId,
                self::STORE_ID => $storeId
            ]
        );
    }
}
