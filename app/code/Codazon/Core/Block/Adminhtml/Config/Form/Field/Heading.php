<?php
/**
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Core\Block\Adminhtml\Config\Form\Field;
  
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Codazon\Core\Block\Adminhtml\Config\Form\Field\Variable;
  
class Heading extends \Magento\Config\Block\System\Config\Form\Field
{
    const ROLE_NAME = 'set_param';
    
    protected function _decorateRowHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $html)
    {
        $html = '<tr><td colspan="4"><h4>' . $element->getLabel() . '</h4>';
        if ($comment = $element->getComment()) {
            $html .= '<p class="note" style="padding-left: 2.8rem;">' . $comment . '</p>';
        }
        $html .= '</td></tr>';
        return $html;
    }
  
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }
}