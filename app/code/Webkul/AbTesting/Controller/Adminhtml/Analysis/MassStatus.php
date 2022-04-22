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
namespace Webkul\AbTesting\Controller\Adminhtml\Analysis;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\AbTesting\Model\ResourceModel\TestMain\CollectionFactory;

class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $postParams = $this->getRequest()->getParams();
        $updateStatus = 1;
        if (!empty($postParams['status'])) {
            if ($postParams['status'] == 2) {
                $updateStatus = 0;
            }
        }
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $data) {
            $data->setTestStatus($updateStatus);
            $data->save();
        }
        $message = 'Enabled';
        if (!empty($postParams['status']) && $postParams['status']==2) {
            $message = 'Disabled';
        }
        $this->messageManager->addSuccess(__('Test Status has been '.$message.' successfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('abtesting/analysis/index');
    }
}
