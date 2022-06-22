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
  
class Separator extends \Magento\Config\Block\System\Config\Form\Field
{
    const ROLE_NAME = 'set_param';
    
    protected function _decorateRowHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $html)
    {
        return '<tr><td colspan="4"><div style="width: 100%; border-bottom: 1px dashed #cccccc; margin-top: 20px"></td></tr>';
    }
  
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '';
    }
}