<?php
/**
 *
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Controller\Amphandle\Data;

use \Codazon\GoogleAmpManager\Model\AmpConfig;

class Export extends \Magento\Framework\App\Action\Action
{   
	protected $resultLayoutFactory;
    
    protected $resultPageFactory;
    
    protected $helper;
    
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Codazon\GoogleAmpManager\Helper\Export $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
		parent::__construct($context);
    }
    
    protected function displayMessage($messages) {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            echo "<div style='margin-bottom: 10px;'>{$message}</div>";
        }
    }
    
    public function execute()
    {
        $result = $this->helper->exportLessDefaultVariablesFile();
        $this->displayMessage($result['messages']);
        $result = $this->helper->exportDefaultConfigFile();
        $this->displayMessage($result['messages']);
        $result = $this->helper->exportCmsPageAmp();
        $this->displayMessage($result['messages']);
    }
}