<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Email extends Field
{
    protected function _getElementHtml(AbstractElement $element): string
    {
        $js = '<script type="text/javascript">
            require([
                "Magento_Ui/js/lib/view/utils/async",
                "Amasty_Acart/js/form/tag-it"
            ], function ($) {
                "use strict";

                var input = "#' . $element->getHtmlId() . '";
                $.async(input, (function() {
                    $(input).tagit();
                }));
            });
            </script>';
        $element->setAfterElementJs($js);
        $element->setData('class', 'amacart-input');

        return parent::_getElementHtml($element);
    }
}
