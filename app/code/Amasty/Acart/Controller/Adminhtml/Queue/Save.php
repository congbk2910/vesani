<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Queue;

use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Controller\Adminhtml\Queue;
use Amasty\Acart\Model\History as HistoryModel;
use Amasty\Acart\Model\HistoryFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Queue
{
    public const DATA_PERSISTOR_KEY = 'amasty_acart_queue';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        HistoryRepositoryInterface $historyRepository,
        HistoryFactory $historyFactory
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->historyRepository = $historyRepository;
        $this->historyFactory = $historyFactory;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                if ($id = (int)$this->getRequest()->getParam(HistoryModel::HISTORY_ID)) {
                    $history = $this->historyRepository->getById($id);
                } else {
                    $history = $this->historyFactory->create();
                }
                $history->setData($data);
                $this->historyRepository->save($history);
                $this->messageManager->addSuccessMessage(__('You saved the queue item.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->resultRedirectFactory->create()->setPath(
                        'amasty_acart/*/edit',
                        ['id' => $history->getHistoryId()]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->saveFormDataAndRedirect($data, $id);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the queue data. Please review the error log.')
                );

                return $this->saveFormDataAndRedirect($data, $id);
            }
        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/');
    }

    private function saveFormDataAndRedirect(array $data, int $id)
    {
        $this->dataPersistor->set(self::DATA_PERSISTOR_KEY, $data);

        $resultRedirect = $this->resultRedirectFactory->create();
        if (!empty($id)) {
            $resultRedirect->setPath('amasty_acart/*/edit', ['id' => $id]);
        } else {
            $resultRedirect->setPath('amasty_acart/*/index');
        }

        return $resultRedirect;
    }
}
