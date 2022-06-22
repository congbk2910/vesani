<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Plugin\Controller\Catalog\Product;

class View
{
    /**
     * Plugin
     *
     * @param \Magento\Catalog\Controller\Product\View $controller
     * @param \Closure $proceed
     */
    public function aroundExecute(
        \Magento\Catalog\Controller\Product\View $controller,
        \Closure $proceed
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get(\Codazon\GoogleAmpManager\Helper\Data::class);
        if ($helper->isAmpPage()) {
            $layout = $helper->getLayout();
            $layout->getUpdate()->addPageHandles(['amp_default', 'amp_catalog_product_view']);
            $result = $proceed();
            $controller->getResponse()->clearBody()->setBody($helper->renderAmpPageContent());
        } else {
            return $proceed();
        }
    }
}
