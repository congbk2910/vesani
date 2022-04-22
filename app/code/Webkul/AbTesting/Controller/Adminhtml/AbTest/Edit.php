<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_AbTesting
 * @author Webkul
 * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\AbTesting\Controller\Adminhtml\AbTest;

use Magento\Framework\Controller\ResultFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var TestMainFactory
     */
    protected $testMainFactory;

    /**
     * @var TestMainFactory
     */
    protected $resultPageFactory;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Webkul\AbTesting\Model\TestMainFactory $testMainFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Webkul\AbTesting\Model\TestMainFactory $testMainFactory
    ) {
        $this->_backendSession = $context->getSession();
        $this->_registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->testMainFactory = $testMainFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $testMainData = $this->testMainFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $testMainData = $testMainData->load($rowId);
            if (!$testMainData->getEntityId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                return;
            }
        }
        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $testMainData->setData($data);
        }
        $this->_registry->register('abtesting_abtest', $testMainData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_AbTesting::analysis');
        $resultPage->getConfig()->getTitle()->prepend(__('Information'));
        $resultPage->addBreadcrumb(__('Information'), __('Information'));
        return $resultPage;
    }

    /**
     * return is allowed
     *
     * @return void
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_AbTesting::analysis');
    }
}
