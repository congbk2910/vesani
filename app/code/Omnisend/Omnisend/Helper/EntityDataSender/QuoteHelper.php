<?php

namespace Omnisend\Omnisend\Helper\EntityDataSender;

use Magento\Quote\Model\Quote;
use Omnisend\Omnisend\Helper\CookieHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Attribute\IsImported\QuoteAttributeUpdater;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Quote as QuoteDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Quote
 * @package Omnisend\Omnisend\Helper\EntityDataSender
 */
class QuoteHelper
{
    const EMAIL_ID = 'email_id';
    const CONTACT_ID = 'contact_id';
    const CUSTOMER_EMAIL = 'customer_email';

    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var QuoteDataSender
     */
    protected $quoteDataSender;

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
     * @param QuoteDataSender $quoteDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param QuoteAttributeUpdater $quoteAttributeUpdater
     * @param CookieHelper $cookieHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        QuoteDataSender $quoteDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        QuoteAttributeUpdater $quoteAttributeUpdater,
        CookieHelper $cookieHelper,
        LoggerInterface $logger
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->quoteDataSender = $quoteDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->quoteAttributeUpdater = $quoteAttributeUpdater;
        $this->cookieHelper = $cookieHelper;
        $this->logger = $logger;
    }

    /**
     * @param Quote $quote
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function send(Quote $quote)
    {
        $quoteId = $quote->getId();
        $storeId = $quote->getStoreId();

        if (!$this->isQuoteOwnerIdentifiable($quote) ||
            !$this->responseRateManager->check($storeId) ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled()
        ) {
            $this->quoteAttributeUpdater->setIsImported($quoteId, 0);

            return false;
        }

        if ($contactId = $this->cookieHelper->getOmnisendContactId()) {
            $quote->setData(self::CONTACT_ID, $contactId);
        }

        if ($contactId = $this->cookieHelper->getOmnisendEmailId()) {
            $quote->setData(self::EMAIL_ID, $contactId);
        }

        if ($email = $quote->getCustomerEmail()) {
            $quote->setData(self::CUSTOMER_EMAIL, $email);
        }

        $quoteItemCount = $quote->getItemsCount();
        $isQuotePosted = $quote->getData('omnisend_post_status');

        if (!$quoteItemCount && !$isQuotePosted) {
            return false;
        }

        $postStatusOnSuccess = 1;

        if ($quoteItemCount) {
            $response = $this->quoteDataSender->send($quote);
        } else {
            $response = $this->quoteDataSender->delete($quoteId, $storeId);
            $postStatusOnSuccess = 0;
        }

        $isImported = $this->importStatus->getImportStatus($response);
        $this->quoteAttributeUpdater->setIsImported($quoteId, $isImported);

        if ($isQuotePosted != $postStatusOnSuccess && $isImported) {
            $this->logger->debug(self::class . "Setting post status");
            $this->quoteAttributeUpdater->setPostStatus($quoteId, $postStatusOnSuccess);
        }

        return true;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    protected function isQuoteOwnerIdentifiable($quote)
    {
        return ($quote->getCustomerEmail() || $this->cookieHelper->getOmnisendContactId());
    }
}
