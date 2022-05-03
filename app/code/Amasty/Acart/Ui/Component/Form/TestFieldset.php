<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;

class TestFieldset extends Fieldset
{
    public const RULE_ID = 'id';

    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $componentVisible = $context->getRequestParam(self::RULE_ID) ? true : false;

        if (isset($data['config']) && !$componentVisible) {
            $data['config']['componentDisabled'] = true;
        }

        parent::__construct($context, $components, $data);
    }
}
