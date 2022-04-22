<?php

namespace Cminds\Salesrep\Block\Adminhtml\Product\Commission\Form\Buttons;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Commission Group Save Button
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Save button
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 4,
        ];
    }
}
