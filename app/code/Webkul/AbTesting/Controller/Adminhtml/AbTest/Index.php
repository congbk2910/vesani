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

class Index extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_AbTesting::analysis');
        $resultPage->getConfig()->getTitle()->prepend(__('Ab Test Information'));
        $resultPage->addBreadcrumb(__('Ab Test Information'), __('Ab Test Information'));
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
