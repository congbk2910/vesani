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

namespace Webkul\AbTesting\Block\Adminhtml\AbTest;

class VariantsContainer extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'abtest/variants-tmpl.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Webkul\AbTesting\Helper\Data $moduleHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        array $data = []
    ) {
        
        $this->_registry = $registry;
        $this->_jsonEncoder = $jsonEncoder;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    /**
     * return current test id
     *
     * @return int
     */
    public function getCurrentTestId()
    {
        return  $testId = $this->getRequest()->getParam("id");
    }

    /**
     * return variant type
     *
     * @param int $testId
     * @return array
     */
    public function getVariantTypeFromTestId($testId)
    {
        return $this->moduleHelper->getAllTrackTypes();
    }

    /**
     * return control url
     *
     * @param int $testId
     * @return int
     */
    public function getControlUrlFromTestId($testId)
    {
        return $this->moduleHelper->getControlUrlFromTestId($testId);
    }
}
