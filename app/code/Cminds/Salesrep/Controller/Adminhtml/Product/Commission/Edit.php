<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Product\Commission;

use Cminds\Salesrep\Model\CommissionGroupRepository;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

/**
 * Commission Group edit action
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class Edit extends AbstractGroup
{
    protected $resultPageFactory;
    protected $commissionGroupRepository;
    protected $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CommissionGroupRepository $commissionGroupRepository,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->commissionGroupRepository = $commissionGroupRepository;
        $this->registry = $registry;
    }

    public function execute(): Page
    {
        if ($id = $this->getRequest()->getParam('entity_id')) {
            $this->initGroup($id);
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cminds_Salesrep::commission_groups');
        $resultPage->addBreadcrumb(__('Product Commission Group Management'), __('Product Commission Group Management'));
        $resultPage->getConfig()->getTitle()->prepend(!$id ? __('Group Create') : __('Group Modify'));
        return $resultPage;
    }

    protected function initGroup(int $id)
    {
        $icon = $this->commissionGroupRepository->getById($id);
        $this->registry->register('commission_group', $icon);
    }
}
