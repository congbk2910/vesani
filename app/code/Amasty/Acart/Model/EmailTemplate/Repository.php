<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\EmailTemplate;

use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface;
use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterfaceFactory;
use Amasty\Acart\Api\ScheduleEmailTemplateRepositoryInterface;
use Amasty\Acart\Model\AbstractCachedRepository;
use Amasty\Acart\Model\EmailTemplate as EmailTemplateModel;
use Amasty\Acart\Model\ResourceModel\EmailTemplate as EmailTemplateResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

class Repository extends AbstractCachedRepository implements ScheduleEmailTemplateRepositoryInterface
{
    /**
     * @var ScheduleEmailTemplateInterfaceFactory
     */
    private $emailTemplateFactory;

    /**
     * @var EmailTemplateResource
     */
    private $emailTemplateResource;

    public function __construct(
        ScheduleEmailTemplateInterfaceFactory $emailTemplateFactory,
        EmailTemplateResource $emailTemplateResource
    ) {
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->emailTemplateResource = $emailTemplateResource;
    }

    private function getBy($value, $field = EmailTemplateModel::TEMPLATE_ID): ScheduleEmailTemplateInterface
    {
        if (($result = $this->getFromCache($field, $value)) !== null) {
            return $result;
        }

        /** @var ScheduleEmailTemplateInterface $emailTemplate */
        $emailTemplate = $this->emailTemplateFactory->create();
        $this->emailTemplateResource->load($emailTemplate, $value, $field);
        if (!$emailTemplate->getTemplateId()) {
            throw new NotFoundException(
                __('Email template with specified %1 "%2" not found.', $field, $value)
            );
        }

        return $this->addToCache($field, $value, $emailTemplate);
    }

    public function getById(int $id): ScheduleEmailTemplateInterface
    {
        return $this->getBy($id);
    }

    public function getByScheduleId(int $scheduleId): ScheduleEmailTemplateInterface
    {
        return $this->getBy($scheduleId, EmailTemplateModel::SCHEDULE_ID);
    }

    public function save(ScheduleEmailTemplateInterface $emailTemplate): ScheduleEmailTemplateInterface
    {
        try {
            $this->emailTemplateResource->save($emailTemplate);
            $this->invalidateCache($emailTemplate);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Unable to save the email template. Error: %1', $e->getMessage())
            );
        }

        return $emailTemplate;
    }

    public function delete(ScheduleEmailTemplateInterface $emailTemplate): bool
    {
        try {
            $this->emailTemplateResource->delete($emailTemplate);
            $this->invalidateCache($emailTemplate);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to delete the email template. Error: %1', $e->getMessage())
            );
        }

        return true;
    }

    public function deleteById(int $id): bool
    {
        $this->delete($this->getById($id));

        return true;
    }
}
