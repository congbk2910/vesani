<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Plugin\Controller\Adminhtml\Cms\Page;

class Save
{
    /**
     * Plugin
     *
     * @param \Magento\Catalog\Controller\Product\View $controller
     * @param \Closure $proceed
     */
    public function afterExecute(
        \Magento\Cms\Controller\Adminhtml\Page\Save $controller,
        $result
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get(\Codazon\GoogleAmpManager\Helper\Data::class);
        if ($page = $helper->getCoreRegistry()->registry('cms_page')) {
            if ($pageId = (int)$page->getId()) {
                $ampContent = $helper->getRequest()->getParam('amp_content');
                $options = $helper->getRequest()->getParam('options');
                if (is_array($options)) {
                    $options = json_encode($options);
                }
                $ampModel = $objectManager->create(\Codazon\GoogleAmpManager\Model\Page::class);
                $ampModel->load($pageId, 'page_id');
                $ampModel->addData([
                    'page_id'       => $pageId,
                    'amp_content'   => $ampContent,
                    'options'       => $options
                ]);
                $ampModel->save();
            }
        }
        return $result;
    }
}
