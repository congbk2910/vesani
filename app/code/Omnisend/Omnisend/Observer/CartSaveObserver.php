<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Omnisend\Omnisend\Helper\EntityDataSender\QuoteHelper;
use Psr\Log\LoggerInterface;

class CartSaveObserver implements ObserverInterface
{
    const ACTION_BLACK_LIST = [
        'checkout_sidebar_removeItem',
        'checkout_cart_delete'
    ];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var QuoteHelper
     */
    protected $quoteHelper;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @param QuoteHelper $quoteHelper
     * @param Http $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        QuoteHelper $quoteHelper,
        Http $request,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->quoteHelper = $quoteHelper;
        $this->request = $request;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if (in_array($this->request->getFullActionName(), self::ACTION_BLACK_LIST)) {
            return;
        }

        /** @var Cart $cart */
        $cart = $observer->getEvent()->getData('cart');
        $quote = $cart->getQuote();
        $this->quoteHelper->send($quote);
    }
}
