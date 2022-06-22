<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\ThemeOptions\Controller\Ajax;

class Captcha extends \Magento\Framework\App\Action\Action
{  
	protected $helper;   
	public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
		parent::__construct($context);
    }
    
    public function execute()
    {
        $captcha = $this->_objectManager->create(\Magento\Captcha\Block\Captcha::class);
        $captcha->setFormId('user_login')->setImgWidth(230)->setImgHeight(50);
        $html = $captcha->setData('cache_lifetime', false)->toHtml();
        echo $html ? $html."<br />" : '';
        die();
    }
}