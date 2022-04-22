<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Block\Adminhtml\Analysis;

class TestActionList extends \Magento\Backend\Block\Widget\Container
{
    /**
     * prepare layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_test',
            'label' => __('Add Test'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
            'options' => $this->_getCustomActionListOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'CustomActionList' split button
     *
     * @return array
     */
    protected function _getCustomActionListOptions()
    {
        /*list of button which you want to add*/
        $splitButtonOptions=[
        'action_1'=>['label'=>__('Ab Test'),'onclick'=>"setLocation('" . $this->_getAbTestUrl() . "')"],
        'action_2'=>['label'=>__('Split Test'),'onclick'=>"setLocation('" . $this->_getCreateUrl() . "')"]
        ];
        /* in above list you can also pass others attribute of buttons*/
        return $splitButtonOptions;
    }

    /**
     * return split test create url
     *
     * @return void
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            'abtesting/abtest/index/type/split'
        );
    }

    /**
     * return abtest create url
     *
     * @return void
     */
    protected function _getAbTestUrl()
    {
        return $this->getUrl(
            'abtesting/abtest/index/type/ab'
        );
    }
}
