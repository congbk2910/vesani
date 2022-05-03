<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\EmailTemplate;

use Amasty\Acart\Api\Data\ScheduleEmailTemplateInterface;
use Amasty\Acart\Api\Data\ScheduleInterface;
use Amasty\Acart\Api\ScheduleEmailTemplateRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\RuntimeException;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\TemplateInterface;

class EmailTemplateResolver
{
    /**
     * @var FactoryInterface
     */
    private $templateFactory;

    /**
     * @var ScheduleEmailTemplateRepositoryInterface
     */
    private $emailTemplateRepository;

    public function __construct(
        FactoryInterface $templateFactory,
        ScheduleEmailTemplateRepositoryInterface $emailTemplateRepository
    ) {
        $this->templateFactory = $templateFactory;
        $this->emailTemplateRepository = $emailTemplateRepository;
    }

    public function execute(ScheduleInterface $schedule): TemplateInterface
    {
        if ($templateId = $schedule->getTemplateId()) {
            return $this->templateFactory->get($templateId);
        }

        try {
            /** @var ScheduleEmailTemplateInterface $customTemplate */
            $customTemplate = $this->emailTemplateRepository->getByScheduleId((int)$schedule->getScheduleId());
        } catch (NotFoundException $e) {
            throw new RuntimeException(__('Failed to get email template for schedule item.'));
        }

        return $this->templateFactory->get($customTemplate->getTemplateId(), ScheduleEmailTemplateInterface::class);
    }
}
