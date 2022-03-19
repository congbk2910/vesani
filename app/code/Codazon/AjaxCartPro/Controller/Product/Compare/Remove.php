<?php
/**
 * Copyright © 2019 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Codazon\AjaxCartPro\Controller\Product\Compare;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\LayoutFactory;

class Remove extends \Codazon\AjaxCartPro\Controller\Product\Compare
{
    protected $resultLayoutFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Visitor $customerVisitor,
        \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $compareItemFactory, $itemCollectionFactory, $customerSession,$customerVisitor, $catalogProductCompareList, $catalogSession,
            $storeManager, $formKeyValidator, $resultPageFactory, $productRepository);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }
    /**
     * Remove item from compare list
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postResult = [
            'success' => false,
            'message' => __('We can\'t remove the item from comparison list right now.')
        ];
        
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if ($product) {
                /** @var $item \Magento\Catalog\Model\Product\Compare\Item */
                $item = $this->_compareItemFactory->create();
                if ($this->_customerSession->isLoggedIn()) {
                    $item->setCustomerId($this->_customerSession->getCustomerId());
                } elseif ($this->_customerId) {
                    $item->setCustomerId($this->_customerId);
                } else {
                    $item->addVisitorId($this->_customerVisitor->getId());
                }

                $item->loadByProduct($product);
                /** @var $helper \Magento\Catalog\Helper\Product\Compare */
                $helper = $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class);
                if ($item->getId()) {
                    $item->delete();
                    $productName = $this->_objectManager->get(\Magento\Framework\Escaper::class)
                        ->escapeHtml($product->getName());
                    $this->_eventManager->dispatch(
                        'catalog_product_compare_remove_product',
                        ['product' => $item]
                    );
                    $helper->calculate();
                    $postResult['message'] = __('You removed product %1 from the comparison list.', $productName);
                    $postResult['success'] = true;
                    if ($this->getRequest()->getParam('isCompareIndexPage')) {
                        $resultLayout = $this->resultLayoutFactory->create(true);
                        $resultLayout->addHandle(['ajax_compare_list']);
                        $compareList = $resultLayout->getLayout()->getOutput();
                        if ($compareList) {
                            $postResult['compare_list_html'] =  $compareList;
                        }
                    }
                }
            }
        }
        
        return $this->returnResult($postResult);
    }
    
    protected function returnResult($postResult) {
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($postResult)
        );
    }
}
