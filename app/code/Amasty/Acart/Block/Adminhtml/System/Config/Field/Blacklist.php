<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Block\Adminhtml\System\Config\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Blacklist extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Acart::upload.phtml';

    protected function _getElementHtml(AbstractElement $element): string
    {
        $this->setData('name', $element->getName());
        $this->setData('htmlId', $element->getHtmlId());

        return $this->toHtml();
    }

    public function getName(): string
    {
        return $this->getData('name');
    }

    public function getHtmlId(): string
    {
        return $this->getData('htmlId');
    }
}
