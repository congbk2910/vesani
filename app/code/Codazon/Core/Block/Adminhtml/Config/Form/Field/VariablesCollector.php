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
  
class VariablesCollector extends \Magento\Config\Block\System\Config\Form\Field
{
    const ROLE_NAME = 'set_param';
    
    protected function _decorateRowHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $html)
    {
        return '<tr style="display:none" id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
  
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $after = '';
        $htmlId = $element->getHtmlId();
        $html = '<script>
        window.addEventListener("load", function() {
            require(["jquery"], function($) {
                var params = {}, $field = $("#'. $htmlId . '"), initialParams = {};
                $("[role=' . self::ROLE_NAME . ']").each(function() {
                    var $picker = $(this), $input = $("#" + $picker.data("target")), id = $picker.data("paramid") ? $picker.data("paramid") : $input.attr("id"), inputId = $input.attr("id");
                    if (!$input.is(":disabled")) {
                        params[id] =  $input.val();
                    }
                    var getParam = function(e) {
                        if ($input.is(":disabled")) {
                            params[id] = undefined;
                        } else {
                            params[id] = $input.val();
                        }
                        $field.val(JSON.stringify(params));
                    }
                    $picker.on("change", getParam);
                    $input.on("change", getParam);
                    $("#" + inputId + "_inherit").on("change", getParam);
                });
                $field.val(JSON.stringify(params));
                initialParams = $.extend({}, params);
                $field.prop("disabled", false);
                $("#" + $field.attr("id") + "_inherit").prop("checked", false);
            });
        });
        </script>
        ';
        return parent::_getElementHtml($element) . $html;
    }
}