<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\ThemeOptions\Controller\Csp;

class Report extends \Magento\Framework\App\Action\Action
{
    protected $block;
    
	protected $helper;
    
	public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
		parent::__construct($context);
    }
    
    public function execute()
    {
        return $this->getResponse()->representJson(
            json_encode(['now' => date("Y-m-d H:i:s")])
        );
    }
}