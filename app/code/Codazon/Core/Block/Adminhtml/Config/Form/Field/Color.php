<?php
/**
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Core\Block\Adminhtml\Config\Form\Field;
  
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Codazon\Core\Block\Adminhtml\Config\Form\Field\VariablesCollector;
  
class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId = $element->getHtmlId();
        $colorPickerId = 'color_picker_' . $htmlId;
        $attributes = $element->serialize(['disabled']);
        $html = ' <input '.$attributes.' role="' . VariablesCollector::ROLE_NAME . '" type="color" data-paramid="'.$fieldConfig['id'].'" data-target="' . $htmlId . '" id="' . $colorPickerId . '" value="' . $element->getValue() . '" onchange="'.$htmlId.'.value = this.value; '.$htmlId.'.style.backgroundColor = this.value" />';
        $html .= "<script>if({$htmlId}.value){{$htmlId}.style.backgroundColor = {$htmlId}.value;}{$htmlId}.addEventListener('change', function() {{$colorPickerId}.value = this.value; this.style.backgroundColor = this.value});</script><style>#{$htmlId}{max-width: 200px}</style>";
        return parent::_getElementHtml($element) . $html;
    }
}