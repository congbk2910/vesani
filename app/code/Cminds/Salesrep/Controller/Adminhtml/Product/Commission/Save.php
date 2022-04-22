<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Product\Commission;

use Cminds\Salesrep\Model\CommissionGroup;
use Cminds\Salesrep\Model\CommissionGroupFactory;
use Cminds\Salesrep\Model\CommissionGroupRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Commission Group save action
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class Save extends AbstractGroup
{
    protected $resultPageFactory;
    protected $dataPersistor;
    private $commissionGroupFactory;
    private $commissionGroupRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CommissionGroupFactory $commissionGroupFactory,
        CommissionGroupRepository $commissionGroupRepository,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        $this->commissionGroupFactory = $commissionGroupFactory;
        $this->commissionGroupRepository = $commissionGroupRepository;
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();

        if ($data) {
            $id = $this->getRequest()->getParam('entity_id');

            if (!$id) {
                $id = $this->getRequest()->getParam('general')['entity_id'];
            }
            $data['entity_id'] = $id;

            try {
                /** @var CommissionGroup $model */
                $model = $this->commissionGroupFactory->create();

                if ($id) {
                    try {
                        $model = $this->commissionGroupRepository->getById($id);
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage(__('This group no longer exists.'));
                        return $resultRedirect->setPath('*/*/');
                    }
                }
                $model->setData($data);
                $this->commissionGroupRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the commission group.'));
                $this->dataPersistor->clear('commission_group');
                return $this->processReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the commission group.')
                );
            }

            $this->dataPersistor->set('icon', $data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function processReturn(
        CommissionGroup $model, array $data, ResultInterface $resultRedirect
    ): ResultInterface {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue' || $redirect === 'edit') {

            $resultRedirect->setPath('*/*/edit', [
                'entity_id' => $model->getId()
            ]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/product_commission/index');
        }
        return $resultRedirect;
    }
}
