<?php

namespace Omnisend\Omnisend\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Controller\Ajax\Login;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Omnisend\Omnisend\Helper\EntityDataSender\QuoteHelper;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class AjaxLoginPlugin
 * @package Omnisend\Omnisend\Plugin\Config
 */
class AjaxLoginPlugin
{
    /**
     * @var QuoteHelper
     */
    protected $quoteHelper;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * AssignCustomerPlugin constructor.
     * @param QuoteHelper $quoteHelper
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     * @param Json $serializer
     */
    public function __construct(
        QuoteHelper $quoteHelper,
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository,
        Json $serializer
    ) {
        $this->quoteHelper = $quoteHelper;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->serializer = $serializer;
    }

    public function afterExecute(Login $subject, $result)
    {
        try {
            $credentials = $this->serializer->unserialize($subject->getRequest()->getContent());
            $customer = $this->customerRepository->get($credentials['username']);
            $quote = $this->quoteRepository->getForCustomer($customer->getId());
            if ($quote->getItemsCount() == 0) {
                return $result;
            }
            $this->quoteHelper->send($quote);
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
