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

class MassDelete extends \Magento\Backend\App\Action
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
     * @param \Webkul\AbTesting\Model\ControlUrlInfoFactory $controlUrlInfo
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\AbTesting\Model\ControlUrlInfoFactory $controlUrlInfo
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->controlUrlInfo = $controlUrlInfo;
        parent::__construct($context);
    }

    /**
     * checks allowed role
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_AbTesting::analysis');
    }

    /**
     * execute
     *
     * @return void
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        foreach ($collection as $testCase) {
            $controlUrlDb = $this->controlUrlInfo->create()->getCollection()
            ->addFieldToFilter('test_id', $testCase->getId());
            if (!empty($controlUrlDb->getSize())) {
                foreach ($controlUrlDb as $controlUrl) {
                    $controlUrl->delete();
                }
            }
                $this->removeItem($testCase);
        }
        $this->messageManager->addSuccess(__('Test Cases(s) deleted succesfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Remove Item
     *
     * @param object $item
     */
    protected function removeItem($item)
    {
        $item->delete();
    }
}
