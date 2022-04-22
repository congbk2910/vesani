<?php

namespace Omnisend\Omnisend\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Disable
 * @package Omnisend\Omnisend\Block\Adminhtml\System\Config\From\Field
 */
class Disable extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('disabled', 'disabled');
        return $element->getElementHtml();
    }
}
