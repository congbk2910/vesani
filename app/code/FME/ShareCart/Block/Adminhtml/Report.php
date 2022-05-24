<?php
namespace FME\ShareCart\Block\Adminhtml;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use FME\ShareCart\Helper\Data;

/**
 * Main contact form block
 */
class Report extends Template
{
    protected $_helper;
    protected $_pastOrders;
    protected $_trustpilotLog;
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }
}
