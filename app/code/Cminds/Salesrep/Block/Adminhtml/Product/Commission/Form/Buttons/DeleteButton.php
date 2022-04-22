<?php

namespace Cminds\Salesrep\Block\Adminhtml\Product\Commission\Form\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Commission Group Delete Button
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Delete button
     *
     * @return array
     */
    public function getButtonData()
    {
        if ($this->getCommissionGroup()) {
            return [
                'id' => 'delete',
                'label' => __('Delete'),
                'on_click' => "deleteConfirm('" .__('Are you sure you want to delete this commission group?') ."', '"
                    . $this->getDeleteUrl() . "', {data: {}})",
                'class' => 'delete',
                'sort_order' => 2
            ];
        }

        return [];
    }

    /**
     * @param array $args
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        $params = array_merge($this->getDefaultUrlParams(), $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }
}
