<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AbTesting\Controller\Adminhtml\AbTest;

use Webkul\AbTesting\Model\TestMainFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var TestMainFactory
     */
    protected $testMainFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param TestMainFactory $testMainFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        TestMainFactory $testMainFactory
    ) {
        $this->testMainFactory = $testMainFactory;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('entity_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->testMainFactory->create();
                $model->load($id);
                if (!empty($model)) {
                    $model->delete();
                }
                $msg = 'Test Case deleted succesfully';
                $this->messageManager->addSuccessMessage(__($msg));
                // go to grid
                return $resultRedirect->setPath('abtesting/analysis/index');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('abtesting/analysis/index');
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a record to delete.'));
        // go to grid
        return $resultRedirect->setPath('abtesting/analysis/index');
    }
}
