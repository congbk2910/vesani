<?php

namespace Omnisend\Omnisend\Plugin;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Checkout\Model\Session;
use Omnisend\Omnisend\Model\RequestBodyBuilder\GuestContact;
use Omnisend\Omnisend\Observer\QuoteUpdateObserver;
use Omnisend\Omnisend\Model\EntityDataSender\Guest as GuestDataSender;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;
use Omnisend\Omnisend\Helper\EntityDataSender\QuoteAddressHelper;


/**
 * Class AccountManagementPlugin
 * @package Omnisend\Omnisend\Plugin
 */
class AccountManagementPlugin
{
    /**
     * @var Session 
     */
    protected $checkoutSession;

    /**
     * @var GuestDataSender
     */
    protected $guestDataSender;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var QuoteAddressHelper
     */
    protected $quoteAddressHelper;

    /**
     * Class Constructor
     *
     * @param Session $checkoutSession
     * @param GuestDataSender $guestDataSender
     * @param ImportStatus $importStatus
     * @param Json $serializer
     * @param LoggerInterface $logger
     * @param QuoteAddressHelper $quoteAddressHelper
     */
    public function __construct(
        Session $checkoutSession,
        GuestDataSender $guestDataSender,
        ImportStatus $importStatus,
        Json $serializer,
        LoggerInterface $logger,
        QuoteAddressHelper $quoteAddressHelper
    ) {
        $this->checkoutSession    = $checkoutSession;
        $this->guestDataSender    = $guestDataSender;
        $this->importStatus       = $importStatus;
        $this->serializer         = $serializer;
        $this->logger             = $logger;
        $this->quoteAddressHelper = $quoteAddressHelper;
    }

    public function afterIsEmailAvailable(AccountManagement $subject, $result, $customerEmail)
    {
        $guestData[GuestContact::FIRST_NAME] = '';
        $guestData[GuestContact::LAST_NAME] = '';
        $guestData[GuestContact::EMAIL] = $customerEmail;
        $quote = $this->checkoutSession->getQuote();

        try {
            if (!empty($guestData[GuestContact::EMAIL])) {
                $response = $this->guestDataSender->send($guestData);
                if ($this->importStatus->getImportStatus($response)) {
                    $responseBody = $this->serializer->unserialize($response);
                    $quote->setData(QuoteUpdateObserver::CONTACT_ID, $responseBody['contactID']);
                }
            }
            $this->quoteAddressHelper->send($quote);
            return $result;
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
            return $result;
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());
            return $result;
        }
    }
}
