<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface;
use Amasty\Acart\Model\ResourceModel\EmailTemplate as EmailTemplateResource;
use Magento\Email\Model\Template as ModelTemplate;

class EmailTemplate extends ModelTemplate implements ScheduleEmailTemplateInterface
{
    public const TEMPLATE_ID = 'template_id';
    public const SCHEDULE_ID = 'schedule_id';
    public const TEMPLATE_CODE = 'template_code';
    public const TEMPLATE_TEXT = 'template_text';
    public const TEMPLATE_STYLES = 'template_styles';
    public const TEMPLATE_TYPE = 'template_type';
    public const TEMPLATE_SUBJECT = 'template_subject';
    public const ORIG_TEMPLATE_VARIABLES = 'orig_template_variables';
    public const IS_LEGACY = 'is_legacy';

    /**
     * Initialize email template model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(EmailTemplateResource::class);
        $this->setIdFieldName(self::TEMPLATE_ID);
    }

    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    public function setTemplateId($templateId): ScheduleEmailTemplateInterface
    {
        $this->setData(self::TEMPLATE_ID, $templateId);

        return $this;
    }

    public function getScheduleId(): int
    {
        return (int)$this->getData(self::SCHEDULE_ID);
    }

    public function setScheduleId(int $scheduleId): ScheduleEmailTemplateInterface
    {
        $this->setData(self::SCHEDULE_ID, $scheduleId);

        return $this;
    }

    public function getTemplateText(): string
    {
        return $this->getData(self::TEMPLATE_TEXT);
    }

    public function setTemplateText(string $templateText): ScheduleEmailTemplateInterface
    {
        $this->setData(self::TEMPLATE_TEXT, $templateText);

        return $this;
    }

    public function getTemplateCode(): ?string
    {
        return $this->getData(self::TEMPLATE_CODE);
    }

    public function setTemplateCode(string $templateCode): ScheduleEmailTemplateInterface
    {
        $this->setData(self::TEMPLATE_CODE, $templateCode);

        return $this;
    }

    public function getTemplateStyles(): ?string
    {
        return $this->getData(self::TEMPLATE_STYLES);
    }

    public function setTemplateStyles(?string $templateStyles): ScheduleEmailTemplateInterface
    {
        $this->setData(self::TEMPLATE_STYLES, $templateStyles);

        return $this;
    }

    public function getTemplateType(): ?int
    {
        return $this->getData(self::TEMPLATE_TYPE) !== null
            ? (int)$this->getData(self::TEMPLATE_TYPE)
            : null;
    }

    public function setTemplateType(?int $templateType): ScheduleEmailTemplateInterface
    {
        $this->setData(self::TEMPLATE_TYPE, $templateType);

        return $this;
    }

    public function getTemplateSubject(): string
    {
        return $this->getData(self::TEMPLATE_SUBJECT);
    }

    public function setTemplateSubject(string $templateSubject): ScheduleEmailTemplateInterface
    {
        $this->setData(self::TEMPLATE_SUBJECT, $templateSubject);

        return $this;
    }

    public function getOrigTemplateVariables(): ?string
    {
        return $this->getData(self::ORIG_TEMPLATE_VARIABLES);
    }

    public function setOrigTemplateVariables(?string $origTemplateVariables): ScheduleEmailTemplateInterface
    {
        $this->setData(self::ORIG_TEMPLATE_VARIABLES, $origTemplateVariables);

        return $this;
    }

    public function getIsLegacy(): bool
    {
        return (bool)$this->getData(self::IS_LEGACY);
    }

    public function setIsLegacy(bool $isLegacy): ScheduleEmailTemplateInterface
    {
        $this->setData(self::IS_LEGACY, $isLegacy);

        return $this;
    }
}
