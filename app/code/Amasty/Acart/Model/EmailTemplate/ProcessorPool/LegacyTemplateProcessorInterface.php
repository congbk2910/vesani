<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\EmailTemplate\ProcessorPool;

use Magento\Framework\Mail\TemplateInterface;

interface LegacyTemplateProcessorInterface
{
    public function execute(TemplateInterface $template);
}
