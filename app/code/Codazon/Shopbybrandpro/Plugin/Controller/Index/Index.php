<?php
/**
 * Copyright © 2021 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Plugin\Controller\Index;

class Index
{
    protected $helper;
    
    const AJAX_PARAM = 'ajax_load';
        
    public function afterExecute(
        \Codazon\Shopbybrandpro\Controller\Index\Index $controller,
        $page
    ){
        $request = $controller->getRequest();
        if ($request->getParam(self::AJAX_PARAM)) {
            $request->setParam(self::AJAX_PARAM, null);
            $request->setQueryValue(self::AJAX_PARAM, null);
            $layout = $page->getLayout();
            $result = [];
            $result['list'] = $layout->getBlock('brand_list')->toHtml();
            $json = \Magento\Framework\App\ObjectManager::getInstance()->create(\Magento\Framework\Controller\Result\JsonFactory::class)->create();
            $json->setData($result);
            return $json;
        } else {
            return $page;
        }
    }
}
