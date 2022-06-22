<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Plugin\Model\Cms\Page;

use Magento\Framework\App\ObjectManager;

class DataProvider
{    
    protected $loadedData;
    
    public function afterGetData(
        \Magento\Cms\Model\Page\DataProvider $dataProvider,
        $result
    ) {
        if ($this->loadedData === null) {
            $objectManager = ObjectManager::getInstance();
            $helper = $objectManager->get(\Codazon\GoogleAmpManager\Helper\Data::class);
            if ($pageId = $helper->getRequest()->getParam('page_id')) {
                if (isset($result[$pageId])) {
                    $ampModel = $objectManager->create(\Codazon\GoogleAmpManager\Model\Page::class)->load($pageId, 'page_id');
                    $options = $ampModel->getData('options') ? : '{}';
                    $result[$pageId]['amp_content'] = $ampModel->getData('amp_content');
                    $result[$pageId]['options'] = json_decode($options, true);
                }
            }
            $this->loadedData = $result;
        }
        return $this->loadedData;
    }
}
