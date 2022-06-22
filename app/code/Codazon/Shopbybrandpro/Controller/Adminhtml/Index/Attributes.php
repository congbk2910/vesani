<?php
/**
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Controller\Adminhtml\Index;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Attributes extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
    
	public function __construct(Context $context, PageFactory $resultPageFactory)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}
	public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Codazon_Shopbybrandpro::shopbybrandpro');
        $resultPage->addBreadcrumb(__('Attributes List'), __('Attributes List'));
        $resultPage->addBreadcrumb(__('Attributes List'), __('Attributes List'));
        $resultPage->getConfig()->getTitle()->prepend(__('Attributes List'));

        return $resultPage;
    }
	/**
     * Is the user allowed to view the menu grid.
     *
     * @return bool
     */
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Codazon_Shopbybrandpro::index');
    }
}