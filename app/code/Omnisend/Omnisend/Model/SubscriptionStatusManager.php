<?php

namespace Omnisend\Omnisend\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\ScopeInterface;
use Omnisend\Omnisend\Helper\GmtDateHelper;
use Omnisend\Omnisend\Helper\SubscriberStatusHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\SubscriberAttributeUpdater;

class SubscriptionStatusManager implements SubscriptionStatusManagerInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var GmtDateHelper
     */
    protected $gmtDateHelper;

    /**
     * @var SubscriberAttributeUpdater
     */
    protected $subscriberAttributeUpdater;

    /**
     * @var SubscriptionProviderInterface
     */
    protected $subscriptionProvider;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param GmtDateHelper $gmtDateHelper
     * @param SubscriberAttributeUpdater $subscriberAttributeUpdater
     * @param SubscriptionProviderInterface $subscriptionProvider
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        GmtDateHelper $gmtDateHelper,
        SubscriberAttributeUpdater $subscriberAttributeUpdater,
        SubscriptionProviderInterface $subscriptionProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->gmtDateHelper = $gmtDateHelper;
        $this->subscriberAttributeUpdater = $subscriberAttributeUpdater;
        $this->subscriptionProvider = $subscriptionProvider;
    }

    /**
     * @param int $customerId
     * @param string $customerEmail
     * @param int $storeId
     * @return array
     */
    public function handleCustomerSubscriptionStatus($customerId, $customerEmail, $storeId)
    {
        $customerSubscription = $this->subscriptionProvider->getSubscription($customerId, $customerEmail, $storeId);
        $subscriberId = $customerSubscription->getId();

        if (!$subscriberId) {
            return $this->processPostedSubscriptionStatus($this->checkPostForSubscriptionStatus());
        }

        if (!SubscriberStatusHelper::subscriberStatusChanged($customerSubscription)) {
            return $this->formatSubscriptionStatusData(self::STATUS_NON_SUBSCRIBED);
        }

        $subscriberStatus = $customerSubscription->getStatus();
        $this->subscriberAttributeUpdater->updatePreviousSubscriberStatus($subscriberId, $subscriberStatus);

        return $this->formatSubscriptionStatusData($this->processSubscriberStatus($subscriberStatus));
    }

    /**
     * @param Subscriber $subscriber
     * @return array
     */
    public function handleGuestSubscriptionStatus($subscriber)
    {
        $subscriberId = $subscriber->getId();
        $subscriberStatus = $subscriber->getStatus();

        $this->subscriberAttributeUpdater->updatePreviousSubscriberStatus($subscriberId, $subscriberStatus);

        return $this->formatSubscriptionStatusData($this->processSubscriberStatus($subscriberStatus));
    }

    /**
     * @param int $status
     * @return string
     */
    protected function processSubscriberStatus($status)
    {
        switch ($status) {
            case Subscriber::STATUS_SUBSCRIBED:
                return self::STATUS_SUBSCRIBED;
            case Subscriber::STATUS_UNSUBSCRIBED:
                return self::STATUS_UNSUBSCRIBED;
            default:
                return self::STATUS_NON_SUBSCRIBED;
        }
    }

    /**
     * @return bool
     */
    protected function checkPostForSubscriptionStatus()
    {
        $postArray = $this->request->getPostValue();

        if (array_key_exists(self::IS_SUBSCRIBED, $postArray) && $postArray[self::IS_SUBSCRIBED] == 1) {
            return true;
        }

        return false;
    }

    /**
     * @param bool $isSubscribed
     * @return array
     */
    protected function processPostedSubscriptionStatus($isSubscribed)
    {
        $status = self::STATUS_NON_SUBSCRIBED;

        $isConfirmNeeded = $this->scopeConfig->getValue(
            Subscriber::XML_PATH_CONFIRMATION_FLAG,
            ScopeInterface::SCOPE_STORE
        );

        if ($isSubscribed && !$isConfirmNeeded) {
            $status = self::STATUS_SUBSCRIBED;
        }

        return $this->formatSubscriptionStatusData($status);
    }

    /**
     * @param $status
     * @return array
     */
    protected function formatSubscriptionStatusData($status)
    {
        return [
            self::SUBSCRIPTION_STATUS => $status,
            self::STATUS_DATE => $this->gmtDateHelper->getGmtDate()
        ];
    }
}
