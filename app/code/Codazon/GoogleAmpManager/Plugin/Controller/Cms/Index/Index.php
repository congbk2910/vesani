<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Plugin\Controller\Cms\Index;

class Index
{
    /**
     * Plugin
     *
     * @param \Magento\Cms\Controller\Index\Index $controller
     * @param \Closure $proceed
     */
    public function aroundExecute(
        \Magento\Cms\Controller\Index\Index $controller,
        \Closure $proceed
    ) {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(\Codazon\GoogleAmpManager\Helper\Data::class);
        if ($helper->isAmpPage()) {
            $layout = $helper->getLayout();
            $layout->getUpdate()->addPageHandles(['amp_default', 'amp_cms_index_index']);
            $result = $proceed();
            $controller->getResponse()->clearBody()->setBody($helper->renderAmpPageContent());
        } else {
            return $proceed();
        }
    }
}
