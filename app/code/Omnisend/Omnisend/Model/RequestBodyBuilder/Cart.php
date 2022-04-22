<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Item;
use Omnisend\Omnisend\Helper\GmtDateHelper;
use Omnisend\Omnisend\Helper\PriceHelper;
use Omnisend\Omnisend\Model\QuoteRecoveryHandler;
use Omnisend\Omnisend\Observer\QuoteUpdateObserver;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

class Cart extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const CART_ID = 'cartID';
    const EMAIL = 'email';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const CURRENCY = 'currency';
    const CART_SUM = 'cartSum';
    const CART_RECOVERY_URL = 'cartRecoveryUrl';
    const PRODUCTS = 'products';
    // const EMAIL_ID = 'emailID';
    const CONTACT_ID = 'contactID';

    const CART_PRODUCT_ID = 'cart_product_id';

    const NULL_DATE_TIME = '0000-00-00 00:00:00';

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var GmtDateHelper
     */
    protected $gmtDateHelper;

    /**
     * @var CartItemFactory
     */
    protected $cartItemFactory;

    /**
     * @var QuoteRecoveryHandler
     */
    protected $quoteRecoveryHandler;

    /**
     * @var Json
     */
    protected $serializer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param UrlInterface $url
     * @param PriceHelper $priceHelper
     * @param GmtDateHelper $gmtDateHelper
     * @param CartItemFactory $cartItemFactory
     * @param QuoteRecoveryHandler $quoteRecoveryHandler
     * @param Json $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlInterface $url,
        PriceHelper $priceHelper,
        GmtDateHelper $gmtDateHelper,
        CartItemFactory $cartItemFactory,
        QuoteRecoveryHandler $quoteRecoveryHandler,
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->url = $url;
        $this->priceHelper = $priceHelper;
        $this->gmtDateHelper = $gmtDateHelper;
        $this->cartItemFactory = $cartItemFactory;
        $this->quoteRecoveryHandler = $quoteRecoveryHandler;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @param CartInterface $quote
     * @return string
     * @throws NoSuchEntityException
     */
    public function build($quote)
    {
        $quoteId = $quote->getId();

        $updatedAt = $quote->getUpdatedAt();
        $createdAt = $quote->getCreatedAt();

        if ($updatedAt == self::NULL_DATE_TIME) {
            $updatedAt = $createdAt;
        }

        $this->setCartOwnerIdentifier($quote);

        $this->addData(self::CART_ID, $quoteId);
        $this->addData(self::CREATED_AT, $this->gmtDateHelper->getGmtDate($createdAt));
        $this->addData(self::UPDATED_AT, $this->gmtDateHelper->getGmtDate($updatedAt));
        $this->addData(self::CURRENCY, $quote->getCurrency()->getGlobalCurrencyCode());
        $this->addData(self::CART_SUM, $this->priceHelper->getPriceInCents($quote->getGrandTotal()));
        $this->addData(self::CART_RECOVERY_URL, $this->quoteRecoveryHandler->getQuoteRecoveryUrl($quote));
        // $this->addData(self::EMAIL_ID, $quote->getData(QuoteUpdateObserver::EMAIL_ID));

        /** @var Item[] $quoteProducts */
        $quoteProducts = $quote->getAllVisibleItems();
        $omnisendProducts = [];

        foreach ($quoteProducts as $quoteProduct) {
            if ($quoteProduct->getPrice() === null) {
                continue;
            }

            $quoteProduct->setData(self::CART_PRODUCT_ID, $quoteProduct->getId());

            $cartItemBuilder = $this->cartItemFactory->create();
            $cartItemBuilder->build($quoteProduct);

            array_push($omnisendProducts, $cartItemBuilder->getData());
        }

        $this->addData(self::PRODUCTS, $omnisendProducts);

        return $this->serializer->serialize($this->getData());
    }

    /**
     * @param CartInterface $quote
     */
    protected function setCartOwnerIdentifier($quote)
    {
        if ($customerEmail = $quote->getCustomerEmail()) {
            $this->addData(self::EMAIL, $quote->getCustomerEmail());

            return;
        }
        $this->addData(self::CONTACT_ID, $quote->getData(QuoteUpdateObserver::CONTACT_ID));
    }
}
