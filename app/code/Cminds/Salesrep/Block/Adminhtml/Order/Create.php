<?php

namespace Cminds\Salesrep\Block\Adminhtml\Order;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm;
use Magento\Backend\Model\Auth\Session;

class Create extends AbstractForm
{
    protected $salesrepHelper;

    protected $authSession;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        Session $authSession,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Cminds\Salesrep\Helper\Data $salesrepHelper
    ) {
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $formFactory,
            $dataObjectProcessor
        );
        $this->authSession = $authSession;
        $this->salesrepHelper = $salesrepHelper;
    }

    /**
     * Return Header CSS Class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-account';
    }

    /**
     * Return header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $salesrepHeader = $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/checkout/header',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        if ($salesrepHeader) {
            return $salesrepHeader;
        }
        return __('Sales Representative');
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        return $this;
    }

    public function getSalesrepList()
    {
        return $this->salesrepHelper->getAdminsForBackend();
    }

    public function getSalesrepNote()
    {
        return $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/checkout/label',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isDisplayed()
    {
        $isAdminSelectorEnabled = $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/checkout/representative_selector_backend',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        $isModuleEnabled = $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/module_status/enabled',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        if ($isModuleEnabled) {
            if ($isAdminSelectorEnabled) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the current customer data.
     *
     * @return \Magento\User\Model\User|null
     */
    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }
}
