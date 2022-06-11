<?php
/**
 * A Magento 2 module named Sharespine/Api
 * Copyright (C) 2019  Sharespine
 *
 * This file is part of Sharespine/Api.
 *
 * Sharespine/Api is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Sharespine\Api\Model;

use Magento\Framework\Exception\InputException;

class OrderManagement implements \Sharespine\Api\Api\OrderManagementInterface
{

    protected $storeManager;
    protected $productRepository;
    protected $formkey;
    protected $quote;
    protected $quoteManagement;
    protected $customerFactory;
    protected $customerRepository;
    protected $orderService;
    protected $responseInterface;
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $shippingConfig;
    protected $quoteCurrency;
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Data\Form\FormKey $formkey,
     * @param \Magento\Quote\Model\QuoteFactory $quote,
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory,
     * @param \Magento\Sales\Model\Service\OrderService $orderService,
     * @param \Magento\Framework\App\ResponseInterface $responseInterface,
     * @param \Magento\Sales\Model\OrderRepository $orderRepository,
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
     * @param \Magento\Shipping\Model\Config $shipconfig,
     * @param \Magento\Quote\Model\Cart\Currency $quoteCurrency
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Framework\App\ResponseInterface $responseInterface,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Shipping\Model\Config $shipconfig,
        \Magento\Quote\Model\Cart\Currency $quoteCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteCurrency = $quoteCurrency;
        $this->shippingConfig = $shipconfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->responseInterface = $responseInterface;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function postOrder($order)
    {
        //checking data fields
        $valid = $this->validate($order);
        if ($valid !== true){
            throw new InputException(__($valid));
        }
        return $this->createMageOrder($order);
    }

    private function createMageOrder($orderData) {
        $store = $this->storeManager->getStore($orderData['storeId']);
        $quote=$this->quote->create();
        $quote->setCustomerId(null);
        $quote->setCustomerEmail($orderData['email']);
        $quote->setCustomerIsGuest(true);
        $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
        $quote->setCheckoutMethod(\Magento\Quote\Api\CartManagementInterface::METHOD_GUEST);
        $quote->setStore($store);
        $quote->setQuoteCurrencyCode($orderData['currency']);
        if (array_key_exists('billingAddress', $orderData) && $this->validateAddress($orderData['billingAddress']) === true){
            $quote->getBillingAddress()->addData($orderData['billingAddress']);
        }
        else {
            $quote->getBillingAddress()->addData($orderData['shippingAddress']);
        }
        $quote->getShippingAddress()->addData($orderData['shippingAddress']);
        if (array_key_exists('vatNr', $orderData)){
            $quote->getShippingAddress()->setVatId($orderData['vatNr']);
            $quote->getBillingAddress()->setVatId($orderData['vatNr']);
            $quote->getShippingAddress()->save();
            $quote->getBillingAddress()->save();
        }
        if (array_key_exists('orgNr', $orderData)) {
            $quote->getShippingAddress()->setCompany($orderData['orgNr']);
        }
        $catalogPath = "tax/calculation/price_includes_tax";
        $includingTax = $this->scopeConfig->getValue($catalogPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $orderData['storeId']);
        foreach($orderData['cart']['items'] as $item){
            $product = $this->productRepository->getById($item['id']);
            $product->setStoreId($orderData['storeId']);
            $product->setPrice($item['price']);
            $quoteItem = $quote->addProduct(
                $product,
                intval($item['qty'])
            );
            $quoteItem->setNoDiscount(1);
            if (array_key_exists('taxrate', $item)) {
                $taxMultiplier = 1 + ($item['taxrate'] / 100);
            }
            if ($includingTax > 0){
                $quoteItem->setPriceInclTax($item['price']);
                if (array_key_exists('taxrate', $item)) {
                    $quoteItem->setPrice($item['price'] / $taxMultiplier);
                    $quoteItem->setCustomPrice($item['price']);
                    $quoteItem->setOriginalCustomPrice($item['price']);
                }
            }
            else {
                $quoteItem->setPrice($item['price']);
                $quoteItem->setCustomPrice($item['price']);
                $quoteItem->setOriginalCustomPrice($item['price']);
                if (array_key_exists('taxrate', $item)) {
                    $quoteItem->setPriceInclTax($item['price'] * $taxMultiplier);
                }
            }
            if (array_key_exists('taxrate', $item)){
                $quoteItem->setTaxPercent($item['taxrate']);
            }
            if (array_key_exists('name', $item)){
                $quoteItem->setName($item['name']);
            }
            if (array_key_exists('sku', $item)){
                $quoteItem->setSku($item['sku']);
            }
            if (array_key_exists('discount', $item)){
                $quoteItem->setDiscountAmount($item['discount']);
            }
            if (array_key_exists('comment', $item)){
                $quoteItem->addMessage($item['comment']);
            }
        }
        if (array_key_exists('ordernumber', $orderData)){
            $quote->setReservedOrderId($orderData['ordernumber']);
        }

        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod($orderData['cart']['shippingMethod']);
        $quote->setPaymentMethod($orderData['cart']['paymentMethod']);
        $quote->setInventoryProcessed(false);
        $quote->save();

        $quote->getPayment()->importData(['method' => $orderData['cart']['paymentMethod']]);


        $quote->collectTotals()->save();
        $quote->setQuoteCurrencyCode($orderData['currency']);

        $order = $this->quoteManagement->submit($quote);
        if (array_key_exists("extraFields", $orderData)){
            foreach ($orderData['extraFields'] as $key => $val){
                $order->setData($key, $val);
            }
        }
        if (array_key_exists("comments", $orderData)){
            if (is_array($orderData['comments'])) {
                foreach ($orderData['comments'] as $comment) {
                    $order->addStatusHistoryComment($comment);
                }
            }
            else {
                $order->addStatusHistoryComment($orderData['comments']);
            }
        }
        $order->setStatus($orderData['cart']['orderstatus']);
        if (array_key_exists('purchaseDate', $orderData)) {
            $order->setCreatedAt($orderData['purchaseDate']);
            $order->setUpdatedAt($orderData['purchaseDate']);
        }
        $order->save();
        $order->setEmailSent(0);
        $tmp = $order->getShippingAmount();
        $tmpBase = $order->getBaseShippingAmount();
        $tmpTax = $order->getTaxAmount();
        $tmpShippingTax = $order->getShippingTaxAmount();
        $order->setGrandTotal($order->getGrandTotal() - $tmp - $tmpShippingTax);
        $order->setBaseGrandTotal($order->getBaseGrandTotal() - $tmpBase - $tmpShippingTax);
        $order->setTaxAmount($tmpTax - $tmpShippingTax);
        $order->setShippingAmount(0);
        $order->setShippingInclTax(0);
        $order->setShippingTaxAmount(0);
        $order->setBaseShippingAmount(0);
        $order->setBaseShippingInclTax(0);
        $order->setBaseShippingTaxAmount(0);
        $order->save();
        return $this->orderRepository->get($order->getEntityId());
    }

    private function validate($order){
        if (!array_key_exists('shippingAddress', $order)){
            return "missing shippingAddress";
        }
        $valid = $this->validateAddress($order['shippingAddress']);
        if ($valid !== true){
            return "missing shipping address " . $valid;
        }
        if (!array_key_exists('orderstatus', $order['cart'])){
            return "missing orderstatus";
        }
        if (!array_key_exists('email', $order)){
            return "missing email";
        }
        if (!array_key_exists('cart', $order)){
            return "missing cart";
        }
        if (!array_key_exists('items', $order['cart'])){
            return "missing items";
        }
        if (!array_key_exists('shippingMethod', $order['cart'])){
            return "missing shippingMethod";
        }
        if (!array_key_exists('paymentMethod', $order['cart'])){
            return "missing paymentMethod";
        }
        if (!array_key_exists('currency', $order)){
            return "missing currency";
        }
        if (!array_key_exists('storeId', $order)){
            return "missing storeId";
        }
        if (count($order['cart']['items']) < 1){
            return "no items";
        }
        foreach ($order['cart']['items'] as $item){
            if (!array_key_exists('id', $item)){
                return "missing product id";
            }
            if (!array_key_exists('qty', $item)){
                return "missing qty";
            }
            if (!array_key_exists('price', $item)){
                return "missing price";
            }
        }
        return true;
    }

    private function validateAddress($address){
        if (!array_key_exists('firstname', $address)){
            return "firstname";
        }
        if (!array_key_exists('lastname', $address)){
            return "lastname";
        }
        if (!array_key_exists('street', $address)){
            return "street";
        }
        if (!array_key_exists('city', $address)){
            return "city";
        }
        if (!array_key_exists('country_id', $address)){
            return "country_id";
        }
        if (!array_key_exists('postcode', $address)){
            return "postcode";
        }
        return true;
    }
}
