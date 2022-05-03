<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Api\Data;

interface ScheduleEmailTemplateInterface
{
    /**
     * @return int|null
     */
    public function getTemplateId();

    /**
     * @param int|null $templateId
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setTemplateId($templateId): ScheduleEmailTemplateInterface;

    /**
     * @return int
     */
    public function getScheduleId(): int;

    /**
     * @param int $scheduleId
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setScheduleId(int $scheduleId): ScheduleEmailTemplateInterface;

    /**
     * @return string|null
     */
    public function getTemplateCode(): ?string;

    /**
     * @param string $templateCode
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setTemplateCode(string $templateCode): ScheduleEmailTemplateInterface;

    /**
     * @return string
     */
    public function getTemplateText(): string;

    /**
     * @param string $templateText
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setTemplateText(string $templateText): ScheduleEmailTemplateInterface;

    /**
     * @return string|null
     */
    public function getTemplateStyles(): ?string;

    /**
     * @param string|null $templateStyles
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setTemplateStyles(?string $templateStyles): ScheduleEmailTemplateInterface;

    /**
     * @return int|null
     */
    public function getTemplateType(): ?int;

    /**
     * @param int|null $templateType
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setTemplateType(?int $templateType): ScheduleEmailTemplateInterface;

    /**
     * @return string
     */
    public function getTemplateSubject(): string;

    /**
     * @param string $templateSubject
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setTemplateSubject(string $templateSubject): ScheduleEmailTemplateInterface;

    /**
     * @return string|null
     */
    public function getOrigTemplateVariables(): ?string;

    /**
     * @param string|null $origTemplateVariables
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setOrigTemplateVariables(?string $origTemplateVariables): ScheduleEmailTemplateInterface;

    /**
     * @return bool
     */
    public function getIsLegacy(): bool;

    /**
     * @param bool $hours
     *
     * @return \Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface
     */
    public function setIsLegacy(bool $isLegacy): ScheduleEmailTemplateInterface;
}
