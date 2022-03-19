<?php
/**
 * Copyright © Codazon 2019, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\AjaxCartPro\Controller\Product\Wishlist;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateItemOptions extends \Magento\Wishlist\Controller\AbstractIndex
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param Action\Context $context
     * @param Session $customerSession
     * @param WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Action\Context $context,
        Session $customerSession,
        WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Action to accept new configuration for a wishlist item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $postResult = [
            'success'   =>  false,
        ];
        
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $postResult['message'] = __('Your session has expired.');
            return $this->returnResult($postResult);
        }

        $productId = (int)$this->getRequest()->getParam('product');
        if (!$productId) {
             $postResult['message'] = __('We can\'t specify a product.');
            return $this->returnResult($postResult);
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            $postResult['message'] = __('We can\'t specify a product.');
            return $this->returnResult($postResult);
        }

        try {
            $id = (int)$this->getRequest()->getParam('id');
            /* @var \Magento\Wishlist\Model\Item */
            $item = $this->_objectManager->create(\Magento\Wishlist\Model\Item::class);
            $item->load($id);
            $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
            if (!$wishlist) {
                $postResult['message'] = __('We can\'t specify a wish list.');
                return $this->returnResult($postResult);
            }

            $buyRequest = new \Magento\Framework\DataObject($this->getRequest()->getParams());

            $wishlist->updateItem($id, $buyRequest)->save();

            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();
            $this->_eventManager->dispatch(
                'wishlist_update_item',
                ['wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id)]
            );
            
            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();

            $postResult['message'] = __('%1 has been updated in your Wish List.', $product->getName());
            $postResult['success'] = true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $postResult['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $postResult['message'] = __('We can\'t update your Wish List right now.');
        }
        return $this->returnResult($postResult);
    }
    
    protected function returnResult($postResult) {
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($postResult)
        );
    }
}
