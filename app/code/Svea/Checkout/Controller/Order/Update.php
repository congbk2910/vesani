<?php

namespace Svea\Checkout\Controller\Order;

use Svea\Checkout\Model\CheckoutException;

abstract class Update extends \Svea\Checkout\Controller\Checkout
{
    //ajax updates
    protected function _sendResponse($blocks = null, $updateCheckout = true)
    {
        $response = [];

        //reload the blocks even we have an error
        if (is_null($blocks)) {
            $blocks = ['shipping_method','cart','coupon','messages', 'svea','newsletter'];
        } elseif ($blocks) {
            $blocks = (array)$blocks;
        } else {
            $blocks = [];
        }

        if (!in_array('messages', $blocks)) {
            $blocks[] = 'messages';
        }

        $shouldUpdateSvea = false;
        if ($updateCheckout) {
            $key = array_search('svea', $blocks);
            if ($key !== false) {
                $shouldUpdateSvea = true;
                unset($blocks[$key]); //this will be set later
            }
        }

        $checkout = $this->getSveaCheckout();
        $checkout->setCheckoutContext($this->sveaCheckoutContext);

        if ($updateCheckout) {  //if blocks contains only "messages" do not update
            $sveaPaymentId = null;
            try {
                $checkout = $checkout->initCheckout();

                //set new quote signature
                $response['ctrlkey'] = $checkout->getQuoteSignature();

                if ($shouldUpdateSvea) {
                    //update svea iframe
                    $sveaPaymentId = $this->getCheckoutSession()->getSveaOrderId();

                    $checkout->updateSveaPayment($sveaPaymentId);
                    $response['ctrlkey'] = $checkout->getQuoteSignature();
                }
            } catch (CheckoutException $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    $e->getMessage()
                );
                if ($e->isReload()) {
                    $response['reload'] = 1;
                    $response['messages'] = $e->getMessage();
                    $this->messageManager->addNoticeMessage($e->getMessage());
                } elseif ($e->getRedirect()) {
                    $response['redirect'] = $e->getRedirect();
                    $response['messages'] = $e->getMessage();
                    $this->messageManager->addErrorMessage($e->getMessage());
                } else {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                //do nothing, we will just show the message
                $this->messageManager->addErrorMessage($e->getMessage() ? $e->getMessage() : __('Cannot update checkout (%1)', get_class($e)));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage() ? $e->getMessage() : __('Cannot initialize Svea Checkout (%1)', get_class($e)));
            }

            if (!empty($response['redirect'])) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    $response['redirect'] = $this->storeManager->getStore()->getUrl($response['redirect']);
                    $this->getResponse()->setBody(json_encode($response));
                } else {
                    $this->_redirect($response['redirect']);
                }
                return;
            }

            /*
            if($shouldUpdateSvea &&  (empty($updatedSveaPaymentId) || $updatedSveaPaymentId != $sveaPaymentId)) {
                //another svea order was created, add svea block (need to be reloaded)
                $blocks[] = 'svea';
                //if svea have same location, we will use svea api resume
            }
            */
        }

        $response['ok'] = true; //to avoid empty response

        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect('*');
            return;
        }

        $response['ok'] = true;
        if ($blocks) {
            $this->_view->loadLayout('svea_checkout_order_update');
            foreach ($blocks as $id) {
                $name = "svea_checkout.{$id}";
                $block = $this->_view->getLayout()->getBlock($name);
                if ($block) {
                    $response['updates'][$id] = $block->toHtml();
                }
            }
        }

        if (in_array('svea_snippet', $blocks)) {
            $response['updates']['svea'] = '<div id="svea-checkout_svea">' . $checkout->getSveaPaymentHandler()->getIframeSnippet() . '</div>';
        }

        $this->getResponse()->setBody(json_encode($response));
    }
}
