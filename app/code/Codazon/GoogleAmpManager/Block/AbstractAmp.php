<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Block;

class AbstractAmp extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Codazon\GoogleAmpManager\Helper\Data
     */
    protected $helper;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Codazon\GoogleAmpManager\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }
    
    public function getFooterContentArray()
    {
        $result = [];
        $maxColumns = (int) $this->helper->getConfig('googleampmanager/footer/footer_max_columns');
        $maxColumns = $maxColumns ? $maxColumns : 2;
        for ($i = 1; $i <= $maxColumns; $i++) {
            $result[] = $this->helper->htmlFilter($this->helper->getConfig('googleampmanager/footer/content_' . $i));
        }
        return $result;
    }
    
    public function getFooterBottomContent()
    {
        return $this->helper->htmlFilter($this->helper->getConfig('googleampmanager/footer/bottom_text'));
    }
}
