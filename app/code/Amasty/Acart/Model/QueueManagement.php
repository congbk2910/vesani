<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\HistoryInterface;
use Amasty\Acart\Api\HistoryRepositoryInterface;
use Amasty\Acart\Api\QueueManagementInterface;

class QueueManagement implements QueueManagementInterface
{
    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    public function __construct(
        HistoryRepositoryInterface $historyRepository
    ) {
        $this->historyRepository = $historyRepository;
    }

    /**
     * @inheritDoc
     */
    public function markAsDeleted(HistoryInterface $history): HistoryInterface
    {
        $history->setStatus(History::STATUS_ADMIN);
        $this->historyRepository->save($history);

        return $history;
    }

    /**
     * @inheritDoc
     */
    public function markAsDeletedById(int $id): HistoryInterface
    {
        $history = $this->historyRepository->getById($id);

        return $this->markAsDeleted($history);
    }
}
