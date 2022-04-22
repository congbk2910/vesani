<?php
namespace Cminds\Salesrep\Block\Adminhtml\Order\View\Tab\Tabs;

class Commissions extends \Magento\Backend\Block\Widget\Form\Container
{

    public function _construct()
    {
        parent::_construct();

        $this->_objectId = 'commissions_form';
        $this->_controller = 'adminhtml_order_view_tab_tabs_commissions';
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
}
