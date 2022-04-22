<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Product\Commission;

use Cminds\Salesrep\Model\CommissionGroupRepository;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Commission Group Delete Action
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class Delete extends AbstractGroup
{
    private $commissionGroupRepository;

    public function __construct(
        Context $context,
        CommissionGroupRepository $commissionGroupRepository
    ) {
        parent::__construct($context);
        $this->commissionGroupRepository = $commissionGroupRepository;
    }

    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $commissionGroupId = (int)$this->getRequest()->getParam('entity_id');
        if ($commissionGroupId) {
            try {
                $model = $this->commissionGroupRepository->getById($commissionGroupId);
                $this->commissionGroupRepository->delete($model);
                $this->messageManager->addSuccessMessage(__('You deleted the group.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while trying to delete the group.'));
                return $resultRedirect->setPath('*/*/edit', ['_current' => true]);
            }
        }
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
