<?php
namespace Cminds\Salesrep\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;
use Magento\Customer\Controller\RegistryConstants;

class Salesrep extends TabWrapper
{

    protected $coreRegistry = null;

    protected $isAjaxLoaded = true;


    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->coreRegistry = $registry;
    }

    public function canShowTab()
    {
        $isModuleEnabled = $this->_scopeConfig->getValue(
            'cminds_salesrep_configuration/module_status/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$isModuleEnabled) {
            return false;
        }
        if ($this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID) !== null) {
            return true;
        }

        return false;
    }

    /**
     * Return Tab label
     *
     * @codeCoverageIgnore
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Sales Representative');
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl(
            'salesrep/customer/salesrep',
            ['_current' => true]
        );
    }
}
