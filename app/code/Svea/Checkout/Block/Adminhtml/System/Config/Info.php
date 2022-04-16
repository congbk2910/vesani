<?php

namespace Svea\Checkout\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Info extends Template implements RendererInterface
{
    /**
     * @var string
     */
    protected
        $_template = 'Svea_Checkout::svea.phtml';

    /**
     * Render element html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public
    function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $elementOriginalData = $element->getOriginalData();
        return $this->toHtml();
    }
}