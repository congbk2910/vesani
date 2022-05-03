<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Blacklist;

use Amasty\Acart\Api\BlacklistRepositoryInterface;
use Amasty\Acart\Controller\Adminhtml\Blacklist;
use Amasty\Acart\Model\BlacklistFactory;
use Amasty\Acart\Model\Blacklist as BlacklistModel;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Blacklist
{
    public const DATA_PERSISTOR_KEY = 'amasty_acart_blacklist';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;
    /**
     * @var BlacklistFactory
     */
    private $blacklistFactory;

    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        BlacklistRepositoryInterface $blacklistRepository,
        BlacklistFactory $blacklistFactory
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->blacklistRepository = $blacklistRepository;
        $this->blacklistFactory = $blacklistFactory;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $id = (int)$this->getRequest()->getParam(BlacklistModel::BLACKLIST_ID);
                if ($id) {
                    $blacklist = $this->blacklistRepository->getById($id);
                } else {
                    /** @var BlacklistModel $blacklist */
                    $blacklist = $this->blacklistFactory->create();
                }
                $blacklist->setData($data);
                $this->blacklistRepository->save($blacklist);

                $this->messageManager->addSuccessMessage(__('You saved the blacklist record.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->resultRedirectFactory->create()->setPath(
                        'amasty_acart/*/edit',
                        ['id' => $blacklist->getBlacklistId()]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->saveFormDataAndRedirect($data, $id);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the blacklist data. Please review the error log.')
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
