<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Omnisend\Omnisend\Helper\CookieHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Attribute\IsImported\QuoteAttributeUpdater;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Cart as CartDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CartUpdateObserver
 * @package Omnisend\Omnisend\Observer
 */
class CartRemoveItemObserver implements ObserverInterface
{
    const EMAIL_ID = 'email_id';
    const CONTACT_ID = 'contact_id';
    const CUSTOMER_EMAIL = 'customer_email';

    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var CartDataSender
     */
    protected $cartDataSender;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var QuoteAttributeUpdater
     */
    protected $quoteAttributeUpdater;

    /**
     * @var CookieHelper
     */
    protected $cookieHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param CartDataSender $cartDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param QuoteAttributeUpdater $quoteAttributeUpdater
     * @param CookieHelper $cookieHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        CartDataSender $cartDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        QuoteAttributeUpdater $quoteAttributeUpdater,
        CookieHelper $cookieHelper,
        LoggerInterface $logger
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->cartDataSender = $cartDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->quoteAttributeUpdater = $quoteAttributeUpdater;
        $this->cookieHelper = $cookieHelper;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Item $quoteItem */
        $quoteItem = $observer->getEvent()->getData('quote_item');
        $quote = $quoteItem->getQuote();
        $quoteId = $quoteItem->getQuoteId();
        $storeId = $quoteItem->getStoreId();
        $isPosted = $quote->getData('omnisend_post_status');

        if (!$this->isQuoteOwnerIdentifiable($quote) ||
            !$this->responseRateManager->check($storeId) ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled() ||
            !$isPosted
        ) {
            return null;
        }

        if ($quote->getItemsCount() == 1 && $isPosted) {
            $this->quoteAttributeUpdater->setPostStatus($quoteId, 0);
            $quote->setOmnisendPostStatus(0);
            $quote->setIsImported(0);
            return $this->cartDataSender->deleteCart($quote);
        }

        if ($isPosted) {
            return $this->cartDataSender->removeCartItem($quoteItem);
        }

        return null;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    protected function isQuoteOwnerIdentifiable($quote)
    {
        return ($quote->getCustomerEmail() ||
            $quote->getShippingAddress()->getEmail() ||
            $quote->getBillingAddress()->getEmail() ||
            $this->cookieHelper->getOmnisendContactId());
    }
}
