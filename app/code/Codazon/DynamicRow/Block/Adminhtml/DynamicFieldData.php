<?php
namespace Codazon\DynamicRow\Block\Adminhtml;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Codazon\DynamicRow\Block\Adminhtml\Form\Field\CustomColumn;
class DynamicFieldData extends AbstractFieldArray {
private $dropdownRenderer;
    protected function _prepareToRender() {
        $this->addColumn('subject',['label' => __('Subject'),'class' => 'required-entry',]);
        $this->addColumn('value_subject',['label' => __('value'),'class' => 'required-entry',]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
    protected function _prepareArrayRow(DataObject $row) {
        $options = [];
        $dropdownField = $row->getDropdownField();
        if ($dropdownField !== null) {
                $options['option_' . $this->getDropdownRenderer()->calcOptionHash($dropdownField)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
    private function getDropdownRenderer() {
        if (!$this->dropdownRenderer) {
                $this->dropdownRenderer = $this->getLayout()->createBlock(
                    CustomColumn::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                );
        }
        return $this->dropdownRenderer;
    }
}