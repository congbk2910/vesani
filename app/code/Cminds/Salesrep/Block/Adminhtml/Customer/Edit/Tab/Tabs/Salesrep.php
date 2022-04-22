<?php
namespace Cminds\Salesrep\Block\Adminhtml\Customer\Edit\Tab\Tabs;

class Salesrep extends \Magento\Backend\Block\Widget\Form\Container
{

    public function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customer_edit_tab_tabs_salesrep';
        $this->_blockGroup = 'Cminds_Salesrep';
    }

    public function getHeaderHtml()
    {
        return '';
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-cms-page';
    }

    /**
     * Create form block
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Get buttons html
     *
     * rewritten to hide all buttons (header, toolbar, etc.)
     *
     * @param string|null $region Button region
     * @return string
     */
    public function getButtonsHtml($region = null)
    {
        return '';
    }
}
