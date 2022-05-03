<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Controller\Email;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Api\CartRepositoryInterface;

class Grab extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        $email = $this->getRequest()->getParam('email');

        try {
            $quote = $this->checkoutSession->getQuote();

            if ($quote->getId()
                && $quote->getExtensionAttributes()->getAmAcartQuoteEmail()
                && $quote->getExtensionAttributes()->getAmAcartQuoteEmail()->getCustomerEmail() !== $email
            ) {
                $quote->getExtensionAttributes()->getAmAcartQuoteEmail()->setCustomerEmail($email);
                $this->quoteRepository->save($quote);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
