<?php

namespace Svea\Checkout\Controller\Order;

use Magento\Framework\Exception\NoSuchEntityException;

class Confirmation extends Push
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $checkout = $this->getSveaCheckout();
        $checkout->setCheckoutContext($this->sveaCheckoutContext);
        $sveaHash = $this->getRequest()->getParam('hash'); // for security!

        // needed by extended class (Push)
        $this->pushRepo = $this->pushRepositoryFactory->create();


        // it seems like we dont have quote information here, so we try to load everything with the svea_order_id instead!
        if (!$sveaOrderId = $checkout->getRefHelper()->getSveaOrderId()) {
            $checkout->getLogger()->error(sprintf("Confirmation Error: Svea Order Not found. Svea Order ID %s.", $sveaOrderId));
            $this->messageManager->addErrorMessage(sprintf("Missing Svea Order ID. Please try again."));
            return $this->_redirect('*');
        }


        $pushRepo = $this->pushRepositoryFactory->create();
        $push = null;
        for ($i = 0; $i<4;$i++) {
            try {
                $push = $pushRepo->get($sveaOrderId);
                if ($push->getOrderId()) {
                   break;
                }
            } catch (NoSuchEntityException $e) {
                // do nothing
                $push = null;
            }

            // sleep for 5 seconds, we wait for the push!
            sleep(5);
        }

        $jofabLastOrderId = $this->checkMagentoOrderBySveaId($sveaOrderId);

        // the push should have been created in validateOrder, should be long before we are here...
        if ((!$jofabLastOrderId && !$push) ) {
            $checkout->getLogger()->error(sprintf("Confirmation Error: Could not create order. No push found.Svea order id: %s.", $sveaOrderId));
            $this->messageManager->addErrorMessage(sprintf("Could not create order. Please contact the website admin. Svea Order ID: %s.", $sveaOrderId));
            return $this->_redirect('*');
        }

        // if the push hasn't created an order yet, (we have waited 20 seconds), we will try to create the order here!
        if ($push->getOrderId()) {
            $lastOrderId = $push->getOrderId();
        } elseif ($jofabLastOrderId) {
            $lastOrderId = $jofabLastOrderId;
        } else {
            try {
                $lastOrderId = $this->tryToCreateOrder($sveaOrderId, $sveaHash);
                if (!$lastOrderId) {
                    throw new \Exception("Could not create order.");
                }
            } catch (\Exception $e) {
                $checkout->getLogger()->error(sprintf("Confirmation Error: Could not create order, push id: %s svea order id: %s. Error: %s", $push->getId(), $sveaOrderId, $e->getMessage()));
                $this->messageManager->addErrorMessage(sprintf("Could not create order. Please contact the website admin, with this push id: %s and this order id: %s.", $push->getId(), $sveaOrderId));
                return $this->_redirect('*');
            }
        }


        // try load order by id
        try {
            $order = $this->loadOrder($lastOrderId);
        } catch (\Exception $e) {
            // If there is an order, but we couldn't load it due to technical problems, this could in worst cases lead to the user places an new order...

            $checkout->getLogger()->error(sprintf("Confirmation Error: Could not load Order, push id: %s last order id: %s. Error: %s", $push->getId(), $lastOrderId, $e->getMessage()));
            $this->messageManager->addErrorMessage(sprintf("Could not continue. Please contact the website admin, with this push id: %s and this order id: %s.", $push->getId(), $lastOrderId));
            return $this->_redirect('*');
        }


        // unset our checkout sessions
        $this->getSveaCheckout()->getRefHelper()->unsetSessions(true, true);
        // clear old sessions
        $session = $this->getCheckoutSession();
        $session->clearHelperData();
        $session->clearQuote()->clearStorage();

        // add order information to the session
        $session
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId())
            ->setLastOrderStatus($order->getStatus());

        // we set new sessions
        $session
            ->setSveaOrderId($sveaOrderId) // we need this in the success page
            ->setLastQuoteId($order->getQuoteId()) // we need this in the success page
            ->setLastSuccessQuoteId($order->getQuoteId());

        return $this->_redirect('*/*/success');
    }

    /**
     * @param $sveaOrderId
     *
     * @return bool
     */
    private function checkMagentoOrderBySveaId($sveaOrderId)
    {
        $orderCollection = $this->sveaCheckoutContext->getOrderCollectionFactory()->create();
        $orderCollection
            ->addFieldToFilter('svea_order_id', ['eq' => $sveaOrderId])
            ->load();

        return $orderCollection->count() ? $orderCollection->getFirstItem()->getId() : null;
    }

    /**
     * Only works for >= 2.3.0
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
    */
}
