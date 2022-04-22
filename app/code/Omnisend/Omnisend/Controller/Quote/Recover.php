<?php

namespace Omnisend\Omnisend\Controller\Quote;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Quote\Model\Quote;
use Omnisend\Omnisend\Helper\CookieHelper;
use Omnisend\Omnisend\Model\QuoteRecoveryHandler;
use Omnisend\Omnisend\Model\Validator\ValidatorInterface;

class Recover extends Action
{
    const ACTION_PATH = 'omnisend/quote/recover';

    const ERROR_MESSAGE_UNABLE_TO_LOAD_QUOTE = 'We were unable to load the requested cart.';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var QuoteRecoveryHandler
     */
    protected $quoteRecoveryHandler;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var CookieHelper
     */
    protected $cookieHelper;

    /**
     * @param Context $context
     * @param ValidatorInterface $validator
     * @param QuoteRecoveryHandler $quoteRecoveryHandler
     * @param CheckoutSession $checkoutSession
     * @param HttpContext $httpContext
     * @param CookieHelper $cookieHelper
     */
    public function __construct(
        Context $context,
        ValidatorInterface $validator,
        QuoteRecoveryHandler $quoteRecoveryHandler,
        CheckoutSession $checkoutSession,
        HttpContext $httpContext,
        CookieHelper $cookieHelper
    ) {
        $this->validator = $validator;
        $this->quoteRecoveryHandler = $quoteRecoveryHandler;
        $this->checkoutSession = $checkoutSession;
        $this->httpContext = $httpContext;
        $this->cookieHelper = $cookieHelper;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $guestQuoteRecoveryData = (array) $this->getRequest()->getParams();

        if (!isset($guestQuoteRecoveryData['cart']) || $this->isLoggedIn()) {
            $resultRedirect->setUrl($this->handleCustomerQuoteRecovery());

            return $resultRedirect;
        }

        $resultRedirect->setUrl($this->_url->getUrl('checkout/cart', ['_secure' => true]));

        if (!$this->validator->validate($guestQuoteRecoveryData)) {
            $this->messageManager->addErrorMessage(__(self::ERROR_MESSAGE_UNABLE_TO_LOAD_QUOTE));

            return $resultRedirect;
        }

        $recoveredQuote = $this->quoteRecoveryHandler->getLostQuote($guestQuoteRecoveryData['cart']);

        if (!$recoveredQuote instanceof Quote) {
            $this->messageManager->addErrorMessage(__(self::ERROR_MESSAGE_UNABLE_TO_LOAD_QUOTE));

            return $resultRedirect;
        }

        if ($this->checkoutSession->getQuoteId() != $recoveredQuote->getId()) {
            $this->checkoutSession->replaceQuote($recoveredQuote);
        }

        return $resultRedirect;
    }

    /**
     * @return string
     */
    protected function handleCustomerQuoteRecovery()
    {
        if ($this->isLoggedIn()) {
            return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
        }

        $this->messageManager->addNoticeMessage(__('Please log in to your account, to complete the purchase.'));
        $this->cookieHelper->setOmnisendRedirectCookie(1);

        return $this->_url->getUrl('customer/account/login');
    }

    /**
     * @return bool
     */
    protected function isLoggedIn()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }
}
