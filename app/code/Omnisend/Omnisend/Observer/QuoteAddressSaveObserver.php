<?php

namespace Omnisend\Omnisend\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Model\Quote;
use Omnisend\Omnisend\Helper\EntityDataSender\QuoteAddressHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\EntityDataSender\Guest as GuestDataSender;
use Omnisend\Omnisend\Model\RequestBodyBuilder\GuestContact;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

class QuoteAddressSaveObserver implements ObserverInterface
{
    const ACTION_BLACK_LIST = [
        'checkout_cart_add',
        'checkout_sidebar_removeItem',
        'checkout_cart_delete',
        'checkout_cart_updatePost'
    ];
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var QuoteAddressHelper
     */
    protected $quoteAddressHelper;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var GuestDataSender
     */
    protected $guestDataSender;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @param QuoteAddressHelper $quoteAddressHelper
     * @param GuestDataSender $guestDataSender
     * @param Request $request
     * @param LoggerInterface $logger
     * @param Json $serializer
     * @param ImportStatus $importStatus
     */
    public function __construct(
        QuoteAddressHelper $quoteAddressHelper,
        GuestDataSender $guestDataSender,
        Request $request,
        LoggerInterface $logger,
        Json $serializer,
        ImportStatus $importStatus
    ) {
        $this->logger = $logger;
        $this->quoteAddressHelper = $quoteAddressHelper;
        $this->request = $request;
        $this->guestDataSender = $guestDataSender;
        $this->serializer = $serializer;
        $this->importStatus = $importStatus;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $guestData = [];
        $params = $this->request->getRequestData();

        if (in_array($this->request->getActionName(), self::ACTION_BLACK_LIST)) {
            return;
        }

        /** @var Quote\Address $quoteAddress */
        $quoteAddress = $observer->getEvent()->getData('quote_address');
        $quote = $quoteAddress->getQuote();


        if (empty($params["email"])) {
            return;
        }

        if (!empty($quote->getCustomerId())) {
            return;
        }

        $email = $params["email"];
        $guestData[GuestContact::FIRST_NAME] = $quoteAddress->getFirstname();
        $guestData[GuestContact::LAST_NAME] = $quoteAddress->getLastname();
        $guestData[GuestContact::EMAIL] = $email;

        if (!empty($guestData[GuestContact::EMAIL])) {
            $response = $this->guestDataSender->send($guestData);
            if ($this->importStatus->getImportStatus($response)) {
                $responseBody = $this->serializer->unserialize($response);
                $quote->setData(QuoteUpdateObserver::CONTACT_ID, $responseBody['contactID']);
            }
        }
        $this->quoteAddressHelper->send($quote);
    }
}
