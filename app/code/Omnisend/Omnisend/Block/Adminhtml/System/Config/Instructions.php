<?php

namespace Omnisend\Omnisend\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Omnisend\Omnisend\Model\Config\GeneralConfig;

class Instructions extends Field
{
    const INSTRUCTIONS_TEMPLATE = 'system/config/instructions.phtml';

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @param Context $context
     * @param GeneralConfig $generalConfig
     * @param array $data
     */
    public function __construct(Context $context, GeneralConfig $generalConfig, array $data = [])
    {
        $this->generalConfig = $generalConfig;

        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (!$this->getTemplate()) {
            $this->setTemplate(self::INSTRUCTIONS_TEMPLATE);
        }

        return $this;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->addData([
            'website_url' => $this->generalConfig->getInstructionsUrl()
        ]);

        return $this->_toHtml();
    }
}
