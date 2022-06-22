<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\ThemeOptions\Controller\Ajax;

class Insta extends \Magento\Framework\App\Action\Action
{
    protected $insta;
    
    protected $resultLayoutFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Codazon\ThemeOptions\Block\Instagramphotos $insta,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->insta = $insta;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
        
    }
    
    public function execute()
    {
        $request = $this->getRequest();
        $resultJson = $this->resultJsonFactory->create();
        $result = [];
        $result['success'] = false;
        $result['html'] = null;
        //if ($request->getPost('post_template')) {
            $params = $request->getParams();
            $params['full_html'] = 1;
            $this->insta->setData($params);
            $result['success'] = true;
            $result['html'] = $this->insta->toHtml();
        //}
        return $resultJson->setJsonData(json_encode($result));
    }
}