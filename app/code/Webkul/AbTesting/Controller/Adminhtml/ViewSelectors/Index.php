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

namespace Webkul\AbTesting\Controller\Adminhtml\ViewSelectors;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_AbTesting::viewselectors');
        $resultPage->getConfig()->getTitle()->prepend(__('View Selectors'));
        $resultPage->addBreadcrumb(__('View Selectors'), __('View  Selectors'));
        return $resultPage;
    }

    /**
     * return is allowed
     *
     * @return void
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_AbTesting::viewselectors');
    }
}
