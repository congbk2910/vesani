<?php
/**
 * Copyright © 2019 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Codazon\AjaxCartPro\Controller\Product\Compare;

use Magento\Framework\Controller\ResultFactory;

class Clear extends \Codazon\AjaxCartPro\Controller\Product\Compare
{
    /**
     * Remove item from compare list
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postResult = [
            'success' => false,
            'message' => __('Something went wrong  clearing the comparison list.')
        ];
        
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection $items */
        $items = $this->_itemCollectionFactory->create();

        if ($this->_customerSession->isLoggedIn()) {
            $items->setCustomerId($this->_customerSession->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId($this->_customerVisitor->getId());
        }

        try {
            $items->clear();
            $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();
            $postResult['message'] = __('You cleared the comparison list.');
            $postResult['success'] = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $postResult['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $postResult['message'] = __('Something went wrong  clearing the comparison list.');
        }
        
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($postResult)
        );
    }
}
