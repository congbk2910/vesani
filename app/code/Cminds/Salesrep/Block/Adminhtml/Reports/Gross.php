<?php

namespace Cminds\Salesrep\Block\Adminhtml\Reports;

class Gross extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'report/gross/grid/container.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Cminds_Salesrep';
        $this->_controller = 'adminhtml_reports_gross';
        $this->_headerText = __('Total Ordered Report');
        parent::_construct();

        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            [
                'label' => __('Show Report'),
                'onclick' => 'filterFormSubmit()',
                'class' => 'primary'
            ]
        );
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/gross', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
