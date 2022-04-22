<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\App\Request\Http;
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
 * Class CartUpdateItemQtyObserver
 * @package Omnisend\Omnisend\Observer
 */
class CartUpdateItemObserver implements ObserverInterface
{
    const ACTION_BLACK_LIST = [
        'checkout_cart_add',
        'checkout_sidebar_removeItem',
        'checkout_cart_delete',
    ];

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
     * @var Http
     */
    protected $request;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param CartDataSender $cartDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param QuoteAttributeUpdater $quoteAttributeUpdater
     * @param CookieHelper $cookieHelper
     * @param Http $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        CartDataSender $cartDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        QuoteAttributeUpdater $quoteAttributeUpdater,
        CookieHelper $cookieHelper,
        Http $request,
        LoggerInterface $logger
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->cartDataSender = $cartDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->quoteAttributeUpdater = $quoteAttributeUpdater;
        $this->cookieHelper = $cookieHelper;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (in_array($this->request->getFullActionName(), self::ACTION_BLACK_LIST)) {
            return null;
        }

        /** @var Item $quoteItem */
        $quoteItem = $observer->getEvent()->getData('quote_item');

        if (!$quoteItem) {
            return null;
        }

        $quote = $quoteItem->getQuote();
        $storeId = $quoteItem->getStoreId();
        $isPosted = $quote->getData('omnisend_post_status');

        if (!$this->isQuoteOwnerIdentifiable($quote) ||
            !$this->responseRateManager->check($storeId) ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled() ||
            !$isPosted
        ) {
            return null;
        }

        if ($quoteItem->getQty() == 0) {
            return $this->cartDataSender->removeCartItem($quoteItem);
        }

        if ($isPosted) {
            return $this->cartDataSender->updateCartItem($quoteItem);
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
