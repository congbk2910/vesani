<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\AjaxCartPro\Controller\Product\Wishlist;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Add cart item to wishlist and remove from cart controller.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Fromcart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var CheckoutCart
     */
    protected $cart;

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param Action\Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param WishlistHelper $wishlistHelper
     * @param CheckoutCart $cart
     * @param CartHelper $cartHelper
     * @param Escaper $escaper
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        WishlistProviderInterface $wishlistProvider,
        WishlistHelper $wishlistHelper,
        CheckoutCart $cart,
        CartHelper $cartHelper,
        Escaper $escaper,
        Validator $formKeyValidator
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->wishlistHelper = $wishlistHelper;
        $this->cart = $cart;
        $this->cartHelper = $cartHelper;
        $this->escaper = $escaper;
        $this->formKeyValidator = $formKeyValidator;
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($context);
    }

    /**
     * Add cart item to wishlist and remove from cart
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $postResult = [
            'success'   =>  false,
        ];
        if (!$this->_customerSession->isLoggedIn()) {
            if ($currentUrl = $this->getRequest()->getParam('currentUrl')) {
                $this->getRequest()->setParam('referer', $this->_objectManager->get('Magento\Framework\Url\EncoderInterface')->encode($currentUrl));
            }
            $layout = $this->resultLayoutFactory->create();
            $layout->addHandle(['ajax_wishlist']);
            $postResult['login_form_html'] = $layout->getLayout()->getOutput();
            return $this->returnResult($postResult);
        }
        
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $postResult['message'] = __('Your session has expired.');
            return $this->returnResult($postResult);
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        try {
            $itemId = (int)$this->getRequest()->getParam('item');
            $item = $this->cart->getQuote()->getItemById($itemId);
            if (!$item) {
                throw new LocalizedException(
                    __("The cart item doesn't exist.")
                );
            }

            $productId = $item->getProductId();
            $buyRequest = $item->getBuyRequest();
            $wishlist->addNewItem($productId, $buyRequest);

            $this->cart->getQuote()->removeItem($itemId);
            $this->cart->save();

            $this->wishlistHelper->calculate();
            $wishlist->save();
            
                       
            $postResult['message'] = __('%1 has been moved to your wish list.', $item->getProduct()->getName());
            $postResult['success'] = true;
        } catch (LocalizedException $e) {
            $postResult['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $postResult['message'] = $e->getMessage();
            $this->messageManager->addExceptionMessage($e, __('We can\'t move the item to the wish list.'));
        }
        return $this->returnResult($postResult);
    }
    
    protected function returnResult($postResult) {
        return $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($postResult)
        );
    }
}
