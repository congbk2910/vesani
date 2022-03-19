<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Controller;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ObjectManager;
use Codazon\GoogleAmpManager\Helper\Data as AmpHelper;

class Router implements \Magento\Framework\App\RouterInterface
{
    protected $actionFactory;
    
    protected $helper;
    
    protected $allowedPaths = [
        'checkout/sidebar/updateItemQty',
        'checkout/sidebar/removeItem'
    ];
    
    protected function allowAutoFormKey($request)
    {
        $pathInfo = $request->getPathInfo();
        foreach ($this->allowedPaths as $path) {
            if (stripos($pathInfo, $path) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if ($request->getFrontName() == 'amp') {
            $objectManager = ObjectManager::getInstance();
            if ($objectManager->get(AmpHelper::class)->enableGoogleAmp()) {
                $pathInfo = $request->getOriginalPathInfo();
                if (stripos($pathInfo, "amphandle") !== false) {
                    $request->setModuleName('ampmanager');
                    $path = explode('/', $pathInfo);
                    $request->setControllerName($path[2]);
                    $request->setActionName($path[3]);
                    if ($request->isPost()) {
                        $request->setParam('form_key', $objectManager->get(\Magento\Framework\Data\Form\FormKey::class)->getFormKey());
                    }
                    return null;
                }
                $pathInfo = substr($pathInfo, strlen('/amp'));
                if (!empty($pathInfo)) {
                    $request->setParam(AmpHelper::AMP_PARAM, 1);
                    $request->setPathInfo($pathInfo);
                } else {
                    $request->setParam(AmpHelper::AMP_PARAM, 1);
                    $request->setPathInfo('cms/index/index');
                }
            }
        } else {
            if ($request->getParam(AmpHelper::AMP_PARAM) && $request->isPost() && $this->allowAutoFormKey($request)) {
                if (ObjectManager::getInstance()->get(AmpHelper::class)->enableGoogleAmp()) {
                    $request->setParam('form_key', ObjectManager::getInstance()->get(\Magento\Framework\Data\Form\FormKey::class)->getFormKey());
                }
            }
        }
        return null;
    }
}
