<?php

namespace Omnisend\Omnisend\Plugin;

use Magento\Customer\Controller\Account\LoginPost;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\UrlInterface;
use Omnisend\Omnisend\Helper\CookieHelper;

class LoginPostPlugin
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CookieHelper
     */
    protected $cookieHelper;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @param CustomerSession $customerSession
     * @param CookieHelper $cookieHelper
     * @param ResultFactory $resultFactory
     * @param UrlInterface $url
     */
    public function __construct(
        CustomerSession $customerSession,
        CookieHelper $cookieHelper,
        ResultFactory $resultFactory,
        UrlInterface $url
    ) {
        $this->customerSession = $customerSession;
        $this->cookieHelper = $cookieHelper;
        $this->resultFactory = $resultFactory;
        $this->url = $url;
    }

    /**
     * @param LoginPost $subject
     * @param Redirect $result
     * @return Redirect|ResultInterface
     */
    public function afterExecute(LoginPost $subject, $result)
    {
        if (!$this->customerSession->isLoggedIn() || !$this->cookieHelper->getOmnisendRedirectCookie()) {
            return $result;
        }

        $this->cookieHelper->deleteOmnisendRedirectCookie();

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $cartUrl = $this->url->getUrl('checkout/cart', ['_secure' => true]);
        $resultRedirect->setUrl($cartUrl);

        return $resultRedirect;
    }
}
