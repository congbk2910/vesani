<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api;

use Amasty\Acart\Api\Data\HistoryInterface;

interface HistoryRepositoryInterface
{
    /**
     * Get history by ID.
     *
     * @param int $id
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getById(int $id): HistoryInterface;

    /**
     * Get history by ID.
     *
     * @param int $salesRuleId
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getBySalesRuleId(int $salesRuleId): HistoryInterface;

    /**
     * Save history.
     *
     * @param \Amasty\Acart\Api\Data\HistoryInterface $history
     *
     * @return \Amasty\Acart\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(HistoryInterface $history): HistoryInterface;

    /**
     * Delete history.
     *
     * @param \Amasty\Acart\Api\Data\HistoryInterface $history
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(HistoryInterface $history): bool;

    /**
     * Delete history by ID.
     *
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
