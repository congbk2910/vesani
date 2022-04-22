<?php

namespace Omnisend\Omnisend\Controller\Adminhtml\OrderStatus;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Omnisend\Omnisend\Api\Data\OmnisendOrderStatusInterface;
use Omnisend\Omnisend\Api\OmnisendOrderStatusRepositoryInterface;
use Omnisend\Omnisend\Model\OmnisendOrderStatusFactory;
use RuntimeException;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Backend::content';

    /**
     * @var OmnisendOrderStatusFactory
     */
    protected $omnisendOrderStatusFactory;

    /**
     * @var OmnisendOrderStatusRepositoryInterface
     */
    protected $omnisendOrderStatusRepository;

    /**
     * @param Action\Context $context
     * @param OmnisendOrderStatusRepositoryInterface $omnisendOrderStatusRepository
     * @param OmnisendOrderStatusFactory $omnisendOrderStatusFactory
     */
    public function __construct(
        Context $context,
        OmnisendOrderStatusRepositoryInterface $omnisendOrderStatusRepository,
        OmnisendOrderStatusFactory $omnisendOrderStatusFactory
    ) {
        $this->omnisendOrderStatusRepository = $omnisendOrderStatusRepository;
        $this->omnisendOrderStatusFactory = $omnisendOrderStatusFactory;

        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $omnisendOrderStatus = $this->omnisendOrderStatusFactory->create();
        $statusId = $this->getRequest()->getParam(OmnisendOrderStatusInterface::STATUS);

        if ($statusId) {
            $omnisendOrderStatus = $this->omnisendOrderStatusRepository->getById($statusId);
        }

        $omnisendOrderStatus->setData($data);

        $this->_eventManager->dispatch(
            'omnisend_orderstatus_prepare_save',
            [
                'orderstatus' => $omnisendOrderStatus,
                'request' => $this->getRequest()
            ]
        );

        try {
            $this->omnisendOrderStatusRepository->save($omnisendOrderStatus);
            $this->messageManager->addSuccess(__('You have saved this entry.'));
            $this->_getSession()->setFormData(false);

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        OmnisendOrderStatusInterface::STATUS => $omnisendOrderStatus->getStatus(),
                        '_current' => true
                    ]
                );
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving this entry.'));
        }

        $this->_getSession()->setFormData($data);

        return $resultRedirect->setPath(
            '*/*/edit',
            [OmnisendOrderStatusInterface::STATUS => $this->getRequest()->getParam(OmnisendOrderStatusInterface::STATUS)]
        );
    }
}
