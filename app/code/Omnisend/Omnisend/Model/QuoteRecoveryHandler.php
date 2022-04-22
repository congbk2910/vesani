<?php

namespace Omnisend\Omnisend\Model;

use Exception;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteRepository;
use Omnisend\Omnisend\Controller\Quote\Recover;
use Psr\Log\LoggerInterface;

class QuoteRecoveryHandler
{
    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param UrlInterface $url
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlInterface $url,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteRepository $quoteRepository,
        LoggerInterface $logger
    ) {
        $this->url = $url;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @param $maskedId
     * @return CartInterface|Quote|null
     */
    public function getLostQuote($maskedId)
    {
        if (!$maskedId) {
            return null;
        }

        try {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($maskedId, 'masked_id');
            $quoteId = $quoteIdMask->getQuoteId();

            return $this->quoteRepository->get($quoteId);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param Quote $quote
     * @return null|string
     */
    public function getQuoteRecoveryUrl($quote)
    {
        if (!$quote instanceof Quote) {
            return null;
        }

        if ($quote->getCustomerEmail()) {
            return $this->url->getUrl(Recover::ACTION_PATH);
        }

        $quoteId = $quote->getId();

        try {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
            return null;
        }

        $maskedQuoteId = $quoteIdMask->getMaskedId();

        return $this->url->getUrl(Recover::ACTION_PATH, ['cart' => $maskedQuoteId]);
    }
}
