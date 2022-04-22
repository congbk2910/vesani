<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Controller\Adminhtml\Report;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Reports\Controller\Adminhtml\Report\AbstractReport;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Model\Flag;

class Gross extends AbstractReport
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context, $fileFactory, $dateFilter, $timezone);
    }


    public function execute()
    {
        $this->_showLastExecutionTime(Flag::REPORT_ORDER_FLAG_CODE, 'sales');

        $this->_initAction()->_setActiveMenu(
            'Cminds_Salesrep::report_salesrep'
        )->_addBreadcrumb(
            __('Sales Representative Report'),
            __('Sales Representative Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            __('Sales Representative Report')
        );
        $gridBlock = $this->_view->getLayout()->getBlock(
            'adminhtml_reports_gross.grid'
        );
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form.gross');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }

    /**
     * Report action init operations
     *
     * @param array|\Magento\Framework\DataObject $blocks
     * @return $this
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }

        $requestData = $this->_objectManager->get(
            'Magento\Backend\Helper\Data'
        )->prepareFilterString(
            $this->getRequest()->getParam('filter')
        );
        $inputFilter = new \Zend_Filter_Input(
            ['from' => $this->_dateFilter, 'to' => $this->_dateFilter],
            [],
            $requestData
        );
        $requestData = $inputFilter->getUnescaped();
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }

        return $this;
    }
}
