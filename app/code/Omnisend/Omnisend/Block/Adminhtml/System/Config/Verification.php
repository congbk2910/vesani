<?php
namespace Omnisend\Omnisend\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Omnisend\Omnisend\Model\Config\GeneralConfig;

/**
 * Class Verification
 * @package Omnisend\Omnisend\Block\Adminhtml\System\Config
 */
class Verification extends Field
{
    const VERIFICATION_TEMPLATE = 'system/config/verification.phtml';

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @param Context $context
     * @param GeneralConfig $generalConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        GeneralConfig $generalConfig,
        array $data = []
    ) {
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
            $this->setTemplate(self::VERIFICATION_TEMPLATE);
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
            'verification_message' => $this->_getVerificationMessage(),
            'verification_message_color' => $this->_getVerificationMessageColor(),
        ]);

        return $this->_toHtml();
    }

    /**
     * @return string
     */
    private function _getVerificationMessage()
    {
        return $this->generalConfig->getIsVerified() ? 'Your Omnisend account is verified.' : 'Your Omnisend account is not verified.';
    }

    /**
     * @return string
     */
    private function _getVerificationMessageColor()
    {
        return $this->generalConfig->getIsVerified() ? '#00b200' : '#e50000';
    }
}
