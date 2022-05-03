<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api;

use Amasty\Acart\Api\Data\HistoryInterface;

interface QueueManagementInterface
{
    /**
     * @param HistoryInterface $history
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function markAsDeleted(HistoryInterface $history): HistoryInterface;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function markAsDeletedById(int $id): HistoryInterface;
}
