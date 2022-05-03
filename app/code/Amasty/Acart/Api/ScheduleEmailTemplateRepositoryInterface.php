<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api;

use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface;

interface ScheduleEmailTemplateRepositoryInterface
{
    /**
     * @param int $id
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getById(int $id): ScheduleEmailTemplateInterface;

    /**
     * @param int $scheduleId
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getByScheduleId(int $scheduleId): ScheduleEmailTemplateInterface;

    /**
     * @param \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface $schedule
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ScheduleEmailTemplateInterface $schedule): ScheduleEmailTemplateInterface;

    /**
     * @param \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface $schedule
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ScheduleEmailTemplateInterface $schedule): bool;

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
