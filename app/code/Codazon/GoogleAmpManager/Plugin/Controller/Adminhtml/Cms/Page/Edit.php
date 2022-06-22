<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Plugin\Controller\Adminhtml\Cms\Page;

class Edit
{
    public function aroundExecute(
        \Magento\Cms\Controller\Adminhtml\Page\Edit $controller,
        \Closure $proceed
    ) {
        /* $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get(\Codazon\GoogleAmpManager\Helper\Data::class);
        $result = $proceed();
        
       if ($page = $helper->getCoreRegistry()->registry('cms_page')) {
            if ($pageId = $page->getId()) {
                $ampModel = $objectManager->create(\Codazon\GoogleAmpManager\Model\Page::class)->load($pageId, 'page_id');
                if ($ampModel->getId()) {
                    $page->setData('amp_content', $ampModel->getData('amp_content'));
                }
                $objectManager->get(\Magento\Framework\App\Request\DataPersistor::class)
                    ->set('cms_page', $page->getData());
            }
        }
        return $result; */
        return $proceed();
    }
}
