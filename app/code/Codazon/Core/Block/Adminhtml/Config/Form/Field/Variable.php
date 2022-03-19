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

class Variable extends \Magento\Config\Block\System\Config\Form\Field
{  
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $html = ' <div style="display:none" role="' . VariablesCollector::ROLE_NAME .'" data-paramid="'.$fieldConfig['id'].'" data-target="' . $element->getHtmlId() . '"></div>';
        return $html . parent::_getElementHtml($element);
    }
}