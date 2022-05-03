<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Controller\Email;

use Amasty\Acart\Api\BlacklistRepositoryInterface;
use Amasty\Acart\Api\Data\BlacklistInterfaceFactory;
use Amasty\Acart\Api\QuoteManagementInterface;
use Amasty\Acart\Api\RuleQuoteRepositoryInterface;
use Amasty\Acart\Controller\Email;
use Amasty\Acart\Model\ConfigProvider;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Url extends Email
{
    /**
     * @var \Amasty\Acart\Model\App\Response\Redirect
     */
    private $redirect;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var RuleQuoteRepositoryInterface
     */
    private $ruleQuoteRepository;

    /**
     * @var BlacklistRepositoryInterface
     */
    protected $blacklistRepository;

    /**
     * @var BlacklistInterfaceFactory
     */
    protected $blacklistFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var QuoteManagementInterface
     */
    private $quoteManagement;

    public function __construct(
        Context $context,
        \Amasty\Acart\Model\UrlManager $urlManager,
        \Amasty\Acart\Model\RuleQuote $ruleQuote,
        \Amasty\Acart\Model\ResourceModel\RuleQuote $ruleQuoteResource,
        \Amasty\Acart\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Amasty\Acart\Model\App\Response\Redirect $redirect,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        RuleQuoteRepositoryInterface $ruleQuoteRepository,
        BlacklistRepositoryInterface $blacklistRepository,
        BlacklistInterfaceFactory $blacklistFactory,
        ConfigProvider $configProvider,
        QuoteManagementInterface $quoteManagement
    ) {
        parent::__construct(
            $context,
            $urlManager,
            $ruleQuote,
            $ruleQuoteResource,
            $historyCollectionFactory,
            $customerSession,
            $checkoutSessionFactory,
            $customerFactory,
            $quoteFactory
        );

        $this->redirect = $redirect;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->ruleQuoteRepository = $ruleQuoteRepository;
        $this->blacklistRepository = $blacklistRepository;
        $this->blacklistFactory = $blacklistFactory;
        $this->configProvider = $configProvider;
        $this->quoteManagement = $quoteManagement;
    }

    /**
     * @return \Amasty\Acart\Model\History|null
     */
    protected function getHistory()
    {
        $key = $this->getRequest()->getParam('key');

        /** @var \Amasty\Acart\Model\ResourceModel\History\Collection $historyResource */
        $historyResource = $this->historyCollectionFactory->create();
        $historyResource->addRuleQuoteData()
            ->addFieldToFilter('main_table.public_key', $key)
            ->setCurPage(1)
            ->setPageSize(1);

        $history = $historyResource->getFirstItem();

        if (!$history->getHistoryId() || $history->getPublicKey() != $key) {
            return null;
        }

        return $history;
    }

    /**
     * execute
     */
    public function execute()
    {
        $url = $this->getRequest()->getParam('url');
        $mageUrl = $this->getRequest()->getParam('mageUrl');

        $history = $this->getHistory();

        if (!$history || (!$url && !$mageUrl)) {
            $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

            return $result->forward('edit');
        }
        $this->urlManager->init($history->getRule(), $history);
        $target = null;

        if ($url) {
            // @codingStandardsIgnoreLine
            $target = $this->urlManager->getCleanUrl(base64_decode(urldecode($url)));
        } elseif ($mageUrl) {
            $target = $this->_url->getUrl(
                // @codingStandardsIgnoreLine
                $this->urlManager->getCleanUrl(base64_decode(urldecode($mageUrl))),
                $this->urlManager->getUtmParams()
            );
        }

        $this->loginCustomer($history);
        $ruleQuote = $this->ruleQuoteRepository->getById((int)$history->getRuleQuoteId());

        $ruleQuote->clickByLink($history);

        $ruleQuote->setAbandonedStatus(\Amasty\Acart\Model\RuleQuote::ABANDONED_RESTORED_STATUS);

        $this->ruleQuoteRepository->save($ruleQuote);

        return $this->resultRedirectFactory->create()->setUrl($this->redirect->validateRedirectUrl($target));
    }

    /**
     * @param \Amasty\Acart\Model\History $history
     */
    protected function loginCustomer(\Amasty\Acart\Model\History $history)
    {
        /** @var Session $checkoutSession */
        $checkoutSession = $this->checkoutSessionFactory->create();

        if ($this->customerSession->isLoggedIn()) {
            if ($history->getCustomerId() != $this->customerSession->getCustomerId()) {
                $this->customerSession->logout();
            }
        }

        // customer. login
        if ($history->getCustomerId()) {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->customerFactory->create()->load($history->getCustomerId());

            if ($customer->getId()) {
                if ($this->configProvider->isAutoLoginEnabled()) {
                    $this->customerSession->setCustomerAsLoggedIn($customer);
                    $this->flushSection($checkoutSession, 'customer');
                } elseif (!$this->customerSession->isLoggedIn() && $history->getQuoteId()) {
                    try {
                        $quote = $this->quoteManagement->cloneCustomerQuoteToGuest(
                            (int)$customer->getId(),
                            (int)$history->getQuoteId(),
                            $checkoutSession->getQuoteId() ? (int)$checkoutSession->getQuoteId() : null
                        );
                    } catch (\Exception $e) {
                        $quote = null;
                    }

                    if ($quote) {
                        $checkoutSession->replaceQuote($quote);
                    }
                }
            }
        } elseif ($history->getQuoteId()) {
            /**
             * visitor. restore quote in the session
             *
             * @var \Magento\Quote\Model\Quote $quote
             */
            $quote = $this->quoteFactory->create()->load($history->getQuoteId());

            if ($quote) {
                $checkoutSession->replaceQuote($quote);
                $quote->getBillingAddress()->setEmail($history->getEmail());
            }
        }

        if ($history->getSalesRuleCoupon()) {
            $code = $history->getSalesRuleCoupon();
            $quote = $checkoutSession->getQuote();

            if ($code && $quote) {
                $quote->setCouponCode($code)
                    ->collectTotals()
                    ->save();
            }
        }
    }

    /**
     * Change version for make section expired
     *
     * @param Session $checkoutSession
     * @param string $sectionName
     */
    private function flushSection(Session $checkoutSession, $sectionName)
    {
        if ($this->cookieManager->getCookie('mage-cache-sessid')) {
            $metadata = $this->cookieMetadataFactory->createCookieMetadata();
            $metadata->setPath('/');
            $this->cookieManager->deleteCookie('mage-cache-sessid', $metadata);
        }

        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath($checkoutSession->getCookiePath());
        $sectionDataIds = json_decode($this->cookieManager->getCookie('section_data_ids', '{}'), true);
        $sectionDataIds[$sectionName] = isset($sectionDataIds[$sectionName]) ?
            $sectionDataIds[$sectionName] + 1000 :
            1000;
        $this->cookieManager->deleteCookie('section_data_ids');
        $this->cookieManager->setPublicCookie(
            'section_data_ids',
            json_encode($sectionDataIds),
            $metadata
        );
    }
}
