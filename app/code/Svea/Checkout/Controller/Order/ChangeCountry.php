<?php
namespace Svea\Checkout\Controller\Order;

use Magento\Quote\Model\Quote;

class ChangeCountry extends \Svea\Checkout\Controller\Order\Update
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect('*');
            return;
        }

        $countryId = $this->getRequest()->getParam('country_id');

        try {
            if (! $this->countryHasChanged($countryId)) {
                $this->sendNoChangedResponse();
                return;
            }

            $this->changeCountry($countryId);
            $this->sendUpdates();
        } catch (\Exception $e) {
            if ($e->isReload()) {
                $this->handleReloadException($e);
            }
            $this->addExceptionMessage($e);
        }
    }

    /**
     * @param $countryId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function countryHasChanged($countryId)
    {
        $shippingAddress = $this->getShippingAddress();

        return $countryId !== $shippingAddress->getCountryId();
    }

    /**
     * @param $countryId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function changeCountry($countryId) : void
    {
        $address = $this->getShippingAddress();
        $address->setCountryId($countryId);

        $this->createNewSveaOrder($this->checkoutSession->getQuote());
    }

    /**
     * @param Quote $quote
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Svea\Checkout\Model\CheckoutException
     * @throws \Exception
     */
    private function createNewSveaOrder(Quote $quote)
    {
        $checkout = $this->getSveaCheckout();
        $checkout->setCheckoutContext($this->sveaCheckoutContext);
        $refHandler = $checkout->getRefHelper();

        // Genereta new quote signature
        $newSignature = $this->sveaCheckoutContext->getHelper()->generateHashSignatureByQuote($quote);

        if ($newSignature == $refHandler->getQuoteSignature()) {
            return;
        }

        // Init checkout
        $checkout = $checkout->initCheckout(false, false);

        // Generate new response
        $paymentHandler = $checkout->getSveaPaymentHandler();
        $paymentResponse = $paymentHandler->initNewSveaCheckoutPaymentByQuote($quote);

        $orderId = $paymentResponse->getOrderId();
        $refHandler->setSveaOrderId($orderId);
        $refHandler->setQuoteSignature($newSignature);
        $refHandler->generateClientOrderNumberToQuote();

        // Generate new svea hash
        // $refHandler->resetSveaHash();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShippingAddress() : \Magento\Quote\Model\Quote\Address
    {
        $quote = $this->checkoutSession->getQuote();
        $shippingAddress = $quote->getShippingAddress();

        return $shippingAddress;
    }

    /**
     *
     */
    private function sendNoChangedResponse() : void
    {
        $response = ['ok' => true];
        $this->getResponse()->setBody(json_encode($response));
    }

    /**
     * @param \Exception $e
     */
    private function handleReloadException(\Exception $e)
    {
        $response = [];
        $response['reload'] = 1;
        $response['messages'] = $e->getMessage();
        $this->getResponse()->setBody(json_encode($response));
    }

    /**
     * @param array $blocks
     */
    private function sendUpdates($blocks = ['shipping_method','cart','coupon','messages', 'svea','newsletter', 'svea_snippet'])
    {
        if (!in_array('messages', $blocks)) {
            $blocks[] = 'messages';
        }

        $checkout = $this->getSveaCheckout();
        $response = [
            'ctrlkey' => $checkout->getCheckoutSession()->getSveaQuoteSignature(),
            'ok' => true
        ];

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
            $sveaSnippet = sprintf(
                '<div id="svea-checkout_svea"><div id="sveaIframeSnippet">%s</div></div>',
                $checkout->getSveaPaymentHandler()->getIframeSnippet()
            );
            $response['updates']['svea'] = $sveaSnippet;
        }

        $this->getResponse()->setBody(json_encode($response));
    }

    /**
     * @param \Exception $e
     */
    private function addExceptionMessage(\Exception $e)
    {
        $this->messageManager->addErrorMessage(
            $e->getMessage() ? $e->getMessage() : __('Cannot update checkout (%1)', get_class($e))
        );
    }
}
