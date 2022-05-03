<?php

namespace Amasty\Geoip\Block\Adminhtml;

class Template extends \Magento\Backend\Block\Template
{
    /**
     * @var \Amasty\Geoip\Helper\Data
     */
    public $geoipHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\Geoip\Helper\Data $geoipHelper
    ) {
        parent::__construct($context);

        $this->geoipHelper = $geoipHelper;
    }
}
