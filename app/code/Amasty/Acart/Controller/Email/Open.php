<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Controller\Email;

use Amasty\Acart\Api\Data\HistoryInterface;
use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Model\ResourceModel\History\CollectionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\PageCache\NotCacheableInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class Open implements ActionInterface, NotCacheableInterface
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    public function __construct(
        ResultFactory $resultFactory,
        RequestInterface $request,
        CollectionFactory $historyCollectionFactory,
        HistoryRepositoryInterface $historyRepository
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->historyRepository = $historyRepository;
    }

    public function execute()
    {
        try {
            if ($uid = $this->request->getParam('uid')) {
                $historyCollection = $this->historyCollectionFactory->create();
                $historyCollection->addFieldToFilter('main_table.public_key', $uid)
                    ->setCurPage(1)
                    ->setPageSize(1);
                /** @var HistoryInterface $history */
                $history = $historyCollection->getFirstItem();

                if ($history->getHistoryId()) {
                    $history->setOpenedCount($history->getOpenedCount() + 1);
                    $this->historyRepository->save($history);
                }
            }
        } catch (\Exception $e) {
            null;
        }

        $base64Pixel = 'R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setHeader('Content-Type', 'image/gif');
        $resultRaw->setHeader('Content-Length', strlen($base64Pixel));
        $resultRaw->setContents($base64Pixel);

        return $resultRaw;
    }
}
