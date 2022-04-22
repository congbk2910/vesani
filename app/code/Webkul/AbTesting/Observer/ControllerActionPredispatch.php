<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Abtesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AbTesting\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;

class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var \Webkul\AbTesting\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Webkul\AbTesting\Helper\Data $helper
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Webkul\AbTesting\Model\GoalsFactory $goalsFactory
     * @param \Webkul\AbTesting\Model\GoalsDataFactory $goalsDataFactory
     */
    public function __construct(
        \Webkul\AbTesting\Helper\Data $helper,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Webkul\AbTesting\Model\GoalsFactory $goalsFactory,
        \Webkul\AbTesting\Model\GoalsDataFactory $goalsDataFactory,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager
    ) {
        $this->helper = $helper;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->state = $state;
        $this->resultFactory = $resultFactory;
        $this->response=$response;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->urlInterface = $urlInterface;
        $this->_coreSession = $coreSession;
        $this->storeRepository= $storeRepository;
        $this->goalsFactory = $goalsFactory;
        $this->goalsDataFactory = $goalsDataFactory;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager = $sessionManager;
    }

    /**
     * Controller Action Predispatch event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $this;
        }
        try {
            $postParams = $observer->getRequest()->getParams();
            if (!empty($postParams['variantId']) || !empty($postParams['previewId'])) {
                return true;
            }
            $viewPattern = $this->helper->getConfigViewPattern();
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $currentUrl = $this->urlInterface->getCurrentUrl();
            $action = $observer->getControllerAction();
            $fullActionName = $observer->getRequest()->getFullActionName();
            $request = $observer->getRequest();
            $areaCode = $this->state->getAreaCode();
            if ($areaCode == "adminhtml") {
                return true;
            }
            $mainTestId = $this->helper->getTestIdFromControlUrl($currentUrl);
            if (empty($mainTestId)) {
                return true;
            }
            $allVariantsArray = $this->helper->getAllVariantIdOnTestId($mainTestId);
            $shuffledArrayKey = array_rand($allVariantsArray);
            $cookieName = "abtesting_variant";
            if ($viewPattern == 1) {
                if (empty($this->getVariantCookie($cookieName))) {
                    $cookieValue = $allVariantsArray[$shuffledArrayKey];
                    $this->setCustomVariantCookie($cookieName, $cookieValue, 86400);
                }
            } else {
                 $cookieValue = $allVariantsArray[$shuffledArrayKey];
                 $this->setCustomVariantCookie($cookieName, $cookieValue, 86400);
            }
            return $this;
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Get data from cookie set in variant id
     *
     * @return value
     */
    public function getVariantCookie($variantId)
    {
        return $this->_cookieManager->getCookie($variantId);
    }

    /**
     * set variant cookie
     *
     * @return void
     */
    public function setCustomVariantCookie($cookieName, $value, $duration = 86400)
    {
        $metadata = $this->_cookieMetadataFactory
        ->createPublicCookieMetadata()
        ->setDuration($duration)
        ->setPath($this->_sessionManager->getCookiePath())
        ->setDomain($this->_sessionManager->getCookieDomain());

        $this->_cookieManager->setPublicCookie(
            $cookieName,
            $value,
            $metadata
        );
    }
}
