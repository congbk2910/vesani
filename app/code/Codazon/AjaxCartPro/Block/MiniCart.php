<?php
/**
 * Copyright © 2016 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\AjaxCartPro\Block;

class MiniCart extends \Magento\Framework\View\Element\Template
{
    
    /* @var \Codazon\ShoppingCartPro\Helper\Data */
    protected $helper;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Codazon\AjaxCartPro\Helper\Data $helper,
        array $data = []
    ){
        $this->helper = $helper;
        parent::__construct($context, $data);
    }
        
    public function getHelper()
    {
        return $this->helper;
    }
}