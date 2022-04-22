<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Block;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\CookieManagerInterface;

class EditorBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Webkul\AbTesting\Helper\Data $moduleHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Backend\Model\Auth\SessionFactory $authSessionFactory,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        CookieManagerInterface $cookieManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_urlInterface = $urlInterface;
        $this->authSessionFactory = $authSessionFactory;
        $this->formKey = $context->getFormKey();
        $this->moduleHelper = $moduleHelper;
        $this->_backendUrl = $backendUrl;
        $this->_cookieManager = $cookieManager;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }
        
    /**
     * validate variant id
     *
     * @param int $variantId
     * @param string $currentUrl
     * @return boolean
     */
    public function validateVariantId($variantId, $currentUrl)
    {
         return $this->moduleHelper->validateVariantId($variantId, $currentUrl);
    }
        
    /**
     * return current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlInterface->getCurrentUrl();
    }

    /**
     * return module status
     *
     * @return boolean
     */
    public function checkModuleStatus()
    {
        return $this->moduleHelper->isModuleEnabled();
    }

    /**
     * return variant data from variant id
     *
     * @param int $variantId
     * @return void
     */
    public function getVariantDataFromVariantId($variantId)
    {
        return $this->moduleHelper->getVariantDataFromVariantId($variantId);
    }

    /**
     * return control url from variant id
     *
     * @param int $variantId
     * @return void
     */
    public function getControlUrlFromVariant($variantId)
    {
        return $this->moduleHelper->getControlUrlFromVariant($variantId);
    }

    /**
     * return testid from control url
     *
     * @param string $controlUrl
     * @return int
     */
    public function getTestIdFromControlUrl($controlUrl)
    {
        return $this->moduleHelper->getTestIdFromControlUrl($controlUrl);
    }

    /**
     * return active goals
     *
     * @param int $testId
     * @return void
     */
    public function getActiveGoalsOnTestId($testId)
    {
        return $this->moduleHelper->getActiveGoalsOnTestId($testId);
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function checkAdminStatus()
    {
        return $this->_backendUrl->getUrl("abtesting/adminlogin/status");
    }
    
    /**
     * return if is main variant
     *
     * @param int $testId
     * @return boolean
     */
    public function isMainVariant($testId)
    {
        $mainVarId =  $this->moduleHelper->getMainVariantFromTestId($testId);
        if (empty($mainVarId)) {
            return false;
        } else {
            return true;
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
      * mapping encode
      *
      * @param array $optionData
      * @return void
      */
    public function mappingJsonEncode($optionData)
    {
        return  $this->jsonHelper->jsonEncode($optionData);
    }
}
