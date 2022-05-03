<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\EmailTemplate\ProcessorPool;

use Magento\Email\Model\ResourceModel\Template as TemplateResource;
use Magento\Framework\Mail\TemplateInterface;

class DefaultLegacyTemplateProcessor implements LegacyTemplateProcessorInterface
{
    /**
     * @var TemplateResource
     */
    private $templateResource;

    public function __construct(
        TemplateResource $templateResource
    ) {
        $this->templateResource = $templateResource;
    }

    public function execute(TemplateInterface $template)
    {
        $this->templateResource->load($template, $template->getId());
        if (!$template->getData('is_legacy')) {
            $template->setData('is_legacy', 1);
            $this->templateResource->save($template);
        }
    }
}
