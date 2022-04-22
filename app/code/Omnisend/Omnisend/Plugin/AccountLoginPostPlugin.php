<?php

namespace Omnisend\Omnisend\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Controller\Account\LoginPost;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Omnisend\Omnisend\Helper\EntityDataSender\QuoteHelper;
use Psr\Log\LoggerInterface;

/**
 * Class AssignCustomerPlugin
 * @package Omnisend\Omnisend\Plugin
 */
class AccountLoginPostPlugin
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
     * AssignCustomerPlugin constructor.
     * @param QuoteHelper $quoteHelper
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        QuoteHelper $quoteHelper,
        QuoteRepository $quoteRepository,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->quoteHelper = $quoteHelper;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
    }

    public function afterExecute(LoginPost $subject, $result)
    {
        try {
            $login = $subject->getRequest()->getPost('login');
            $customer = $this->customerRepository->get($login['username']);
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
