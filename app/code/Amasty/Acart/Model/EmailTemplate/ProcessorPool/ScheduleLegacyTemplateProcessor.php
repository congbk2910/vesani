<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\EmailTemplate\ProcessorPool;

use Amasty\Acart\Api\ScheduleEmailTemplateRepositoryInterface;
use Magento\Framework\Mail\TemplateInterface;

class ScheduleLegacyTemplateProcessor implements LegacyTemplateProcessorInterface
{
    /**
     * @var ScheduleEmailTemplateRepositoryInterface
     */
    private $emailTemplateRepository;

    public function __construct(
        ScheduleEmailTemplateRepositoryInterface $emailTemplateRepository
    ) {
        $this->emailTemplateRepository = $emailTemplateRepository;
    }

    public function execute(TemplateInterface $template)
    {
        $template = $this->emailTemplateRepository->getById((int)$template->getId());
        if (!$template->getData('is_legacy')) {
            $template->setData('is_legacy', 1);
            $this->emailTemplateRepository->save($template);
        }
    }
}
