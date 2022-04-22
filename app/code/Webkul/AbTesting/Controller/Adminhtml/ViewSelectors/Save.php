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

namespace Webkul\AbTesting\Controller\Adminhtml\ViewSelectors;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Webkul\AbTesting\Model\EditableSelectorsFactory;

class Save extends Action
{
    /**
     * unique constant for mapped id(s)
     */
    const ABTESTING_EDIT_ID = 'wk-ab-';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\AbTesting\Helper\Data $moduleHelper
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param EditableSelectorsFactory $editableClassFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        EditableSelectorsFactory $editableClassFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\File\Csv $csv
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->editableClassFactory = $editableClassFactory;
        $this->_fileUploader = $fileUploaderFactory;
        $this->csv = $csv;
    }

    /**
     * Save promotions controller
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->getParams()) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $postData = $this->getRequest()->getFiles();
            if (!empty($postData['csv_file']['import'])) {
                $file = $postData['csv_file']['import'];
                if (!isset($file['tmp_name'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                }
                $csvData = $this->csv->getData($file['tmp_name']);
                foreach ($csvData as $row => $data) {
                    if (strpos($data[0], '<script>') !== false || strpos($data[1], '<script>') !== false
                    || strpos($data[2], '<script>') !== false) {
                        $this->messageManager->addError(__("Invalid Data Provided"));
                        return $resultRedirect->setPath('*/*/index');
                    }
                    if ($row > 0) {
                        $bulkInsert[] = [
                           'page_class' => $data[0],
                           'parent_class' => $data[1],
                           'edited_class' => $data[2]
                        ];
                    }
                }
                if (!empty($csvData)) {
                    $editableClass = $this->editableClassFactory->create()->getCollection();
                    if (!empty($editableClass->getSize())) {
                        $editableClass->walk('delete');
                    }
                }
                $editableClass = $this->editableClassFactory->create();
                $editableClass->insertMultiple($bulkInsert, 'abtesting_editable_class');
                $editableClass->save();
                $this->updateUniqueIdsOnClasses();
            }
            $this->messageManager->addSuccess(__(" Csv uploaded Successfully"));
            return $resultRedirect->setPath('*/*/index');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $resultRedirect->setPath('*/*/index');
        }
    }

    /**
     * update unique id on classes
     *
     * @return void
     */
    public function updateUniqueIdsOnClasses()
    {
        $editableClass = $this->editableClassFactory->create()->getCollection();
        if (!empty($editableClass->getSize())) {
            foreach ($editableClass as $data) {
                $uniqueId = self::ABTESTING_EDIT_ID.$data->getEntityId();
                $data->setUniqueParentId($uniqueId)->save();
            }
        }
    }
}
